<?php
$bloodtype = $_GET['bloodtype'] ?? '';
$userLat = $_GET['lat'] ?? '';
$userLng = $_GET['lng'] ?? '';

if (!$bloodtype || !$userLat || !$userLng) {
    echo json_encode([]);
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=blooddonationmanagementsystem", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Radius in kilometers (e.g., 25km)
    $radius = 25;

    // Haversine formula to find nearby donors
    $query = "
    SELECT id, name, bloodtype, contact, email, lastdonationdate, latitude, longitude,
    (6371 * acos(
        cos(radians(:userLat)) * cos(radians(latitude)) *
        cos(radians(longitude) - radians(:userLng)) +
        sin(radians(:userLat)) * sin(radians(latitude))
    )) AS distance
    FROM user
    WHERE bloodtype = :bloodtype AND latitude IS NOT NULL AND longitude IS NOT NULL
    HAVING distance < :radius
    ORDER BY distance ASC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':userLat' => $userLat,
        ':userLng' => $userLng,
        ':bloodtype' => $bloodtype,
        ':radius' => $radius
    ]);

    $donors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($donors);
} catch (PDOException $e) {
    echo json_encode([]);
}
