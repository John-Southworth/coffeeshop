<?php 
// Form Database Connection
$conn = new mysqli("localhost", "user1", "password1", "coffeeshop");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully!";
}
?>
