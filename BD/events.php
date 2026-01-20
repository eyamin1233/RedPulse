<?php 
session_start();

if (!isset($_SESSION['bloodbank_id']) || $_SESSION['role'] !== 'bloodbank') {
    header('Location: events.php');
    exit();
}

try {
    $conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $bloodBankID = $_SESSION['bloodbank_id'];

    // Handle event deletion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event_id'])) {
        $deleteId = $_POST['delete_event_id'];
        $stmt = $conn->prepare("DELETE FROM events WHERE id = ? AND BloodBankID = ?");
        $stmt->execute([$deleteId, $bloodBankID]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Handle new event creation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'], $_POST['date'], $_POST['location'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $date = $_POST['date'];
        $location = trim($_POST['location']);

        if ($title && $description && $date && $location) {
            $stmt = $conn->prepare("INSERT INTO events (BloodBankID, Title, Description, EventDate, Location) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$bloodBankID, $title, $description, $date, $location]);

        $stmtName = $conn->prepare("SELECT Name FROM bloodbank WHERE BloodBankID = ?");
        $stmtName->execute([$bloodBankID]);
        $bank = $stmtName->fetch(PDO::FETCH_ASSOC);
        $bankName = $bank ? $bank['Name'] : 'Blood Bank';

        // 3. Create notification message
        $message = "New event '{$title}' organized by {$bankName} on " . date("d M Y", strtotime($date)) . " at {$location}.";

        // 4. Send to all users
        $userIds = $conn->query("SELECT id FROM user")->fetchAll(PDO::FETCH_COLUMN);
        $notifStmt = $conn->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
        foreach ($userIds as $uid) {
            $notifStmt->execute([$uid, $message]);
        }
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }



    // Pagination
    $limit = 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $countStmt = $conn->prepare("SELECT COUNT(*) FROM events WHERE BloodBankID = :bbid");
    $countStmt->execute([':bbid' => $bloodBankID]);
    $totalEvents = $countStmt->fetchColumn();
    $totalPages = ceil($totalEvents / $limit);

    $stmt = $conn->prepare("SELECT * FROM events WHERE BloodBankID = :bbid ORDER BY EventDate DESC LIMIT :offset, :limit");
    $stmt->bindParam(':bbid', $bloodBankID, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Blood Bank Events - RedPulse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
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

        .navbar-toggler {
            border-color: rgba(0, 0, 0, 0.5);
        }

        .container {
            max-width: 960px;
        }

        .event-card {
            background: #fff;
            color: #000;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            position: relative;
        }

        .event-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #b30000;
        }

        .event-info {
            font-size: 0.9rem;
        }

        .btn-delete {
            position: absolute;
            top: 10px;
            right: 10px;
            background: transparent;
            border: none;
            color: #c00000;
            font-size: 1.2rem;
        }

        .btn-delete:hover {
            color: #900;
        }

        .event-form {
            background: rgba(255,255,255,0.9);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 10px;
            color: #000;
        }

        .btn-red {
            background-color: #b30000;
            color: #fff;
        }

        .btn-red:hover {
            background-color:rgb(47, 168, 57);
        }

        .pagination .page-link {
            color: #b30000;
            border-radius: 8px;
        }

        .pagination .active .page-link {
            background-color: #b30000;
            border-color: #b30000;
            color: #fff;
        }
        .modal-body {
            background-color:rgb(242, 242, 242);
            color: #000;
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
        <a class="nav-link" href="bb_profile.php">Profile</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="inventory.php">Inventory</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="events.php">Events</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="signout.php">Sign Out</a>
    </li>
</ul>

        </div>
    </nav>

<div class="container mt-4">
    <!-- Create Event Form -->
    <div class="event-form">
        <h5><i class="bi bi-plus-circle"></i> Create New Event</h5>
        <form method="POST" class="row g-3 mt-2">
            <div class="col-md-6">
                <input type="text" name="title" class="form-control" placeholder="Event Title" required />
            </div>
            <div class="col-md-6">
                <input type="date" name="date" class="form-control" required />
            </div>
            <div class="col-md-6">
                <input type="text" name="location" class="form-control" placeholder="Location" required />
            </div>
            <div class="col-md-6">
                <textarea name="description" class="form-control" placeholder="Short Description" rows="1" required></textarea>
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-red"><i class="bi bi-check-circle"></i> Create Event</button>
            </div>
        </form>
    </div>

    <!-- Event Cards -->
    <h2 class="text-center text-white mb-3">Upcoming Events</h2>
    <?php if (count($events) > 0): ?>
        <?php foreach ($events as $event): ?>
            <div class="event-card">
                <!-- Hidden form for deletion -->
                <form method="POST" id="delete-form-<?= htmlspecialchars($event['ID']) ?>"></form>
                <input type="hidden" form="delete-form-<?= htmlspecialchars($event['ID']) ?>" name="delete_event_id" value="<?= htmlspecialchars($event['ID']) ?>" />
                <!-- Delete button triggers modal -->
                <button type="button" class="btn-delete" title="Delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-eventid="<?= htmlspecialchars($event['ID']) ?>">
                    <i class="bi bi-trash-fill"></i>
                </button>

                <div class="event-title"><?= htmlspecialchars($event['Title']) ?></div>
                <div class="event-info">
                    <i class="bi bi-calendar2-event-fill"></i> <?= htmlspecialchars($event['EventDate']) ?> &nbsp; | &nbsp;
                    <i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($event['Location']) ?>
                </div>
                <div class="mt-1"><?= nl2br(htmlspecialchars($event['Description'])) ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-light text-dark text-center">No events found for your blood bank.</div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteLabel">Confirm Delete</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this event? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let deleteEventId = null;
    let confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    var confirmDeleteModal = document.getElementById('confirmDeleteModal');
    confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        deleteEventId = button.getAttribute('data-eventid');
    });

    confirmDeleteBtn.addEventListener('click', function () {
        if (deleteEventId) {
            document.getElementById('delete-form-' + deleteEventId).submit();
        }
    });
</script>
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
