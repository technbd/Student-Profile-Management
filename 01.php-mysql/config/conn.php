<?php
// Make sure the path is correct
require_once __DIR__ . '/db.php';

// Call the function to get the mysqli connection
$conn = db();

if ($conn && !$conn->connect_error) {
    echo "✅ DB Connected";
} else {
    echo "❌ DB Not Connected";
}
?>

