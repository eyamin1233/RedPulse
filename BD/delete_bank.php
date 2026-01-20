<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_bloodbanks.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['BloodBankID'])) {
    try {
        $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $bloodBankId = $_POST['BloodBankID'];
        // Then delete the blood bank
        $stmt2 = $conn->prepare("DELETE FROM bloodbank WHERE BloodBankID = ?");
        $stmt2->execute([$bloodBankId]);

        // Redirect back to the admin page with a success message
        header("Location: admin_bloodbanks.php?deleted=1");
        exit;

    } catch (PDOException $e) {
        // Redirect back with an error flag (optional)
        header("Location: admin_bloodbanks.php?error=1");
        exit;
    }
} else {
    // Redirect back if accessed improperly
    header("Location: admin_bloodbanks.php");
    exit;
}
?>
