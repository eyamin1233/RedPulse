<?php
session_start();

if (
    isset($_SESSION['email']) && 
    isset($_SESSION['id']) &&
    !empty($_SESSION['email']) &&
    !empty($_SESSION['id'])
) {
    $loginmail = $_SESSION['email'];
    $loginid = $_SESSION['id'];

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        if (
            isset($_POST['u1']) && 
            isset($_POST['u2']) &&
            !empty($_POST['u1']) &&
            !empty($_POST['u2'])
        ) {  
            $content = $_POST['u1'];
            $bid = $_POST['u2'];

            try {
                $con = new PDO('mysql:host=localhost:3306;dbname=blooddonationmanagementsystem;', 'root', ''); 
                $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt = $con->prepare("INSERT INTO review_bloodbank (userid, BloodBankID, review, date) VALUES (:userid, :bloodbankid, :review, NOW())");
                $stmt->bindParam(':userid', $loginid);
                $stmt->bindParam(':bloodbankid', $bid);
                $stmt->bindParam(':review', $content);
                $stmt->execute();
                ?>
                <script>
                    location.assign('bloodbank.php');
                </script>
                <?php
            } catch (PDOException $e) {
                ?>
                <script>
                    alert('Database Error: <?php echo $e->getMessage(); ?>');
                    location.assign('test1.php');
                </script>
                <?php
            }
        } else {
            ?>
            <script>
                location.assign('test2.php');
            </script>
            <?php
        }
    } else {
        ?>
        <script>
            location.assign('test3.php');
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
