<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('config.php');
require_once('functions.php');

// Check if the user is a customer
if ($_SESSION['role'] != 'Customer') {
    header('Location: home.php');
    exit();
}

// Fetch personal orders
$userId = $_SESSION['user_id'];
$orders = getPersonalOrders($userId);

// Handle remove item action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_item'])) {
    $orderItemId = $_POST['order_item_id'];
    removeItemFromOrder($orderItemId);
    header('Location: view_personal_order.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="style.css">
    <title>View My Orders - Coffeeshop System</title>
</head>
<body>
    <h2>My Orders</h2>

    <?php if (empty($orders)): ?>
        <p>No orders available.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($orders as $order): ?>
                <li>
                    Order ID: <?php echo $order['id']; ?> - Status: <?php echo $order['status']; ?> - Total: $<?php echo number_format($order['total'], 2); ?>
                    <ul>
                        <?php $orderItems = getOrderItems($order['id']); ?>
                        <?php foreach ($orderItems as $item): ?>
                            <li>
                                <?php echo htmlspecialchars($item['name']); ?> - Quantity: <?php echo $item['quantity']; ?> - $<?php echo number_format($item['price'], 2); ?>
                                <form method="post" action="">
                                    <input type="hidden" name="order_item_id" value="<?php echo $item['id']; ?>">
                                    <input type="submit" name="remove_item" value="Remove">
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <div class="checkout">
        <h1>Ready To Checkout</h1>
        <div id="orderItem">
            <?php include 'view_personal_order.php'; ?>
        </div>

        <p class="total">Total: <?php echo AddToOrder(); ?></p>

        <!-- Checkout Button -->
        <button id="checkoutButton">Checkout</button>
    </div>

    <script>
        document.getElementById('checkoutButton').addEventListener('click', function () {
            window.location.href = 'checkout.html';
        });

    <form method="post" action="home.html">
        <input type="submit" value="Back To Homepage">
    </form>

    <form method="post" action="home.php">
        <input type="submit" value="Back to Home">
    </form>
</body>
</html>
