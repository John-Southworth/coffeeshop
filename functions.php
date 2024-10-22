<?php 

// Database Connection
function getConnection() {
    $servername = "localhost";
    $username = "user1";
    $password = "password1";
    $dbname = "coffeeshop";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

//Ececute SQL Query
function execQuery($sql) {
    global $conn; // Ensure you access the global connection
    $result = $conn->query($sql);
    if (!$result) {
        die("Query failed: " . $conn->error); // Provide an error message if the query fails
    }
    return $result; // Return the result if the query was successful
}

//Login Confirmation
function loginConf($username, $password) {
    global $conn; // Ensure you access the global connection

    // Prepare the SQL statement to prevent SQL injection
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    
    // Bind the username parameter
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user is found
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();

        // Verify the password against the hashed password
        if (password_verify($password, $userData['password'])) {
            return [$userData]; // Return user data if the password is correct
        }
    }

    return []; // Return an empty array if no user found or password does not match
}

//Login User
function loginUser($username) {
    $sql = "Update users set login_status=1 where username='".$username."'";
    execQuery($sql);
}

//Register User
function registerUser($username, $password, $role) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password, role, login_status) VALUES ('$username', '$hashedPassword', '$role', 0)";
    execQuery($sql);
}