<?php
$conn = new PDO('mysql:host=localhost;dbname=blooddonationmanagementsystem', 'root', '');

$month = $_GET['month'] ?? date('m');
$year  = $_GET['year'] ?? date('Y');

$sql = "SELECT 
    WEEK(donated_at, 1) - WEEK(DATE_SUB(donated_at, INTERVAL DAYOFMONTH(donated_at)-1 DAY), 1) + 1 AS week,
    COUNT(*) AS total
FROM donations
WHERE MONTH(donated_at)=:m AND YEAR(donated_at)=:y
GROUP BY week ORDER BY week";

$stmt = $conn->prepare($sql);
$stmt->execute([':m'=>$month, ':y'=>$year]);

$data = [0,0,0,0];

while($row = $stmt->fetch()){
    if($row['week'] >= 1 && $row['week'] <= 4){
        $data[$row['week']-1] = $row['total'];
    }
}

echo json_encode($data);
