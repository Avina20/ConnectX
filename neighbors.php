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
$stmt = $pdo->prepare("SELECT CONCAT(u.fname, ' ', u.lname) AS neighbor_name, 
                        u.user_name AS neighbor_username, n.neighbor_desc
                        FROM neighbor n
                        INNER JOIN users u ON n.neighbor_user_id = u.user_id
                        WHERE n.user_id = ?");
$stmt->execute([$user_id]);
$neighbors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display the main page content
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="mainpage.php">ConnectX </a>
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
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="container">
        <h4 class="py-3">Neighbors</h4>
        <div class="row">
            <?php foreach ($neighbors as $neighbor) : ?>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $neighbor['neighbor_username']; ?></h5>
                            <p class="card-text"><?php echo $neighbor['neighbor_desc']; ?></p>
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