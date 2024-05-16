<?php
session_start();

// Include database connection file
include_once "db_connection.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit(); // Stop script execution after redirection
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['threadTitle'];
    $groupId = $_POST['groupSelect'];
    $messageBody = $_POST['threadMessage'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $userId = $_SESSION['user_id'];

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Insert thread into the database
        $insertThreadQuery = "INSERT INTO thread (title, thread_user_id, group_id, posted_on) VALUES (:title, :userId, :groupId, NOW())";
        $stmtThread = $pdo->prepare($insertThreadQuery);
        $stmtThread->bindParam(':title', $title);
        $stmtThread->bindParam(':userId', $userId);
        $stmtThread->bindParam(':groupId', $groupId);
        $stmtThread->execute();
        $threadId = $pdo->lastInsertId();

        // Insert message into the database with primary_msg set to true
        $insertMessageQuery = "INSERT INTO message (thread_id, body, author_id, created_on, latitude, longitude, primary_msg) VALUES (:threadId, :messageBody, :userId, NOW(), :latitude, :longitude, TRUE)";
        $stmtMessage = $pdo->prepare($insertMessageQuery);
        $stmtMessage->bindParam(':threadId', $threadId);
        $stmtMessage->bindParam(':messageBody', $messageBody);
        $stmtMessage->bindParam(':userId', $userId);
        $stmtMessage->bindParam(':latitude', $latitude);
        $stmtMessage->bindParam(':longitude', $longitude);
        $stmtMessage->execute();

        // Commit transaction if both thread and message insertion succeed
        $pdo->commit();
        header("Location: mainpage.php?success=Thread%20created%20successfully!");
    } catch (PDOException $e) {
        // Rollback transaction and display error message
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
