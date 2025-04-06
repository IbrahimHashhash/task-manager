<?php
include "header.php";
?>
<section class="container_confirm">
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];

    if ($password !== $password_confirmation) {
        header("Location: form_page.php?error=Passwords do not match");
        exit();
    }else {
        $_SESSION['password'] = $password;
        $_SESSION['username'] = $username;

        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Source</th></tr>"; 
        foreach ($_SESSION as $key => $value) {
            echo "<tr>";
            echo "<td>" . $key . "</td>";
            echo "<td>" . $value . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        }
}
?>
       <form action="process.php" method="post" id="confirmation"> 
       <input type="submit" name="confirm" value="Confirm">
       </form>
</section>

<?php include "footer.html";?>
