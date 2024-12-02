<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('config.php');
require_once('functions.php');

// Check if the user is a Barista or Manager
if ($_SESSION['role'] != 'Manager' && $_SESSION['role'] != 'Barista') {
    header('Location: home.php');
    exit();
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 5;

$orders = getAllOrders($page, $itemsPerPage);
$totalOrders = getTotalOrders();
$totalPages = ceil($totalOrders / $itemsPerPage);

// Handle confirm and complete actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_order'])) {
        $orderId = $_POST['order_id'];
        updateOrderStatus($orderId, 'Confirmed');
    } elseif (isset($_POST['complete_order'])) {
        $orderId = $_POST['order_id'];
        updateOrderStatus($orderId, 'Completed');
    }
    header('Location: view_all_orders.php?page=' . $page);
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
    <title>View All Orders - Coffeeshop System</title>
</head>
<body>
    <h2>All Orders</h2>

    <?php if (empty($orders)): ?>
        <p>No orders available.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($orders as $order): ?>
                <li>
                    Order ID: <?php echo $order['id']; ?> - Status: <?php echo $order['status']; ?> - Total: $<?php echo number_format($order['total'], 2); ?>
                    <form method="post" action="">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <?php if ($order['status'] == 'Pending'): ?>
                            <input type="submit" name="confirm_order" value="Confirm Order">
                        <?php endif; ?>
                        <?php if ($order['status'] == 'Confirmed'): ?>
                            <input type="submit" name="complete_order" value="Complete Order">
                        <?php endif; ?>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
        <div>
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="home.php">
        <input type="submit" value="Back to Home">
    </form>
</body>
</html>