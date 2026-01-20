<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        try {
            $con = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem;', 'root', '');
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Fetch admin by username
            $query = "SELECT * FROM admin WHERE UserName = :username";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $adminData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($adminData && password_verify($password, $adminData['Password'])) {
                // Password matches
                $_SESSION['admin_id'] = $adminData['AdminID'];
                $_SESSION['username'] = $adminData['UserName'];
                header("Location: admin_profile.php");
                exit;
            } else {
                echo "<script>alert('Invalid Username or Password!'); location.assign('admin.php');</script>";
                exit;
            }
        } catch (PDOException $e) {
            echo "<script>alert('Database Error: {$e->getMessage()}'); location.assign('admin.php');</script>";
            exit;
        }
    } else {
        echo "<script>alert('Please fill in all fields!'); location.assign('admin.php');</script>";
        exit;
    }
} else {
    header("Location: signin.php");
    exit;
}
?>
