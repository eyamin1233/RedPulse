<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: users.php");
    exit;
}

if (!isset($_GET['user_id'])) {
    die("Invalid request.");
}

$user_id = $_GET['user_id'];

try {
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user info
    $stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }

    // Step 1: Get donor ID from donors table
    $donorStmt = $conn->prepare("SELECT id FROM donors WHERE user_id = ?");
    $donorStmt->execute([$user_id]);
    $donor = $donorStmt->fetch(PDO::FETCH_ASSOC);

    if (!$donor) {
        $donations = []; // No donor record = no donations
    } else {
        $donor_id = $donor['id'];

        // Step 2: Fetch donations with recipient info using donor_id
        $donationsStmt = $conn->prepare("
            SELECT d.donated_at, r.recipient_name, r.bloodtype, r.contact, r.location, r.received_date
            FROM donations d
            LEFT JOIN recipient r ON d.request_id = r.request_id
            WHERE d.donor_id = ?
            ORDER BY d.donated_at DESC
        ");
        $donationsStmt->execute([$donor_id]);
        $donations = $donationsStmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Donations</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: radial-gradient(circle,rgb(247, 78, 70) 0%,rgb(153, 20, 20) 100%);
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

        .container {
    background-color: rgba(255, 255, 255, 0.95); /* light background for contrast */
    border-radius: 20px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    padding: 40px 30px;
    max-width: 1000px;
    width: 90%;
    animation: slideDown 0.8s ease-out;
    color: #333; /* dark text for readability */
}

.container h2 {
    color: #b30000; /* red tone for heading */
    font-weight: bold;
    text-align: center;
    margin-bottom: 30px;
}

.table {
    background-color: #fff;
    border-radius: 10px;
    overflow: hidden;
}

.table th, .table td {
    vertical-align: middle;
    text-align: center;
}

.alert-warning {
    font-size: 18px;
    text-align: center;
}

.btn-secondary {
    position : relative;
    left: 400px;
    border-radius: 25px;
    font-weight: medium;
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
        <a class="nav-link" href="admn_bloodbanks.php">Blood Banks</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="users.php">Users</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="signout.php">Sign Out</a>
    </li>
</ul>

        </div>
    </nav>

<div class="container py-5">
    <h2 class="mb-4">Donation History for <?= htmlspecialchars($user['name']) ?></h2>

    <?php if (count($donations) > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Donation Date</th>
                    <th>Recipient Name</th>
                    <th>Blood Type</th>
                    <th>Contact</th>
                    <th>Location</th>
                    <th>Received Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donations as $donation): ?>
                    <tr>
                        <td><?= htmlspecialchars($donation['donated_at']) ?></td>
                        <td><?= htmlspecialchars($donation['recipient_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($donation['bloodtype'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($donation['contact'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($donation['location'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($donation['received_date'] ?? 'N/A') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">No donations found for this user.</div>
    <?php endif; ?>

    <a href="users.php" class="btn btn-secondary mt-3">Back to Users</a>
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
