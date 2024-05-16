<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db_connection.php'; // Include your database connection file

    $newEmail = $_POST['newEmail'];
    $userId = $_POST['userId'];

    // Update email in the database
    $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE user_id = ?");
    $stmt->execute([$newEmail, $userId]);

    // Redirect back to the profile page after updating email
    header("Location: profile.php?user_id=" . $userId);
    exit();
} else {
    header("Location: mainpage.php"); // Redirect if accessed directly
    exit();
}
