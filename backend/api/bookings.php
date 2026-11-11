<?php

require_once "../config/database.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Only POST method allowed"
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON"
    ]);
    exit;
}

$show_id = filter_var(
    $data["show_id"] ?? null,
    FILTER_VALIDATE_INT
);

$name = trim($data["customer_name"] ?? "");
$email = trim($data["email"] ?? "");
$seats = $data["seats"] ?? [];

if (
    !$show_id ||
    $name === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    !is_array($seats) ||
    count($seats) < 1 ||
    count($seats) > 10
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Please provide valid booking details"
    ]);
    exit;
}

$seats = array_values(array_unique($seats));

foreach ($seats as $seat) {
    if (!is_string($seat) ||
        !preg_match('/^[A-D][1-8]$/', $seat)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid seat label"
        ]);
        exit;
    }
}

try {

    $conn->beginTransaction();

    $stmt = $conn->prepare(
        "SELECT id, price FROM shows WHERE id = ? FOR UPDATE"
    );

    $stmt->execute([$show_id]);
    $show = $stmt->fetch();

    if (!$show) {
        $conn->rollBack();
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Show not found"
        ]);
        exit;
    }

    $check = $conn->prepare(
        "SELECT id FROM booking_seats
         WHERE show_id = ? AND seat_label = ?"
    );

    foreach ($seats as $seat) {
        $check->execute([$show_id, $seat]);

        if ($check->fetch()) {
            $conn->rollBack();
            http_response_code(409);
            echo json_encode([
                "success" => false,
                "message" => "Seat $seat is already booked"
            ]);
            exit;
        }
    }
$ticket_total = count($seats) * (float)$show["price"];

$fee = 30.00;

$gst = round($fee * 0.18, 2);

$total = round($ticket_total + $fee + $gst, 2);

    $booking = $conn->prepare(
        "INSERT INTO bookings
        (show_id, customer_name, email,
         total_amount, booking_status)
        VALUES (?, ?, ?, ?, 'CONFIRMED')"
    );

    $booking->execute([
        $show_id,
        $name,
        $email,
        $total
    ]);

    $booking_id = $conn->lastInsertId();

    $seatInsert = $conn->prepare(
        "INSERT INTO booking_seats
        (booking_id, show_id, seat_label)
        VALUES (?, ?, ?)"
    );

    foreach ($seats as $seat) {
        $seatInsert->execute([
            $booking_id,
            $show_id,
            $seat
        ]);
    }

    $conn->commit();

   echo json_encode([
    "success" => true,
    "message" => "Booking confirmed successfully",
    "booking_id" => $booking_id,
    "seats" => $seats,
    "ticket_amount" => $ticket_total,
    "convenience_fee" => $fee,
    "gst" => $gst,
    "total_amount" => $total
]);

} catch (PDOException $e) {

    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Booking failed"
    ]);

}

?>