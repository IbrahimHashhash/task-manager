<?php
session_start();
require_once "db.php.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm'])) {
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];
    $name = $_SESSION['firstName'] . " " . $_SESSION['lastName'];
    $flat = $_SESSION['flat'];
    $street = $_SESSION['street'];
    $city = $_SESSION['city'];
    $country = $_SESSION['country'];
    $dob = $_SESSION['dob'];
    $idNumber = $_SESSION['idNumber'];
    $email = $_SESSION['email'];
    $telephone = $_SESSION['telephone'];
    $role = $_SESSION['role'];
    $qualification = $_SESSION['qualification'];
    $skills = $_SESSION['skills'];

    try {
        $sql_check = "SELECT COUNT(*) FROM users WHERE username = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->bindValue(1, $username);
        $stmt_check->execute();
        $user_exists = $stmt_check->fetchColumn();

        if ($user_exists > 0) {
            echo "<p class='error'>The username is already taken. Please choose a different username.<br></p>";
            echo '<a href="information.php">Go back to registration</a>';
            exit;
        }
    
        $sql_address = "INSERT INTO addresses (flat_no, street, city, country) VALUES (?, ?, ?, ?)";
        $stmt_address = $pdo->prepare($sql_address);
        $stmt_address->execute([$flat, $street, $city, $country]);
        $address_id = $pdo->lastInsertId();

        $sql_user = "INSERT INTO users (username, password, full_name, email, phone, address_id, dob, idNumber, role, qualification, skills)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([
            $username,
            $password, 
            $name,
            $email,
            $telephone,
            $address_id,
            $dob,
            $idNumber,
            $role,
            $qualification,
            $skills
        ]);
        session_unset(); session_destroy();
        echo "<!DOCTYPE html>";
        echo "<html lang='en'>";
        echo "<head>";
        echo "<title>Registration Success</title>";
        echo "<link rel='stylesheet' href='styling/style.css'>";
        echo "<link rel='stylesheet' href='styling/header.css'>";
        echo "<link rel='stylesheet' href='styling/form-style.css'>";
        echo "</head>";
        echo "<body>";
        echo "<section id='successful_container'>";
        echo "<h2>Registration successful!</h2>";
        echo "<p><a href='index.php'>Click here to login</a></p>";
        echo "</section>";
        echo "</body>";
        echo "</html>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
