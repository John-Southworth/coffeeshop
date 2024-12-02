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
    $conn = getConnection();
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
    $conn = getConnection(); // Use the connection from getConnection()
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, login_status) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("sss", $username, $hashedPassword, $role);
    return $stmt->execute();
}

// Logout User
function logoutUser() {
    session_start();
    $username = $_SESSION['username'];
    $sql = "UPDATE users SET login_status=0 WHERE username='$username'";
    execQuery($sql);
    session_unset();
    session_destroy();
}

// Fetch Menu Items
function getMenuItems($page, $itemsPerPage) {
    $conn = getConnection();
    $offset = ($page - 1) * $itemsPerPage;
    $sql = "SELECT * FROM menu_items LIMIT $offset, $itemsPerPage";
    $result = $conn->query($sql);
    $menuItems = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $menuItems[] = $row;
        }
    }
    return $menuItems;
}

// Get Total Menu Items
function getTotalMenuItems() {
    $conn = getConnection();
    $sql = "SELECT COUNT(*) as total FROM menu_items";
    $result = $conn->query($sql);
    $total = 0;
    if ($result->num_rows > 0) {
        $total = $result->fetch_assoc()['total'];
    }
    return $total;
}

// Add Menu Item
function addMenuItem($name, $price) {
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO menu_items (name, price) VALUES (?, ?)");
    $stmt->bind_param("sd", $name, $price);
    return $stmt->execute();
}

// Remove Menu Item
function removeMenuItem($id) {
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Edit Menu Item
function editMenuItem($id, $name, $price) {
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE menu_items SET name = ?, price = ? WHERE id = ?");
    $stmt->bind_param("sdi", $name, $price, $id);
    return $stmt->execute();
}

// Change Item Availability
function changeItemAvailability($id, $availability) {
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE menu_items SET availability = ? WHERE id = ?");
    $stmt->bind_param("ii", $availability, $id);
    return $stmt->execute();
}

// Add Item to Order
function addItemToOrder($userId, $menuItemId, $quantity) {
    $conn = getConnection();

    // Check if there is an existing pending order for the user
    $stmt = $conn->prepare("SELECT id FROM orders WHERE user_id = ? AND status = 'Pending'");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Use the existing pending order
        $order = $result->fetch_assoc();
        $orderId = $order['id'];
    } else {
        // Create a new pending order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, status, total) VALUES (?, 'Pending', 0)");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $orderId = $stmt->insert_id;
    }

    // Check if the item already exists in the order
    $stmt = $conn->prepare("SELECT id, quantity FROM order_items WHERE order_id = ? AND menu_item_id = ?");
    $stmt->bind_param("ii", $orderId, $menuItemId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Update the quantity of the existing item
        $orderItem = $result->fetch_assoc();
        $newQuantity = $orderItem['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE order_items SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $newQuantity, $orderItem['id']);
        $stmt->execute();
    } else {
        // Add the item to the order_items table
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $orderId, $menuItemId, $quantity);
        $stmt->execute();
    }

    // Update the total in the orders table
    $stmt = $conn->prepare("UPDATE orders SET total = total + (SELECT price FROM menu_items WHERE id = ?) * ? WHERE id = ?");
    $stmt->bind_param("iii", $menuItemId, $quantity, $orderId);
    $stmt->execute();
}

// Remove Item from Order
function removeItemFromOrder($orderItemId) {
    $conn = getConnection();

    // Get the order ID and quantity of the item to be removed
    $stmt = $conn->prepare("SELECT order_id, menu_item_id, quantity FROM order_items WHERE id = ?");
    $stmt->bind_param("i", $orderItemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $orderItem = $result->fetch_assoc();

    // Remove the item from the order_items table
    $stmt = $conn->prepare("DELETE FROM order_items WHERE id = ?");
    $stmt->bind_param("i", $orderItemId);
    $stmt->execute();

    // Update the total in the orders table
    $stmt = $conn->prepare("UPDATE orders SET total = total - (SELECT price FROM menu_items WHERE id = ?) * ? WHERE id = ?");
    $stmt->bind_param("iii", $orderItem['menu_item_id'], $orderItem['quantity'], $orderItem['order_id']);
    $stmt->execute();
}

// Fetch Personal Orders
function getPersonalOrders($userId) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    return $orders;
}

// Fetch Order Items
function getOrderItems($orderId) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT oi.id, oi.quantity, mi.name, mi.price FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE oi.order_id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $orderItems = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orderItems[] = $row;
        }
    }
    return $orderItems;
}

// Fetch All Orders with Pagination
function getAllOrders($page, $itemsPerPage) {
    $conn = getConnection();
    $offset = ($page - 1) * $itemsPerPage;
    $sql = "SELECT * FROM orders LIMIT $offset, $itemsPerPage";
    $result = $conn->query($sql);
    $orders = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    return $orders;
}

// Get Total Orders Count
function getTotalOrders() {
    $conn = getConnection();
    $sql = "SELECT COUNT(*) as count FROM orders";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }
}

// Update Order Status
function updateOrderStatus($orderId, $status) {
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $orderId);
    return $stmt->execute();
}

?>