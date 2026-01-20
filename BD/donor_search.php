<?php 
session_start();

// Database Connection
$host = "localhost";
$dbname = "blooddonationmanagementsystem";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: donor_search.php");
    exit;
}

$loggedInUserId = $_SESSION['user_id'];

// Get filters

$searchBloodType = $_GET['bloodtype'] ?? '';
$searchLocation = $_GET['location'] ?? '';

$query = "SELECT name, contact, location, bloodtype, lastdonationdate,
    CASE 
        WHEN lastdonationdate IS NULL OR lastdonationdate <= DATE_SUB(CURDATE(), INTERVAL 90 DAY) 
        THEN 'Eligible' 
        ELSE 'Not Eligible' 
    END AS eligibility_status
FROM user
WHERE id != :logged_in_user_id";

$params = [':logged_in_user_id' => $loggedInUserId];

if (!empty($searchBloodType)) {
    $query .= " AND bloodtype = :bloodtype";
    $params[':bloodtype'] = $searchBloodType;
}

if (!empty($searchLocation)) {
    $query .= " AND location LIKE :location";
    $params[':location'] = '%' . $searchLocation . '%';
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$donors = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Donor Search</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: radial-gradient(circle, rgb(255, 99, 90) 0%, rgb(241, 33, 33) 100%);
            color: black;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 110px;
            padding: 20px;
            background: none;
        }
        h2 {
            color: white;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            animation: slideDown 1.5s ease-in-out;
        }
        .text-center {
            text-align: center;
        }
        .table {
            background: rgb(241, 168, 168);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            border-color: black solid 1px;
            margin-top: 20px;
            width: 100%;
        }
        .table th, .table td {
            color: black;
            text-align: center;
            vertical-align: middle;
            padding: 10px;
            border: 1px solid black;
        }
        .search-form {
    margin: 20px auto;
    max-width: 900px;
    background: rgb(252, 87, 87);
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 30px; /* spacing between elements */
}

.search-form label {
    font-weight: 500;
    font-size: 18px;
    color: white;
    margin-right: 5px;
}

.search-form select,
.search-form input[type="text"] {
    max-width: 180px;
    width: 100%;
    margin-right: 5px;
    padding: 5px 10px;
    border-radius: 4px;
    border: 1px solid #ccc;
}

.search-form button {
    background-color: rgb(0, 38, 255);
    border: none;
    padding: 7px 12px;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s, color 0.3s;
    color: white;
}

.search-form button:hover {
    background-color: rgb(0, 20, 200);
}


.table th {
    text-align: center;
    background-color: rgb(65, 58, 58);
    color: white;
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


        /* Eligibility status styling as buttons */
        .eligible-btn {
            color: #fff;
            background-color: #28a745;
            border-radius: 10px;
            font-weight: bold;
        }
        .not-eligible-btn {
            color: #fff;
            background-color: #dc3545;
            border-radius: 10px;
            font-weight: bold;
        }

        /* Centering and adjusting table width */
        .table-responsive {
            margin-top: 20px;
            max-width: 100%;
            overflow-x: auto;
        }

        /* Hover effect for table rows */
        .table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.2);
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
        <a class="nav-link" href="posts.php">Posts</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="bloodbanks_all.php">Blood Banks</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="donor_search.php">Donor Search</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="signout.php">Sign Out</a>
    </li>
</ul>

        </div>
    </nav>
    <div class="container">
        <h2 class="text-center mb-4">Donor Search</h2>
        
        <!-- Combined Search Form -->
<form class="search-form" method="GET">
    <label for="bloodtype">Blood Type:</label>
    <select name="bloodtype" id="bloodtype" class="form-control">
       <option value="">All</option>
            <option value="A+" <?= ($searchBloodType == 'A+') ? 'selected' : '' ?>>A+</option>
            <option value="A-" <?= ($searchBloodType == 'A-') ? 'selected' : '' ?>>A-</option>
            <option value="B+" <?= ($searchBloodType == 'B+') ? 'selected' : '' ?>>B+</option>
            <option value="B-" <?= ($searchBloodType == 'B-') ? 'selected' : '' ?>>B-</option>
            <option value="O+" <?= ($searchBloodType == 'O+') ? 'selected' : '' ?>>O+</option>
            <option value="O-" <?= ($searchBloodType == 'O-') ? 'selected' : '' ?>>O-</option>
            <option value="AB+" <?= ($searchBloodType == 'AB+') ? 'selected' : '' ?>>AB+</option>
            <option value="AB-" <?= ($searchBloodType == 'AB-') ? 'selected' : '' ?>>AB-</option>
    </select>

    <label for="location">Location:</label>
    <input type="text" name="location" id="location" class="form-control"
           placeholder="Enter location"
           value="<?= htmlspecialchars($_GET['location'] ?? '') ?>">

    <button type="submit" class="btn btn-primary">Search</button>
</form>




        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Blood Type</th>
                        <th>Eligibility</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donors as $donor): ?>
                        <tr>
                            <td><?= htmlspecialchars($donor['name']) ?></td>
                            <td><?= htmlspecialchars($donor['contact']) ?></td>
                            <td><?= htmlspecialchars($donor['location']) ?></td>
                            <td><?= htmlspecialchars($donor['bloodtype']) ?></td>
                            <td>
                                <button class="<?= $donor['eligibility_status'] == 'Eligible' ? 'eligible-btn' : 'not-eligible-btn' ?>" disabled>
                                    <?= htmlspecialchars($donor['eligibility_status']) ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($donors)): ?>
                        <tr><td colspan="5" class="text-center">No donors found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
