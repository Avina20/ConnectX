<?php

// Include db_connection.php to establish a database connection
require_once('db_connection.php');


// Retrieve user information from session variables
$user_id = $_SESSION['user_id'];

try {
    // Prepare a SQL query to select users who are not friends of the currently logged-in user along with the logged-in user itself
    $sql = "SELECT u.user_id, u.fname, u.lname
            FROM Users u
            WHERE u.user_id != :currentUserId
            AND u.user_id NOT IN (
                SELECT f.friend_one_id
                FROM Friend f
                WHERE f.friend_two_id = :currentUserId
                UNION
                SELECT f.friend_two_id
                FROM Friend f
                WHERE f.friend_one_id = :currentUserId
            )";

    // Prepare the statement
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':currentUserId', $user_id, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch all rows including the currently logged-in user
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        echo '<option value="' . $user['user_id'] . '">' . $user['fname'] . ' ' . $user['lname'] . '</option>';
    }
} catch (PDOException $e) {
    // Handle errors
    echo "Error: " . $e->getMessage();
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

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

</body>

</html>