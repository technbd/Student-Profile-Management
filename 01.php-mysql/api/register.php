<?php
// api/register.php
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

$raw   = file_get_contents('php://input');
$data  = json_decode($raw, true) ?? [];

// ── Read & sanitise inputs ────────────────────────────────────
$full_name   = trim($data['full_name']        ?? '');
$username    = trim($data['username']         ?? '');
$email       = strtolower(trim($data['email'] ?? ''));
$password    = $data['password']              ?? '';
$confirm     = $data['confirm_password']      ?? '';
$department  = trim($data['department']       ?? '');
$year        = intval($data['year_of_study']  ?? 0);
$phone       = trim($data['phone']            ?? '');
$gender      = trim($data['gender']           ?? '');
$dob         = trim($data['date_of_birth']    ?? '');

// ── Validation ───────────────────────────────────────────────
$errors = [];

if ($full_name === '')  $errors[] = 'Full name is required.';
if ($username  === '')  $errors[] = 'Username is required.';
elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username))
    $errors[] = 'Username: 3–30 chars, letters/numbers/underscore only.';

if ($email === '')  $errors[] = 'Email is required.';
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = 'Invalid email address.';

if (strlen($password) < 8)
    $errors[] = 'Password must be at least 8 characters.';
elseif (!preg_match('/[A-Z]/', $password))
    $errors[] = 'Password must contain at least one uppercase letter.';
elseif (!preg_match('/[0-9]/', $password))
    $errors[] = 'Password must contain at least one number.';

if ($password !== $confirm)
    $errors[] = 'Passwords do not match.';

if ($department === '')
    $errors[] = 'Department is required.';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit();
}

// ── Check duplicates ─────────────────────────────────────────
$db   = db();
$chk  = $db->prepare(
    'SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1'
);
$chk->bind_param('ss', $username, $email);
$chk->execute();
$chk->store_result();
if ($chk->num_rows > 0) {
    echo json_encode(['success' => false,
                      'message' => 'Username or email is already registered.']);
    exit();
}
$chk->close();

// ── Insert user ──────────────────────────────────────────────
$hash  = password_hash($password, PASSWORD_DEFAULT);
$ins   = $db->prepare(
    'INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, "student")'
);
$ins->bind_param('sss', $username, $email, $hash);
if (!$ins->execute()) {
    echo json_encode(['success' => false,
                      'message' => 'Registration failed. Please try again.']);
    exit();
}
$user_id = (int) $db->insert_id;
$ins->close();

// ── Generate student code ─────────────────────────────────────
$student_code = 'STU-' . date('Y') . '-' . str_pad($user_id, 4, '0', STR_PAD_LEFT);
$today        = date('Y-m-d');
$dob_val      = ($dob !== '' && strtotime($dob)) ? $dob : null;
$phone_val    = $phone  !== '' ? $phone  : null;
$gender_val   = $gender !== '' ? $gender : null;
$year_val     = $year   > 0   ? $year   : null;

// ── Insert student profile ────────────────────────────────────
$sp = $db->prepare(
    'INSERT INTO students
       (user_id, student_code, full_name, email, phone,
        date_of_birth, gender, department, year_of_study, enrollment_date)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$sp->bind_param(
    'isssssssss',
    $user_id, $student_code, $full_name, $email, $phone_val,
    $dob_val, $gender_val, $department, $year_val, $today
);

if ($sp->execute()) {
    echo json_encode([
        'success'      => true,
        'message'      => 'Account created successfully!',
        'student_code' => $student_code,
    ]);
} else {
    // Roll back the user row if profile insert failed
    $db->query("DELETE FROM users WHERE id = $user_id");
    echo json_encode(['success' => false,
                      'message' => 'Could not create student profile. Try again.']);
}
$sp->close();

