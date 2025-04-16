<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Initialize message variable
$message = '';

// Handle form submission to add a product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Validate input
    if (!empty($product_name) && !empty($price) && !empty($stock) && is_numeric($price) && is_numeric($stock)) {
        $sql = "INSERT INTO Products (product_name, price, stock) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $product_name, $price, $stock);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Product added successfully!";
        } else {
            $_SESSION['message'] = "Error: " . $conn->error;
        }
        $stmt->close();

        // Redirect to the same page to display the message
        header("Location: add_product.php");
        exit();
    } else {
        $_SESSION['message'] = "Invalid input. Please check your data.";
        header("Location: add_product.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Inventory Management</title>
   <link rel="stylesheet" href="./css/add_product.css">
</head>
<body>
    <div class="form-container">
        <h1>Add Product</h1>
        <?php
        if (isset($_SESSION['message'])) {
            $msg_class = strpos($_SESSION['message'], 'Error') === false ? 'success' : 'error';
            echo "<div class='message $msg_class'>{$_SESSION['message']}</div>";
            // Clear the message after displaying
            unset($_SESSION['message']);
        }
        ?>
        <form method="POST" action="add_product.php">
            <div class="form-group">
                <label for="product_name">Product Name</label>
                <input type="text" name="product_name" required>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" required>
            </div>
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" name="stock" required>
            </div>
            <button type="submit" class="btn">Add Product</button>
        </form> <br> <br>
        <a href="home.php" class="back-btn">Back to Home</a>
    </div>
</body>
</html>
