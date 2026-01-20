<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RedPulse Registration</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    body {
      background: radial-gradient(circle,rgb(255, 99, 90) 0%,rgb(241, 33, 33) 100%);
      font-family: 'Arial', sans-serif;
      padding-top: 80px;
      color: #333;
    }

    .pulse-text {
      font-size: 28px;
      fill: white;
      text-anchor: middle;
      font-weight: bold;
    }

    .pulse-line {
      fill: none;
      stroke: white;
      stroke-width: 2;
      animation: pulseAnim 1.5s infinite;
    }

    @keyframes pulseAnim {
      0% { stroke-dasharray: 0, 100; }
      100% { stroke-dasharray: 100, 0; }
    }

    .toggle-buttons {
      text-align: center;
      margin-top: 20px;
      margin-bottom: 10px;
    }
    .toggle-buttons button {
      background-color:rgb(26, 25, 25);
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 20px;
      border-radius: 15px;
      cursor: pointer;
    }
    .toggle-buttons button:hover {
      background-color:rgb(255, 176, 176);
      color: black;
    }

    .toggle-buttons button {
      margin: 0 10px;
    }
    .toggle-btn.active-btn {
  background-color:rgb(223, 215, 215) !important;
  color: black !important;
}


    .form-section {
      padding: 0 30px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
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
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link" href="signin.php">Sign In</a></li>
      <li class="nav-item"><a class="nav-link active" href="#">Register</a></li>
      <li class="nav-item"><a class="nav-link" href="admin.php">Admin Panel</a></li>
    </ul>
  </div>
</nav>

<!-- Toggle Buttons -->
<div class="toggle-buttons">
  <button id="btnDonor" class="btn btn-light toggle-btn active-btn" onclick="showForm('donor')">Donor Registration</button>
  <button id="btnBloodbank" class="btn btn-light toggle-btn" onclick="showForm('bloodbank')">Blood Bank Registration</button>
</div>


<!-- Registration Forms -->
<div class="form-section">
  <!-- Donor Registration Form -->
  <div id="donorForm">
    <?php include("d_r.php"); ?>
  </div>

  <!-- Blood Bank Registration Form -->
  <div id="bloodbankForm" style="display: none;">
    <?php include("bloodbanks.php"); ?>
  </div>
</div>

<script>
  function showForm(type) {
    document.getElementById('donorForm').style.display = (type === 'donor') ? 'block' : 'none';
    document.getElementById('bloodbankForm').style.display = (type === 'bloodbank') ? 'block' : 'none';

    const donor = document.getElementById('donorForm');
  const bloodbank = document.getElementById('bloodbankForm');

  const btnDonor = document.getElementById('btnDonor');
  const btnBloodbank = document.getElementById('btnBloodbank');

  if (type === 'donor') {
    donor.classList.add('active');
    bloodbank.classList.remove('active');

    btnDonor.classList.add('active-btn');
    btnBloodbank.classList.remove('active-btn');
  } else {
    donor.classList.remove('active');
    bloodbank.classList.add('active');

    btnDonor.classList.remove('active-btn');
    btnBloodbank.classList.add('active-btn');
  }
  }
</script>

<?php if (isset($_SESSION['active_form'])): ?>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    showForm("<?php echo $_SESSION['active_form']; ?>");
  });
</script>
<?php unset($_SESSION['active_form']); ?>
<?php endif; ?>


</body>
</html>
