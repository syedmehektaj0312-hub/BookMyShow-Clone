<?php

require_once "../config/database.php";

header("Content-Type: application/json");

try {

    $sql = "SELECT * FROM theatres ORDER BY id ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $theatres = $stmt->fetchAll();

    echo json_encode([
        "success" => true,
        "theatres" => $theatres
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to fetch theatres"
    ]);

}

?>