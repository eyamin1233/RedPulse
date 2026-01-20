<?php
session_start();

// Ensure admin is logged in
if(isset($_SESSION['username']) && isset($_SESSION['admin_id'])) {
    $admin_name = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        body {
            background: radial-gradient(circle,rgb(255, 99, 90) 0%,rgb(184, 25, 25) 100%);
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .top {
            text-align: center;
            color: white;
            margin-top: 30px;
            font-size: 36px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            animation: slideDown 1s ease-in-out;
        }
        .navbar {
            background-color: rgba(0, 0, 0, 0.8) !important;
            padding: 10px 20px;
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

        .navbar-nav .nav-item {
            margin-right: 20px; /* reduce spacing */
            position: relative;
            left: 1130px;
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

        table {
          background-color: #fff;
          color: #000;
          border-radius: 10px;
          box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
          width: 80%;
          margin-top: 40px;
          font-size: 16px;
        }


        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid black;
        }
        th {
            background-color: rgb(34, 32, 32);
            color: white;
            font-weight: medium;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        tbody tr {
            background-color:rgb(204, 203, 203);
        }
        tbody tr:nth-child(even) {
            background-color:rgb(160, 160, 160);
        }
        tbody tr:hover {
            background-color: #d3d3d3;
        }
        .action-btns {
            display: flex;
            justify-content: space-evenly;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-inverse ">
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
        <ul class="nav navbar-nav">
    <li class="nav-item"><a class="nav-link" href="http://localhost/bd/admin_profile.php">Profile</a></li>
    <li class="nav-item"><a class="nav-link active" href="http://localhost/bd/dashboard.php">Dashboard</a></li>
    <li class="nav-item"><a class="nav-link" href="http://localhost/bd/signout.php">Sign Out</a></li>
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

    <h3 class="top">Welcome, <?php echo htmlspecialchars($admin_name); ?> (Admin)</h3>

    <br />

    <?php 
    try {
        // Database connection
        $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem;', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetching blood bank information
        $selectquery = "SELECT * FROM bloodbank ORDER BY RAND()";
        $test = $conn->query($selectquery);
        $returnobj = $test->fetchAll();

        if ($test->rowCount() > 0) {
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th>Blood Bank Name</th><th>Location</th><th>Comments</th><th>Actions</th></tr></thead>';
            echo '<tbody>';

            foreach ($returnobj as $data) {
                $bbid = $data['BloodBankID'];
                $bloodbank_name = $data['Name'];
                $loc = $data['Location'];

                // Fetching reviews for the blood bank
                $selectquery1 = "SELECT u.name AS reviewer_name, rb.review AS review_text, rb.date AS review_date
                                FROM review_bloodbank AS rb
                                LEFT JOIN user AS u ON rb.userid = u.id
                                WHERE rb.BloodBankID = :bbid
                                ORDER BY rb.date DESC";
                $stmt1 = $conn->prepare($selectquery1);
                $stmt1->bindParam(':bbid', $bbid);
                $stmt1->execute();
                $comments = $stmt1->fetchAll();

                echo '<tr>';
                echo "<td>$bloodbank_name</td>";
                echo "<td>$loc</td>";
                echo "<td>";
                foreach ($comments as $comment) {
                    echo "<p><strong>{$comment['reviewer_name']}</strong> ({$comment['review_date']}): {$comment['review_text']}</p>";
                }
                echo "</td>";
                echo "<td>
                        <div class='action-btns'>
                            <button class='btn btn-danger' onclick='confirmDelete($bbid)'> Delete </button>
                            <button class='btn btn-warning' onclick='openUpdateForm($bbid, \"$bloodbank_name\", \"$loc\")'> Update </button>
                        </div>
                    </td>";
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        } else {
            echo "<p>No blood banks found.</p>";
        }
    } catch (PDOException $ex) {
        echo "Error: " . $ex->getMessage();
    }
    ?>

<div id="updateModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Update Blood Bank Information</h4>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="update_bank.php" method="POST">
                        <input type="hidden" id="bloodBankID" name="bloodBankID">
                        <div class="form-group">
                            <label for="bloodBandkName">Blood Bank Name:</label>
                            <input type="text" class="form-control" id="bloodBankName" name="bloodBandkName" required>
                        </div>
                        <div class="form-group">
                            <label for="bloodBankLocation">Location:</label>
                            <input type="text" class="form-control" id="bloodBankLocation" name="bloodBankLocation" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(bloodBankId) {
            if (confirm("Are you sure you want to delete this blood bank and all its comments?")) {
                window.location.href = 'delete_bank.php?bloodBankId=' + bloodBankId;
            }
        }

        function openUpdateForm(bloodBankId, bloodBankName, bloodBankLocation) {
            document.getElementById('bloodBankID').value = bloodBankId;
            document.getElementById('bloodBankName').value = bloodBankName;
            document.getElementById('bloodBankLocation').value = bloodBankLocation;
            $('#updateModal').modal('show');
        }
    </script>
</body>
</html>

<?php
} else {
    header("Location: signin.php");
    exit;
}
?>
