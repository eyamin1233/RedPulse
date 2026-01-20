<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['bankname'], $_POST['location'], $_POST['contact'], $_POST['email'], $_POST['password'], $_POST['confirmPassword']) &&
        !empty($_POST['bankname']) && !empty($_POST['location']) && !empty($_POST['contact']) &&
        !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['confirmPassword']) 
    ) {
        $name = trim($_POST['bankname']);
        $location = trim($_POST['location']);
        $contact = trim($_POST['contact']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];

        if ($password !== $confirmPassword) {
            $_SESSION['bloodbank_message'] = "Passwords do not match.";
            $_SESSION['bloodbank_message_type'] = "error";
            $_SESSION['active_form'] = "bloodbank";
            header("Location: register.php");
            exit();
        }

        try {
            $pdo = new PDO("mysql:host=localhost;dbname=blooddonationmanagementsystem", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("SELECT * FROM bloodbank WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['bloodbank_message'] = "This email is already registered.";
                $_SESSION['bloodbank_message_type'] = "error";
                $_SESSION['active_form'] = "bloodbank";
                header("Location: register.php");
                exit();
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO bloodbank (Name, Location, ContactNumber, email, password)
                      VALUES (:name, :location, :contact, :email, :password)";
            $stmt = $pdo->prepare($query);

            $result = $stmt->execute([
                ':name' => $name,
                ':location' => $location,
                ':contact' => $contact,
                ':email' => $email,
                ':password' => $hashedPassword,
            ]);

            if ($result) {
                $_SESSION['bloodbank_message'] = "Blood Bank registered successfully! Redirecting to sign in page.";
                $_SESSION['bloodbank_message_type'] = "success";
                $_SESSION['redirect_script'] = "<script>setTimeout(function(){ window.location.href = 'signin.php'; }, 3000);</script>";
            } else {
                $_SESSION['bloodbank_message'] = "Registration failed. Please try again.";
                $_SESSION['bloodbank_message_type'] = "error";
            }

        } catch (PDOException $e) {
            $_SESSION['bloodbank_message'] = "Database Error: " . $e->getMessage();
            $_SESSION['bloodbank_message_type'] = "error";
        } catch (Exception $e) {
            $_SESSION['bloodbank_message'] = "General Error: " . $e->getMessage();
            $_SESSION['bloodbank_message_type'] = "error";
        } finally {
            $pdo = null;
            $_SESSION['active_form'] = "bloodbank";
            header("Location: register.php");
            exit();
        }

    } 
} else {
    $_SESSION['bloodbank_message'] = "Invalid request method.";
    $_SESSION['bloodbank_message_type'] = "error";
    $_SESSION['active_form'] = "bloodbank";
    header("Location: register.php");
    exit();
}
?>
