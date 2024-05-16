<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db_connection.php'; // Include your database connection file

    $block = $_POST['block'];
    $userId = $_POST['userId'];

    // Update block in the database
    $stmt = $pdo->prepare("UPDATE Users SET block_id = ? WHERE user_id = ?");
    $stmt->execute([$block, $userId]);

    // Redirect back to the profile page after updating block
    header("Location: profile.php?user_id=" . $userId);
    exit();
} else {
    header("Location: mainpage.php"); // Redirect if accessed directly
    exit();
}
