<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('config.php');
require_once('functions.php');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 5;

$menuItems = getMenuItems($page, $itemsPerPage);
$totalItems = getTotalMenuItems();
$totalPages = ceil($totalItems / $itemsPerPage);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['menu_item_id'])) {
    $userId = $_SESSION['user_id'];
    $menuItemId = $_POST['menu_item_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1; //Sets default, and takes in quantity.
    addItemToOrder($userId, $menuItemId, $quantity);
    header('Location: home.php');
    exit();
} elseif (isset($_POST['change_availability'])) {
    $id = $_POST['id'];
    $availability = $_POST['availability'];
    changeItemAvailability($id, $availability);
    header('Location: ' . $_SERVER['PHP_SELF'] . '?page=' . $page);
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
    <title>Menu - Coffeeshop System</title>
</head>
<body>
    <h2>Menu</h2>
    <?php if (empty($menuItems)): ?>
        <p>No menu items available.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($menuItems as $item): ?>
                <li>
                    <?php echo htmlspecialchars($item['name']); ?> - $<?php echo number_format($item['price'], 2); ?> : <?php echo $item['availability'] ? 'Available' : 'Unavailable'; ?>
                    <?php if ($_SESSION['role'] == 'Customer' && $item['availability']): ?>
                        <form method="post" action="">
                            <input type="hidden" name="menu_item_id" value="<?php echo $item['id']; ?>">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity" value="1" min="1" required>
                            <input type="submit" value="Add to Order">
                        </form>
                    <?php endif; ?>
                    <?php if ($_SESSION['role'] == 'Manager' || $_SESSION['role'] == 'Barista'): ?>
                        <form method="post" action="">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <select name="availability">
                                <option value="1" <?php echo $item['availability'] ? 'selected' : ''; ?>>Available</option>
                                <option value="0" <?php echo !$item['availability'] ? 'selected' : ''; ?>>Unavailable</option>
                            </select>
                            <input type="submit" name="change_availability" value="Change Availability">
                        </form>
                    <?php endif; ?>
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
</body>
</html>