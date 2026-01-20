<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
    $role = $_POST['role'] ?? '';

    $_SESSION['email_error'] = "";
    $_SESSION['password_error'] = "";

    if (!empty($email) && !empty($password) && !empty($role)) {
        try {
            $con = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Determine table based on role
            if ($role === 'user') {
                $query = "SELECT * FROM user WHERE email = :email";
            } elseif ($role === 'bloodbank') {
                $query = "SELECT * FROM bloodbank WHERE email = :email";
            } else {
                $_SESSION['email_error'] = "Invalid role selected.";
                header("Location: signin.php");
                exit();
            }

            $stmt = $con->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (password_verify($password, $user['password'])) {
                    session_regenerate_id(true);

                    // ✅ Store role
                    $_SESSION['role'] = $role;

                    if ($role === 'user') {
                        // ✅ Set session variables for user
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_name'] = $user['name'];
                        $_SESSION['contact'] = $user['contact'];
                        $_SESSION['bloodtype'] = $user['bloodtype'];
                        $_SESSION['lastdonationdate'] = $user['lastdonationdate'];
                        $_SESSION['location'] = $user['location'];
                        $_SESSION['profile_picture'] = $user['profile_picture'] ?? 'photos/default-placeholder.png';

                        unset($_SESSION['email_error'], $_SESSION['password_error'], $_SESSION['old_email']);
                        echo "<script>location.assign('profile.php');</script>";
                    } elseif ($role === 'bloodbank') {
                        // ✅ Set session variables for blood bank
                        $_SESSION['bloodbank_id'] = $user['BloodBankID'];
                        $_SESSION['bloodbank_email'] = $user['email']; // make sure email column exists in `bloodbank` table
                        $_SESSION['bloodbank_name'] = $user['Name'];
                        $_SESSION['center_name'] = $user['Name'];
                        $_SESSION['center_location'] = $user['Location'];
                        $_SESSION['contact'] = $user['ContactNumber'];
                        $_SESSION['pic1'] = $user['pic1'] ?? 'photos/default-placeholder.png';
                        $_SESSION['pic2'] = $user['pic2'] ?? 'photos/default-placeholder.png';

                        unset($_SESSION['email_error'], $_SESSION['password_error'], $_SESSION['old_email']);
                        echo "<script>location.assign('bb_profile.php');</script>";
                    }

                } else {
                    $_SESSION['password_error'] = "Incorrect password.";
                    header("Location: signin.php");
                    exit();
                }
            } else {
                $_SESSION['email_error'] = "No account found with this email.";
                header("Location: signin.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['email_error'] = "Server error. Please try again.";
            header("Location: signin.php");
            exit();
        }
    } else {
        if (empty($email)) $_SESSION['email_error'] = "Email is required.";
        if (empty($password)) $_SESSION['password_error'] = "Password is required.";
        if (empty($role)) $_SESSION['email_error'] = "Please select your role.";
        header("Location: signin.php");
        exit();
    }
}
?>
