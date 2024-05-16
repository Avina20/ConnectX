<?php
// Check if session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include "db_connection.php";

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit(); // Stop script execution after redirection
}

// Retrieve user information from session variables
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Check if thread_id is provided in the URL
if (!isset($_GET['thread_id']) || empty($_GET['thread_id'])) {
    // Redirect to home page if thread_id is not provided
    header("Location: home.php");
    exit(); // Stop script execution after redirection
}

// Get the thread_id from the URL
$thread_id = $_GET['thread_id'];

// Fetch thread details including group information
$thread_sql = "SELECT 
                    t.title,
                    t.thread_user_id,
                    t.posted_on,
                    CONCAT(u.fname, ' ', u.lname) AS author_name,
                    g.group_name,
                    m.body AS latest_reply_body,
                    CONCAT(u2.fname, ' ', u2.lname) AS latest_reply_author,
                    m.created_on AS latest_reply_created_on
                    FROM 
                    thread t
                    JOIN 
                    users u ON t.thread_user_id = u.user_id
                    JOIN
                    \"group\" g ON t.group_id = g.group_id
                    LEFT JOIN
                    message m ON t.thread_id = m.thread_id
                    LEFT JOIN
                    users u2 ON m.author_id = u2.user_id
                    WHERE 
                    t.thread_id = :thread_id
                    AND t.group_id = 4
                    ORDER BY
                    m.created_on DESC";

$thread_stmt = $pdo->prepare($thread_sql);
$thread_stmt->execute(['thread_id' => $thread_id]);
$thread_result = $thread_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch replies for the thread
$replies_sql = "SELECT 
                    m.body,
                    CONCAT(u.fname, ' ', u.lname) AS author_name,
                    m.created_on
                FROM 
                    message m
                JOIN 
                    users u ON m.author_id = u.user_id
                WHERE 
                    m.thread_id = :thread_id
                    AND m.primary_msg = FALSE"; // Fetch replies where primary_msg is false

$replies_stmt = $pdo->prepare($replies_sql);
$replies_stmt->execute(['thread_id' => $thread_id]);
$replies_result = $replies_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thread Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .reply-container {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 10px;
        }

        .meta {
            font-size: smaller;
            color: #666;
        }

        .reply-form {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px;
            background-color: #f8f9fa;
            /* Adjust background color as needed */
            box-shadow: 0px -2px 10px rgba(0, 0, 0, 0.1);
            /* Optional: Add a shadow */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="mainpage.php">ConnectX</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="friends.php">Friends</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="neighbors.php">Neighbors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="profile.php?user_id=<?php
                                                                                            echo $user_id;
                                                                                            ?>">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Sign Out</a>
                    </li>
                </ul>
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>
    <!-- <div class="container">
        <h3 class="my-3"><?php echo htmlspecialchars($thread_result['title']); ?></h3>
        <p>Started by: <?php echo htmlspecialchars($thread_result['author_name']); ?></p>
        <p>Posted on: <?php echo date("h:i A, M d, Y", strtotime($thread_result['posted_on'])); ?></p>

        <h4>Replies</h4>
        <?php if (!empty($replies_result)) : ?>
            <?php foreach ($replies_result as $reply) : ?>
                <div class="reply-container">
                    <p><?php echo htmlspecialchars($reply['body']); ?></p>
                    <p class="meta">Posted by: <?php echo htmlspecialchars($reply['author_name']); ?> on <?php echo date("h:i A, M d, Y", strtotime($reply['created_on'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No replies yet.</p>
        <?php endif; ?>
    </div> -->

    <div class="container">
        <h3 class="my-3"><?php echo htmlspecialchars($thread_result['title']); ?></h3>
        <p>Started by: <?php echo htmlspecialchars($thread_result['author_name']); ?></p>
        <p>Posted on: <?php echo date("h:i A, M d, Y", strtotime($thread_result['posted_on'])); ?></p>

        <h4>Replies</h4>
        <?php if (!empty($replies_result)) : ?>
            <?php foreach ($replies_result as $reply) : ?>
                <div class="reply-container">
                    <p><?php echo htmlspecialchars($reply['body']); ?></p>
                    <p class="meta">Posted by: <?php echo htmlspecialchars($reply['author_name']); ?> on <?php echo date("h:i A, M d, Y", strtotime($reply['created_on'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No replies yet.</p>
        <?php endif; ?>

        <!-- Reply form -->
        <form id="reply-form" method="post" action="hood_post_reply.php?thread_id=<?php echo htmlspecialchars($thread_id); ?>">
            <div class="mb-3">
                <label for="reply-content" class="form-label">Your Reply</label>
                <textarea class="form-control" id="reply-content" name="reply-content" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" id="reply-btn">Reply</button>
        </form>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-fzqyC+EjwcuqC3FcapfH0iKo0wQvA0v4um7IjUeuNQVWGMcNtEqluqWwluDTP+fO" crossorigin="anonymous"></script>
</body>

</html>