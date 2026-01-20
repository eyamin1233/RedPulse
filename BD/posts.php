<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: posts.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');

// Fetch user's blood type
$stmt = $conn->prepare("SELECT bloodtype FROM user WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
$user_bloodtype = $userData['bloodtype'];


// Check if user has made a request within the last 24 hours
$stmt = $conn->prepare("
    SELECT COUNT(*) as request_count, 
           MAX(created_at) as last_request_time 
    FROM bloodrequest 
    WHERE user_id = :user_id AND created_at >= NOW() - INTERVAL 1 DAY
");
$stmt->execute([':user_id' => $user_id]);
$request_check = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle new blood requests with 24-hour limit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blood_needed'])) {
    // Check if user has already made a request in the last 24 hours
    if ($request_check['request_count'] > 0) {
        // Set a session variable to show the error message
        $_SESSION['daily_limit_error'] = 'You have reached your daily limit to post';
        header('Location: posts.php');
        exit;
    }

    $blood_needed = $_POST['blood_needed'];
    $contact = $_POST['contact'];
    $location = $_POST['location'];
    $bloodtype = $_POST['bloodtype'];

   $urgent_datetime = !empty($_POST['urgent_datetime']) ? $_POST['urgent_datetime'] : null;

$stmt = $conn->prepare("INSERT INTO bloodrequest (user_id, blood_needed, contact, location, bloodtype, urgent_datetime, created_at) 
                        VALUES (:user_id, :blood_needed, :contact, :location, :bloodtype, :urgent_datetime, NOW())");
$stmt->execute([
    ':user_id' => $user_id,
    ':blood_needed' => $blood_needed,
    ':contact' => $contact,
    ':location' => $location,
    ':bloodtype' => $bloodtype,
    ':urgent_datetime' => $urgent_datetime
]);


    // Redirect to avoid duplicate submission
    header('Location: posts.php');
    exit;
}

// Update the marked_received status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_received'])) {
    $request_id = $_POST['request_id'];

    // Fetch blood request details
    $stmt = $conn->prepare("SELECT bloodtype, contact, location FROM bloodrequest WHERE id = :request_id AND user_id = :user_id");
    $stmt->execute([
        ':request_id' => $request_id,
        ':user_id' => $user_id
    ]);
    $request_details = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($request_details) {
        $bloodtype = $request_details['bloodtype'];
        $contact = $request_details['contact'];
        $location = $request_details['location'];

        // Insert into recipient table with additional fields
        $stmt = $conn->prepare("INSERT INTO recipient (user_id, bloodtype, contact, location, request_id, received_date) 
                                VALUES (:user_id, :bloodtype, :contact, :location, :request_id, NOW())");
        $stmt->execute([
            ':user_id' => $user_id,
            ':bloodtype' => $bloodtype,
            ':contact' => $contact,
            ':location' => $location,
            ':request_id' => $request_id
        ]);
    }

    // Update the blood request as marked received
    $stmt = $conn->prepare("UPDATE bloodrequest SET marked_received = 1 WHERE id = :request_id AND user_id = :user_id");
    $stmt->execute([
        ':request_id' => $request_id,
        ':user_id' => $user_id
    ]);

    header('Location: posts.php');
    exit;
}


// Delete requests that have been marked as received for over 24 hours
$stmt = $conn->prepare("
    DELETE FROM bloodrequest 
    WHERE marked_received = 1 
    AND created_at <= NOW() - INTERVAL 1 DAY
");
$stmt->execute();

$filter = '';
$params = [];

if (isset($_GET['filter_bloodtype']) && !empty($_GET['filter_bloodtype'])) {
    $filter = "WHERE br.bloodtype = :bloodtype";
    $params[':bloodtype'] = $_GET['filter_bloodtype'];
}

$stmt = $conn->prepare("
    SELECT br.*, u.name AS user_name 
    FROM bloodrequest br
    JOIN user u ON br.user_id = u.id
    $filter
");
$stmt->execute($params);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    body {
        background: radial-gradient(circle,rgb(255, 99, 90) 0%,rgb(241, 33, 33) 100%);
        padding-top: 50px; /* Adjust based on your navbar height */
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

        .navbar a {
            font-size: 20px;
        }


        .navbar-nav .nav-item .nav-link:hover {
            color:rgb(255, 255, 255) !important;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
        }

        .navbar-nav .nav-item{
            padding: 0px 10px;
        }


        .navbar-toggler {
            border-color: rgba(0, 0, 0, 0.5);
        }
        .btn-success[disabled] {
           background-color: #28a745 !important;
           opacity: 0.7;
           cursor: not-allowed;
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

        .card {
    border: 2px solid rgb(255, 255, 255); /* Red border */
    border-radius: 15px;
    background: linear-gradient(to bottom right, #fff, #ffe6e6); /* Light gradient */
}

.card h2 {
    font-weight: bold;
    color: #b30000;
    text-shadow: 1px 1px 2px #ff9999;
}

.btn-danger {
    background-color: #b30000;
    border: none;
    transition: 0.3s ease;
}

.btn-danger:hover {
    background-color:rgb(147, 223, 85);
    transform: scale(1.03);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    color: black;
}

.form-control:focus {
    border-color: #b30000;
    box-shadow: 0 0 8px rgba(179, 0, 0, 0.5);
}


@keyframes slideDown {
    0% {
        opacity: 0;
        transform: translateY(-30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.request-card {
    background: linear-gradient(135deg, #ffe6e6, #ffffff);
    border: 2px solid #ff4d4d;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
    animation: slideDown 0.8s ease;
}

.request-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 16px rgb(240, 235, 235);
}

.request-header {
    background: linear-gradient(90deg, #ff4d4d, #b30000);
    color: white;
    padding: 10px;
    border-radius: 12px 12px 0 0;
    font-weight: bold;
    text-align: center;
    font-size: 1.5rem;
    letter-spacing: 1px;
    position: relative;
}

.request-header::before {
    position: absolute;
    left: 15px;
    top: 10px;
    font-size: 1.8rem;
    animation: pulse 1.5s infinite alternate;
}

@keyframes pulse {
    0% { transform: scale(1); }
    100% { transform: scale(1.1); }
}

.request-info {
    padding: 15px;
    line-height: 1.6;
    color: #333;
}

.request-info strong {
    color: #b30000;
    font-weight: bold;
}

.status-badge {
    position: absolute;
    top: 5px;
    right: 7px;
    background: #4caf50;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: bold;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
}

.status-badge.pending {
    background: #ff9800;
}

.status-badge.received {
    background: #4caf50;
}

.filter-form {
    background: rgb(221, 36, 36);
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    display: flex;
    box-sizing: border-box;
    width: 500px;
    position: relative;
    left: 400px;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 25px;
}

.filter-form label {
    color: white;
    font-weight: bold;
    font-size: 18px;
}

.filter-form select {
    padding: 8px 15px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 16px;
    outline: none;
    transition: border-color 0.3s ease;
}

.filter-form select:focus {
    border-color: #b30000;
    box-shadow: 0 0 5px rgba(179, 0, 0, 0.5);
}



</style>

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
        <a class="nav-link active" href="posts.php">Posts</a>
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

    <!-- Main content -->
    <div class="container mt-4">
        <?php 
        // Display daily limit error message if exists
        if (isset($_SESSION['daily_limit_error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['daily_limit_error'] ?>
            </div>
            <?php unset($_SESSION['daily_limit_error']); // Clear the error message ?>
        <?php endif; ?>

        <div class="container mt-5">
    <!-- Post a Blood Request Card -->
    <div class="card shadow-lg p-4 mb-4 rounded" style="background-color: #fff; animation: slideDown 1s ease;">
        <h2 class="text-center text-danger mb-4"> Post a Blood Request </h2>

        <form method="POST" action="">
            <div class="form-group mb-3">
                <label for="blood_needed" class="fw-bold">Amount of Blood Needed (in units):</label>
                <input type="number" class="form-control" id="blood_needed" name="blood_needed" placeholder="Enter amount (e.g., 2)" required>
            </div>

            <div class="form-group mb-3">
                <label for="contact" class="fw-bold">Contact Number:</label>
                <input type="text" class="form-control" id="contact" name="contact" placeholder="Enter your contact number" required>
            </div>

            <div class="form-group mb-3">
                <label for="location" class="fw-bold">Location:</label>
                <input type="text" class="form-control" id="location" name="location" placeholder="Enter the location" required>
            </div>

            <div class="form-group mb-4">
                <label for="bloodtype" class="fw-bold">Blood Type:</label>
                <select class="form-control" id="bloodtype" name="bloodtype" required>
                    <option value="" disabled selected>Select Blood Type</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </div>

            <div class="form-group mb-4">
    <label for="urgent_datetime" class="fw-bold">Urgent Date and Time (Optional):</label>
    <input type="datetime-local" class="form-control" id="urgent_datetime" name="urgent_datetime">
</div>


            <button type="submit" class="btn btn-danger w-100 fw-bold">Post Request</button>
        </form>
    </div>
</div>

<div class="mb-4 text-center">
    <form method="GET" action="posts.php" class="filter-form" >
        <label for="filter_bloodtype" class="text-white fw-bold me-2">Filter Posts by Blood Type:</label>
        <select id="filter_bloodtype" name="filter_bloodtype" class="form-control w-auto" onchange="this.form.submit()">
            <option value="">All</option>
            <?php 
            $bloodTypes = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];
            foreach ($bloodTypes as $bt): 
                $selected = (isset($_GET['filter_bloodtype']) && $_GET['filter_bloodtype'] === $bt) ? 'selected' : '';
            ?>
                <option value="<?= $bt ?>" <?= $selected ?>><?= $bt ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>


<div class="container mt-4">
    <h2 class="text-center text-white mb-4"> Active Blood Requests </h2>

    <div class="row g-4">
        <?php foreach ($requests as $request): ?>
            <div class="col-md-6 col-lg-4">
                <div class="request-card">
                    <div class="request-header">
                        <?= htmlspecialchars($request['user_name']) ?>
                    </div>

                    <div class="request-info">
                        <p><strong>Blood Type:</strong> <?= htmlspecialchars($request['bloodtype']) ?></p>
                        <p><strong>Units Needed:</strong> <?= htmlspecialchars($request['blood_needed']) ?></p>
                        <p><strong>Contact:</strong> <?= htmlspecialchars($request['contact']) ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($request['location']) ?></p>
                        <p><strong>Requested On:</strong> <?= date('F j, Y, g:i a', strtotime($request['created_at'])) ?></p>
                        <p><strong>Urgent By:</strong> 
    <?= !empty($request['urgent_datetime']) ? date('F j, Y, g:i a', strtotime($request['urgent_datetime'])) : 'No urgent date given' ?>
</p>

                    </div>

                    <?php if ($request['marked_received']): ?>
                        <div class="status-badge received">✅ Received</div>
                    <?php else: ?>
                        <div class="status-badge pending">⏳ Pending</div>
                    <?php endif; ?>

                    <?php if ($request['user_id'] == $user_id): ?>
                        <form method="POST">
                            <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                            <button type="submit" name="mark_received" class="btn btn-success" <?= $request['marked_received'] ? 'disabled' : '' ?>>Mark as Received</button>
                            <a href="donate_list.php?request_id=<?= $request['id'] ?>" class="btn btn-info">View Donations</a>
                        </form>
                    <?php elseif (!$request['marked_received']): ?>
    <?php
        // Check if this user already submitted donation for this request
        $checkDonation = $conn->prepare("SELECT 1 FROM donation_answers WHERE user_id = :user_id AND request_id = :request_id");
        $checkDonation->execute([
            ':user_id' => $user_id,
            ':request_id' => $request['id']
        ]);
        $hasDonated = $checkDonation->fetchColumn();
    ?>
    <?php if (!$hasDonated): ?>
    <?php if ($user_bloodtype === $request['bloodtype']): ?>
        <a href="donate_form.php?request_id=<?= $request['id'] ?>" class="btn btn-primary">Donate</a>
    <?php else: ?>
        <button class="btn btn-warning" disabled>Blood type mismatch</button>
    <?php endif; ?>
<?php else: ?>
    <button class="btn btn-secondary" disabled>Already submitted</button>
<?php endif; ?>

<?php endif; ?>

                    
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

    


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
