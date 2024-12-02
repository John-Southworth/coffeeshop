<?php
session_start();
require_once('config.php');
require_once('functions.php');

if (isset($_SESSION['username'])) {
    logoutUser();
}

header('Location: login.php');
exit();
?>