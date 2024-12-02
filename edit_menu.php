<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('config.php');
require_once('functions.php');

// Check if the user is a manager
if ($_SESSION['role'] != 'Manager') {
    header('Location: home.php');
    exit();
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 5;

$menuItems = getMenuItems($page, $itemsPerPage);
$totalItems = getTotalMenuItems();
$totalPages = ceil($totalItems / $itemsPerPage);

// Handle add, remove, and edit actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_item'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        addMenuItem($name, $price);
    } elseif (isset($_POST['remove_item'])) {
        $id = $_POST['id'];
        removeMenuItem($id);
    } elseif (isset($_POST['edit_item'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        editMenuItem($id, $name, $price);
    } elseif (isset($_POST['return_to_home'])) {
        header('Location: home.php');
        exit();
    }
    header('Location: edit_menu.php');
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
    <title>Edit Menu - Coffeeshop System</title>
</head>
<body>
    <h2>Edit Menu</h2>
    <form method="post" action="">
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        <label for="price">Price:</label>
        <input type="text" name="price" required>
        <input type="submit" name="add_item" value="Add Item">
    </form>

    <?php if (empty($menuItems)): ?>
        <p>No menu items available.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($menuItems as $item): ?>
                <li>
                    <form method="post" action="">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>
                        <input type="text" name="price" value="<?php echo number_format($item['price'], 2); ?>" required>
                        <input type="submit" name="edit_item" value="Edit">
                        <input type="submit" name="remove_item" value="Remove">
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
    <form method="post" action="">
        <input type="submit" name="return_to_home" value="Back to Home">
    </form>
</body>
</html>