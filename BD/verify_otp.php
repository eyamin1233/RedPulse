<?php
session_start();
$conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem','root','');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$msg = "";

if(!isset($_SESSION['reset_email'])){
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];

// Get user ID
$stmt = $conn->prepare("SELECT id FROM user WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
    $msg = "Invalid session.";
}

if($_SERVER['REQUEST_METHOD'] === "POST") {
    $otp = trim($_POST['otp']);
    $newPassword = trim($_POST['password']);

    // Fetch latest OTP for user
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE user_id=? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$user['id']]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if($reset && password_verify($otp, $reset['otp_hash'])) {
    if(new DateTime() > new DateTime($reset['expires_at'])){
        $msg = "OTP expired!";
    } else {
        // Update password
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $conn->prepare("UPDATE user SET password=? WHERE id=?")->execute([$hashed, $user['id']]);

        // Delete OTP
        $conn->prepare("DELETE FROM password_resets WHERE user_id=?")->execute([$user['id']]);

        unset($_SESSION['reset_email']);
        $success = "Password reset successful! Redirecting to <a href='signin.php' style='color:#fff; text-decoration:underline;'>Sign In</a>...";
    }
} else {
    $msg = "Invalid OTP!";
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Verify OTP - RedPulse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        /* Same styling as index page */
        * {margin:0; padding:0; box-sizing:border-box;}
        body {
            background: radial-gradient(circle,rgb(255, 99, 90) 0%,rgb(241, 33, 33) 100%);
            font-family: 'Arial', sans-serif;
            color: #fff;
            display:flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            padding-top: 80px;
        }
        .success-msg {
    position: fixed;
    top: -100px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #28a745; /* green success */
    color: #fff;
    padding: 15px 30px;
    border-radius: 50px;
    font-size: 18px;
    font-weight: bold;
    z-index: 9999;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    animation: slideDown 1s forwards;
}

@keyframes slideDown {
    from { top: -100px; opacity: 0; }
    to { top: 20px; opacity: 1; }
}

        .navbar {background-color: rgba(0,0,0,0.8) !important; padding:5px 20px;}
        .navbar-brand {color:#fff !important; font-size:22px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; display:flex; align-items:center;}
        /*.navbar-nav .nav-item .nav-link.active {color:#fff !important; background-color: rgba(255,255,255,0.3); border-radius:20px; font-weight:bold;}
        .navbar-nav .nav-item {padding:0 10px;}
        .navbar-nav .nav-link:hover {color:#fff !important; background-color: rgba(255,255,255,0.2); border-radius:20px;}*/
        .hero {text-align:center; margin-top:100px; width:100%; max-width:400px;}
        .hero h2 {font-size:40px; margin-bottom:20px;}
        .hero p {font-size:20px; margin-bottom:30px;}
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
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" >
            <span class="navbar-toggler-icon"></span>
        </button>
    </nav>

    <div class="hero">
        <h2>Verify OTP & Reset Password</h2>
        <p>Enter the OTP sent to your email</p>
        <?php if($msg) echo "<p style='color:black;'>$msg</p>"; ?>
        <form method="POST">
            <input type="text" name="otp" placeholder="Enter OTP" class="form-control mb-3" required>
            <input type="password" name="password" placeholder="New Password" class="form-control mb-3" required>
            <button type="submit" class="btn btn-custom btn-block mb-2">Reset Password</button>
        </form>
        <a href="forgot_password.php" class="btn btn-custom btn-block">Back</a>
        <?php if(isset($success)): ?>
    <div class="success-msg"><?php echo $success; ?></div>
<?php endif; ?>
    </div>

    <?php if(isset($success)): ?>
<script>
    setTimeout(function(){
        window.location.href = 'signin.php';
    }, 3000);
</script>
<?php endif; ?>


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
