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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            background-image: url('images/bg2.jpg');
            background-size: cover;
            /* Cover the entire page */
            background-repeat: no-repeat;
            /* Prevent the image from repeating */
        }

        .navbar {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 10px 10px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
            font-size: 18px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .container {
            margin: 20px auto;
            max-width: 1200px;
            text-align: center;
        }

        .card-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 30%;
            text-align: center;
        }

        .card img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .card h2 {
            margin-top: 10px;
            color: #333;
        }

        .card p {
            color: #777;
            margin-top: 5px;
        }

        .no-products {
            font-size: 20px;
            color: #555;
            padding: 20px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 50px auto;
            /* Center horizontally */
            max-width: 600px;
            /* Restrict width for better centering */

        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-top: 15px;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
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