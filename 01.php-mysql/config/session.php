<?php
require_once __DIR__ . '/db.php';

// Secure session settings (must be set before session_start)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime',  SESSION_TTL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Helpers ──────────────────────────────────────────────────

/** Redirect helper */
function redirect(string $url): void {
    header('Location: ' . $url);
    exit();
}

/** Return true if user is logged in and session is fresh */
function isLoggedIn(): bool {
    if (empty($_SESSION['user_id'])) return false;
    if (time() - ($_SESSION['last_active'] ?? 0) > SESSION_TTL) {
        session_destroy();
        return false;
    }
    $_SESSION['last_active'] = time();
    return true;
}

/** Force login — call at top of protected pages */
function requireLogin(): void {
    if (!isLoggedIn()) redirect('/login.php');
}

