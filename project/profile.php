<?php
include "header.php";
require_once 'db.php.inc.php'; 
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; 
} else {
    echo "You must be logged in to view this page.";
    exit;
}
try {
    $stmt_user = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt_user->execute([$username]);
    $user = $stmt_user->fetch();

    if ($user) {
        $sql_address = "SELECT * FROM addresses WHERE address_id = :address_id";
        $stmt_address = $pdo->prepare($sql_address);
        $stmt_address->bindParam(':address_id', $user['address_id'], PDO::PARAM_INT);
        $stmt_address->execute();
        $address = $stmt_address->fetch();
    } else {
        echo "User not found!";
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>
<div class="p-container">
<div id="profile">
    <div>
    <h2>Personal Info</h2>
    <p><strong>Full Name:</strong> <?php echo $user['full_name']; ?></p>
    <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
    <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
    <p><strong>Phone:</strong> <?php echo $user['phone']; ?></p>
    <p><strong>Role:</strong> <?php echo $user['role']; ?></p>
    <p><strong>Qualification:</strong> <?php echo $user['qualification']; ?></p>
    <p><strong>Skills:</strong> <?php echo $user['skills']; ?></p>
    <p><strong>Date of Birth:</strong> <?php echo $user['dob']; ?></p>
    </div>

    <div>
    <h2>Address</h2>
    <p><strong>Flat No:</strong> <?php echo $address['flat_no']; ?></p>
    <p><strong>Street:</strong> <?php echo $address['street']; ?></p>
    <p><strong>City:</strong> <?php echo $address['city']; ?></p>
    <p><strong>Country:</strong> <?php echo $address['country']; ?></p>

    </div>
</div>
</div>
<?php include "footer.html"; ?>
