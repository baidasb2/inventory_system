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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $additional_stock = $_POST['additional_stock'];

    // Validate input
    if (!empty($product_id) && !empty($additional_stock) && is_numeric($additional_stock)) {
        // Begin a transaction to ensure both operations succeed
        $conn->begin_transaction();
        try {
            // Update stock in the Products table
            $sql_update_stock = "UPDATE Products SET stock = stock + ? WHERE product_id = ?";
            $stmt = $conn->prepare($sql_update_stock);
            $stmt->bind_param("ii", $additional_stock, $product_id);
            if (!$stmt->execute()) {
                throw new Exception("Error updating stock: " . $conn->error);
            }
            $stmt->close();

            // Insert the stock change into StockChanges table
            $quantity_changed = $additional_stock; // Positive value for addition
            $stock_change = $additional_stock; // Positive value for addition
            $sql_insert_change = "INSERT INTO StockChanges (product_id, quantity_changed, stock_change, change_date) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql_insert_change);
            $stmt->bind_param("iii", $product_id, $quantity_changed, $stock_change);
            if (!$stmt->execute()) {
                throw new Exception("Error logging stock change: " . $conn->error);
            }
            $stmt->close();

            // Commit the transaction
            $conn->commit();
            $_SESSION['message'] = "Stock added and logged successfully!";
        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            $_SESSION['message'] = $e->getMessage();
        }
        // Redirect to the same page to display the message
        header("Location: add_stock.php");
        exit();
    } else {
        $_SESSION['message'] = "Invalid input. Please try again.";
        header("Location: add_stock.php");
        exit();
    }
}

// Fetch existing products for the dropdown
$sql = "SELECT product_id, product_name FROM Products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Stock - Inventory Management</title>
    <link rel="stylesheet" href="./css/add_stock.css">
</head>
<body>
    <div class="container">
        <h1>Add Stock to Existing Product</h1>
        
        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='message'>{$_SESSION['message']}</div>";
            // Clear the message after displaying
            unset($_SESSION['message']);
        }
        ?>
        
        <form action="add_stock.php" method="POST">
            <label for="product_id">Select Product:</label>
            <select id="product_id" name="product_id" required>
                <option value="">Select a product</option>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['product_id']}'>{$row['product_name']}</option>";
                    }
                } else {
                    echo "<option value=''>No products available</option>";
                }
                ?>
            </select>
            <label for="additional_stock">Add Stock:</label>
            <input type="number" id="additional_stock" name="additional_stock" min="1" required>
            <input type="submit" value="Add Stock">
        </form> <br> <br>
        <a href="home.php" class="back-btn">Back to Home</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
