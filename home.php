<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Inventory Management</title>
    <link rel="stylesheet" href="./css/home.css">
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="home.php">Home</a>
        <a href="add_product.php">Add Product</a>
        <a href="add_sale.php">Add Sale</a>
        <a href="add_stock.php">Add stock</a>
        <a href="remove_stock.php">Remove stock</a>
        <a href="fetch_summary.php">Fetch Summary</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Content -->
    <div class="container">
        <h1 style="background-color: #007bff; color: white;">Welcome to Inventory Management</h1> <br>

        <!-- Cards Displaying Products -->
        <div class="card-container">
            <?php
            $sql = "SELECT * FROM Products";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <div class='card'>
                        <h2>{$row['product_name']}</h2>
                        <p>Price: {$row['price']} per piece</p>
                        <p>Stock: {$row['stock']} in stock</p>
                        <a href='fetch_summary.php' class='btn'>View Summary Stats</a>
                    </div>";
                }

            } else {
                echo "<div class='no-products'>No products in store, You can add one from above menu.</div>";
            }
            ?>
        </div>
    </div>
</body>

</html>