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


// Display the main page content    
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .center {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            /* background-color: red; */
        }

        /* Customize button size */
        .btn-create-thread {
            padding: 10px 20px;
            font-size: 8px;
        }

        .container {
            margin-bottom: 20px;
        }

        .card-body-scrollable {
            height: 300px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">ConnectX</a>
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
                <form class="d-flex" method="get" action="search.php">
                    <input class="form-control me-2" type="search" placeholder="Search ConnectX" aria-label="Search" name="keyword">
                    <button class="btn btn-outline-success" type="submit" data-bs-toggle="modal" data-bs-target="#messageDetailsModal">Search</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mt-3">
                <a class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createThreadModal" data-user-id="<?php echo $user_id ?>">Create a New Thread</a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row mb-4">
            <!-- Friends Thread Container -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Friends</div>
                    <div class="card-body card-body-scrollable">
                        <?php include 'friends_messages.php' ?>
                    </div>
                </div>
            </div>
            <!-- Neighbors Thread Container -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Neighbors</div>
                    <div class="card-body card-body-scrollable">
                        <?php include 'neighbors_messages.php' ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row row-spacing">
            <!-- Block Messages Thread Container -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Block Messages</div>
                    <div class="card-body card-body-scrollable">
                        <?php include 'block_messages.php' ?>
                    </div>
                </div>
            </div>
            <!-- Hood Messages Thread Container -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Hood Messages</div>
                    <div class="card-body card-body-scrollable">
                        <?php include 'hood_messages.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script>
        // Check if URL parameter 'success' exists
        const urlParams = new URLSearchParams(window.location.search);
        const successMessage = urlParams.get('success');

        // Display success message in a popup if it exists
        if (successMessage) {
            alert(successMessage);
            // Remove the 'success' parameter from the URL
            const url = new URL(window.location.href);
            url.searchParams.delete('success');

            // Revert the URL back to its original state without the 'success' parameter
            window.history.replaceState({}, document.title, url);
        }
    </script>
    <?php include_once 'threadModal.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

</body>

</html>