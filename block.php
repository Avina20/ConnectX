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

// Retrieve user's block
$stmt = $pdo->prepare("SELECT block_id FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_block_id = $user['block_id'];

// Retrieve list of blocks
$stmt = $pdo->prepare("SELECT * FROM block WHERE block_id != ?");
$stmt->execute([$user_block_id]);
$blocks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for requesting membership
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['selectedBlock'])) {
        $selectedBlockId = $_POST['selectedBlock'];

		// Insert membership application
		$stmt = $pdo->prepare("INSERT INTO membership_application (applicant_id, block_id, applicant_ts, application_status) VALUES (?, ?, NOW(), FALSE)");
		$stmt->execute([$user_id, $selectedBlockId]);
		// Redirect or show a success message
        
    }
}

// Retrieve block membership requests for the user's block
$stmt = $pdo->prepare("SELECT ma.application_id, CONCAT(u.fname, ' ', u.lname) AS applicant_username, ma.applicant_ts 
                        FROM membership_application ma 
                        INNER JOIN users u ON ma.applicant_id = u.user_id 
                        WHERE ma.block_id = ? AND ma.application_status = FALSE");
$stmt->execute([$user_block_id]);
$block_membership_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve_membership'])) {
    $application_id = $_POST['application_id'];

    // Check if the user has already approved this request
    $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM membership_approval WHERE application_id = ? AND approver_user_id = ?");
    $stmt->execute([$application_id, $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] == 0) {
        // Insert approval into membership_approval table
        $stmt = $pdo->prepare("INSERT INTO membership_approval (application_id, block_id, approver_user_id, approval_ts) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$application_id, $user_block_id, $user_id]);

        // Check if enough members have approved the request
        $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM membership_approval WHERE application_id = ?");
        $stmt->execute([$application_id]);
        $approval_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
		
        if ($approval_count >= 3) { // Change to >= 3 for 3 or all members if less than 3
            // Update membership_application table to mark the request as approved
            $stmt = $pdo->prepare("UPDATE membership_application SET application_status = TRUE WHERE application_id = ?");
            $stmt->execute([$application_id]);
			
			//Update block_id
			$stmt = $pdo->prepare("UPDATE users AS u SET block_id = ma.block_id	
			FROM membership_application AS ma WHERE ma.application_id = ?");
			$stmt->execute([$application_id]);
        }

        // Redirect to refresh the page or show a success message
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        // User has already approved this request
        // Redirect or show an error message
    }
}

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
        #blocksList {
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

    <div class="container">
        <div class="row justify-content-center py-4">
            <div class="col-md-6">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="input-group">
                        <select class="form-select" name="selectedBlock" id="blocksList"> <!-- Added id attribute -->
                            <option value="" selected disabled>Select a block</option>
                            <?php foreach ($blocks as $block) : ?>
                                <option value="<?php echo $block['block_id']; ?>"><?php echo $block['block_desc']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-outline-secondary">Request Membership</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Section to display block membership requests -->
    <!-- Section to display block membership requests -->
	<div class="container">
		<h4 class="py-3">Membership Requests in Your Block</h4>
		<div class="row">
			<?php foreach ($block_membership_requests as $request) : ?>
				<div class="col-md-6">
					<div class="card mb-3">
						<div class="card-body">
							<h5 class="card-title"><?php echo $request['applicant_username']; ?></h5>
							<p class="card-text">Applied on: <?php echo $request['applicant_ts']; ?></p>
							<?php
							// Check if the current user has already approved or rejected this request
							$stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM membership_approval WHERE application_id = ? AND approver_user_id = ?");
							$stmt->execute([$request['application_id'], $user_id]);
							$result = $stmt->fetch(PDO::FETCH_ASSOC);

							if ($result['count'] == 0) {
								// User hasn't approved/rejected this request yet
								echo '<form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="POST">';
								echo '<input type="hidden" name="application_id" value="' . $request['application_id'] . '">';
								echo '<button type="submit" name="approve_membership" class="btn btn-success">Approve</button>';
								echo '</form>';
							} else {
								// User has already approved/rejected this request
								echo '<p class="text-muted">You have already responded to this request.</p>';
							}
							?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>


    <!-- Bootstrap JS and other scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <!-- Your other scripts here -->
</body>

</html>
