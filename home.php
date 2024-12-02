<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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

    <p>Here, you can access the menu!</p>
    
    <?php include('menu.php'); ?>

    <?php if ($_SESSION['role'] == 'Manager'): ?>
        <form action="edit_menu.php" method="get">
            <input type="submit" value="Edit Menu">
        </form>
    <?php endif; ?>

    <?php if ($_SESSION['role'] == 'Customer'): ?>
        <form action="view_personal_order.php" method="get">
            <input type="submit" value="View My Orders">
        </form>
    <?php endif; ?>

    <?php if ($_SESSION['role'] == 'Manager' || $_SESSION['role'] == 'Barista'): ?>
        <form action="view_all_orders.php" method="get">
            <input type="submit" value="View All Orders">
        </form>
    <?php endif; ?>

    <form action="logout.php" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>
</body>
</html>
