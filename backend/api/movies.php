<?php

require_once "../config/database.php";

header("Content-Type: application/json");

try {

    $sql = "SELECT * FROM movies ORDER BY id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $movies = $stmt->fetchAll();

    echo json_encode([
        "success" => true,
        "movies" => $movies
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to fetch movies"
    ]);

}

?>