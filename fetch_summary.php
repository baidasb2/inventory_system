<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// SQL query to fetch the current stock and total sold items
$currentInventorySql = "
SELECT 
    p.product_id,
    p.product_name,
    p.price,
    p.stock  AS current_stock
FROM 
    Products p
LEFT JOIN 
    StockTransactions st 
ON 
    p.product_id = st.product_id
GROUP BY 
    p.product_id, p.product_name, p.price, p.stock
";

$soldItemsSql = "
SELECT 
    st.transaction_id,
    p.product_name,
    st.quantity,
    st.transaction_date
FROM 
    StockTransactions st
JOIN 
    Products p 
ON 
    st.product_id = p.product_id
";

$remainingStockSql = "
SELECT 
    p.product_id, 
    p.product_name, 
    p.stock AS remaining_stock
FROM 
    Products p
LEFT JOIN 
    StockTransactions st 
ON 
    p.product_id = st.product_id
GROUP BY 
    p.product_id, p.product_name, p.stock
HAVING 
    remaining_stock > 0
";

// New query to get stock changes
$stockChangesSql = "
SELECT 
    sc.change_id,
    p.product_name,
    sc.quantity_changed,
    sc.stock_change,
    sc.change_date
FROM 
    StockChanges sc
JOIN 
    Products p 
ON 
    sc.product_id = p.product_id
ORDER BY 
    sc.change_date DESC
";

// Execute the queries
$currentInventoryResult = $conn->query($currentInventorySql);
$soldItemsResult = $conn->query($soldItemsSql);
$remainingStockResult = $conn->query($remainingStockSql);
$stockChangesResult = $conn->query($stockChangesSql);

// Check if the queries were successful
if ($currentInventoryResult === false || $soldItemsResult === false || $remainingStockResult === false || $stockChangesResult === false) {
    echo "Error: " . $conn->error;
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Summary - Inventory Management</title>
  <link rel="stylesheet" href="./css/summary.css">
</head>
<body>
    <div class="container">
        <h1>Inventory Summary</h1>
        
        <!-- Current Inventory Table -->
        <h2>Current Remaining Inventory</h2>
        <table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Current Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($currentInventoryResult->num_rows > 0) {
                    while ($row = $currentInventoryResult->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['product_id']}</td>
                                <td>{$row['product_name']}</td>
                                <td>{$row['price']}</td>
                                <td>{$row['current_stock']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No inventory data available.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Sold Items Table -->
        <h2>Sold Items</h2>
        <table>
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Transaction Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($soldItemsResult->num_rows > 0) {
                    while ($row = $soldItemsResult->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['transaction_id']}</td>
                                <td>{$row['product_name']}</td>
                                <td>{$row['quantity']}</td>
                                <td>{$row['transaction_date']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No sold items data available.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Remaining Stock Table
        <h2>Remaining Stock</h2>
        <table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Remaining Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // if ($remainingStockResult->num_rows > 0) {
                //     while ($row = $remainingStockResult->fetch_assoc()) {
                //         echo "<tr>
                //                 <td>{$row['product_id']}</td>
                //                 <td>{$row['product_name']}</td>
                //                 <td>{$row['remaining_stock']}</td>
                //               </tr>";
                //     }
                // } else {
                //     echo "<tr><td colspan='3'>No remaining stock data available.</td></tr>";
                // }
                ?>
            </tbody>
        </table> -->

        <!-- Stock Changes Table -->
        <h2>Stock Changes</h2>
        <table>
            <thead>
                <tr>
                    <th>Change ID</th>
                    <th>Product Name</th>
                    <th>Quantity Changed</th>
                    
                    <th>Change Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($stockChangesResult->num_rows > 0) {
                    while ($row = $stockChangesResult->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['change_id']}</td>
                                <td>{$row['product_name']}</td>
                                <td>{$row['quantity_changed']}</td>
                                
                                <td>{$row['change_date']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No stock changes data available.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <br> <br>
    <a href="home.php" class="back-btn">Back to Home</a>
    </div> 
</body>
</html>
