<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>location.assign('profile.php');</script>";
    exit();
}

$userId = $_SESSION['user_id'];
$donorName = $_SESSION['user_name'];
$donorContact = $_SESSION['contact'];
$donorBloodType = $_SESSION['bloodtype'];
$donorLocation = $_SESSION['location'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $recipient_name     = $_POST['recipient_name'];
    $recipient_contact  = $_POST['recipient_contact'];
    $donated_blood_type = $_POST['donated_blood_type'];
    $donation_location  = $_POST['donation_location'];
    $donation_date      = $_POST['donation_date'];

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=blooddonationmanagementsystem", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();

        // 1️⃣ Check if donor already exists
        $check = $pdo->prepare("SELECT id FROM donors WHERE user_id = ?");
        $check->execute([$userId]);


        if ($check->rowCount() > 0) {
            $donor_id = $check->fetchColumn();

            // Update donor last donation date
            $updateDonor = $pdo->prepare("UPDATE donors SET lastdonationdate = ? WHERE id = ?");
            $updateDonor->execute([$donation_date, $donor_id]);
        } else {
            // Insert donor once only
            $stmt2 = $pdo->prepare("
                INSERT INTO donors 
                (user_id, name, location, bloodtype, lastdonationdate, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt2->execute([
                $userId,
                $donorName,
                $donorLocation,
                $donorBloodType,
                $donation_date
            ]);
            $donor_id = $pdo->lastInsertId();
        }

        // 2️⃣ Insert recipient
$stmt3 = $pdo->prepare("
    INSERT INTO recipient 
    (user_id, recipient_name, bloodtype, contact, location, received_date) 
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt3->execute([
    $userId,
    $recipient_name,
    $donated_blood_type,
    $recipient_contact,
    $donation_location,
    $donation_date
]);

$recipient_id = $pdo->lastInsertId();


        // 3️⃣ Insert donation record
        $stmt5 = $pdo->prepare("
            INSERT INTO donations 
            (donor_id, recipient_id, donated_at)
            VALUES (?, ?, ?)
        ");
        $stmt5->execute([
            $donor_id,
            $recipient_id,
            $donation_date
        ]);

        // 4️⃣ Update user last donation date
        $stmt4 = $pdo->prepare("
            UPDATE user 
            SET lastdonationdate = ? 
            WHERE id = ?
        ");
        $stmt4->execute([$donation_date, $userId]);

        $pdo->commit();

        echo "<script>alert('Donation submitted successfully!'); location.assign('donations_list.php');</script>";
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Donate Blood</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: radial-gradient(circle,rgb(255, 99, 90) 0%,rgb(241, 33, 33) 100%);
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 180px;
            background: rgb(248, 228, 228);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
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
        h2 {
            position: relative;
            bottom: 80px;
            font-size: 35px;            
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .form-group {
            margin-bottom: 15px;
            position: relative;
            bottom: 30px;
        }
        label {
            font-weight: normal;
            color: #000;
        }
        .btn-primary {
            background-color: rgb(253, 51, 51);
            border: none;
            font-size: 1.1rem;
            padding: 10px 20px;
            border-radius: 25px;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: lightgreen;
            transform: translateY(-2px);
            color: black;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid rgb(199, 29, 29);
            padding: 10px;
            font-size: 1rem;
        }
        .form-control:focus {
            box-shadow: 0 0 5px rgba(248, 41, 41, 0.52);
            border-color: rgb(253, 51, 51);
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
        <a class="nav-link" href="profile.php">Profile</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="donations.php">Form</a>
    </li>
    
</ul>

        </div>
    </nav>
    <div class="container">
        <h2 class="text-center">Blood Donation Form</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="recipient_name">Recipient Name:</label>
                <input type="text" class="form-control" id="recipient_name" name="recipient_name" placeholder="Enter Name" required>
            </div>

            <div class="form-group">
                <label for="recipient_contact">Recipient Contact:</label>
                <input type="text" class="form-control" id="recipient_contact" name="recipient_contact" placeholder="Enter Contact Number" required>
            </div>

            <div class="form-group">
                <label for="donated_blood_type">Donated Blood Type:</label>
                <select class="form-control" id="donated_blood_type" name="donated_blood_type" required>
                    <option value="" disabled selected>Select Blood Type</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>
            </div>

            <div class="form-group">
                <label for="donation_location">Donation Location:</label>
                <input type="text" class="form-control" id="donation_location" name="donation_location" placeholder="Enter Location" required>
            </div>

            <div class="form-group">
                <label for="donation_date">Donation Date:</label>
                <input type="date" class="form-control" id="donation_date" name="donation_date" required>
            </div>

            <button type="submit" class="btn btn-primary">Submit Donation</button>
        </form>
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
