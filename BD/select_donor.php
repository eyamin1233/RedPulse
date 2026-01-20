<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: posts.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'], $_POST['request_id'])) {
    $selectedUserId = $_POST['user_id'];
    $requestId = $_POST['request_id'];

    try {
        $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 1. Fetch donor (user) info
        $stmt = $conn->prepare("SELECT id, name, bloodtype, location, lastdonationdate FROM user WHERE id = :uid");
        $stmt->execute([':uid' => $selectedUserId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die("Selected user not found.");
        }

        // 2. Check if donor already exists
        $check = $conn->prepare("SELECT id FROM donors WHERE user_id = :uid");
        $check->execute([':uid' => $selectedUserId]);
        $existingDonor = $check->fetch(PDO::FETCH_ASSOC);

        // 3. Get recipient user_id, contact, location from bloodrequest
        $reqStmt = $conn->prepare("SELECT user_id, contact, location FROM bloodrequest WHERE id = :rid");
        $reqStmt->execute([':rid' => $requestId]);
        $requestData = $reqStmt->fetch(PDO::FETCH_ASSOC);
        if (!$requestData) {
            die("Invalid request ID: no matching request found.");
        }

        $recipientUserId = $requestData['user_id'];
        $recipientContact = $requestData['contact'];
        $recipientLocation = $requestData['location'];

        // 4. Insert donor if not exists
        if ($existingDonor) {
            $donorId = $existingDonor['id'];
        } else {
            $insert = $conn->prepare("INSERT INTO donors (user_id, name, location, bloodtype, lastdonationdate, created_at) 
                                      VALUES (:user_id, :name, :location, :bloodtype, :lastdonationdate, NOW())");
            $insert->execute([
                ':user_id' => $user['id'],
                ':name' => $user['name'],
                ':location' => $user['location'],
                ':bloodtype' => $user['bloodtype'],
                ':lastdonationdate' => $user['lastdonationdate']
            ]);
            $donorId = $conn->lastInsertId();
        }

        // 5. Insert donation record
        $donation = $conn->prepare("INSERT INTO donations (donor_id, request_id, donated_at) 
                                    VALUES (:donor_id, :request_id, CURDATE())");
        $donation->execute([
            ':donor_id' => $donorId,
            ':request_id' => $requestId
        ]);

        // 5.1 Send notification to selected donor including recipient name
$reqDetails = $conn->prepare("
    SELECT br.blood_needed, br.location, u.name AS recipient_name 
    FROM bloodrequest br
    JOIN user u ON br.user_id = u.id
    WHERE br.id = :rid
");
$reqDetails->execute([':rid' => $requestId]);
$req = $reqDetails->fetch(PDO::FETCH_ASSOC);

if ($req) {
    $message = "You have been selected as a donor for {$req['recipient_name']}'s request of {$req['blood_needed']} bag(s) blood in {$req['location']}.";
    $notif = $conn->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (:uid, :msg, NOW())");
    $notif->execute([
        ':uid' => $selectedUserId,
        ':msg' => $message
    ]);
}


        // 6. Fetch recipient name and bloodtype from user table
        $recStmt = $conn->prepare("SELECT name, bloodtype FROM user WHERE id = :rid");
        $recStmt->execute([':rid' => $recipientUserId]);
        $recipient = $recStmt->fetch(PDO::FETCH_ASSOC);

        if ($recipient) {
            // 7. Check if already in recipient table for this request
            $checkRec = $conn->prepare("SELECT id FROM recipient WHERE user_id = :uid AND request_id = :rid");
            $checkRec->execute([
                ':uid' => $recipientUserId,
                ':rid' => $requestId
            ]);

            if (!$checkRec->fetch()) {
                // 8. Insert into recipient table
                $insertRec = $conn->prepare("INSERT INTO recipient 
                    (user_id, request_id, recipient_name, bloodtype, contact, location, received_date)
                    VALUES 
                    (:user_id, :request_id, :recipient_name, :bloodtype, :contact, :location, CURDATE())");
                $insertRec->execute([
                    ':user_id' => $recipientUserId,
                    ':request_id' => $requestId,
                    ':recipient_name' => $recipient['name'],
                    ':bloodtype' => $recipient['bloodtype'],
                    ':contact' => $recipientContact,
                    ':location' => $recipientLocation
                ]);
            }
        }

        echo "<script>alert('Donor and recipient data successfully recorded!'); location.href='posts.php';</script>";
        exit;

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    die("Invalid request.");
}
