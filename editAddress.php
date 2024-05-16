<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db_connection.php'; // Include your database connection file

    $addressLine1 = $_POST['addressLine1'];
    $addressLine2 = $_POST['addressLine2'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zipcode = $_POST['zipcode'];
    $country = $_POST['country'];
    $userId = $_POST['userId'];

    // Update address in the database
    $stmt = $pdo->prepare("UPDATE users SET address_line1 = ?, address_line2 = ?, city = ?, state = ?, zipcode = ?, country = ? WHERE user_id = ?");
    $stmt->execute([$addressLine1, $addressLine2, $city, $state, $zipcode, $country, $userId]);

    // Redirect back to the profile page after updating address
    header("Location: profile.php?user_id=" . $userId);
    exit();
} else {
    header("Location: mainpage.php"); // Redirect if accessed directly
    exit();
}
