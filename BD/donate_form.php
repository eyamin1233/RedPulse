<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: posts.php');
    exit;
}

if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');

    // Fetch blood request details
    $stmt = $conn->prepare("SELECT * FROM bloodrequest WHERE id = :request_id");
    $stmt->execute([':request_id' => $request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    $user_id = $_SESSION['user_id'];
$checkDonation = $conn->prepare("SELECT 1 FROM donation_answers WHERE user_id = :user_id AND request_id = :request_id");
$checkDonation->execute([':user_id' => $user_id, ':request_id' => $request_id]);

if ($checkDonation->fetchColumn()) {
    echo "<script>alert('You have already submitted your donation information for this request.'); location.href='posts.php';</script>";
    exit;
}


    if ($request) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user_id = $_SESSION['user_id'];
            $health_issue = $_POST['health_issue'];
            $medical_conditions = $_POST['medical_conditions'];
            $medications = $_POST['medications'];
            $hospital_visits = $_POST['hospital_visits'];
            $fatigue = $_POST['fatigue'];

            // Insert answers into database
            $stmt = $conn->prepare("INSERT INTO donation_answers (user_id, request_id, health_issue, medical_conditions, medications, hospital_visits, fatigue) 
                                    VALUES (:user_id, :request_id, :health_issue, :medical_conditions, :medications, :hospital_visits, :fatigue)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':request_id' => $request_id,
                ':health_issue' => $health_issue,
                ':medical_conditions' => $medical_conditions,
                ':medications' => $medications,
                ':hospital_visits' => $hospital_visits,
                ':fatigue' => $fatigue
            ]);

            echo "<script>alert('Your submittion is recorded!'); location.assign('posts.php');</script>";      
            
            // Fetch recipient details (who created the post)
$recipientStmt = $conn->prepare("SELECT u.name, u.bloodtype, b.location 
                                 FROM user u 
                                 JOIN bloodrequest b ON b.user_id = u.id 
                                 WHERE b.id = :rid");
$recipientStmt->execute([':rid' => $request_id]);
$recipientInfo = $recipientStmt->fetch(PDO::FETCH_ASSOC);

// Build the notification message
if ($recipientInfo) {
    $notifMsg = "You have submitted your health info for " . $recipientInfo['name'] . 
                "'s request (" . $recipientInfo['bloodtype'] . " blood in " . $recipientInfo['location'] . ").";

    $notifInsert = $conn->prepare("INSERT INTO notifications (user_id, message, created_at) 
                                   VALUES (:user_id, :message, NOW())");
    $notifInsert->execute([
        ':user_id' => $user_id,
        ':message' => $notifMsg
    ]);
}

            exit;
        }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate Blood</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: radial-gradient(circle,rgb(255, 99, 90) 0%,rgb(219, 31, 31) 100%);
            font-family: 'Arial', sans-serif;
            color: black;
            font-size: 18px;
            padding: 60px;
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
        .form-group {
            margin-top: 20px;
            color: black;
            font-size: 20px;
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid #ccc;
            max-width: 620px;
            padding: 10px;
            font-size: 16px;
        }
        .btn-primary {
            background-color: purple;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 16px;
        }
        .btn-primary:hover {
            background-color: black;
        }
        h2 {
            color: black;
            margin-bottom: 20px;
            font-size: 40px;
        }
        h3 {
            color: black;
            margin-bottom: 10px;
            font-size: 25px;
        }
        .col-md-4{
            position: relative;
            top: 60px;
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
        <a class="nav-link active" href="">Health Issues</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="posts.php">Posts</a>
    </li>
 </ul>
      </div>
    </nav>

<div class="container mt-4">
    <div class="row">
        <!-- Main Form Column -->
        <div class="col-md-8">
            <h2>Donate Blood for Request</h2>
            <h3>Blood Needed: <?= htmlspecialchars($request['blood_needed']) ?> bags</h3>
            <h3>Location: <?= htmlspecialchars($request['location']) ?></h3>

            <form method="POST">
                <input type="hidden" name="request_id" value="<?= htmlspecialchars($request_id) ?>">

                <div class="form-group">
                    <label>Have you experienced any fever, cough, or difficulty breathing?</label>
                    <select class="form-control" name="health_issue" required>
                        <option value="">Select</option>
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Do you have any existing medical conditions?</label>
                    <select class="form-control" name="medical_conditions" required>
                        <option value="">Select</option>
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Have you taken any prescription medications in the last 24 hours?</label>
                    <select class="form-control" name="medications" required>
                        <option value="">Select</option>
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Have you had any hospital visits in the past six months?</label>
                    <select class="form-control" name="hospital_visits" required>
                        <option value="">Select</option>
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Do you feel fatigued most of the time?</label>
                    <select class="form-control" name="fatigue" required>
                        <option value="">Select</option>
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Submit</button>
            </form>
        </div>

        <!-- Right Info Box Column -->
        <div class="col-md-4">
            <div class="card shadow p-3 mb-5 bg-light rounded">
                <h2 class="text-center text-danger fw-bold">Important Information</h2>
                <p class="text-center">Please read the following guidelines before donating blood:</p>
                <h4 class="text-danger fw-bold">Who Can Donate Blood?</h4>
                <ul>
                    <li>Age between 18–65 years</li>
                    <li>Weight over 50kg</li>
                    <li>Good general health</li>
                    <li>No fever/cough in the past 14 days</li>
                    <li>At least 3 months since last donation</li>
                </ul>
                <h4 class="text-danger fw-bold mt-4">Who Cannot Donate Blood?</h4>
                <ul>
                    <li>Recent illness or infection</li>
                    <li>Chronic diseases like diabetes (if not controlled)</li>
                    <li>History of jaundice, hepatitis, or HIV</li>
                    <li>Recent tattoos or piercings (last 6 months)</li>
                    <li>Pregnancy or breastfeeding</li>
                    <li>On antibiotics or strong medications</li>
                </ul>
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

</body>
</html>

<?php
    } else {
        echo "<p>Invalid request ID.</p>";
    }
} else {
    echo "<p>No request ID provided.</p>";
}
?>
