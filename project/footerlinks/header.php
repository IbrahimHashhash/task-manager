<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Profile</title>
    <link rel="stylesheet" href="../styling/header.css"> 
    <link rel="stylesheet" href="footerlinkstyle.css"> 

    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Rationale&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
<div class="header">
    <div class="left-section">  
    <h1 id="logotxt">TAP</h1>
    </div>
    <div class="middle-section">
    </div>
    <div class="right-section">
                    <?php if (isset($_SESSION['username'])): ?>
                        <a href="../logout.php">Logout</a>
                        <a href="../navigation.php">Dashboard</a>
                        <a href="../profile.php?username=<?php echo $_SESSION['username'];?>" >
                            <div class="userContainer">
                             <img src="../images/image.png" alt="User Profile" id="userPhoto"> 
                             <p class="user_info" ><?php echo $_SESSION['username']; ?></p> 
                            </div>
                        </a>
                        <?php else: ?>
                       <a href="../" >Login</a>
                       <a href="../information.php" >Sign-Up</a>
                <?php endif; ?>
    </div>
</div>
<main>