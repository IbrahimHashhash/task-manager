<?php
include "header.php";
?>

<?php
if (isset($_GET['error'])) {
    echo "<p class='error'>".$_GET['error']."</p>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $_SESSION['firstName'] = $_POST['firstName'];
        $_SESSION['lastName'] = $_POST['lastName'];
        $_SESSION['flat'] = $_POST['flat'];
        $_SESSION['street'] = $_POST['street'];
        $_SESSION['city'] = $_POST['city'];
        $_SESSION['country'] = $_POST['country'];
        $_SESSION['dob'] = $_POST['dob'];
        $_SESSION['idNumber'] = $_POST['idNumber'];
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['telephone'] = $_POST['telephone'];
        $_SESSION['role'] = $_POST['role'];
        $_SESSION['qualification'] = $_POST['qualification'];
        $_SESSION['skills'] = $_POST['skills'];
}
?>

<form action="confirmation.php" method="post">
    <p class="title"> Sign up</p>
    <fieldset>
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" pattern="[a-zA-Z0-9]{6,13}" title="6-13 alphanumeric characters" required><br><br>

    <label for="password">Password:</label><br>
    <input type="password"  name="password" pattern="(?=.*\d)(?=.*[a-zA-Z]).{8,12}" title="8-12 characters, must include letters and numbers" required><br><br>

    <label for="password_confirmation">Password Confirmation:</label><br>
    <input type="password" name="password_confirmation" pattern="(?=.*\d)(?=.*[a-zA-Z]).{8,12}" title="Must match the entered password" required><br><br>

    <input type="submit" value="Proceed">
    </fieldset>
</form>

<?php include "footer.html";?>
