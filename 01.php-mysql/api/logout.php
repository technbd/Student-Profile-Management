<?php
// api/logout.php
require_once dirname(__DIR__) . '/config/session.php';
$_SESSION = [];
session_destroy();
header('Location: /login.php');
exit();

