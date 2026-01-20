<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: profile.php');
    exit;
}

$user_id = $_SESSION['id'];

try {
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bloodtype = $_POST['bloodtype'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $location = $_POST['location'] ?? '';
    $request_id = $_POST['request_id'] ?? '';

    if (empty($bloodtype) || empty($contact) || empty($location) || empty($request_id)) {
        $_SESSION['error'] = 'All fields are required!';
        header('Location: posts.php');
        exit;
    }

    try {
        // Insert data into the recipient table
        $stmt = $conn->prepare("
            INSERT INTO recipient (user_id, bloodtype, contact, location, request_id, received_date)
            VALUES (:user_id, :bloodtype, :contact, :location, :request_id, NOW())
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':bloodtype' => $bloodtype,
            ':contact' => $contact,
            ':location' => $location,
            ':request_id' => $request_id
        ]);

        // Update the request's received status
        $updateStmt = $conn->prepare("
            UPDATE bloodrequest SET received = 1 WHERE id = :request_id
        ");
        $updateStmt->execute([':request_id' => $request_id]);

        $_SESSION['success'] = 'Recipient record added and request marked as received!';
        header('Location: posts.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error saving recipient record: ' . $e->getMessage();
        header('Location: posts.php');
        exit;
    }
}
?>
