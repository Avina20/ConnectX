<?php
error_reporting(E_ALL); // Enable error reporting
session_start();
include 'db_connection.php';


if (isset($_POST['Submit'])) {
    $username = $_POST['UserName'];
    $password = $_POST['password'];

    // Query the database for the entered username and password
    $stmt = $pdo->prepare("SELECT user_id, password FROM users WHERE user_name = ? AND password = ?");
    $stmt->execute([$username, $password]);

    // Check if the query was executed successfully
    if ($stmt->rowCount() > 0) { // Check if any rows were returned
        $user = $stmt->fetch();

        // Check if the user already exists in the access table
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM access WHERE user_id = ?");
        $checkStmt->execute([$user['user_id']]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            // If the user exists in the access table, update the last_signin timestamp
            $updateStmt = $pdo->prepare("UPDATE access SET last_signin = NOW() WHERE user_id = ?");
            $updateStmt->execute([$user['user_id']]);
        } else {
            // If the user does not exist in the access table, insert a new record
            $insertStmt = $pdo->prepare("INSERT INTO access (user_id, user_name, last_signin) VALUES (?, ?, NOW())");
            $insertStmt->execute([$user['user_id'], $username]);
        }

        // Start a session and store user ID
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $username;
        // Redirect to main page
        header("Location: mainpage.php");
        exit(); // Stop script execution after redirection
    } else {
        echo "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <!-- <link rel="stylesheet" href="css/style.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .center-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;

        }

        .login-form {
            width: 300px;
        }
    </style>

</head>

<body>
    <div class="center-container">
        <div class="container login-form">
            <h4 class="py-3">Login</h4>
            <form method="POST">
                <div class="mb-3">
                    <label for="UserName" class="form-label">User Name</label>
                    <input type="text" class="form-control" name="UserName" aria-describedby="emailHelp">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password">
                </div>
                <button type="submit" class="btn btn-primary" name="Submit">Submit</button>
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>

</html>