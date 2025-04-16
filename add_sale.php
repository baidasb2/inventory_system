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

// Handle form submission to record a sale
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Validate input
    if (!empty($product_id) && !empty($quantity) && is_numeric($product_id) && is_numeric($quantity)) {
        // Begin a transaction
        $conn->begin_transaction();
        try {
            // Insert into stocktransactions
            $sql_insert = "INSERT INTO stocktransactions (product_id, quantity) VALUES (?, ?)";
            $stmt = $conn->prepare($sql_insert);
            $stmt->bind_param("ii", $product_id, $quantity);
            if (!$stmt->execute()) {
                throw new Exception("Error recording sale: " . $conn->error);
            }
            $stmt->close();

            // Update the stock in the Products table
            $sql_update = "UPDATE Products SET stock = stock - ? WHERE product_id = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("ii", $quantity, $product_id);
            if (!$stmt->execute()) {
                throw new Exception("Error updating stock: " . $conn->error);
            }
            $stmt->close();

            // Commit the transaction
            $conn->commit();
            $_SESSION['message'] = "Sale recorded successfully!";
        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            $_SESSION['message'] = $e->getMessage();
        }

        // Redirect to the same page to display the message
        header("Location: add_sale.php");
        exit();
    } else {
        $_SESSION['message'] = "Invalid input. Please check your data.";
        header("Location: add_sale.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sale - Inventory Management</title>
   <link rel="stylesheet" href="./css/add_sale.css">
</head>
<body>
    <div class="form-container">
        <h1>Add Sale</h1>
        <?php
        if (isset($_SESSION['message'])) {
            $msg_class = strpos($_SESSION['message'], 'Error') === false ? 'success' : 'error';
            echo "<div class='message $msg_class'>{$_SESSION['message']}</div>";
            // Clear the message after displaying
            unset($_SESSION['message']);
        }
        ?>
        <form method="POST" action="add_sale.php">
            <div class="form-group">
                <label for="product_id">Product ID</label>
                <input type="number" name="product_id" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" required>
            </div>
            <button type="submit" class="btn">Record Sale</button>
        </form> <br> <br>
        <a href="home.php" class="back-btn">Back to Home</a>
    </div>
</body>
</html>
