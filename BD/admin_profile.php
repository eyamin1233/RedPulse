<?php
session_start();

try {
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin.php");
        exit;
    }

    $adminId = $_SESSION['admin_id'];

    $admin = $conn->prepare("SELECT * FROM admin WHERE AdminID=?");
    $admin->execute([$adminId]);
    $admin = $admin->fetch(PDO::FETCH_ASSOC);

    $totalUsers = $conn->query("SELECT COUNT(*) FROM user")->fetchColumn();
    $totalDonations = $conn->query("SELECT COUNT(*) FROM donations")->fetchColumn();
    $totalDonors = $conn->query("SELECT COUNT(DISTINCT donor_id) FROM donations")->fetchColumn();
    $totalBloodBanks = $conn->query("SELECT COUNT(*) FROM bloodbank")->fetchColumn();
    $activeRequests = $conn->query("SELECT COUNT(*) FROM bloodrequest WHERE status='Pending'")->fetchColumn();

    $newUsers = $conn->query("SELECT name,email,created_at FROM user WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY created_at DESC");

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - RedPulse</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    background: radial-gradient(circle,rgb(255,99,90) 0%,rgb(241,33,33) 100%);
    color:#fff;
    min-height:100vh;
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

.sidebar {
    position: fixed;
    left: -320px;
    top: 80px; 
    width: 300px;
    height: calc(50% - 70px);
    background: #111;
    padding: 25px;
    transition: 0.4s ease;
    z-index: 1000;
}

.sidebar.active {
    left: 0;
}

/* Toggle Button */
.profile-toggle {
    position: fixed;
    top: 90px;
    left: 18px;
    width: 42px;
    height: 42px;
    background: #111;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 1100;
    transition: 0.4s ease;
}

.profile-toggle span {
    display: block;
    width: 22px;
    height: 3px;
    background: #fff;
    margin: 3px 0;
}

/* Move toggle together with sidebar */
.profile-toggle.active {
    left: 330px;
}


/* Dashboard Layout */
.dashboard-wrapper {
    max-width:1300px;
    margin:auto;
    padding:120px 25px 40px;
}

/* Stat Cards */
.stat-row {
    display:grid;
    grid-template-columns: repeat(auto-fit,minmax(180px,1fr));
    gap:25px;
    margin-bottom:40px;
}

.stat-box {
    background:#fff;
    color:#000;
    padding:25px 20px;
    border-radius:15px;
    text-align:center;
    box-shadow:0 4px 15px rgba(0,0,0,.15);
}

.stat-box h2 {
    color:#dc3545;
    font-size:2.3rem;
}

/* Management Cards */
.manage-grid {
    display:grid;
    grid-template-columns: repeat(auto-fit,minmax(260px,1fr));
    gap:30px;
    margin-bottom:45px;
}

.card-box {
    background:#fff;
    color:#000;
    padding:28px;
    border-radius:15px;
    text-align:center;
    box-shadow:0 4px 15px rgba(0,0,0,.15);
}

/* User Table */
.table {
    background:#fff;
    color:#000;
    border-radius:12px;
    overflow:hidden;
}

.table th {
    background:#dc3545;
    color:#fff;
}

/* Chart Box */
.chart-box {
    background:#fff;
    color:#000;
    padding:30px;
    border-radius:18px;
    box-shadow:0 4px 15px rgba(0,0,0,.15);
    margin-top:45px;
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
        <a class="nav-link active" href="admin_profile.php">Dashboard</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin_bloodbanks.php">Blood Banks</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="users.php">Users</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="signout.php">Sign Out</a>
    </li>
</ul>

        </div>
    </nav>

<div class="profile-toggle" onclick="toggleSidebar()">
    <div>
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>

<div class="sidebar" id="sidebar">
    <h4>Admin Profile</h4>
    <hr>
    <p><strong>Name:</strong><br><?= $admin['UserName'] ?></p>
    <p><strong>Email:</strong><br><?= $admin['Email'] ?></p>

    <a href="admin_profile_edit.php" class="btn btn-primary btn-sm w-100 mb-2">Edit Profile</a>
    <a href="admin_password_change.php" class="btn btn-warning btn-sm w-100">Change Password</a>
</div>

<div class="dashboard-wrapper">

    <!-- Stats -->
    <div class="stat-row">
        <div class="stat-box"><h2><?= $totalUsers ?></h2><p>Users</p></div>
        <div class="stat-box"><h2><?= $totalDonors ?></h2><p>Donors</p></div>
        <div class="stat-box"><h2><?= $totalBloodBanks ?></h2><p>Blood Banks</p></div>
        <div class="stat-box"><h2><?= $totalDonations ?></h2><p>Donations</p></div>
        <div class="stat-box"><h2><?= $activeRequests ?></h2><p>Active Requests</p></div>
    </div>

    <!-- Management -->
    <div class="manage-grid">
        <div class="card-box">
            <h5>Blood Bank Management</h5>
            <p>View, verify and manage blood banks.</p>
            <a href="admin_bloodbanks.php" class="btn btn-danger">Manage</a>
        </div>
        <div class="card-box">
            <h5>Donor Management</h5>
            <p>Control donor records & activities.</p>
            <a href="users.php" class="btn btn-danger">Manage</a>
        </div>
    </div>

    <!-- New Users -->
    <div class="card-box">
        <h5 class="mb-3">Newly Registered Users (Last 7 Days)</h5>
        <table class="table table-bordered table-hover">
            <tr><th>Name</th><th>Email</th><th>Date</th></tr>
            <?php while($u=$newUsers->fetch()): ?>
            <tr>
                <td><?= $u['name'] ?></td>
                <td><?= $u['email'] ?></td>
                <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Chart -->
    <div class="chart-box">
        <h5 class="mb-3">Donation Statistics</h5>
        <canvas id="donationChart" height="110"></canvas>
    </div>

</div>

<script>
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('active');
    document.querySelector('.profile-toggle').classList.toggle('active');
}


const ctx = document.getElementById('donationChart');
let donationChart;

function loadChart(month, year){
    fetch(`get_donation_stats.php?month=${month}&year=${year}`)
        .then(res => res.json())
        .then(data => {
            if(donationChart) donationChart.destroy();

            donationChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Week 1','Week 2','Week 3','Week 4'],
                    datasets: [{
                        label: 'Donations',
                        data: data,
                        borderWidth: 2,
                        tension: .4,
                        fill: true
                    }]
                }
            });
        });
}

loadChart(new Date().getMonth()+1, new Date().getFullYear());

document.getElementById('month').addEventListener('change', ()=>{
    loadChart(month.value, year.value);
});

document.getElementById('year').addEventListener('change', ()=>{
    loadChart(month.value, year.value);
});

</script>

</body>
</html>
