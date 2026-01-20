<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: bloodbanks_all.php");
    exit();
}
// rest of your code


try {
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all blood banks
    $stmt = $conn->query("SELECT * FROM bloodbank ORDER BY BloodBankID ASC");
    $bloodbanks = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Blood Banks - RedPulse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
       body {
            background: radial-gradient(circle,rgb(255, 99, 90) 0%,rgb(241, 33, 33) 100%);
            font-family: 'Arial', sans-serif;
            color: #fff;
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

        .section-title {
            font-size: 2rem;
            font-weight: bold;
            position: relative;
            top: 80px;
            margin-bottom: 120px;
            color: white;
            text-shadow: 1px 1px 2px #999;
        }

        .bank-card {
            background: #ffffff;
            color: black;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 30px;
            transition: transform 0.2s ease-in-out;
        }
        

.reviews-btn {
    position: relative;
    bottom: 250px;
    margin-left: 1100px;
    padding: 10px 18px;
    background-color: #b30000;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    text-decoration: none;
    cursor: pointer;
    white-space: nowrap;
    transition: background-color 0.3s ease;
}

.reviews-btn:hover {
    background-color: #800000;
    color: white;
}

        .bank-card:hover {
            transform: scale(1.02);
        }

        .bank-title {
            font-size: 2rem;
            font-weight: bold;
            color: #b30000;
        }

        .bank-info i {
            color: #b30000;
            margin-right: 6px;
        }

        .event-list {
            padding-left: 1rem;
        }

        .event-list li {
            margin-bottom: 6px;
            font-size: 0.95rem;
        }

        .blood-table {
            margin-top: 10px;
        }

        .blood-table th, .blood-table td {
            padding: 6px;
            font-size: 0.9rem;
            text-align: center;
        }

        .blood-table th {
            background-color: #ffcccc;
            color: #990000;
        }

        .text-muted {
            font-style: italic;
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
        <a class="nav-link" href="profile.php">Profile</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="posts.php">Posts</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="bloodbanks_all.php">Blood Bank</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="donor_search.php">Donor Search</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="signout.php">Sign Out</a>
    </li>
</ul>

        </div>
    </nav>

<div class="container mt-4">
    <div class="section-title text-center">Available Blood Banks</div>

    <?php if (count($bloodbanks) > 0): ?>
        <?php foreach ($bloodbanks as $bank): ?>
            <div class="bank-card">
                <div class="bank-title"><?= htmlspecialchars($bank['Name']) ?></div>
                <div class="bank-info mt-2 mb-3">
                    <p class="mb-1"><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($bank['Location']) ?></p>
                    <p class="mb-1"><i class="bi bi-telephone-fill"></i>
                        <a href="tel:<?= htmlspecialchars($bank['ContactNumber']) ?>" style="text-decoration:none;color:inherit;">
                            <?= htmlspecialchars($bank['ContactNumber']) ?>
                        </a>
                    </p>
                    <p class="mb-0"><i class="bi bi-clock-fill"></i> <?= htmlspecialchars($bank['OperatingHours']) ?></p>
                </div>

                <!-- Events -->
                <h6 class="mt-3"><i class="bi bi-calendar-event"></i> Upcoming Events:</h6>
                <ul class="event-list">
                    <?php
                        $eventStmt = $conn->prepare("SELECT Title, EventDate, Location FROM events WHERE BloodBankID = ? ORDER BY EventDate DESC LIMIT 3");
                        $eventStmt->execute([$bank['BloodBankID']]);
                        $events = $eventStmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <?php if (count($events) > 0): ?>
                        <?php foreach ($events as $event): ?>
                            <li>
                                <strong><?= htmlspecialchars($event['Title']) ?></strong> –
                                <?= htmlspecialchars($event['EventDate']) ?> at <?= htmlspecialchars($event['Location']) ?>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="text-muted">No upcoming events.</li>
                    <?php endif; ?>
                </ul>

                <!-- Blood Units -->
                <h6 class="mt-4"><i class="bi bi-droplet-fill"></i> Available Blood Units:</h6>
                <?php
                    $unitStmt = $conn->prepare("SELECT BloodType, Units FROM inventory WHERE BloodBankID = ?");
                    $unitStmt->execute([$bank['BloodBankID']]);
                    $rawUnits = $unitStmt->fetchAll(PDO::FETCH_ASSOC);

                    $units = [];
                    foreach ($rawUnits as $row) {
                        $units[$row['BloodType']] = $row['Units'];
                    }
                ?>
                <?php if ($units): ?>
                    <table class="table table-bordered blood-table table-sm">
                        <thead>
                            <tr>
                                <th>A+</th><th>A-</th><th>B+</th><th>B-</th>
                                <th>O+</th><th>O-</th><th>AB+</th><th>AB-</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= $units['A+'] ?? 0 ?></td>
                                <td><?= $units['A-'] ?? 0 ?></td>
                                <td><?= $units['B+'] ?? 0 ?></td>
                                <td><?= $units['B-'] ?? 0 ?></td>
                                <td><?= $units['O+'] ?? 0 ?></td>
                                <td><?= $units['O-'] ?? 0 ?></td>
                                <td><?= $units['AB+'] ?? 0 ?></td>
                                <td><?= $units['AB-'] ?? 0 ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No unit data available.</p>
                <?php endif; ?>
                 <form action="reviews.php" method="POST" style="display:inline;">
    <input type="hidden" name="bname" value="<?= htmlspecialchars($bank['Name']) ?>">
    <button type="submit" class="reviews-btn" title="View Reviews">Reviews</button>
</form>

            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-warning text-center">No blood banks found.</div>
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
