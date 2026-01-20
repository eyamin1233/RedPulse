<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: profile.php');
    exit;
}

try {
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get POST data
        $user_id = $_SESSION['id']; // ID of the logged-in user
        $request_id = $_POST['request_id']; // Blood request ID (if needed)
        $name = $_POST['donor_name'];
        $email = $_POST['donor_email'];
        $contact_number = $_POST['contact_number'];
        $address = $_POST['address'];
        $blood_type = $_POST['blood_type'];
        $last_donation_date = date('Y-m-d'); // Assuming the donation is made today
        $admin_id = 1; // Set AdminID (change as required)

        // Insert into the donor table
        $stmt = $conn->prepare("
            INSERT INTO donor (Name, BloodType, LastDonationDate, ContactNumber, Email, Address, AdminID)
            VALUES (:name, :blood_type, :last_donation_date, :contact_number, :email, :address, :admin_id)
        ");
        $stmt->execute([
            ':name' => $name,
            ':blood_type' => $blood_type,
            ':last_donation_date' => $last_donation_date,
            ':contact_number' => $contact_number,
            ':email' => $email,
            ':address' => $address,
            ':admin_id' => $admin_id
        ]);

        // Set a session variable to disable the "Donate" button
        $_SESSION['donation_submitted'] = true;

        // Redirect back to posts page
        $_SESSION['success'] = 'Donation submitted successfully!';
        header('Location: posts.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
    header('Location: posts.php');
    exit;
}
?>
