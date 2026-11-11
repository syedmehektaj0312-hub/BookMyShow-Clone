<?php
session_start();
require_once "../config/database.php";

// Protect dashboard
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

// Fetch dashboard statistics
$totalMovies = $conn->query(
    "SELECT COUNT(*) FROM movies"
)->fetchColumn();

$totalTheatres = $conn->query(
    "SELECT COUNT(*) FROM theatres"
)->fetchColumn();

$totalShows = $conn->query(
    "SELECT COUNT(*) FROM shows"
)->fetchColumn();

$totalBookings = $conn->query(
    "SELECT COUNT(*) FROM bookings"
)->fetchColumn();

// Fetch recent bookings
$recentBookings = $conn->query(
    "SELECT id, customer_name, email,
            total_amount, booking_status, created_at
     FROM bookings
     ORDER BY id DESC
     LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | BookMyShow</title>

<style>
* { box-sizing: border-box; }

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f5f9;
    color: #222;
}

header {
    background: #171b2c;
    color: white;
    padding: 20px 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 24px;
    font-weight: bold;
}

.logo span { color: #f84464; }

header a {
    color: white;
    text-decoration: none;
    background: #f84464;
    padding: 10px 18px;
    border-radius: 6px;
}

.container {
    max-width: 1200px;
    margin: 35px auto;
    padding: 20px;
}

.welcome {
    margin-bottom: 30px;
}

.stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.stat {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 3px 12px #0001;
}

.stat h3 {
    color: #777;
    font-size: 15px;
}

.stat p {
    font-size: 32px;
    font-weight: bold;
    margin: 10px 0;
}

.section {
    background: white;
    padding: 25px;
    margin-top: 30px;
    border-radius: 12px;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 650px;
}

th, td {
    padding: 15px;
    border-bottom: 1px solid #eee;
    text-align: left;
}

th { background: #f7f7f7; }

.status {
    color: green;
    font-weight: bold;
}

@media(max-width: 800px) {
    .stats { grid-template-columns: repeat(2, 1fr); }
}

@media(max-width: 450px) {
    .stats { grid-template-columns: 1fr; }
}
</style>
</head>

<body>

<header>
    <div class="logo">
        BOOK<span>MY</span>SHOW ADMIN
    </div>

    <a href="logout.php">Logout</a>
</header>

<div class="container">

    <div class="welcome">
        <h1>Welcome, <?= htmlspecialchars($_SESSION["admin_name"]) ?>!</h1>
        <p>Manage your BookMyShow platform.</p>
    </div>

    <div class="stats">

        <div class="stat">
            <h3>Total Movies</h3>
            <p><?= $totalMovies ?></p>
        </div>

        <div class="stat">
            <h3>Total Theatres</h3>
            <p><?= $totalTheatres ?></p>
        </div>

        <div class="stat">
            <h3>Total Shows</h3>
            <p><?= $totalShows ?></p>
        </div>

        <div class="stat">
            <h3>Total Bookings</h3>
            <p><?= $totalBookings ?></p>
        </div>

    </div>

    <div class="section">
        <h2>Recent Bookings</h2>

        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($recentBookings as $booking): ?>
                <tr>
                    <td>#<?= (int)$booking["id"] ?></td>
                    <td><?= htmlspecialchars($booking["customer_name"]) ?></td>
                    <td><?= htmlspecialchars($booking["email"]) ?></td>
                    <td>₹<?= number_format((float)$booking["total_amount"], 2) ?></td>
                    <td class="status">
                        <?= htmlspecialchars($booking["booking_status"]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($recentBookings)): ?>
                <tr>
                    <td colspan="5">No bookings yet.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>