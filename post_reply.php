<?php
// Include database connection
include "db_connection.php";

// Start the session
session_start();

// Check if the reply form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reply-content"])) {
    // Get the reply content from the form
    $reply_content = $_POST["reply-content"];

    // Get the thread_id from the URL
    $thread_id = $_GET['thread_id']; // Assuming thread_id is passed in the URL

    $user_id = $_SESSION['user_id'];

    // Insert the reply into the database
    $insert_sql = "INSERT INTO message (thread_id, body, author_id, created_on, primary_msg) 
                   VALUES (:thread_id, :body, :user_id, NOW(), FALSE)";
    $insert_stmt = $pdo->prepare($insert_sql);
    $insert_stmt->execute([
        'thread_id' => $thread_id,
        'body' => $reply_content,
        'user_id' => $_SESSION['user_id']
    ]); // Assuming user_id is stored in the session

    // Redirect back to the thread details page after inserting the reply
    header("Location: friend_thread_details.php?thread_id=$thread_id");
    exit(); // Stop script execution after redirection
}
