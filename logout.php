<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    // session_start();

    // // Unset all of the session variables
    // $_SESSION = array();

    // // Destroy the session
    // session_destroy();

    // // Redirect to the login page after logout
    // header("Location: home.php");
    // exit();
    include 'db_connection.php';
    session_start();

    if (isset($_SESSION['user_id'])) {
        // Update the last sign-out timestamp for the user
        $user_id = $_SESSION['user_id'];
        $user_name = $_SESSION['username'];

        // Prepare the SQL statement to update the last sign-out timestamp
        $updateStmt = $pdo->prepare("UPDATE access SET last_signout = NOW() WHERE user_id = ?");
        $updateStmt->execute([$user_id]);

        // Unset all of the session variables
        $_SESSION = array();

        // Destroy the session
        session_destroy();

        // Redirect to the login page after logout
        header("Location: home.php");
        exit();
    } else {
        // If the user is not logged in, simply redirect to the home page
        header("Location: home.php");
        exit();
    }
    ?>


</body>

</html>