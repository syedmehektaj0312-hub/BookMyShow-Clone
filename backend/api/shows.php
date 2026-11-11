<?php

require_once "../config/database.php";

header("Content-Type: application/json");

try {

    $movie_id = $_GET['movie_id'] ?? null;

    if (!$movie_id || !filter_var($movie_id, FILTER_VALIDATE_INT)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Valid movie_id is required"
        ]);
        exit;
    }

    $sql = "SELECT
                shows.id,
                movies.title,
                theatres.name AS theatre_name,
                theatres.address,
                shows.show_date,
                shows.show_time,
                shows.price
            FROM shows
            JOIN movies ON shows.movie_id = movies.id
            JOIN theatres ON shows.theatre_id = theatres.id
            WHERE shows.movie_id = ?
            ORDER BY shows.show_time";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$movie_id]);

    echo json_encode([
        "success" => true,
        "shows" => $stmt->fetchAll()
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to fetch shows"
    ]);

}

?>