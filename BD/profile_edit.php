<?php
session_start();

if (isset($_SESSION['user_email']) && isset($_SESSION['user_id']) && !empty($_SESSION['user_email']) && !empty($_SESSION['user_id'])) {
    $loginmail = $_SESSION['user_email'];
    $loginid = $_SESSION['user_id'];

    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem;', 'root', '');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get current user data
$stmt = $conn->prepare("SELECT * FROM user WHERE id = :id");
$stmt->execute([':id' => $loginid]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [];
    $params = [':id' => $loginid];

    if (!empty($_POST['name'])) {
        $fields[] = "name = :name";
        $params[':name'] = $_POST['name'];
    }

    if (!empty($_POST['contact'])) {
        $fields[] = "contact = :contact";
        $params[':contact'] = $_POST['contact'];
    }

    if (!empty($_POST['bloodtype'])) {
        $fields[] = "bloodtype = :bloodtype";
        $params[':bloodtype'] = $_POST['bloodtype'];
    }

    if (!empty($_POST['location'])) {
        $fields[] = "location = :location";
        $params[':location'] = $_POST['location'];
    }

    if (!empty($_POST['lastdonationdate'])) {
        $fields[] = "lastdonationdate = :lastdonationdate";
        $params[':lastdonationdate'] = $_POST['lastdonationdate'];
    }

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = __DIR__ . "/photos/";
        $unique_name = uniqid() . "_" . basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . $unique_name;

        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $fields[] = "profile_picture = :profile_picture";
                $params[':profile_picture'] = "photos/" . $unique_name;
            } else {
                echo "<script>alert('Error uploading profile picture.');</script>";
            }
        } else {
            echo "<script>alert('File is not a valid image.');</script>";
        }
    }

    if (!empty($fields)) {
        $update_query = "UPDATE user SET " . implode(", ", $fields) . " WHERE id = :id";
        $stmt = $conn->prepare($update_query);
        $stmt->execute($params);

        // Optional: update session values if needed
        echo "<script>
                alert('Profile updated successfully!');
                location.assign('profile.php');
              </script>";
    } else {
        echo "<script>alert('No changes submitted.');</script>";
    }


        $update_query .= " WHERE id = :id";

        try {
            $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem;', 'root', '');
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare($update_query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':contact', $contact);
            $stmt->bindParam(':bloodtype', $bloodtype);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':lastdonationdate', $lastdonationdate);
            $stmt->bindParam(':id', $loginid);

            if ($profile_picture_path) {
                $stmt->bindParam(':profile_picture', $profile_picture_path);
            }

            $stmt->execute();

            $_SESSION['name'] = $name;
            $_SESSION['contact'] = $contact;
            $_SESSION['bloodtype'] = $bloodtype;
            $_SESSION['location'] = $location;
            $_SESSION['lastdonationdate'] = $lastdonationdate;
            if ($profile_picture_path) {
                $_SESSION['profile_picture'] = $profile_picture_path;
            }

            echo "<script>
                    alert('Profile updated successfully!');
                    location.assign('profile.php');
                  </script>";
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }
} else {
    echo "<script>location.assign('signin.php');</script>";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg,rgb(255, 65, 65),rgb(248, 25, 25));
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .container {
            background-color: #fff;
            border-radius: 15px;
            padding: 30px;
            max-width: 700px;
            margin: 105px auto;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color:rgb(63, 53, 55);
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 16px;
            font-weight: 500;
            color: #444;
        }

        .form-control {
            font-size: 16px;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid rgb(199, 29, 29);
        }

        .form-control:focus {
            border-color: #FF4B2B;
            box-shadow: 0 0 10px rgba(255, 75, 43, 0.5);
        }

        .form-group {
            margin-bottom: 20px;
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

        .btn-primary {
            background-color:rgb(253, 50, 50);
            border: none;
            padding: 12px 20px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: lightgreen;
            color: black;
            transform: translateY(-2px);
            box-shadow: 0px 8px 15px rgba(255, 75, 43, 0.4);
        }

        .image-preview {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            h2 {
                font-size: 28px;
            }

            .btn-primary {
                font-size: 16px;
            }
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
        <a class="nav-link active" href="profile.php">Profile Edit</a>
    </li>
    
</ul>

        </div>
    </nav>

    <div class="container">
        
        <form action="profile_edit.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($currentUser['name']) ?>" class="form-control" id="name" placeholder="Enter your full name">
            </div>
            <div class="form-group">
                <label for="contact">Contact Number:</label>
                <input type="text" name="contact" value="<?= htmlspecialchars($currentUser['contact']) ?>" class="form-control" id="contact" placeholder="Enter your contact number">
            </div>
            <div class="form-group">
              <label for="bloodtype">Blood Type:</label>
               <select name="bloodtype" class="form-control" id="bloodtype">
    <option value="" disabled>Select your blood type</option>
    <?php
    $bloodTypes = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];
    foreach ($bloodTypes as $type) {
        $selected = ($currentUser['bloodtype'] === $type) ? 'selected' : '';
        echo "<option value=\"$type\" $selected>$type</option>";
    }
    ?>
</select>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" name="location" value="<?= htmlspecialchars($currentUser['location']) ?>" class="form-control" id="location" placeholder="Enter your location">
            </div>
            <div class="form-group">
                <label for="lastdonationdate">Last Donation Date:</label>
                <input type="date" name="lastdonationdate" value="<?= $currentUser['lastdonationdate'] ?>" class="form-control" id="lastdonationdate">
            </div>
            <div class="form-group">
                <label for="profile_picture">Upload Profile Picture:</label>
                <input type="file" class="form-control" id="profile_picture" name="profile_picture" onchange="previewImage(event)">
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const imgPreview = document.getElementById('imagePreview');
            const file = event.target.files[0];
            if (file) {
                imgPreview.src = URL.createObjectURL(file);
            }
        }
    </script>
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

