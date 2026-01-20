<?php
session_start();

if (
    isset($_SESSION['user_email']) &&
    isset($_SESSION['user_id']) &&
    !empty($_SESSION['user_email']) &&
    !empty($_SESSION['user_id'])
) {
    $loginmail = $_SESSION['user_email'];
    $loginid = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        if (
            isset($_POST['bname']) &&
            !empty($_POST['bname'])
        ) {
            $bname = $_POST['bname'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Search results</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        body {
            background: radial-gradient(circle, rgb(255, 99, 90) 0%, rgb(241, 33, 33) 100%);
            font-family: Arial, sans-serif;
            color: #fff;
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
            left: 550px;
        }

        .res {
            text-align: center;
        }

        .bt {
            background-color: white;
            color: black;
            width: 100px;
            text-align: center;

        }
        

        .top {
            font-size: 35px;
            font-weight: medium;
            margin-top: 20px;
            text-align: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .box {
            background-color: rgb(241, 223, 223);
            width: 100%;
            padding: 25px;
            border-radius: 10px;
            box-shadow: rgba(0, 0, 0, 0.15) 2.4px 2.4px 3.2px;
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
        .btn{
            background-color: white;
            color: black;
            width: 90px;
            text-align: center;
        }
        .btn:hover{
            background-color: black;
            color: white;
            width: 100px;
            text-align: center;
        }

        .back {
            position: absolute;
            top: 200px;
            right: 60px;
            text-align: center;
            margin: 20px 800px;
            padding: 10px 30px;
            background-color:rgb(128, 36, 29);
            color: white;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
        }
        .back:hover {
            background-color: rgb(0, 0, 0);
            color: white;
        }
        .no_review {
            text-align: center;
            font-size: 18px;
            color: black;
            margin-top: 20px;
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
    <li class="nav-item"><a class="nav-link" href="http://localhost/bd/profile.php">Profile</a></li>
    <li class="nav-item"><a class="nav-link" href="http://localhost/bd/bloodbanks_all.php">Blood Bank</a></li>
    <li class="nav-item"><a class="nav-link active" href="#">Reviews</a></li>
    <li class="nav-item"><a class="nav-link" href="http://localhost/bd/signout.php">Sign Out</a></li>
</ul>

        </div>

    </nav>
    <br />

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
    <?php

            try {
                $conn = new PDO('mysql:host=localhost:3306;dbname=blooddonationmanagementsystem;', 'root', '');
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $searchKey = "%" . $bname . "%";  // prepare the LIKE pattern

$selectquery = "SELECT * FROM bloodbank WHERE BloodBankID = :bname OR Name LIKE :name";
$stmt = $conn->prepare($selectquery);
$stmt->bindParam(':bname', $bname, PDO::PARAM_STR);       // still works if bname is numeric or text
$stmt->bindParam(':name', $searchKey, PDO::PARAM_STR);   // fixed: use actual search key

$stmt->execute();


                $returnobj = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($stmt->rowCount() != 0) {
                    foreach ($returnobj as $data) {
                        $bid = $data['BloodBankID'];
                        $bloodbank = $data['Name'];
                        $loc = $data['Location'];
                        $contact = $data['ContactNumber'];
                        $image1 = $data['pic1'];
                        $image2 = $data['pic2'];
    ?>
    <h3 class="top">Search results for : <?php echo htmlspecialchars($bname); ?></h3>
    <div class="container">
        <div class="box">
            <div class="innbox">
                <h2><?php echo htmlspecialchars($bloodbank); ?></h2>
                <small><?php echo htmlspecialchars($loc); ?></small>
                <hr />
                <br /> <br />


                <div class="comment_box">
                    <form action="comments.php" method="POST" class="form_">
                        <div class="form-group">
                            <label for="u1">Write your review bellow</label>
                            <input type="hidden" name="u2" value="<?php echo $bid ?>" />
                            <textarea type="text" class="form-control" id="u1" name="u1" rows="5" placeholder="start typing..."></textarea>
                        </div>

                        <input type="submit" class="btn btn-default" value="Submit">
                        <br>
                    </form>

                </div>

                <?php

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
                    <p class="p1"><?php echo htmlspecialchars($name); ?></p>
                    <small1><?php echo htmlspecialchars($date); ?></small1>
                    <hr />
                    <p><?php echo htmlspecialchars($rev); ?></p>
                </div>
                </div>
                <br />
                <?php
                                    }
                                } else {
                                ?>
                <div class="review_box no_review">
                    <p>No reviews yet!</p>
                </div>
                <?php
                                }
                ?>
            </div>
        </div>
        <?php
                    }
                } else {
        ?>
        <h3 class="top">No centre found as: <?php echo htmlspecialchars($bname); ?></h3>
        <a href="http://localhost/bd/bloodbanks_all.php" class="back">Back</a>
        <?php
                }
            } catch (PDOException $ex) {
        ?>
        <script>
            alert("Database Error: <?php echo $ex->getMessage(); ?>");
            location.assign('bloodbank.php');
        </script>
        <?php
            }
        ?>
</body>

</html>
<?php
        }
    }
} else {
?>
<script>
    location.assign('signin.php')
</script>
<?php
}
?>
