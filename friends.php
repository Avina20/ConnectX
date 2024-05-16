<?php
// Start the session
session_start();

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

// Retrieve list of friends
$stmt = $pdo->prepare("SELECT CONCAT(u.fname, ' ', u.lname) AS friend_name, u.user_name,  
                        DATE(f.ts_since_friend) AS date_since_friend
                        FROM friend f INNER JOIN users u ON (f.friend_two_id = u.user_id)
                        WHERE f.friend_one_id = ?");
$stmt->execute([$user_id]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display the main page content
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Custom CSS for styling the datalist */
        #usersList {
            width: calc(100% - 70px);
            /* Adjust the value according to your input field's padding and button width */
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
                        <a class="nav-link" href="block.php">Block</a>
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
            </div>
        </div>
    </nav>
    <!-- <div class="row justify-content-center py-4">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" list="usersList" placeholder="Add Friend">
                <datalist id="usersList">
                    <?php include "userList.php" ?>
                </datalist>
                <div class="input-group-append px-4">
                    <button class="btn btn-outline-secondary" type="button">Search</button>
                </div>
            </div>
        </div>
    </div> -->

    <div class="modal fade" id="friendModal" tabindex="-1" aria-labelledby="friendModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="friendModalLabel">Friend's Name</h5> <!-- Placeholder title -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>
                        Do you want to send a friend request to <span id="friendNameSpan"></span>?
                    </p>
                </div>
                <div class="modal-footer">
                    <form method="post" action="send_friend_request.php" id="friendRequestForm">
                        <input type="hidden" id="selectedFriendID" name="selectedFriendID">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-primary">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center py-4">
            <div class="col-md-6">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="input-group">
                        <select class="form-select" name="selectedUser" id="userSelect"> <!-- Added id attribute -->
                            <option value="" selected disabled>Select a user</option>
                            <?php include('userList.php'); ?>
                        </select>
                        <button type="submit" class="btn btn-outline-secondary">Send Friend Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <button type="submit" class="btn btn-outline-secondary">Incoming Friend Request</button>
            </div>
        </div>
    </div> -->


    <script>
        document.getElementById('userSelect').addEventListener('change', function() {
            var selectedFriendName = this.options[this.selectedIndex].text; // Get selected friend name
            var selectedFriendID = this.value; // Get selected friend ID

            // Set the friend name in the modal body
            document.getElementById('friendNameSpan').textContent = selectedFriendName;

            // Set the friend ID in the hidden input field
            document.getElementById('selectedFriendID').value = selectedFriendID;

            // Set the friend's full name as the modal title
            document.getElementById('friendModalLabel').textContent = selectedFriendName;

            // Show the modal
            var friendModal = new bootstrap.Modal(document.getElementById('friendModal'));
            friendModal.show();
            document.getElementById('friendRequestForm').action = 'send_friend_request.php?selectedFriendID=' + selectedFriendID;

        });
    </script>
    <div class="container">
        <h4 class="py-3">Friends</h4>
        <div class="row">
            <?php foreach ($friends as $friend) : ?>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $friend['friend_name']; ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo $friend['user_name']; ?></h6>
                            <p class="card-text">Date since friend: <?php echo $friend['date_since_friend']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="container">
        <h4 class="py-3">Incoming Friend Requests</h4>
        <div class="row">
            <?php
            // Prepare SQL statement to select incoming friend requests for the logged-in user
            $stmt = $pdo->prepare("SELECT CONCAT(u.fname, ' ', u.lname) AS sender_name, u.user_name AS sender_username, 
                                u.user_id AS sender_id,
                                DATE(fr.request_sent_ts) AS request_received_date
                                FROM friend_request fr
                                INNER JOIN users u ON fr.sender_id = u.user_id
                                WHERE fr.receiver_id = ?");
            $stmt->execute([$user_id]);
            $incomingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Loop through incoming friend requests and display them
            foreach ($incomingRequests as $request) : ?>
                <div class="col-md-6">
                    <div class="card mb-3" data-request-id="<?php echo $request['sender_id']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $request['sender_name']; ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo $request['sender_username']; ?></h6>
                            <p class="card-text">Request received on: <?php echo $request['request_received_date']; ?></p>
                            <form action="process_friend_request.php" method="POST" onsubmit="handleFriendRequest(this); ">
                                <input type="hidden" name="sender_id" value="<?php echo $request['sender_id']; ?>">
                                <button type="submit" class="btn btn-success" name="accept_request">Accept</button>
                                <button type="submit" class="btn btn-danger" name="reject_request">Reject</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Bootstrap JS and other scripts -->
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <!-- Your other scripts here -->
</body>

</html>