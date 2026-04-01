<?php
// api/login.php
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

$raw      = file_get_contents('php://input');
$data     = json_decode($raw, true) ?? [];

$username = trim($data['username'] ?? '');
$password = $data['password']      ?? '';

if ($username === '' || $password === '') {
    echo json_encode(['success' => false,
                      'message' => 'Username and password are required.']);
    exit();
}

$db   = db();

// Accept login by username OR email
$stmt = $db->prepare(
    'SELECT id, username, email, password, role, is_active
     FROM users
     WHERE username = ? OR email = ?
     LIMIT 1'
);
$stmt->bind_param('ss', $username, $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
    exit();
}

if (!$user['is_active']) {
    echo json_encode(['success' => false, 'message' => 'Account is disabled. Contact admin.']);
    exit();
}

if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
    exit();
}

// ── Build session ─────────────────────────────────────────────
session_regenerate_id(true);

$_SESSION['user_id']     = $user['id'];
$_SESSION['username']    = $user['username'];
$_SESSION['role']        = $user['role'];
$_SESSION['last_active'] = time();

// Get student profile
$sp = $db->prepare(
    'SELECT student_code, full_name, profile_picture
     FROM students WHERE user_id = ? LIMIT 1'
);
$sp->bind_param('i', $user['id']);
$sp->execute();
$student = $sp->get_result()->fetch_assoc();
$sp->close();

$_SESSION['full_name'] = $student['full_name'] ?? $user['username'];

echo json_encode([
    'success'   => true,
    'message'   => 'Login successful.',
    'redirect'  => '/profile.php',
    'user'      => [
        'username'     => $user['username'],
        'role'         => $user['role'],
        'full_name'    => $_SESSION['full_name'],
        'student_code' => $student['student_code'] ?? null,
    ],
]);

