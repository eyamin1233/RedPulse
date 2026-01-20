<?php
session_start();

// Ensure user is logged in and is a blood bank
if (!isset($_SESSION['bloodbank_id']) || $_SESSION['role'] !== 'bloodbank') {
    header('Location: bb_profile.php');
    exit();
}

try {
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $bloodBankID = $_SESSION['bloodbank_id'];

    $stmt = $conn->prepare("SELECT * FROM bloodbank WHERE BloodBankID = :bloodbank_id");
    $stmt->bindParam(':bloodbank_id', $bloodBankID);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $bb = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "<div class='alert alert-danger'>Blood bank profile not found.</div>";
        exit();
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error fetching profile: " . $e->getMessage() . "</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Blood Donation Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
       .profile-card {
    background: #fff;
    border-radius: 15px;
    padding: 25px 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    color: #000;
}

.title {
    font-weight: bold;
    color: #b30000;
    text-transform: uppercase;
}

.list-group-item {
    background-color:rgb(250, 248, 248);
    color: #000;
    border: none;
    border-radius: 6px;
    margin-bottom: 6px;
    font-size: medium;
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
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-top: 100px;
            box-shadow: 0 4px 8px rgba(255, 0, 0, 0.2);
            color: #000;
        }
        .title {
            font-weight: bold;
            color: #b30000;
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
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="bb_profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Inventory.php">Inventory</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="events.php">Events</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="signout.php">Sign Out</a>
                </li>
            </ul>
        </div>
    </nav>

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

   <div class="main-wrapper d-flex flex-wrap justify-content-center align-items-start px-4 mt-5" style="margin-top: 130px; gap: 50px; width: 100%;">

    <!-- Left Column: Profile Info -->
    <div class="profile-card shadow" style="flex: 1; min-width: 500px; max-width: 380px; box-shadow: black; font-size: 20px;">
        <h4 class="title text-center mb-4">Blood Bank Profile</h4>
        <p><strong>Name:</strong>  <?= htmlspecialchars($bb['Name']) ?></p>
        <p><strong>Email:</strong>  <?= htmlspecialchars($bb['email']) ?></p>
        <p><strong>Location:</strong>  <?= htmlspecialchars($bb['Location']) ?></p>
        <p><strong>Contact Number:</strong>  <?= htmlspecialchars($bb['ContactNumber']) ?></p>
        <p><strong>Operating Hours:</strong>  <?= htmlspecialchars($bb['OperatingHours']) ?: 'Not specified' ?></p>

        <div class="text-center mt-3">
            <a href="bb_profile_edit.php" class="btn btn-outline-danger btn-sm px-4 rounded-pill font-weight-bold">Edit Profile</a>
        </div>
    </div>

    <!-- Right Column: Events and Inventory -->
    <div style="flex: 2; min-width: 350px; max-width: 800px;">
        <!-- Events -->
        <div class="profile-card shadow mb-4">
            <h5 class="title mb-3"> Current Events</h5>
            <ul class="list-group">
                <?php
                $events = $conn->query("SELECT Title, EventDate FROM events WHERE BloodBankID = $bloodBankID ORDER BY EventDate DESC LIMIT 3");
                if ($events->rowCount() > 0) {
                    foreach ($events as $event) {
                        echo "<li class='list-group-item'>" . htmlspecialchars($event['Title']) . " — <small>" . htmlspecialchars($event['EventDate']) . "</small></li>";
                    }
                } else {
                    echo "<li class='list-group-item text-muted'>No upcoming events.</li>";
                }
                ?>
            </ul>
        </div>

        <!-- Inventory -->
        <div class="profile-card shadow">
            <h5 class="title mb-3"> Available Blood in Inventory</h5>
            <div class="row">
                <?php
                $inventory = $conn->prepare("SELECT BloodType, Units FROM inventory WHERE BloodBankID = :bbid ORDER BY BloodType");
                $inventory->bindParam(':bbid', $bloodBankID);
                $inventory->execute();

                if ($inventory->rowCount() > 0) {
                    foreach ($inventory as $row) {
                        echo "<div class='col-sm-4 mb-2'>
                                <div class='bg-light p-2 rounded text-center shadow-sm'>
                                    <strong>" . htmlspecialchars($row['BloodType']) . "</strong><br>
                                    " . htmlspecialchars($row['Units']) . " units
                                </div>
                              </div>";
                    }
                } else {
                    echo "<div class='col-12 text-center text-muted'>No inventory data available.</div>";
                }
                ?>
            </div>
        </div>
    </div>

</div>

</body>
</html>
