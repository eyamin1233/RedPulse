<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: users.php");
    exit;
}

try {
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->query("SELECT * FROM user ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch donations grouped by donor_id
    // Step 1: Get mapping of donor_id to user_id
$donorMapStmt = $conn->query("SELECT id AS donor_id, user_id FROM donors");
$donorToUserMap = [];

while ($row = $donorMapStmt->fetch(PDO::FETCH_ASSOC)) {
    $donorToUserMap[$row['donor_id']] = $row['user_id'];
}

// Step 2: Get all donations and map to user_id using donor_id → user_id
$donationsStmt = $conn->query("SELECT donor_id, donated_at FROM donations");
$donationData = [];

while ($row = $donationsStmt->fetch(PDO::FETCH_ASSOC)) {
    $donor_id = $row['donor_id'];
    if (isset($donorToUserMap[$donor_id])) {
        $user_id = $donorToUserMap[$donor_id];
        $donationData[$user_id][] = $row['donated_at'];
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
    <title>All Users - RedPulse Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: radial-gradient(circle, rgb(255, 99, 90) 0%, rgb(241, 33, 33) 100%);
            font-family: 'Arial', sans-serif;
            color: #fff;
            padding: 40px 20px;
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

        .container {
            max-width: 1200px;
            margin: auto;
        }

        h2 {
            color: #fff;
            margin-bottom: 100px;
            position: relative;
            animation: slideDown 0.5s ease-in-out;
            top: 80px;
        }

        .user-card {
            background-color: #fff;
            color: #333;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            align-items: center;
            transition: 0.3s;
        }

        .user-card:hover {
            transform: translateY(-6px);
        }

        .profile-pic {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #dc3545;
        }

        .user-details p {
            margin: 5px 0;
        }

        .user-details strong {
            color: #b30000;
        }

        

        @media (max-width: 768px) {
            .user-card {
                flex-direction: column;
                text-align: center;
            }

            .profile-pic {
                margin-bottom: 10px;
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

        @keyframes draw {
            0% {
                stroke-dashoffset: 200;
            }
            100% {
                stroke-dashoffset: 0;
            }
        }

        @keyframes glow {
            0% {
                stroke-opacity: 0.8;
            }
            100% {
                stroke-opacity: 1;
            }
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
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <a class="navbar-brand">
            <div class="logo-container">
                <svg width="180" height="60" viewBox="0 0 180 60" xmlns="http://www.w3.org/2000/svg">
                    <text x="90" y="40" class="pulse-text">RedPulse</text>
                    <polyline points="10,40 30,10 50,50 70,15 90,45 110,20 130,40 150,10 170,30"
                              class="pulse-line"
                              stroke-linecap="round"
                              stroke-linejoin="round">
                    </polyline>
                </svg>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link" href="admin_profile.php">Dashboard</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin_bloodbanks.php">Blood Banks</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="users.php">Users</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="signout.php">Sign Out</a>
    </li>
</ul>

        </div>
    </nav>

<div class="container">
    <h2 class="text-center"> All Registered Users</h2>

    <?php if (count($users) > 0): ?>
        <?php foreach ($users as $user): ?>
            <div class="user-card justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-4">
        <img src="<?= htmlspecialchars($user['profile_picture']) ?: 'default.jpg' ?>" alt="Profile" class="profile-pic">

        <div class="user-details">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Contact:</strong> <?= htmlspecialchars($user['contact']) ?></p>
            <p><strong>Blood Type:</strong> <?= htmlspecialchars($user['bloodtype']) ?></p>
            <p><strong>Last Donation:</strong> <?= htmlspecialchars($user['lastdonationdate']) ?: 'N/A' ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($user['location']) ?></p>
        </div>
    </div>

    <?php
        $donationCount = isset($donationData[$user['id']]) ? count($donationData[$user['id']]) : 0;
    ?>
    <a href="user_donations.php?user_id=<?= $user['id'] ?>" class="btn btn-danger" style="height: fit-content;">
        Total Donations<br><strong><?= $donationCount ?></strong>
    </a>
</div>

        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            No users found.
        </div>
    <?php endif; ?>
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
