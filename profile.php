<?php

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit(); // Stop script execution after redirection
}

// Include the database connection file
include 'db_connection.php';

// Retrieve the user ID from the query parameter
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Retrieve user information from the database based on the user ID
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $fname = $user['fname'];
    $lname = $user['lname'];
    $email = $user['email'];
    $uname = $user['user_name'];
    $addr1 = $user['address_line1'];
    $addr2 = $user['address_line2'];
    $city = $user['city'];
    $state = $user['state'];
    $zcode = $user['zipcode'];
    $country = $user['country'];
    $block = $user['block_id'];
    $dependents_desc = $user['dependents_desc'];

    $address = $addr1 . ', ' . $addr2 . ', ' . $city . ', ' . $state . ' ' . $zcode . ', ' . $country;
} else {
    // Redirect back to main page if user ID is not provided
    header("Location: mainpage.php");
    exit(); // Stop script execution after redirection
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
                        <a class="nav-link active" aria-current="page" href="mainpage.php<?php

                                                                                            ?>">Home</a>
                    </li>
                </ul>
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>
    <section style="background-color: #eee;">
        <div class="container py-5">

            <div class="row">
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp" alt="avatar" class="rounded-circle img-fluid" style="width: 150px;">
                            <h5 class="my-3"><?php echo $fname . " " . $lname; ?></h5>

                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Full Name</p>
                                </div>
                                <div class="col-sm-9 d-flex align-items-center">
                                    <p class="text-muted mb-0"><?php echo $fname . " " . $lname; ?></p>
                                    <button class="btn btn-link" type="button" name="edit_names" data-bs-toggle="modal" data-bs-target="#editNameModal"><i class=" bi bi-pen-fill"></i></i></button>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Email</p>
                                </div>
                                <div class="col-sm-9 d-flex align-items-center">
                                    <p class="text-muted mb-0"><?php echo $email; ?></p>
                                    <button class="btn btn-link" type="button" name="edit_email" data-bs-toggle="modal" data-bs-target="#editEmailModal"><i class="bi bi-pen-fill"></i></i></button>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Username</p>
                                </div>
                                <div class="col-sm-9 d-flex align-items-center">
                                    <p class="text-muted mb-0"><?php echo $uname; ?></p>
                                    <button class="btn btn-link" type="button" name="edit_uname" data-bs-toggle="modal" data-bs-target="#editUsernameModal"><i class="bi bi-pen-fill"></i></i></button>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Address</p>
                                </div>
                                <div class="col-sm-9 d-flex align-items-center">
                                    <p class="text-muted mb-0"><?php echo $address; ?></p>
                                    <button class="btn btn-link" type="button" name="edit_address" data-bs-toggle="modal" data-bs-target="#editAddressModal"><i class="bi bi-pen-fill"></i></i></button>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Block</p>
                                </div>
                                <div class="col-sm-9 d-flex align-items-center">
                                    <p class="text-muted mb-0"><?php echo $block; ?></p>
                                    <button class="btn btn-link" type="button" name="edit_block" data-bs-toggle="modal" data-bs-target="#editBlockModal"><i class="bi bi-pen-fill"></i></i></button>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Dependents Description</p>
                                </div>
                                <div class="col-sm-9 d-flex align-items-center">
                                    <p class="text-muted mb-0"><?php echo $dependents_desc; ?></p>
                                    <button class="btn btn-link" type="button" name="edit_description" data-bs-toggle="modal" data-bs-target="#editDependentsDescModal"><i class="bi bi-pen-fill"></i></i></button>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Modal for editing full name -->
        <div class="modal fade" id="editNameModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form fields for editing user full name -->
                        <form action="editName.php" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                            <div class="mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $fname; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $lname; ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Editing Email Modal -->
        <div class="modal fade" id="editEmailModal" tabindex="-1" aria-labelledby="editEmailModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEmailModalLabel">Edit Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editEmailForm" action="editEmail.php" method="POST">
                            <div class="mb-3">
                                <label for="newEmail" class="form-label">New Email</label>
                                <input type="email" class="form-control" id="newEmail" name="newEmail" required value="<?php echo $email ?>">
                            </div>
                            <input type="hidden" name="userId" value="<?php echo $user_id; ?>">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Username Modal -->
        <div class="modal fade" id="editUsernameModal" tabindex="-1" aria-labelledby="editUsernameModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUsernameModalLabel">Edit Username</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editUsernameForm" action="editUsername.php" method="POST">
                            <div class="mb-3">
                                <label for="newUsername" class="form-label">New Username</label>
                                <input type="text" class="form-control" id="newUsername" name="newUsername" value="<?php echo $uname; ?>">
                            </div>
                            <input type="hidden" name="userId" value="<?php echo $user_id; ?>">
                            <?php if (isset($new_username)) { ?>
                                <input type="hidden" name="newUsername" value="<?php echo $new_username; ?>">
                            <?php } ?>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Address Modal -->
        <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAddressModalLabel">Edit Address</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editAddressForm" action="editAddress.php" method="POST">
                            <div class="mb-3">
                                <label for="addressLine1" class="form-label">Address Line 1</label>
                                <input type="text" class="form-control" id="addressLine1" name="addressLine1" value="<?php echo $addr1; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="addressLine2" class="form-label">Address Line 2</label>
                                <input type="text" class="form-control" id="addressLine2" name="addressLine2" value="<?php echo $addr2; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" value="<?php echo $city; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state" value="<?php echo $state; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="zipcode" class="form-label">Zipcode</label>
                                <input type="text" class="form-control" id="zipcode" name="zipcode" value="<?php echo $zcode; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" name="country" value="<?php echo $country; ?>" required>
                            </div>
                            <input type="hidden" name="userId" value="<?php echo $user_id; ?>">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Block Modal -->
        <div class="modal fade" id="editBlockModal" tabindex="-1" aria-labelledby="editBlockModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBlockModalLabel">Edit Block</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editBlockForm" action="editBlock.php" method="POST">
                            <div class="mb-3">
                                <label for="block" class="form-label">Block</label>
                                <input type="text" class="form-control" id="block" name="block" value="<?php echo $block; ?>" required>
                            </div>
                            <input type="hidden" name="userId" value="<?php echo $user_id; ?>">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Dependents Description Modal -->
        <div class="modal fade" id="editDependentsDescModal" tabindex="-1" aria-labelledby="editDependentsDescModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDependentsDescModalLabel">Edit Dependents Description</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editDependentsDescForm" action="editDependentsDesc.php" method="POST">
                            <div class="mb-3">
                                <label for="dependentsDesc" class="form-label">Dependents Description</label>
                                <textarea class="form-control" id="dependentsDesc" name="dependentsDesc" rows="4"><?php echo $dependents_desc; ?></textarea>
                            </div>
                            <input type="hidden" name="userId" value="<?php echo $user_id; ?>">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>



    </section>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>


</body>

</html>