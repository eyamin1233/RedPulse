<?php
if (isset($_SESSION['bloodbank_message'])):
  $type = ($_SESSION['bloodbank_message_type'] === 'success') ? 'success' : 'danger';
?>
  <div class="alert alert-<?php echo $type; ?> text-center" role="alert">
    <?php echo $_SESSION['bloodbank_message']; ?>
    <?php if (isset($_SESSION['redirect_script'])) echo $_SESSION['redirect_script']; ?>
  </div>
<?php
unset($_SESSION['bloodbank_message'], $_SESSION['bloodbank_message_type'], $_SESSION['redirect_script']);
endif;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Register Blood Bank - RedPulse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        .form-container {
            width: 100%;
            max-width: 800px;
            background: rgba(0, 0, 0, 0.9);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0);
            margin-top: 50px;
        }

        .form-container h2 {
            margin-bottom: 20px;
            text-align: center;
            color:rgb(255, 255, 255);
        }

        .form-group label {
            color:rgb(255, 255, 255);
        }

        .form-control {
            background: transparent;
            border: 1px solid #ffcccb;
            color:rgb(151, 151, 151);
            border-radius: 5px;
        }

        .form-control:focus, .form-control:hover {
            border-color:rgb(255, 4, 0);
            box-shadow: 0 0 5px #ffcccb;
            outline: none;
        }

        .form-group select {
            background: rgba(0, 0, 0, 0.1);
        }

        .btn-danger {
            width: 100%;
            padding: 10px;
            background-color:rgb(255, 204, 203);
            border: none;
            color: red;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-danger:hover {
            background-color:rgb(255, 8, 0);
            transform: translateY(-2px);
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
        <a class="nav-link" href="index.php">Home</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="signin.php">Sign In</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="Register.php">Register</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="admin.php">Admin Panel</a>
    </li>
</ul>

        </div>
    </nav>
    

   <!-- Blood Bank Registration Form -->
<div class="form-container">
  <h2>Blood Bank Registration</h2>
  <form action="b_r_p.php" method="POST" enctype="multipart/form-data" onsubmit="return validateBloodBankForm()">
    <div class="row">
      <!-- Left Column -->
      <div class="col-md-6">
        <div class="form-group">
          <label for="bb_bankname">Blood Bank Name:</label>
          <input type="text" class="form-control" id="bb_bankname" name="bankname" required>
          <small id="bb_bankNameError" class="text-danger"></small>
        </div>
        <div class="form-group">
          <label for="bb_location">Location:</label>
          <input type="text" class="form-control" id="bb_location" name="location" required>
        </div>
        <div class="form-group">
          <label for="bb_contact">Contact Number:</label>
          <input type="tel" class="form-control" id="bb_contact" name="contact" required>
          <small id="bb_contactError" class="text-danger"></small>
        </div>
      </div>

      <!-- Right Column -->
      <div class="col-md-6">
        <div class="form-group">
          <label for="bb_email">Email:</label>
          <input type="email" class="form-control" id="bb_email" name="email" required>
          <small id="bb_emailError" class="text-danger"></small>
        </div>
        <div class="form-group">
          <label for="bb_password">Password:</label>
          <input type="password" class="form-control" id="bb_password" name="password" required oninput="checkBBPasswordStrength()">
          <small class="form-text text-muted">
            Must be at least 8 characters, include uppercase, lowercase, number, and a special character.
          </small>
          <small id="bb_passwordError" class="text-danger"></small>
        </div>
        <div class="form-group">
          <label for="bb_confirmPassword">Confirm Password:</label>
          <input type="password" class="form-control" id="bb_confirmPassword" name="confirmPassword" required>
          <small id="bb_confirmPasswordError" class="text-danger"></small>
        </div>
      </div>
    </div>
    <button type="submit" class="btn btn-danger mt-3">Register</button>
  </form>
</div>

<!-- Blood Bank JS Validation -->
<script>
  function validateBloodBankForm() {
    let isValid = true;

    // Clear previous errors
    document.getElementById("bb_bankNameError").innerText = "";
    document.getElementById("bb_contactError").innerText = "";
    document.getElementById("bb_emailError").innerText = "";
    document.getElementById("bb_passwordError").innerText = "";
    document.getElementById("bb_confirmPasswordError").innerText = "";

    const bankname = document.getElementById("bb_bankname").value.trim();
    const contact = document.getElementById("bb_contact").value.trim();
    const email = document.getElementById("bb_email").value.trim();
    const password = document.getElementById("bb_password").value;
    const confirmPassword = document.getElementById("bb_confirmPassword").value;

    const nameRegex = /^[A-Za-z0-9\s]+$/;
    const contactRegex = /^01[0-9]{9}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const passwordRegex = /^(?=.*\d)(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

    if (!nameRegex.test(bankname)) {
      document.getElementById("bb_bankNameError").innerText = "Only letters, numbers and spaces allowed.";
      isValid = false;
    }

    if (!contactRegex.test(contact)) {
      document.getElementById("bb_contactError").innerText = "Must be 11 digits and start with '01'.";
      isValid = false;
    }

    if (!emailRegex.test(email)) {
      document.getElementById("bb_emailError").innerText = "Invalid email format.";
      isValid = false;
    }

    if (!passwordRegex.test(password)) {
      document.getElementById("bb_passwordError").innerText = "Password is weak.";
      isValid = false;
    }

    if (password !== confirmPassword) {
      document.getElementById("bb_confirmPasswordError").innerText = "Passwords do not match.";
      isValid = false;
    }

    return isValid;
  }

  function checkBBPasswordStrength() {
    const password = document.getElementById("bb_password").value;
    const passwordError = document.getElementById("bb_passwordError");
    const regex = /^(?=.*\d)(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

    passwordError.innerText = regex.test(password)
      ? ""
      : "Password must include uppercase, lowercase, number, and special character.";
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
