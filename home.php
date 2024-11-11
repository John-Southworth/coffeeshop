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

    <p>Here, you can access the menu, login, or register</p>
    <nav>
            <ul>
                <li>
                    <?php include('login.php');?>
                </li>

                <li>
                    <?php include('register.php');?>
                </li>
            </ul>
        </nav>
    

    <form action="logout.php" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>
</body>
</html>
