<?php
session_start();
require_once "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT * FROM admins WHERE email = ?"
    );
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin["password"])) {
        session_regenerate_id(true);

        $_SESSION["admin_id"] = $admin["id"];
        $_SESSION["admin_name"] = $admin["name"];

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login | BookMyShow</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #171b2c;
    display: grid;
    place-items: center;
    min-height: 100vh;
}
.login {
    background: white;
    padding: 35px;
    width: 320px;
    border-radius: 12px;
}
h2 { text-align: center; }
input {
    width: 100%;
    padding: 12px;
    margin: 12px 0;
    box-sizing: border-box;
}
button {
    width: 100%;
    padding: 13px;
    background: #f84464;
    color: white;
    border: 0;
    border-radius: 6px;
    cursor: pointer;
}
.error { color: red; }
</style>
</head>
<body>
<div class="login">
    <h2>BOOK<span style="color:#f84464">MY</span>SHOW</h2>
    <h3>Admin Login</h3>

    <p class="error">
        <?= htmlspecialchars($error) ?>
    </p>

    <form method="POST">
        <input type="email" name="email"
               placeholder="Admin Email" required>

        <input type="password" name="password"
               placeholder="Password" required>

        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>