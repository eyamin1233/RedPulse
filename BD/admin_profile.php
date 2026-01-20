<?php
session_start();

try {
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if admin is logged in
    if (!isset($_SESSION['admin_id'])) {
        header("Location: signin.php");
        exit;
    }

    $adminId = $_SESSION['admin_id'];

    // Fetch admin info
    $stmt = $conn->prepare("SELECT * FROM admin WHERE AdminID = ?");
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Total users
    $totalUsers = $conn->query("SELECT COUNT(*) FROM user")->fetchColumn();

    // Total donations
    $totalDonations = $conn->query("SELECT COUNT(*) FROM donations")->fetchColumn();

    // Total donors
    $totalDonors = $conn->query("SELECT COUNT(DISTINCT donor_id) FROM donations")->fetchColumn();

    // Total blood banks
$totalBloodBanks = $conn->query("SELECT COUNT(*) FROM bloodbank")->fetchColumn();


} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Profile - RedPulse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap & jQuery -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        body {
            background: radial-gradient(circle,rgb(255, 99, 90) 0%,rgb(241, 33, 33) 100%);
            font-family: 'Arial', sans-serif;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.8) !important;
            padding: 5px 20px;
        }

        .navbar-brand {
            color: #fff !important;
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
        }


        .navbar-nav .nav-item .nav-link.active {
            color:rgb(253, 253, 253) !important;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            font-weight: bold;
        }

        .navbar-nav .nav-item{
            padding: 0px 10px;
        }

        .navbar a {
            font-size: 20px;
        }


        .navbar-nav .nav-item .nav-link:hover {
            color:rgb(255, 255, 255) !important;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
        }

        .navbar-toggler {
            border-color: rgba(0, 0, 0, 0.5);
        }



        .container-box {
    display: flex;
    flex-wrap: wrap;
    max-width: 1000px;
    margin: 120px auto 50px auto;
    gap: 24px;
}


        .profile-card {
    position: center;
    height: 60%;
    background: white;
    color: #000;
    font-size: 1.1rem;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}


        .stats-card {
            background: white;
            color: #000;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .profile-card {
    flex: 1;
    min-width: 260px;
    max-width: 280px;
}

.stats-card {
    flex: 2;
    min-width: 500px;
    padding: 25px;
}


        .stat-box {
    padding: 15px;
    border-radius: 10px;
    background: #f8f9fa;
    margin-bottom: 15px;
    text-align: center;
}


        .stat-box h2 {
    font-size: 2.2rem;
    margin-bottom: 4px;
    color: #dc3545;
}


        .change-btn {
            margin-top: 20px;
        }

        .logo-container {
            width: 180px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pulse-line {
            stroke: red;
            stroke-width: 3;
            fill: none;
            stroke-dasharray: 100;
            stroke-dashoffset: 200;
            animation: draw 2s infinite linear, glow 1.5s infinite alternate;
        }

        .pulse-text {
            font-family: Arial, sans-serif;
            font-size: 24px;
            fill: red;
            font-weight: bold;
            text-anchor: middle;
            animation: textGlow 1.5s infinite alternate;
        }

        .profile-card .btn {
            font-size: 0.95rem;
        }


        @keyframes draw {
            0% { stroke-dashoffset: 200; }
            100% { stroke-dashoffset: 0; }
        }

        @keyframes glow {
            0% { stroke-opacity: 0.8; }
            100% { stroke-opacity: 1; }
        }

        @keyframes textGlow {
            0% {
                fill: rgb(255, 80, 80);
                text-shadow: 0 0 5px rgba(255, 80, 80, 0.5);
            }
            100% {
                fill: rgb(255, 0, 0);
                text-shadow: 0 0 10px rgba(255, 0, 0, 1);
            }
        }
        .floating-drops {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
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

        @keyframes slideDown {
            0% {
                opacity: 0;
                transform: translateY(-50px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0% {
                transform: translateY(100vh);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                transform: translateY(-10vh);
                opacity: 0;
            }
        }

        .drop:nth-child(odd) {
            animation-duration: 10s;
        }

        .drop:nth-child(even) {
            animation-duration: 12s;
        }
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
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link active" href="admin_profile.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_bloodbanks.php">Blood Banks</a></li>
            <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="signout.php">Sign Out</a></li>
        </ul>
    </div>
</nav>

<div class="container-box">
    <!-- Left: Admin Info -->
    <div class="profile-card">
        <h2> Admin Profile</h2>
        <p><strong>Name:</strong><br><?= htmlspecialchars($admin['UserName']) ?></p>
        <p><strong>Email:</strong><br><?= htmlspecialchars($admin['Email']) ?></p>
        <a href="admin_profile_edit.php" class="btn btn-primary w-100 mb-2">
    Edit Profile
</a>

<a href="admin_password_change.php" class="btn btn-warning w-100 change-btn">
    Change Password
</a>

    </div>

    <!-- Right: Statistics -->
    <div class="stats-card">
        <h3> Website Statistics</h3>

        <div class="stat-box">
            <h2><?= $totalUsers ?></h2>
            <p>Registered Users</p>
        </div>

        <div class="stat-box">
            <h2><?= $totalDonations ?></h2>
            <p>Total Donations</p>
        </div>

        <div class="stat-box">
            <h2><?= $totalDonors ?></h2>
            <p>Donors</p>
        </div>
        <div class="stat-box">
    <h2><?= $totalBloodBanks ?></h2>
    <p>Registered Blood Banks</p>
</div>

    </div>
</div>

<div class="floating-drops">
        <div class="drop" style="left: 10%; animation-delay: 0s;"></div>
        <div class="drop" style="left: 20%; animation-delay: 2s;"></div>
        <div class="drop" style="left: 30%; animation-delay: 4s;"></div>
        <div class="drop" style="left: 40%; animation-delay: 1s;"></div>
        <div class="drop" style="left: 50%; animation-delay: 3s;"></div>
        <div class="drop" style="left: 60%; animation-delay: 5s;"></div>
        <div class="drop" style="left: 70%; animation-delay: 0s;"></div>
        <div class="drop" style="left: 80%; animation-delay: 2s;"></div>
        <div class="drop" style="left: 90%; animation-delay: 4s;"></div>
    </div>

</body>
</html>
