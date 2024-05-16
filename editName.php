<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db_connection.php';

    $user_id = $_POST['user_id'];
    $fname = $_POST['firstName'];
    $lname = $_POST['lastName'];

    // Update first name and last name in the database
    $stmt = $pdo->prepare("UPDATE users SET fname = ?, lname = ? WHERE user_id = ?");
    $stmt->execute([$fname, $lname, $user_id]);

    // Redirect back to the profile page after updating name
    header("Location: profile.php?user_id=" . $user_id);
    exit();
} else {
    header("Location: mainpage.php"); // Redirect if accessed directly
    exit();
}
