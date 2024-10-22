<?php 
session_start();
require_once('config.php');
require_once('functions.php');

//Variable Initialization
$registerError = '';
$registerSuccess = '';

if (isset($_POST['submit']) && isset($_POST['username']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $sql = "SELECT * FROM users WHERE username='" . $_POST['username'] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $registerError = 'Username already exists';
    } else {
        $role = 'user'; // Default role
        if (!empty($_POST['role_password']) && $_POST['role_password'] === 'Apricot') {
            $role = 'Manager';
        }
        else if (!empty($_POST['role_password']) && $_POST['role_password'] === 'Coffee') {
            $role = 'Barista';
        }
        registerUser($_POST['username'], $_POST['password'], $role);
        $registerSuccess = 'User registered successfully';
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Register to the Coffeeshop System</h2>
    <form action="register.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <label for="role password">Role Password (Staff Only):</label>
        <input type="password" name="role_password"><br><br>

        <input type="submit" name="submit" value="Register">
    </form>
    <p><?php echo $registerError; ?></p>
    <p><?php echo $registerSuccess; ?></p>
</html>