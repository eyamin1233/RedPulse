<?php
if (isset($_POST['bloodBankID'])) {
    $bankId = $_POST['bloodBankID'];
    $bankName = $_POST['bloodBandkName'];
    $bankLocation = $_POST['bloodBankLocation'];

    try {
        $conn = new PDO('mysql:host=localhost:3306;dbname=blooddonationmanagementsystem;', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $updateBank = "UPDATE bloodbank SET Name = :bloodBandkName, location = :bloodBankLocation WHERE id = :bloodBankID";
        $stmt = $conn->prepare($updateBank);
        $stmt->bindParam(':bloodBandkName', $bankName);
        $stmt->bindParam(':bloodBankLocation', $bankLocation);
        $stmt->bindParam(':bloodBankID', $bankId);
        $stmt->execute();

        echo "<script>alert('Blood Bank updated successfully!'); location.assign('dashboard.php');</script>";
    } catch (PDOException $ex) {
        echo "Error: " . $ex->getMessage();
    }
}
?>
