<?php
// Start the session
session_start();

// Include database connection
include "db_connection.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Retrieve logged-in user ID from session
$user_id = $_SESSION['user_id'];

// Check if form is submitted and the accept or reject button is clicked
if (isset($_POST['accept_request']) || isset($_POST['reject_request'])) {
    // Check if sender ID is provided
    if (isset($_POST['sender_id'])) {
        // Get sender ID from form data
        $sender_id = $_POST['sender_id'];

        // Check which button is clicked
        if (isset($_POST['accept_request'])) {
            // Accept friend request

            // Update status in friend_request_relation table
            $stmt = $pdo->prepare("UPDATE friend_request SET status = 'accepted', updated_ts=NOW() WHERE sender_id = ? AND receiver_id = ?");
            $stmt->execute([$sender_id, $user_id]);

            // Insert into friends table
            $stmt = $pdo->prepare("INSERT INTO friend (friend_one_id, friend_two_id, ts_since_friend) VALUES (?, ?, NOW())");
            // Assuming the logged-in user ID is friend_one_id and the sender ID is friend_two_id
            $stmt->execute([$user_id, $sender_id]);

            // Delete the friend request from the friend_request_relation table
            $stmt = $pdo->prepare("DELETE FROM friend_request WHERE sender_id = ? AND receiver_id = ?");
            $stmt->execute([$sender_id, $user_id]);

            // Redirect to a page (e.g., friends.php) after processing
            header("Location: friends.php");
            echo $sender_id;
            exit();
        } elseif (isset($_POST['reject_request'])) {
            // Reject friend request

            // Update status in friend_request_relation table
            $stmt = $pdo->prepare("UPDATE friend_request SET status = 'rejected', updated_ts=NOW() WHERE sender_id = ? AND receiver_id = ?");
            $stmt->execute([$sender_id, $user_id]);

            // Redirect to a page (e.g., friends.php) after processing
            header("Location: friends.php");
            exit();
        }
    }
}

// If form is not submitted or sender ID is not provided, redirect to friends.php
header("Location: friends.php");
exit();
