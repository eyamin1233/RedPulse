<?php
    session_start();

if(
    isset($_SESSION['bloodbank_email']) && 
    isset($_SESSION['bloodbank_id']) &&
    !empty($_SESSION['bloodbank_email']) &&
    !empty($_SESSION['bloodbank_id'])
    ){
     $loginmail=$_SESSION['bloodbank_email'];
    $loginid=$_SESSION['bloodbank_id'];
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>centres</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        body {
             background: radial-gradient(circle,rgb(255, 99, 90) 0%,rgb(241, 33, 33) 100%);
            background-size: 100%;
        }
        
        .navbar-inverse {
            background-color: rgba(0, 0, 0, 0.8) !important;
            border-color: rgba(0, 0, 0, 0.8) !important;
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


        .navbar  .navbar-nav .active {
            color:rgb(253, 253, 253) !important;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            font-weight: bold;
        }

        .navbar  .navbar-nav .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 20px;
        }

        .navbar a {
            font-size: 20px;
        }
        .navbar-nav{
            margin-left: 400px;
        }
        .navbar-nav .nav-item {
            margin-right: 20px;
            position: relative;
            left: 700px;
        }
        .res {
            text-align: center;
        }
        .btn{
            background-color: rgb(250, 237, 236);
            color: black;
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: lightgreen;
            color: white;
            transition: background-color 0.3s ease;
        }
        .top {
            font-size: 40px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 60px;
            text-align: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            animation: slideDown 1.5s ease-in-out;
        }

        .box {
            background-color: rgb(251, 169, 169);
            width: 100%;
            padding: 25px;
            border-radius: 10px;
            box-shadow: rgba(0, 0, 0, 0.15) 2.4px 2.4px 3.2px;
            position: relative;
        }

        .innbox {
            margin-left: 90px;
        }

        .innbox h2 {
            color: black;
            font-size: 30px;
            margin-bottom: 10px;
        }
        .innbox small {
            color: black;
            font-size: 20px;
        }
        .p1 {
            font-size: 15px;
            color: blue;
        }

        .comment_box {
            margin-right: 70px;
        }

        .review_box {
            box-shadow: rgba(0, 0, 0, 0.15) 2.4px 2.4px 3.2px;
            margin-right: 70px;
        }

        .contents {
            padding: 10px;
            background-color: white;
            color: black;
            border-radius: 10px;
        }

        .contents p {
            margin: 0;
        }
        .contents small {
            color: gray;
        }

        .contents hr {
            margin: 5px 0;
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
        #search-form {
            position: absolute;
            top: 150px; 
            right: 700px;
            width: 400px;
         }
  
         .search-wrapper input {
            height: 40px; /* Or whatever height you want */
            padding-right: 40px;
            font-size: 18px;
}


  .search-wrapper button {
    position: absolute;
    top: 40%;
    right: 10px;
    transform: translateY(-50%);
    border: none;
    background: none;
    padding: 0;
    color: #6c757d;
  }

  .search-wrapper button:hover {
    color: #000;
  }

    </style>
</head>

<body>

<form id="search-form" action="http://localhost/bd/search.php" method="POST">
  <div class="form-group search-wrapper">
    <input type="text" class="form-control" placeholder="Search Centre(Name)" name="key" required>
    <button type="submit">
      <i class="fas fa-search"></i>
    </button>
  </div>
</form>

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
    <li class="nav-item"><a class="nav-link active" href="http://localhost/bd/bloodbank.php">Blood Bank reviews</a></li>
    <li class="nav-item"><a class="nav-link" href="http://localhost/bd/profile.php">Profile</a></li>
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
    
    <h3 class="top">Centres</h3>
    <br />
    <?php 
    try {
        $conn = new PDO('mysql:host=localhost:3306;dbname=blooddonationmanagementsystem;', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $selectquery = "SELECT * FROM bloodbank ORDER BY BloodBankID ASC";
        $test = $conn->query($selectquery);
        $returnobj = $test->fetchAll();

        foreach ($returnobj as $data) {
            $bid = $data['BloodBankID'];
            $bloodbank = $data['Name'];
            $loc = $data['Location'];
            $contact = $data['ContactNumber'];
            $image1 = $data['pic1'];
            $image2 = $data['pic2'];
            ?>
            <div class="container">
    <div class="box">
        <div class="innbox">
            <h2><?php echo $bloodbank; ?></h2>
            <small><?php echo $loc; ?></small>
            <p><i class="fa fa-phone"></i> <?php echo $contact; ?></p>
            <hr />

            <!-- Review Submission -->
            <div class="comment_box">
                <form action="comments.php" method="POST">
                    <div class="form-group">
                        <label for="u1">Write your review below</label>
                        <input type="hidden" name="u2" value="<?php echo $bid; ?>" />
                        <textarea class="form-control" id="u1" name="u1" rows="3" placeholder="Start typing..." required></textarea>
                    </div>
                    <input type="submit" class="btn" value="Submit">
                </form>
            </div>

            <!-- Reviews -->
            <br />
            <?php
            try {
                $selectquery1 = "SELECT u.name AS u1,
                                        b.Name AS u2,
                                        ub.review AS u3,
                                        ub.date AS u4
                                 FROM review_bloodbank AS ub
                                 LEFT JOIN user AS u ON ub.userid = u.id
                                 LEFT JOIN bloodbank AS b ON ub.BloodBankID = b.BloodBankID
                                 WHERE ub.BloodBankID = $bid
                                 ORDER BY ub.date DESC";

                $test1 = $conn->query($selectquery1);
                $returnobj1 = $test1->fetchAll();

                if ($test1->rowCount() != 0) {
                    foreach ($returnobj1 as $data1) {
                        $name = $data1['u1'];
                        $rev = $data1['u3'];
                        $date = $data1['u4'];
                        ?>
                        <div class="review_box">
                            <div class="contents">
                                <p class="p1"><?php echo $name; ?></p>
                                <small><?php echo $date; ?></small>
                                <hr />
                                <p><?php echo $rev; ?></p>
                            </div>
                        </div>
                        <br />
                        <?php
                    }
                } else {
                    echo '<p class="text-light">No reviews yet!</p>';
                }
            } catch (PDOException $e) {
                echo "<p class='text-warning'>Error loading reviews: {$e->getMessage()}</p>";
            }
            ?>
        </div>
    </div>
    <br />
</div>

            <?php
        }
    } catch (PDOException $ex) {
        ?>
        <script>
            alert('Database Error: <?php echo $ex->getMessage(); ?>');
            location.assign('index.php');
        </script>
        <?php
    }
} else {
    ?>
    <script>
        location.assign('signin.php');
    </script>
    <?php
}
?>
