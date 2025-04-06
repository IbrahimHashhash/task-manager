<?php
include "header.php";
require_once "db.php.inc.php";
$error = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $username);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && $password == $user['password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: navigation.php");
        } else {
            $error = "Invalid username or password."; 
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage(); 
    }
}
?>

    <form action="index.php" method="post">
        <fieldset>
        <legend>Login</legend>
        <label for="username">Username:</label><br>
        <input type="text" name="username" required><br><br>
        
        <label for="password">Password:</label><br>
        <input type="password" name="password" required><br><br>
        
        <input type="submit" value="Login">
        <?php if (!empty($error)): ?>
        <p class='error'><?php echo $error; ?></p>
        <?php endif; ?>
        </fieldset>
    </form>
<?php include "footer.html";
?>