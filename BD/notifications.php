<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: notifications.php");
    exit;
}
$userId = $_SESSION['user_id'];
$conn = new PDO("mysql:host=localhost;dbname=blooddonationmanagementsystem", "root", "");

// Mark all as read
$conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$userId]);

// Fetch notifications
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

        /* Container styling */
.container {
    background-color: #fff;
    padding: 25px 30px;
    border-radius: 15px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    color: #333;
}

/* Heading style */
.container h3 {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 20px;
    color: #dc3545; /* Red tone */
}

/* List group item styles */
.list-group-item {
    border: none;
    border-bottom: 1px solid #eee;
    font-size: 1.3rem;
    padding: 15px 20px;
    transition: background-color 0.3s;
}

/* Hover effect */
.list-group-item:hover {
    background-color: #f8f9fa;
}

/* Link/button inside item */
.list-group-item .btn-link {
    font-weight: bold;
    color: #007bff;
    text-decoration: none;
    padding-left: 10px;
}

.list-group-item .btn-link:hover {
    text-decoration: underline;
    color: #0056b3;
}

/* Timestamp */
.list-group-item .text-muted {
    font-size: 0.9rem;
    color: #888;
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
        <a class="nav-link" href="profile.php">Profile</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="notifications.php">Notifications</a>
    </li>
</ul>

        </div>
    </nav>
<div class="container mt-5">
    <h3>Your Notifications</h3>
    <ul class="list-group">
        <?php foreach ($notifications as $note): ?>
            <li class="list-group-item">
                <?= htmlspecialchars($note['message']) ?>
                <?php if (!empty($note['link'])): ?>
                    <a href="<?= htmlspecialchars($note['link']) ?>" class="btn btn-sm btn-link">View</a>
                <?php endif; ?>
                <span class="text-muted float-right"><?= date('d M Y, h:i A', strtotime($note['created_at'])) ?></span>
            </li>
        <?php endforeach; ?>
        <?php if (empty($notifications)) echo "<li class='list-group-item'>No notifications found.</li>"; ?>
    </ul>
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
