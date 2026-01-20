<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=blooddonationmanagementsystem", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $updates = [
        ['BloodBankID' => 1, 'email' => 'quantumlab@gmail.com', 'password' => 'Quantum123$'],
        ['BloodBankID' => 2, 'email' => 'bbbank@gmail.com', 'password' => 'Bbbank123%'],
        ['BloodBankID' => 3, 'email' => 'rcbloodc@gmail.com', 'password' => 'Rcbank234%'],
        ['BloodBankID' => 4, 'email' => 'sbbdcentre@gmail.com', 'password' => 'Sbbank123^'],
        ['BloodBankID' => 5, 'email' => 'flbbank@gmail.com', 'password' => 'Flbbank234%'],
    ];

    foreach ($updates as $bank) {
        $hashedPassword = password_hash($bank['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE bloodbank SET email = :email, password = :password WHERE BloodBankID = :id");
        $stmt->execute([
            ':email' => $bank['email'],
            ':password' => $hashedPassword,
            ':id' => $bank['BloodBankID'],
        ]);
    }

    echo "Blood bank credentials updated successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
