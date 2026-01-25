<?php 
session_start();

// Include PHPMailer manually
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// DB connection
$conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem','root','');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$msg = "";

if($_SERVER['REQUEST_METHOD'] === "POST") {

    $email = trim($_POST['email']);

    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM user WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$user){
        $msg = "Email not found!";
    } else {
        // Generate OTP
        $otp = random_int(100000, 999999);
        $otpHash = password_hash($otp, PASSWORD_DEFAULT);
        $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        // Delete previous OTPs
        $conn->prepare("DELETE FROM password_resets WHERE user_id=?")->execute([$user['id']]);

        // Insert new OTP
        $insert = $conn->prepare("INSERT INTO password_resets (user_id, otp_hash, expires_at) VALUES (?,?,?)");
        $insert->execute([$user['id'], $otpHash, $expiry]);

        $_SESSION['reset_email'] = $email;

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ehossain213174@bscse.uiu.ac.bd';   // <-- your Gmail
            $mail->Password = 'luhp botx ralk byej';              // <-- app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('ehossain213174@bscse.uiu.ac.bd', 'RedPulse');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'RedPulse Password Reset OTP';
            $mail->Body = "<h2>Your OTP: $otp</h2><p>Valid for 5 minutes</p>";

            $mail->send();

            $_SESSION['message'] = "OTP sent to your email!";
            header("Location: verify_otp.php");
            exit();

        } catch (Exception $e) {
            $msg = "Email sending failed: " . $mail->ErrorInfo;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Forgot Password - RedPulse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        /* Same styling as index page */
        * {margin: 0; padding: 0; box-sizing: border-box;}
        body {
            background: radial-gradient(circle,rgb(255, 99, 90) 0%,rgb(241, 33, 33) 100%);
            font-family: 'Arial', sans-serif;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            padding-top: 80px;
        }
        .navbar {background-color: rgba(0,0,0,0.8) !important; padding: 5px 20px;}
        .navbar-brand {color:#fff !important; font-size:22px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; display:flex; align-items:center;}
        /*.navbar-nav .nav-item .nav-link.active {color: #fff !important; background-color: rgba(255,255,255,0.3); border-radius: 20px; font-weight:bold;}
        .navbar-nav .nav-item {padding:0 10px;}
        .navbar-nav .nav-link:hover {color:#fff !important; background-color: rgba(255,255,255,0.2); border-radius:20px;}*/
        .hero {text-align:center; margin-top:150px; width:100%; max-width:400px;}
        .hero h2 {font-size:40px; margin-bottom:20px;}
        .hero p {font-size:22px; margin-bottom:30px;}
        .form-control {border-radius:50px; padding:15px; font-size:16px;}
        .btn-custom {background-color:#fff; color:#b22222; font-weight:bold; border-radius:50px; padding:10px 25px; transition:0.3s;}
        .btn-custom:hover {background-color:#f8f4f4; transform: translateY(-3px);}
        .floating-drops {position:absolute; top:0; left:0; width:100%; height:100%; overflow:hidden; z-index:-1;}
        .drop {position:absolute; width:15px; height:15px; background-color:#ffcccb; border-radius:50%; animation:float 10s infinite ease-in-out; opacity:0.8;}
        @keyframes float {0%{transform:translateY(100vh);opacity:0;}50%{opacity:1;}100%{transform:translateY(-10vh);opacity:0;}}
        .drop:nth-child(odd){animation-duration:10s;} .drop:nth-child(even){animation-duration:12s;}
        .logo-container {width:180px;height:60px;display:flex;align-items:center;justify-content:center;}
        .pulse-line {stroke:red; stroke-width:3; fill:none; stroke-dasharray:100; stroke-dashoffset:200; animation:draw 2s infinite linear, glow 1.5s infinite alternate;}
        .pulse-text {font-family:Arial;font-size:24px; fill:red; font-weight:bold; text-anchor:middle; animation:textGlow 1.5s infinite alternate;}
        @keyframes draw {0%{stroke-dashoffset:200;}100%{stroke-dashoffset:0;}}
        @keyframes glow {0%{stroke-opacity:0.8;}100%{stroke-opacity:1;}}
        @keyframes textGlow {0%{fill:rgb(255,80,80); text-shadow:0 0 5px rgba(255,80,80,0.5);}100%{fill:rgb(255,0,0); text-shadow:0 0 10px rgba(255,0,0,1);}}
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <a class="navbar-brand">
            <div class="logo-container">
                <svg width="180" height="60" viewBox="0 0 180 60" xmlns="http://www.w3.org/2000/svg">
                    <text x="90" y="40" class="pulse-text">RedPulse</text>
                    <polyline points="10,40 30,10 50,50 70,15 90,45 110,20 130,40 150,10 170,30" class="pulse-line" stroke-linecap="round" stroke-linejoin="round"></polyline>
                </svg>
            </div>
        </a>
    </nav>

    <div class="hero">
        <h2>Forgot Password?</h2>
        <p>Enter your email to receive a password reset OTP</p>
        <?php if($msg) echo "<p style='color:yellow;'>$msg</p>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" class="form-control mb-3" required>
            <button type="submit" class="btn btn-custom btn-block mb-2">Send OTP</button>
        </form>
        <a href="signin.php" class="btn btn-custom btn-block">Back to Sign In</a>
    </div>

    <!-- Floating Drops Animation -->
    <div class="floating-drops">
        <div class="drop" style="left:10%; animation-delay:0s;"></div>
        <div class="drop" style="left:20%; animation-delay:2s;"></div>
        <div class="drop" style="left:30%; animation-delay:4s;"></div>
        <div class="drop" style="left:40%; animation-delay:1s;"></div>
        <div class="drop" style="left:50%; animation-delay:3s;"></div>
        <div class="drop" style="left:60%; animation-delay:5s;"></div>
        <div class="drop" style="left:70%; animation-delay:0s;"></div>
        <div class="drop" style="left:80%; animation-delay:2s;"></div>
        <div class="drop" style="left:90%; animation-delay:4s;"></div>
    </div>
</body>
</html>
