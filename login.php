<?php 
session_start();
require_once('config.php');
require_once('functions.php');

$login_error = '';
if (isset($_SESSION['username'])) {
    header('Location: ./home.php');
    exit;
}

if (isset($_POST['submit']) && isset($_POST['username']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    // Call the loginConf function
    $loginData = loginConf($_POST['username'], $_POST['password']);
    
    // Check if loginData is empty
    if (empty($loginData)) {
        $login_error = 'Invalid username or password';
    } else {
        // User Data Acquisition
        $userData = $loginData[0];
        
        // Check login status
        if ($userData["login_status"] == 1) {
            $login_error = 'User is already logged in';
        } else {
            // Log in the user
            loginUser($userData['username']);
            $_SESSION['username'] = $userData['username'];
            header('Location: home.php');
            exit;
        }
    }
} else {
    $login_error = '';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login to the Coffeeshop System</h2>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <input type="submit" name="submit" value="Login">
    </form>
    <form action="register.php" method="post">
        <input type="submit" value="Register">
    </form>
    <?php if (!empty($login_error)) echo "<p style='color:red;'>$login_error</p>"; ?>
</body>
</html>
