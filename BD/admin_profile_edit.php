<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: signin.php");
    exit;
}

$success = '';
$error = '';

try {
    $conn = new PDO(
        'mysql:host=localhost;dbname=blooddonationmanagementsystem',
        'root',
        ''
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $adminId = $_SESSION['admin_id'];

    // Fetch current admin data
    $stmt = $conn->prepare("SELECT UserName, Email FROM admin WHERE AdminID = ?");
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        die("Admin not found.");
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        if (empty($username) || empty($email)) {
            $error = "All fields are required.";
        } else {
            $update = $conn->prepare(
                "UPDATE admin SET UserName = ?, Email = ? WHERE AdminID = ?"
            );
            $update->execute([$username, $email, $adminId]);

            $success = "Profile updated successfully! Redirecting...";
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
    <title>Edit Admin Profile - RedPulse</title>
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
            color: rgb(253, 253, 253) !important;
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
            color: rgb(255, 255, 255) !important;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
        }

        .edit-card {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
        }

        .edit-card h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #dc3545;
        }

        label {
            font-weight: bold;
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
                    <a class="nav-link  active" href="admin_profile_edit.php">Profile Edit</a>
                </li>
            </ul>
        </div>
    </nav>

<div class="edit-card">
    <h3>Edit Admin Profile</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input
                type="text"
                name="username"
                class="form-control"
                value="<?= htmlspecialchars($admin['UserName']) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label>Email</label>
            <input
                type="email"
                name="email"
                class="form-control"
                value="<?= htmlspecialchars($admin['Email']) ?>"
                required
            >
        </div>

        <button type="submit" class="btn btn-danger w-100">
            Save Changes
        </button>

        <a href="admin_profile.php" class="btn btn-secondary w-100 mt-2">
            Cancel
        </a>
    </form>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success text-center"
        style="position: fixed; top: 80px; left: 50%; transform: translateX(-50%); z-index: 9999; width: auto; max-width: 400px;">
        <?= htmlspecialchars($success) ?>
    </div>

    <script>
        setTimeout(() => {
            window.location.href = "admin_profile.php";
        }, 2000);
    </script>
<?php endif; ?>


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
