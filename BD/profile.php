<?php
session_start();

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

} catch (PDOException $e) {
    $totalDonations = 0;
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

?>
<!DOCTYPE html>
<html lang="en">
<head>


    <title>User Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* General body styling */
        body {
            background: radial-gradient(circle,rgb(255, 99, 90) 0%,rgb(241, 33, 33) 100%);
            color: #fff;
            font-family: Arial, sans-serif;
        }

        /* Profile container */
        .profile-container {
            /*background-color: rgba(255, 255, 255, 0.1);*/
            padding: 9px;
            border-radius: 15px;
            /*box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);*/
            margin-top: 80px;
            max-width: 1200px;
            /*border: 3px solid #f2f2f2; /* Highlight border */
        }

        /* Profile picture styling */
        .profile-picture {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            margin: 20px auto;
            display: block;
            position: relative;
            top: 10px;
            right: 525px;
        }

        .profile-header {
            text-align: left;
            margin-bottom: 20px;
        }

        .profile-header h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color:rgb(17, 17, 17); /* Highlight profile text color */
            position: relative;
            bottom: 1px;
            right: 30px;
        }

        .profile-details p {
            font-family: josefin sans;
            font-size: 1.7rem;
            margin: 3px 0;
            text-align: left;
            position: relative;
            right: 30px;
            bottom: 10px;
            color:rgb(0, 0, 0);
        }

        .profile-details p strong {
            color:rgb(0, 0, 0);
            font-family: Arial;
        }

        .btn-primary {
            background-color: rgb(255, 135, 135);
            border: 1px solid rgb(0, 0, 0);
            color: black;
            font-size: 1.3rem;
            font-weight: bold;
            padding: 10px 80px;
            border-radius: 25px;
            margin-left: 800px;
            position: relative;
            bottom: 100px;
            right: 850px;
        }

        .btn-primary:hover {
            background-color: lightgreen;
            color: black;
        }

        .btn-success {
            background-color: rgb(236, 202, 176);
            border: 1px solid rgb(0, 0, 0);
            font-size: 1.3rem;
            color: black;
            font-weight: bold;
            padding: 10px 50px;
            border-radius: 25px;
            margin-left: 800px;
            position: relative;
            bottom: 159px;
            right: 550px;
        }

        .btn-success:hover {
            background-color: lightgreen;
            color: black;
        }
        

        /* Eligibility Box */
        .eligibility-box {
            background: <?php echo $isEligible ? '#dff0d8' : '#f8d7da'; ?>;
            color: <?php echo $isEligible ? '#3c763d' : '#721c24'; ?>;
            padding: 15px;
            border-radius: 10px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
            margin-left: 650px;
            font-size: 1.2rem;
            position: relative;
            top: 100px;
            right: 240px;
        }

        .eligibility-btn {
            background: <?php echo $isEligible ? '#4CAF50' : '#dc3545'; ?>;
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: default;
        }

        .donated-btn {
            margin-left: 15px;
            padding: 8px 15px;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            background: <?php echo $isEligible ? '#28a745' : '#6c757d'; ?>;
            color: white;
            pointer-events: <?php echo $isEligible ? 'auto' : 'none'; ?>;
        }

        .donated-btn:hover {
            background: <?php echo $isEligible ? 'white' : '#6c757d'; ?>;
            color: black;
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

        .container-box {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin: 20px;
        }
        .card1 {
            background: #f8d7da;
            color: #444;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 30px;
            width: 250px;
            text-align: center;
            position: relative;
            bottom: 580px;
            left: 350px;
        }

        .card2 {
            background: #f8d7da;
            color: #444;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 25px;
            width: 250px;
            text-align: center;
            position: relative;
            bottom: 580px;
            left: 350px;
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

    <!-- Profile Content -->
    <div class="container">
        <div class="profile-box">
        <div class="profile-container mx-auto">
            <div class="profile-header">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-picture">
                <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
            </div>
            <div class="profile-details">
                <p><strong>Contact:</strong> <?php echo htmlspecialchars($contact); ?></p>
                <p><strong>Blood Type:</strong> <?php echo htmlspecialchars($bloodtype); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($location); ?></p>
                <p><strong>Last Donation Date:</strong> <?php echo htmlspecialchars($lastdonationdate); ?></p>
                <p><strong>Next Eligible Donation Date:</strong> <?php echo htmlspecialchars($nextDonationDate); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            </div>
                <!-- Real-Time Countdown Timer -->
<p id="countdown" style="font-size: 1.4rem; font-weight: bold; color:rgb(63, 50, 50); position: relative; top: 240px; left: 70px; text-align: center"></p>

<!-- Eligibility Box -->
<div class="eligibility-box" id="eligibility-box">
    <span>Eligibility Status:</span>
    <button class="eligibility-btn" id="eligibility-btn" disabled>
        <?php echo $isEligible ? "✔ Eligible" : "✘ Not Eligible"; ?>
    </button>
    <!-- Donate Button -->
    <a href="donations.php" class="donated-btn" id="donate-btn">
        Donate
    </a>
</div>

<script>
    function updateCountdown() {
        const nextDonationDate = new Date("<?php echo $nextDonationDate; ?>").getTime();
        const now = new Date().getTime();
        const timeLeft = nextDonationDate - now;

        const countdownElement = document.getElementById("countdown");
        const eligibilityBox = document.getElementById("eligibility-box");
        const eligibilityBtn = document.getElementById("eligibility-btn");
        const donateBtn = document.getElementById("donate-btn");

        if (timeLeft > 0) {
            const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            countdownElement.innerText = `Next donation in: ${days}d ${hours}h ${minutes}m ${seconds}s`;
            eligibilityBtn.innerText = "✘ Not Eligible";
            donateBtn.style.pointerEvents = "none"; // Disable donate button
            donateBtn.style.background = "#6c757d";
        } else {
            countdownElement.innerText = "Now you are eligible to donate!";
            eligibilityBox.style.background = "#dff0d8";  // Green background
            eligibilityBtn.innerText = "✔ Eligible";
            eligibilityBtn.style.background = "#4CAF50";
            donateBtn.style.pointerEvents = "auto"; // Enable donate button
            donateBtn.style.background = "#28a745";
        }
    }

    // Update the countdown every second
    setInterval(updateCountdown, 1000);
    updateCountdown(); // Initial call
</script>

            <div class="text-center mt-4">
                <a href="profile_edit.php" class="btn btn-primary mb-2">Edit Profile</a>
                <a href="donations_list.php" class="btn btn-success">Donations History</a>
            </div>
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



</body>
</html>
