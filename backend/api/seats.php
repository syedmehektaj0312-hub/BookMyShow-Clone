<?php

require_once "../config/database.php";

header("Content-Type: application/json");

$show_id = filter_var(
    $_GET["show_id"] ?? null,
    FILTER_VALIDATE_INT
);

if (!$show_id) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Valid show ID required"
    ]);
    exit;
}

try {

    $stmt = $conn->prepare("
        SELECT
            shows.id,
            shows.show_date,
            shows.show_time,
            shows.price,
            movies.title,
            theatres.name AS theatre_name
        FROM shows
        JOIN movies ON shows.movie_id = movies.id
        JOIN theatres ON shows.theatre_id = theatres.id
        WHERE shows.id = ?
    ");

    $stmt->execute([$show_id]);

    $show = $stmt->fetch();

    if (!$show) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Show not found"
        ]);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT seat_label
        FROM booking_seats
        WHERE show_id = ?
    ");

    $stmt->execute([$show_id]);

    $bookedSeats = $stmt->fetchAll(
        PDO::FETCH_COLUMN
    );

    echo json_encode([
        "success" => true,
        "show" => $show,
        "booked_seats" => $bookedSeats
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to fetch seats"
    ]);

}

?>