<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    echo "<script>location.assign('profile.php');</script>";
    exit();
}

$userId = $_SESSION['user_id'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=blooddonationmanagementsystem", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Count total donations
    $stmt = $pdo->prepare("SELECT id FROM donors WHERE user_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$userId]);
    $donor_id = $stmt->fetchColumn();

    $totalDonations = 0;

    if ($donor_id) {
        // Now count donations using donor_id
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM donations WHERE donor_id = ?");
        $stmt->execute([$donor_id]);
        $totalDonations = $stmt->fetchColumn();
    }

    // Fetch leaderboard (top 3)
    $stmtLeaderboard = $pdo->prepare("
        SELECT u.name, COUNT(don.id) as donation_count 
        FROM user u
        LEFT JOIN donors d ON u.id = d.user_id
        LEFT JOIN donations don ON d.id = don.donor_id
        GROUP BY u.id, u.name
        ORDER BY donation_count DESC
        LIMIT 3
    ");
    $stmtLeaderboard->execute();
    $leaderboard = $stmtLeaderboard->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $totalDonations = 0;
    $leaderboard = [];
}

// Fetch latest user data from database
$stmt = $pdo->prepare("SELECT name, email, contact, bloodtype, lastdonationdate, location, profile_picture FROM user WHERE id = ?");
$stmt->execute([$userId]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

// Assign values
$name = $userData['name'];
$email = $userData['email'];
$contact = $userData['contact'];
$bloodtype = $userData['bloodtype'];
$lastdonationdate = $userData['lastdonationdate'];
$location = $userData['location'];
$profile_picture = $userData['profile_picture'] ?? 'photos/default-placeholder.png';

// Calculate next donation date (3 months after last donation)
$nextDonationDate = date('Y-m-d', strtotime($lastdonationdate . ' + 3 months'));

// Determine eligibility (Eligible if next donation date is today or earlier)
$isEligible = (strtotime($nextDonationDate) <= time());

// Calculate days since last donation
$daysSinceLastDonation = floor((time() - strtotime($lastdonationdate)) / (60 * 60 * 24));

// Calculate estimated blood donated (assuming 450ml per donation)
$totalBloodDonated = $totalDonations * 450;

// Calculate lives potentially saved (1 donation can save up to 3 lives)
$livesSaved = $totalDonations * 3;

// Calculate progress to next badge
$nextBadge = 1;
$progressPercent = 0;
if ($totalDonations >= 20) {
    $nextBadge = 20;
    $progressPercent = 100;
} elseif ($totalDonations >= 10) {
    $nextBadge = 20;
    $progressPercent = ($totalDonations / 20) * 100;
} elseif ($totalDonations >= 5) {
    $nextBadge = 10;
    $progressPercent = ($totalDonations / 10) * 100;
} elseif ($totalDonations >= 1) {
    $nextBadge = 5;
    $progressPercent = ($totalDonations / 5) * 100;
} else {
    $nextBadge = 1;
    $progressPercent = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_email'])) {

    $inviteEmail = filter_var($_POST['invite_email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($inviteEmail, FILTER_VALIDATE_EMAIL)) {

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ehossain213174@bscse.uiu.ac.bd';
            $mail->Password   = 'luhp botx ralk byej';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('ehossain213174@bscse.uiu.ac.bd', 'RedPulse');
            $mail->addAddress($inviteEmail);

            $mail->isHTML(false);
            $mail->Subject = "Join RedPulse - Save Lives Through Blood Donation";

            $mail->Body =
"Hello,

{$name} has invited you to join RedPulse.

{$name} has already made {$totalDonations} donation(s)
and saved approximately {$livesSaved} lives.

Join us in saving lives.

Best regards,
RedPulse Team";

            $mail->send();
            $inviteSuccess = "Invitation sent successfully!";

        } catch (Exception $e) {
            $inviteError = "Mailer Error: " . $mail->ErrorInfo;
        }

    } else {
        $inviteError = "Invalid email address.";
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        
        body {
            background: radial-gradient(circle,rgb(255, 99, 90) 0%,rgb(241, 33, 33) 100%);
            color: #fff;
            font-family: Arial, sans-serif;
        }

        
        .sidebar {
            position: fixed;
            left: -350px;
            top: 75px;
            width: 320px;
            height: calc(80% - 70px);
            background: #111;
            padding: 25px;
            transition: 0.4s;
            z-index: 1050;
            overflow-y: auto;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar h5 {
            color: #fff;
            margin-bottom: 15px;
            font-size: 1.3rem;
            font-weight: bold;
        }

        .sidebar hr {
            background-color: #dc3545;
            height: 2px;
        }

        .sidebar p {
            margin: 12px 0;
            font-size: 15px;
            color: #ccc;
            line-height: 1.6;
        }

        .sidebar strong {
            color: #dc3545;
            display: inline-block;
            min-width: 120px;
        }

        .sidebar .eligibility-status {
            background: <?php echo $isEligible ? '#28a745' : '#dc3545'; ?>;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .sidebar .donate-btn-sidebar {
            background: <?php echo $isEligible ? '#28a745' : '#6c757d'; ?>;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            display: block;
            text-decoration: none;
            font-weight: bold;
            margin-top: 15px;
            pointer-events: <?php echo $isEligible ? 'auto' : 'none'; ?>;
        }

        .sidebar .donate-btn-sidebar:hover {
            background: <?php echo $isEligible ? '#218838' : '#6c757d'; ?>;
            color: white;
            text-decoration: none;
        }

        
        .profile-toggle {
            position: fixed;
            top: 85px;
            left: 15px;
            width: 42px;
            height: 42px;
            background: #111;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 1100;
            transition: 0.4s;
            border: 2px solid #dc3545;
        }

        .profile-toggle span {
            width: 22px;
            height: 3px;
            background: #fff;
            margin: 3px 0;
            transition: 0.3s;
        }

        .profile-toggle.active {
            left: 335px;
        }

        .profile-toggle:hover span {
            background: #dc3545;
        }

        .profile-header-center {
            text-align: center;
            margin-top: 80px;
            margin-bottom: 30px;
        }

        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            margin: 0 auto 15px;
            display: block;
        }

        .profile-header-center h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: rgb(17, 17, 17);
            margin-bottom: 15px;
        }

        #status-message {
            font-size: 1.3rem;
            font-weight: bold;
            color: rgb(63, 50, 50);
            padding: 12px 20px;
            border-radius: 10px;
            display: inline-block;
        }

        /* Info Cards Container */
        .info-cards-container {
            max-width: 1200px;
            margin: 0 auto 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 0 20px;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.8) !important;
            padding: 3px 20px;
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

        .info-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .info-card h3 {
            color: #dc3545;
            font-size: 1.3rem;
            margin-bottom: 15px;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 8px;
        }

        .progress-bar-custom {
            height: 25px;
            background: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #dc3545, #ff6b6b);
            transition: width 0.5s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: red;
            font-weight: bold;
            font-size: 0.85rem;
        }

        .leaderboard-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            margin: 8px 0;
            background: rgba(220, 53, 69, 0.1);
            border-radius: 8px;
            border-left: 4px solid #dc3545;
        }

        .leaderboard-item.top {
            background: rgba(255, 215, 0, 0.2);
            border-left-color: gold;
        }

        .leaderboard-name {
            color: #333;
            font-weight: 600;
        }

        .leaderboard-count {
            color: #dc3545;
            font-weight: bold;
        }

        .reminder-item {
            padding: 12px;
            margin: 8px 0;
            background: rgba(40, 167, 69, 0.1);
            border-radius: 8px;
            border-left: 4px solid #28a745;
            color: #333;
            font-size: 0.95rem;
        }

        .reminder-item strong {
            color: #28a745;
        }

        /* Social Sharing */
        .share-buttons {
            display: flex;
            gap: 10px;
            margin: 15px 0;
        }

        .share-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .share-btn:hover {
            transform: translateY(-2px);
        }

        .share-btn.facebook { background: #3b5998; }
        .share-btn.twitter { background: #1da1f2; }
        .share-btn.whatsapp { background: #25d366; }

        .invite-form {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .invite-form input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .invite-form button {
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        .invite-form button:hover {
            background: #c82333;
        }

        /* Quick Stats Cards */
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin: 30px auto;
            max-width: 1200px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            border-radius: 12px;
            padding: 20px;
            width: 180px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h4 {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            color: #dc3545;
            font-weight: bold;
            margin: 5px 0;
        }

        .stat-card .stat-label {
            font-size: 0.85rem;
            color: #888;
        }

        .container-box {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin: 20px;
            margin-top: 30px;
        }
        .card1 {
            background: #f8d7da;
            color: #444;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 30px;
            width: 250px;
            text-align: center;
        }

        .card2 {
            background: #f8d7da;
            color: #444;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 25px;
            width: 250px;
            text-align: center;
        }

        .card3 {
            background: #fff3cd;
            color: #444;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 25px;
            width: 520px;
            text-align: left;
        }

        .card3 h3 {
            color: #856404;
            margin-bottom: 15px;
            text-align: center;
        }

        .compatibility-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 10px;
        }

        .compatibility-item {
            background: rgba(255, 255, 255, 0.7);
            padding: 10px;
            border-radius: 8px;
            border-left: 4px solid #856404;
        }

        .compatibility-item strong {
            color: #856404;
        }

        .card1 h2 {
            font-size: 2.5rem;
            color: #f44336;
        }
        .badges img {
            width: 60px;
            margin: 10px;
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
            width: 25px;
            height: 25px;
            background-color: #ffcccb;
            border-radius: 50%;
            animation: float 10s infinite ease-in-out;
            opacity: 0.8;
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

        .alert-success, .alert-danger {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
    </style>
</head>
<body>

<!-- Toggle Button -->
<div class="profile-toggle" onclick="toggleSidebar()">
    <span></span>
    <span></span>
    <span></span>
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h5>Profile Details</h5>
    <hr>
    <p><strong>Contact:</strong> <?php echo htmlspecialchars($contact); ?></p>
    <p><strong>Blood Type:</strong> <?php echo htmlspecialchars($bloodtype); ?></p>
    <p><strong>Location:</strong> <?php echo htmlspecialchars($location); ?></p>
    <p><strong>Last Donation:</strong> <?php echo htmlspecialchars($lastdonationdate); ?></p>
    <p><strong>Next Eligible:</strong> <?php echo htmlspecialchars($nextDonationDate); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <p><strong>Eligibility:</strong> <span class="eligibility-status" id="sidebar-eligibility"><?php echo $isEligible ? '✔ Eligible' : '✘ Not Eligible'; ?></span></p>

    <a href="donations.php" class="donate-btn-sidebar" id="donate-btn-sidebar">Donate Now</a>
    <a href="profile_edit.php" class="btn btn-primary btn-sm w-100 mt-3">Edit Profile</a>
    <a href="donations_list.php" class="btn btn-success btn-sm w-100 mt-2">Donation History</a>
</div>

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
        <a class="nav-link active" href="profile.php">Profile</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="posts.php">Posts</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="bloodbanks_all.php">Blood Banks</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="donor_search.php">Donor Search</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="signout.php">Sign Out</a>
    </li>
    <li class="nav-item">
    <a class="nav-link position-relative active" href="notifications.php">
        🔔 
        <?php
        // Fetch unread notifications count
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        $unreadCount = $stmt->fetchColumn();
        if ($unreadCount > 0) {
            echo "<span class='badge badge-danger' style='position: absolute; top: -5px; right: -10px;'>$unreadCount</span>";
        }
        ?>
    </a>
</li>

</ul>

        </div>
    </nav>

    <!-- Profile Header - Centered at Top -->
    <div class="profile-header-center">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-picture">
        <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
        
        <!-- Status Message: Shows eligibility or countdown -->
        <p id="status-message">
            <?php 
            if ($isEligible) {
                echo " You are eligible to donate!";
            } else {
                echo "Loading...";
            }
            ?>
        </p>
    </div>

    <!-- Info Cards Grid (2 columns) -->
    <div class="info-cards-container">
        <!-- Achievement Progress Bar (Left) -->
        <div class="info-card">
            <h3> Achievement Progress</h3>
            <p style="color: #333; margin-bottom: 5px;">
                <strong>Next Badge:</strong> 
                <?php 
                if ($totalDonations >= 20) echo "Legend (Achieved!)";
                elseif ($totalDonations >= 10) echo "Legend at 20 donations";
                elseif ($totalDonations >= 5) echo "Gold at 10 donations";
                elseif ($totalDonations >= 1) echo "Silver at 5 donations";
                else echo "Bronze at 1 donation";
                ?>
            </p>
            <div class="progress-bar-custom">
                <div class="progress-fill" style="width: <?php echo $progressPercent; ?>%;">
                    <?php echo round($progressPercent); ?>%
                </div>
            </div>
            <p style="color: #666; font-size: 0.9rem; margin-top: 5px;">
                <?php 
                if ($totalDonations >= 20) {
                    echo " You've unlocked all badges!";
                } else {
                    $donationsNeeded = $nextBadge - $totalDonations;
                    echo "{$donationsNeeded} more donation(s) to unlock next badge!";
                }
                ?>
            </p>
        </div>

        <!-- Leaderboard (Right) -->
        <div class="info-card">
            <h3> Top Donors Leaderboard</h3>
            <?php foreach ($leaderboard as $index => $donor): ?>
                <div class="leaderboard-item <?php echo $index === 0 ? 'top' : ''; ?>">
                    <span class="leaderboard-name">
                        <?php echo ($index + 1) . ". " . htmlspecialchars($donor['name']); ?>
                        <?php if ($donor['name'] === $name) echo " (You)"; ?>
                    </span>
                    <span class="leaderboard-count"><?php echo $donor['donation_count']; ?> donations</span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Health Reminders (Left) -->
        <div class="info-card">
            <h3> Health Reminders</h3>
            <div class="reminder-item">
                <strong>Before Donation:</strong> Eat iron-rich foods and stay hydrated 24 hours before.
            </div>
            <div class="reminder-item">
                <strong>After Donation:</strong> Rest for 10-15 minutes and drink plenty of fluids.
            </div>
            <div class="reminder-item">
                <strong>Recovery:</strong> Avoid heavy exercise for 24 hours post-donation.
            </div>
        </div>

        <!-- Social Sharing (Right) -->
        <div class="info-card">
            <h3> Share Your Achievement</h3>
            <p style="color: #333; margin-bottom: 10px;">Spread the word and inspire others to donate blood!</p>
            <div class="share-buttons">
                <button class="share-btn facebook" onclick="shareOnFacebook()">Facebook</button>
                <button class="share-btn twitter" onclick="shareOnTwitter()">Twitter</button>
                <button class="share-btn whatsapp" onclick="shareOnWhatsApp()">WhatsApp</button>
            </div>
            
            <h4 style="color: #333; margin-top: 20px; font-size: 1.1rem;">Invite Friends via Email</h4>
            <form method="POST" class="invite-form">
                <input type="email" name="invite_email" placeholder="Friend's email address" required>
                <button type="submit">Send Invite</button>
            </form>
            <?php if (isset($inviteSuccess)): ?>
                <div class="alert-success"><?php echo $inviteSuccess; ?></div>
            <?php endif; ?>
            <?php if (isset($inviteError)): ?>
                <div class="alert-danger"><?php echo $inviteError; ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Stats Dashboard -->
    <div class="stats-container">
        <div class="stat-card">
            <h4>Days Since Last Donation</h4>
            <div class="stat-value"><?php echo $daysSinceLastDonation; ?></div>
            <div class="stat-label">days</div>
        </div>
        <div class="stat-card">
            <h4>Total Blood Donated</h4>
            <div class="stat-value"><?php echo $totalBloodDonated; ?></div>
            <div class="stat-label">ml</div>
        </div>
        <div class="stat-card">
            <h4>Lives Potentially Saved</h4>
            <div class="stat-value"><?php echo $livesSaved; ?></div>
            <div class="stat-label">lives</div>
        </div>
        <div class="stat-card">
            <h4>Next Badge At</h4>
            <div class="stat-value">
                <?php 
                if ($totalDonations < 1) echo "1";
                elseif ($totalDonations < 5) echo "5";
                elseif ($totalDonations < 10) echo "10";
                elseif ($totalDonations < 20) echo "20";
                else echo "20+";
                ?>
            </div>
            <div class="stat-label">donations</div>
        </div>
    </div>

    <div class="container-box">
    <!-- Total Donations Card -->
    <div class="card1">
        <h3>Total Donations</h3>
        <h2><?php echo $totalDonations; ?></h2>
        <p>
            <?php
            if ($totalDonations == 0) {
                echo "Donate to become a hero!";
            } else {
                echo "Thanks for being a hero!";
            }
            ?>
        </p>
    </div>

    <!-- Badge Card -->
    <div class="card2">
        <h3>Your Badges</h3>
        <div class="badges">
            <p>
                <?php
                if ($totalDonations == 0) {
                    echo "Donate to earn badges!";
                } else {
                    echo "Keep donating to earn more!";
                }
                ?>
            </p>

            <?php
            if ($totalDonations >= 1) {
                echo '<img src="Photos/B_b.webp" alt="Bronze Badge" title="1+ Donations">';
            }
            if ($totalDonations >= 5) {
                echo '<img src="Photos/S_s.webp" alt="Silver Badge" title="5+ Donations">';
            }
            if ($totalDonations >= 10) {
                echo '<img src="Photos/G_g.png" alt="Gold Badge" title="10+ Donations">';
            }
            if ($totalDonations >= 20) {
                echo '<img src="assets/badges/legend.png" alt="Legend Badge" title="20+ Donations">';
            }
            ?>
        </div>
    </div>

    <!-- Blood Type Compatibility Card -->
    <div class="card3">
        <h3>🩸 Blood Type Compatibility</h3>
        <div class="compatibility-grid">
            <div class="compatibility-item">
                <strong>You can donate to:</strong>
                <p style="margin: 5px 0; color: #333;">
                    <?php
                    $canDonateTo = [
                        'A+' => 'A+, AB+',
                        'A-' => 'A+, A-, AB+, AB-',
                        'B+' => 'B+, AB+',
                        'B-' => 'B+, B-, AB+, AB-',
                        'AB+' => 'AB+',
                        'AB-' => 'AB+, AB-',
                        'O+' => 'A+, B+, AB+, O+',
                        'O-' => 'All Blood Types'
                    ];
                    echo $canDonateTo[$bloodtype] ?? 'N/A';
                    ?>
                </p>
            </div>
            <div class="compatibility-item">
                <strong>You can receive from:</strong>
                <p style="margin: 5px 0; color: #333;">
                    <?php
                    $canReceiveFrom = [
                        'A+' => 'A+, A-, O+, O-',
                        'A-' => 'A-, O-',
                        'B+' => 'B+, B-, O+, O-',
                        'B-' => 'B-, O-',
                        'AB+' => 'All Blood Types',
                        'AB-' => 'A-, B-, AB-, O-',
                        'O+' => 'O+, O-',
                        'O-' => 'O-'
                    ];
                    echo $canReceiveFrom[$bloodtype] ?? 'N/A';
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>

    <!-- Floating Drops Animation -->
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

<script>
    function updateCountdown() {
        const nextDonationDate = new Date("<?php echo $nextDonationDate; ?>").getTime();
        const now = new Date().getTime();
        const timeLeft = nextDonationDate - now;

        const statusMessage = document.getElementById("status-message");
        const sidebarEligibility = document.getElementById("sidebar-eligibility");
        const donateBtnSidebar = document.getElementById("donate-btn-sidebar");

        if (timeLeft > 0) {
            // Not eligible - show countdown timer
            const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            statusMessage.innerText = `Next donation in: ${days}d ${hours}h ${minutes}m ${seconds}s`;
            statusMessage.style.background = "#f8d7da";
            statusMessage.style.color = "#721c24";
            
            // Update sidebar
            sidebarEligibility.innerText = "✘ Not Eligible";
            sidebarEligibility.style.background = "#dc3545";
            donateBtnSidebar.style.pointerEvents = "none";
            donateBtnSidebar.style.background = "#6c757d";
        } else {
            // Eligible - show eligibility message
            statusMessage.innerText = "✔ You are eligible to donate!";
            statusMessage.style.background = "#dff0d8";
            statusMessage.style.color = "#155724";
            
            // Update sidebar
            sidebarEligibility.innerText = "✔ Eligible";
            sidebarEligibility.style.background = "#28a745";
            donateBtnSidebar.style.pointerEvents = "auto";
            donateBtnSidebar.style.background = "#28a745";
        }
    }

    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.querySelector('.profile-toggle').classList.toggle('active');
    }

    // Update the countdown every second
    setInterval(updateCountdown, 1000);
    updateCountdown(); // Initial call
</script>

</body>
</html>