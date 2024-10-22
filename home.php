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
<head>
    <title>Home - Coffeeshop System</title>
</head>
<body>
    <h2>Welcome to the Coffeeshop System, <?php echo $_SESSION['username']; ?>!</h2>

    <p>This is the Home Page</p>

    <form action="logout.php" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>
</body>
</html>
