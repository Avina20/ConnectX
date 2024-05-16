<?php
// Start or resume session
session_start();

// Check if the user is logged in and session variable is set
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or handle accordingly
    header("Location: login.php");
    exit();
}

// Get the logged-in user ID from session
$loggedInUserId = $_SESSION['user_id'];

// Get the selected friend's user ID from the query parameters
if (isset($_GET['selectedFriendID'])) {
    $selectedFriendId = $_GET['selectedFriendID'];

    // Your database connection code
    require_once('db_connection.php');

    try {
        // Prepare SQL statement to insert into friend_request_relation
        $sql = "INSERT INTO friend_request (sender_id, receiver_id, request_sent_ts, status) 
                VALUES (:sender_id, :receiver_id, NOW(), 'pending')";
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':sender_id', $loggedInUserId, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_id', $selectedFriendId, PDO::PARAM_INT);

        // Execute statement
        $stmt->execute();

        // Show JavaScript alert confirming friend request sent
        echo '<script>alert("Friend request has been sent to the selected friend.");</script>';
        echo '<script>window.location.href = "friends.php";</script>';
    } catch (PDOException $e) {
        // Handle errors
        echo "Error: " . $e->getMessage();
    }
} else {
    echo '<script>alert("Error: Selected friend\'s user ID not found.");</script>';
}
