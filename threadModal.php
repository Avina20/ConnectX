<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit(); // Stop script execution after redirection
}

$user_id = $_SESSION['user_id'];

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
        $conn->beginTransaction();

        // Insert thread into the database
        $insertThreadQuery = "INSERT INTO thread (title, thread_user_id, group_id) VALUES (:title, :userId, :groupId)";
        $stmtThread = $conn->prepare($insertThreadQuery);
        $stmtThread->bindParam(':title', $title);
        $stmtThread->bindParam(':userId', $userId);
        $stmtThread->bindParam(':groupId', $groupId);
        $stmtThread->execute();
        $threadId = $conn->lastInsertId();

        // Insert message into the database with primary_msg set to true
        $insertMessageQuery = "INSERT INTO message (thread_id, body, author_id, latitude, longitude, primary_msg) VALUES (:threadId, :messageBody, :userId, :latitude, :longitude, TRUE)";
        $stmtMessage = $conn->prepare($insertMessageQuery);
        $stmtMessage->bindParam(':threadId', $threadId);
        $stmtMessage->bindParam(':messageBody', $messageBody);
        $stmtMessage->bindParam(':userId', $userId);
        $stmtMessage->bindParam(':latitude', $latitude);
        $stmtMessage->bindParam(':longitude', $longitude);
        $stmtMessage->execute();

        // Commit transaction if both thread and message insertion succeed
        $conn->commit();
        echo "Thread created successfully!";
    } catch (PDOException $e) {
        // Rollback transaction and display error message
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}

try {
    // Fetch groups from the database
    $fetchGroupsQuery = "SELECT * FROM \"group\"";
    $stmtGroups = $conn->query($fetchGroupsQuery);
    $groups = $stmtGroups->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching groups: " . $e->getMessage();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="modal fade" id="createThreadModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel" data-user-id="<?php echo $user_id; ?>">Start a new thread</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="process_thread.php" method="POST">
                        <div class="mb-3">
                            <label for="threadTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="threadTitle" name="threadTitle" placeholder="Enter title">
                        </div>
                        <div class="mb-3">
                            <label for="groupSelect" class="form-label">Select Groups</label>
                            <select class="form-select" id="groupSelect" name="groupSelect">
                                <option value="1">Friends</option>
                                <option value="2">Neighbors</option>
                                <option value="4">Hood Members</option>
                                <option value="3">Block Members</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="threadMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="threadMessage" name="threadMessage" rows="4" placeholder="Enter message"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Enter latitude">
                        </div>
                        <div class="mb-3">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Enter longitude">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Thread</button>
                </div>
                </form>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>

</html>