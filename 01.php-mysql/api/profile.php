<?php
// api/profile.php
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/config/session.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}

$action = $_GET['action'] ?? '';

// PHP 7.4 compatible — match() requires PHP 8.0+
switch ($action) {
    case 'get':    getProfile();    break;
    case 'update': updateProfile(); break;
    case 'upload': uploadPicture(); break;
    default:       jsonError('Unknown action: ' . htmlspecialchars($action));
}

// ── GET ───────────────────────────────────────────────────────
function getProfile(): void {
    $db   = db();
    $uid  = (int) $_SESSION['user_id'];
    $stmt = $db->prepare(
        'SELECT s.*, u.username, u.email AS account_email, u.role
         FROM students s
         JOIN users u ON u.id = s.user_id
         WHERE s.user_id = ?'
    );
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Profile not found.']);
        return;
    }

    $picFile = UPLOAD_DIR . $row['profile_picture'];
    $row['picture_url'] = (
        $row['profile_picture'] !== 'default.png' && file_exists($picFile)
    )
        ? UPLOAD_URL . $row['profile_picture']
        : 'https://ui-avatars.com/api/?name=' . urlencode($row['full_name'])
          . '&size=200&background=c9a84c&color=0a0c10&bold=true&format=png';

    echo json_encode(['success' => true, 'profile' => $row]);
}

// ── UPDATE ────────────────────────────────────────────────────
function updateProfile(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonError('POST required.'); return;
    }

    // Admins have no student row — block update
    if (($_SESSION['role'] ?? '') === 'admin') {
        jsonError('Admin accounts do not have an editable student profile.'); return;
    }

    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        jsonError('Invalid JSON payload.'); return;
    }

    $uid = (int) $_SESSION['user_id'];
    $db  = db();

    // email is intentionally excluded — it has a UNIQUE constraint
    // and is managed separately; updating it here causes DB errors.
    $allowed = [
        'full_name', 'phone', 'date_of_birth', 'gender',
        'department', 'year_of_study', 'address', 'gpa'
    ];

    $sets  = [];
    $vals  = [];
    $types = '';

    foreach ($allowed as $field) {
        if (!array_key_exists($field, $data)) continue;
        $v = (string) $data[$field];
        $v = ($v === '') ? null : trim($v);
        $sets[]  = "`$field` = ?";
        $vals[]  = $v;
        $types  .= 's';
    }

    if (empty($sets)) {
        jsonError('No valid fields to update.'); return;
    }

    $vals[]  = $uid;
    $types  .= 'i';

    $sql  = 'UPDATE students SET ' . implode(', ', $sets) . ' WHERE user_id = ?';
    $stmt = $db->prepare($sql);

    if (!$stmt) {
        jsonError('Query prepare failed: ' . $db->error); return;
    }

    $stmt->bind_param($types, ...$vals);

    if (!$stmt->execute()) {
        jsonError('Update failed: ' . $stmt->error); return;
    }

    $stmt->close();

    if (!empty($data['full_name'])) {
        $_SESSION['full_name'] = trim($data['full_name']);
    }

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
}

// ── UPLOAD ────────────────────────────────────────────────────
function uploadPicture(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonError('POST required.'); return;
    }

    if (!isset($_FILES['picture'])) {
        jsonError('No file received. Make sure the field name is "picture".'); return;
    }

    $file = $_FILES['picture'];

    // Check PHP upload error first
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errs = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload_max_filesize limit.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form MAX_FILE_SIZE limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension blocked the upload.',
        ];
        jsonError($errs[$file['error']] ?? 'Upload error code: ' . $file['error']);
        return;
    }

    if ($file['size'] > MAX_UPLOAD) {
        jsonError('File too large. Maximum allowed size is 5 MB.'); return;
    }

    // Validate MIME type using finfo (more reliable than $_FILES['type'])
    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mimeType, $allowedMimes, true)) {
        jsonError('Invalid file type "' . $mimeType . '". Only JPG, PNG, GIF, WEBP allowed.'); return;
    }

    // Confirm it is a real image
    if (!@getimagesize($file['tmp_name'])) {
        jsonError('File does not appear to be a valid image.'); return;
    }

    // Ensure upload directory exists and is writable
    if (!is_dir(UPLOAD_DIR)) {
        if (!mkdir(UPLOAD_DIR, 0755, true)) {
            jsonError('Could not create upload directory: ' . UPLOAD_DIR); return;
        }
    }

    if (!is_writable(UPLOAD_DIR)) {
        jsonError('Upload directory is not writable. Run: chmod 775 ' . UPLOAD_DIR); return;
    }

    $uid      = (int) $_SESSION['user_id'];
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'stu_' . $uid . '_' . time() . '.' . $ext;
    $dest     = UPLOAD_DIR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        jsonError('move_uploaded_file() failed. Check directory permissions on: ' . UPLOAD_DIR); return;
    }

    // Delete old picture if it exists and is not the default
    $db       = db();
    $oldStmt  = $db->prepare('SELECT profile_picture FROM students WHERE user_id = ?');
    $oldStmt->bind_param('i', $uid);
    $oldStmt->execute();
    $oldRow = $oldStmt->get_result()->fetch_assoc();
    $oldStmt->close();

    if (!empty($oldRow['profile_picture']) && $oldRow['profile_picture'] !== 'default.png') {
        $oldFile = UPLOAD_DIR . $oldRow['profile_picture'];
        if (file_exists($oldFile)) @unlink($oldFile);
    }

    // Save new filename to DB
    $stmt = $db->prepare('UPDATE students SET profile_picture = ? WHERE user_id = ?');
    if (!$stmt) {
        jsonError('DB prepare failed: ' . $db->error); return;
    }
    $stmt->bind_param('si', $filename, $uid);
    if (!$stmt->execute()) {
        jsonError('DB update failed: ' . $stmt->error); return;
    }
    $stmt->close();

    echo json_encode([
        'success'     => true,
        'message'     => 'Profile picture updated.',
        'picture_url' => UPLOAD_URL . $filename,
    ]);
}

function jsonError(string $msg): void {
    echo json_encode(['success' => false, 'message' => $msg]);
}

