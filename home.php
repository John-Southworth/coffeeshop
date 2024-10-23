<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="style.css">
    <title>Home - Coffeeshop System</title>
</head>
<body>
    <h2>Welcome to the Coffeeshop System, <?php echo $_SESSION['username']; ?>!</h2>

    <p>This is the Home Page</p>
    <nav>
            <ul>
                <li><a href="#Login">Login</a></li>  <!--connect the php link-->
                <li><a href="#About">About</a></li>
                <li><a href="#Menu">Menu</a></li>
            </ul>
        </nav>
    

    <form action="logout.php" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>
</body>
</html>
