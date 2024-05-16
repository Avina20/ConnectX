<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db_connection.php'; // Include your database connection file

    $dependentsDesc = $_POST['dependentsDesc'];
    $userId = $_POST['userId'];

    // Update dependents description in the database
    $stmt = $pdo->prepare("UPDATE Users SET dependents_desc = ? WHERE user_id = ?");
    $stmt->execute([$dependentsDesc, $userId]);

    // Redirect back to the profile page after updating dependents description
    header("Location: profile.php?user_id=" . $userId);
    exit();
} else {
    header("Location: mainpage.php"); // Redirect if accessed directly
    exit();
}
