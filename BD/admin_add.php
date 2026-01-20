<?php
$username = 'Eyamin Hossain';
$password = 'Mywebsite@123';

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $con = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem;', 'root', '');
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if admin already exists
    $check = $con->query("SELECT COUNT(*) FROM admin");
    $exists = $check->fetchColumn();

    if ($exists > 0) {
        echo "Admin already exists. No new account created.";
        exit();
    }

    // Insert admin only once
    $stmt = $con->prepare("INSERT INTO admin (UserName, Password) VALUES (:username, :password)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->execute();

    echo "Admin account created successfully.";
} 
catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
