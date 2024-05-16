<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>signup screen</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <h1>Sign Up</h1>
            <form method="POST" class="form">
                <input type="text" name="First_Name" placeholder="John" required>
                <input type="text" name="Last_Name" placeholder="Doe" required>
                <input type="text" name="Username" placeholder="Username" required>
                <input type="password" name="Password" placeholder="Password" required>
                <input type="password" name="Repassword" placeholder="RetypePassword" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="Address_Line_1" placeholder="Apt, House Num" required>
                <input type="text" name="Address_Line_2" placeholder="Street Name" required>
                <input type="text" name="Zipcode" placeholder="Zipcode" required>
                <input type="text" name="City" placeholder="New York City" required>
                <input type="text" name="State" placeholder="NY" required>
                <input type="text" name="Country" placeholder="US" required>
                <input type="text" name="Block" placeholder="Block Id" required>
                <input type="text" name="Dependents" placeholder="Dependents Description" required>
                <input type="text" name="Photo" placeholder="Upload Photo" required>
                <input type="text" name="Latitude" placeholder="Latitude" required>
                <input type="text" name="Longitude" placeholder="Longitude" required>
                <button class="btn btn-lg btn-primary btn-block" type="submit" name="regis-signup">Signup</button>
            </form>
            <?php
            include 'db_connection.php';

            //session start
            session_start();

            $valid = "";
            $pas = "";
            $conn = "";
            $a = "";
            $err = "";
            $stmt = "";
            $mail = "";
            $ins1 = "";
            $register = "";
            $insuc = "";
            $uid = "";

            if (isset($_POST['regis-signup'])) {
                $fname = $_POST['First_Name'];
                $lname = $_POST['Last_Name'];
                $uname = $_POST['Username'];
                $pass = $_POST['Password'];
                $repass = $_POST['Repassword'];
                $email = $_POST['email'];
                $addr1 = $_POST['Address_Line_1'];
                $addr2 = $_POST['Address_Line_2'];
                $zcode = $_POST['Zipcode'];
                $city = $_POST['City'];
                $state = $_POST['State'];
                $country = $_POST['Country'];
                $block = $_POST['Block'];
                $dependents = $_POST['Dependents'];
                $photo = $_POST['Photo'];
                $lat = $_POST['Latitude'];
                $long = $_POST['Longitude'];

                // Check if passwords match
                if ($pass != $repass) {
                    echo "Passwords do not match.";
                    exit(); // Stop further execution
                }

                // Check if username already exists
                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE user_name = ?");
                $checkStmt->execute([$uname]);
                $count = $checkStmt->fetchColumn();


                if ($count > 0) {
                    echo "Username already exists. Please choose a different username.";
                } else {
                    // Insert user if username is unique
                    $stmt = $pdo->prepare("INSERT INTO Users (fname, lname, user_name, address_line1, address_line2, city, state, zipcode, country, email, block_id, dependents_desc, photo_uri, latitude, longitude, password, last_access) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())");
                    $stmt->execute([$fname, $lname, $uname, $addr1, $addr2, $city, $state, $zcode, $country, $email, $block, $dependents, $photo, $lat, $long, $pass]);

                    // Registration successful, set session variables
                    $_SESSION['user_id'] = $pdo->lastInsertId();
                    $_SESSION['username'] = $uname;

                    // Insert into access relation and record sign-in time
                    $insertStmt = $pdo->prepare("INSERT INTO access (user_id, user_name, last_signin) VALUES (?, ?, NOW())");
                    $insertStmt->execute([$_SESSION['user_id'], $uname]);

                    // Redirect to main page
                    header("Location: mainpage.php");
                    exit(); // Stop script execution after redirection
                }
            }
            ?>

</body>

</html>