<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (
        isset($_POST['name'], $_POST['email'], $_POST['contact'], $_POST['password'], $_POST['bloodtype'], $_POST['lastdonationdate'], $_POST['address']) &&
        !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['contact']) &&
        !empty($_POST['password']) && !empty($_POST['bloodtype']) && !empty($_POST['lastdonationdate']) && !empty($_POST['address']) 
    ) {
        try {
            $con = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Hash the password securely
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $query = "INSERT INTO user (name, email, contact, password, bloodtype, lastdonationdate, location, profile_picture) 
                      VALUES (:name, :email, :contact, :password, :bloodtype, :lastdonationdate, :location, :profile_picture)";

            $stmt = $con->prepare($query);
            $result = $stmt->execute([
                ':name' => $_POST['name'],
                ':email' => $_POST['email'],
                ':contact' => $_POST['contact'],
                ':password' => $hashedPassword,
                ':bloodtype' => $_POST['bloodtype'],
                ':lastdonationdate' => $_POST['lastdonationdate'],
                ':location' => $_POST['address'],
                ':profile_picture' => 'photos/images.png' // default image
            ]);

            if ($result) {
                $_SESSION['donor_message'] = " Registration successful! Redirecting to Sign In page...";
                $_SESSION['donor_message_type'] = "success";
                // JS redirect after delay
                $_SESSION['redirect_script'] = "<script>setTimeout(function(){ window.location.href = 'signin.php'; }, 3000);</script>";
            } else {
                $_SESSION['donor_message'] = " Insert failed. Please try again.";
                $_SESSION['donor_message_type'] = "error";
            }

        } 
        catch (PDOException $e) {
    $_SESSION['donor_message'] = "PDO Error: " . $e->getMessage();
    $_SESSION['donor_message_type'] = "error";
    header("Location: register.php");
    exit;
}

        catch (Exception $e) {
            $_SESSION['donor_message'] = " General Error: " . $e->getMessage();
            $_SESSION['donor_message_type'] = "error";
        } finally {
            $con = null;
            header("Location: register.php");
            exit();
        }

    } else {
        $_SESSION['donor_message'] = " All fields are required.";
        $_SESSION['donor_message_type'] = "error";
        header("Location: register.php");
        exit();
    }

} else {
    $_SESSION['donor_message'] = " Invalid request method.";
    $_SESSION['donor_message_type'] = "error";
    header("Location: register.php");
    exit();
}
?>
