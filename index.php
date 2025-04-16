<?php
session_start();
include 'config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate user credentials (for simplicity, we'll use plain text, but in real applications, you should use hashed passwords)
    $sql = "SELECT * FROM Users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Successful login, store user info in session
        $_SESSION['username'] = $username;
        header("Location: home.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory Management</title>
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="index.php">
            <div class="form-group">
                <label for="username">Username</label> <br>
                <input type="text" name="username" required>
            </div> 
            <div class="form-group">
                <label for="password">Password</label> <br>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login system</button>
        </form>
    </div>
</body>
</html>
