<?php
session_start();

// Check blood bank login
if (!isset($_SESSION['bloodbank_id']) || $_SESSION['role'] !== 'bloodbank') {
    header('Location: bb_profile_edit.php');
    exit();
}

try {
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $bloodBankID = $_SESSION['bloodbank_id'];

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $location = $_POST['location'] ?? '';
        $contact = $_POST['contact'] ?? '';
        $email = $_POST['email'] ?? '';
        $hours = $_POST['hours'] ?? '';

        // Basic validation (you can extend it)
        if (!$name || !$location || !$contact || !$email) {
            $error = "Please fill in all required fields.";
        } else {
            $updateStmt = $conn->prepare("
                UPDATE bloodbank SET
                    Name = :name,
                    Location = :location,
                    ContactNumber = :contact,
                    email = :email,
                    OperatingHours = :hours
                WHERE BloodBankID = :id
            ");

            $updateStmt->execute([
                ':name' => $name,
                ':location' => $location,
                ':contact' => $contact,
                ':email' => $email,
                ':hours' => $hours,
                ':id' => $bloodBankID,
            ]);

            $success = "Profile updated successfully.";
        }
    }

    // Fetch current profile data
    $stmt = $conn->prepare("SELECT * FROM bloodbank WHERE BloodBankID = :id");
    $stmt->execute([':id' => $bloodBankID]);
    $bb = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bb) {
        die("Blood bank profile not found.");
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Blood Bank Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
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
        <a class="nav-link" href="bb_profile.php">Profile</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="bb_profile_edit.php">Edit Profile</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="inventory.php">Inventory</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="event.php">Event</a>
    </li>
</ul>

        </div>
    </nav>
<div class="container mt-5" style="max-width:600px; background:#fff; color:#000; padding:30px; border-radius:15px;">
    <h2 class="mb-4 text-center" style="color:#b30000;">Edit Blood Bank Profile</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!empty($success)): ?>
    <div class="alert alert-success" id="success-msg"><?= htmlspecialchars($success) ?></div>
    <script>
        setTimeout(function() {
            window.location.href = 'bb_profile.php';
        }, 3000); // 3 seconds
    </script>
<?php endif; ?>


    <form method="POST" action="">
        <div class="form-group mb-3">
            <label>Name <span style="color:red">*</span></label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($bb['Name']) ?>" />
        </div>

        <div class="form-group mb-3">
            <label>Location <span style="color:red">*</span></label>
            <input type="text" name="location" class="form-control" required value="<?= htmlspecialchars($bb['Location']) ?>" />
        </div>

        <div class="form-group mb-3">
            <label>Contact Number <span style="color:red">*</span></label>
            <input type="text" name="contact" class="form-control" required value="<?= htmlspecialchars($bb['ContactNumber']) ?>" />
        </div>

        <div class="form-group mb-3">
            <label>Email <span style="color:red">*</span></label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($bb['email']) ?>" />
        </div>

        <div class="form-group mb-4">
            <label>Operating Hours</label>
            <input type="text" name="hours" class="form-control" value="<?= htmlspecialchars($bb['OperatingHours']) ?>" />
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-danger px-5">Save Changes</button>
            <a href="bb_profile.php" class="btn btn-secondary ml-2">Cancel</a>
        </div>
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
