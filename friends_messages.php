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

// Execute SQL query to retrieve Friend feed Messages
$sql = "SELECT 
            t.thread_id,
            t.title, 
            t.thread_user_id, 
            m.body, 
            m.created_on, 
            COUNT(r.message_id) AS reply_count, -- Count of replies
            CONCAT(u.fname, ' ', u.lname) AS author_name
            FROM 
                thread t
            JOIN 
                message m ON t.thread_id = m.thread_id AND m.primary_msg = TRUE -- Only messages where primary_msg is true
            LEFT JOIN 
                message r ON m.thread_id = r.thread_id AND r.primary_msg = FALSE -- Replies where primary_msg is false
            JOIN 
                usergroup ug ON t.group_id = ug.group_id
            JOIN
                users u ON t.thread_user_id = u.user_id
            WHERE 
                ug.user_id = :user_id AND t.group_id = 1
            GROUP BY 
                t.thread_id, 
                t.title, 
                t.thread_user_id, 
                m.body, 
                m.created_on,
                author_name;";

// Prepare and execute the query
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<style>
    .message-container {
        margin-bottom: 20px;
        border: 1px solid #ccc;
        padding: 10px;
    }

    .meta {
        font-size: smaller;
        color: #666;
    }
</style>

<!-- HTML code starts here -->
<?php if (!empty($result)) : ?>
    <?php foreach ($result as $row) : ?>
        <div class="message-container">
            <h5 style="cursor: pointer;" onclick="window.location.href='friend_thread_details.php?thread_id=<?php echo $row['thread_id']; ?>'"><?php echo htmlspecialchars($row['title']); ?></h5>
            <p class="meta">
                by: <?php echo htmlspecialchars($row['author_name']); ?>
                on: <?php echo date("h:i A, M d, Y", strtotime($row['created_on'])); ?>
            </p>
            <p><?php echo htmlspecialchars($row['body']); ?></p>
            <?php
            // Format the count of replies
            $reply_count_formatted = $row['reply_count'] == 1 ? $row['reply_count'] . ' reply' : $row['reply_count'] . ' replies';
            ?>
            <p><?php echo htmlspecialchars($reply_count_formatted); ?></p>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <p>No messages available.</p>
<?php endif; ?>