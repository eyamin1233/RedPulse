<?php
session_start();

try {
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $success = '';
    $error = '';

    // Step 1: Check if email exists and show reset form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $new_password = trim($_POST['new_password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        // If user is submitting email only
        if (!empty($email) && empty($new_password)) {
            $stmt = $conn->prepare("SELECT id, name FROM user WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['reset_user_id'] = $user['id'];
                $_SESSION['reset_email'] = $email;
            } else {
                $error = "Email not found in our records.";
            }
        }

        // If user is submitting new password
        if (!empty($new_password)) {
            if ($new_password !== $confirm_password) {
                $error = "Passwords do not match.";
            } elseif (!isset($_SESSION['reset_user_id'])) {
                $error = "Session expired. Please enter your email again.";
            } else {
                $hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE user SET password = ? WHERE id = ?");
                $update->execute([$hash, $_SESSION['reset_user_id']]);

                $success = "Password updated successfully! You can now log in.";
                // Clear session
                unset($_SESSION['reset_user_id'], $_SESSION['reset_email']);
            }
        }
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - RedPulse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            background: radial-gradient(circle,rgb(255, 99, 90) 0%,rgb(241, 33, 33) 100%);
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar {
            background-color: rgba(0,0,0,0.8) !important;
            padding: 5px 20px;
        }
        .navbar-brand {
            color: #fff !important;
            font-size: 22px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .navbar-nav .nav-link.active {
            color: rgb(253, 253, 253) !important;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            font-weight: bold;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
        }

        .reset-card {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
        }

        .reset-card h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #dc3545;
        }

        label {
            font-weight: bold;
        }

        .floating-drops {
            position: absolute;
            top: 0; left:0;
            width: 100%; height: 100%;
            overflow: hidden; z-index: -1;
        }
        .drop {
            position: absolute;
            width: 15px;
            height: 15px;
            background-color: #ffcccb;
            border-radius: 50%;
            animation: float 10s infinite ease-in-out;
            opacity: 0.8;
        }
        @keyframes float {
            0% { transform: translateY(100vh); opacity:0; }
            50% { opacity:1; }
            100% { transform: translateY(-10vh); opacity:0; }
        }

        .drop:nth-child(odd) { animation-duration: 10s; }
        .drop:nth-child(even) { animation-duration: 12s; }

        .logo-container { width:180px; height:60px; display:flex; align-items:center; justify-content:center; }
        .pulse-line { stroke:red; stroke-width:3; fill:none; stroke-dasharray:100; stroke-dashoffset:200; animation: draw 2s infinite linear, glow 1.5s infinite alternate; }
        .pulse-text { font-family: Arial; font-size:24px; fill:red; font-weight:bold; text-anchor:middle; animation:textGlow 1.5s infinite alternate; }
        @keyframes draw { 0% { stroke-dashoffset:200; } 100% { stroke-dashoffset:0; } }
        @keyframes glow { 0% { stroke-opacity:0.8; } 100% { stroke-opacity:1; } }
        @keyframes textGlow { 0% { fill: rgb(255,80,80); text-shadow:0 0 5px rgba(255,80,80,0.5); } 100% { fill: rgb(255,0,0); text-shadow:0 0 10px rgba(255,0,0,1); } }

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <a class="navbar-brand" href="#">
        <div class="logo-container">
            <svg width="180" height="60" viewBox="0 0 180 60" xmlns="http://www.w3.org/2000/svg">
                <text x="90" y="40" class="pulse-text">RedPulse</text>
                <polyline points="10,40 30,10 50,50 70,15 90,45 110,20 130,40 150,10 170,30"
                          class="pulse-line"
                          stroke-linecap="round"
                          stroke-linejoin="round"></polyline>
            </svg>
        </div>
    </a>
</nav>

<div class="reset-card mt-5">
    <h3>Forgot Password</h3>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if(!empty($success)): ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
        <a href="signin.php" class="btn btn-danger w-100 mt-2">Login Now</a>
    <?php else: ?>

    <form method="POST">
        <?php if (!isset($_SESSION['reset_user_id'])): ?>
            <div class="form-group">
                <label>Enter your registered email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-danger w-100">Verify Email</button>
        <?php else: ?>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-danger w-100">Change Password</button>
        <?php endif; ?>
    </form>
    <?php endif; ?>
</div>

<div class="floating-drops">
    <div class="drop" style="left:10%;"></div>
    <div class="drop" style="left:20%;"></div>
    <div class="drop" style="left:30%;"></div>
    <div class="drop" style="left:40%;"></div>
    <div class="drop" style="left:50%;"></div>
    <div class="drop" style="left:60%;"></div>
    <div class="drop" style="left:70%;"></div>
    <div class="drop" style="left:80%;"></div>
    <div class="drop" style="left:90%;"></div>
</div>

</body>
</html>
