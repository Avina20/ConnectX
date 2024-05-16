<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db_connection.php'; // Include your database connection file

    $newUsername = $_POST['newUsername'];
    $userId = $_POST['userId'];

    // Update username in Users table
    $stmt = $pdo->prepare("UPDATE users SET user_name = ? WHERE user_id = ?");
    $stmt->execute([$newUsername, $userId]);

    // Update username in Access relation
    $stmt_access = $pdo->prepare("UPDATE access SET user_name = ? WHERE user_id = ?");
    $stmt_access->execute([$newUsername, $userId]);

    // Redirect back to the profile page after updating username
    header("Location: profile.php?user_id=" . $userId);
    exit();
} else {
    header("Location: mainpage.php"); // Redirect if accessed directly
    exit();
}
