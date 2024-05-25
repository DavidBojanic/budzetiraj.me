<?php
include 'session.php';
include 'db.php';

$user_id = $_SESSION['user_id'];

$query = $conn->prepare("SELECT category, SUM(amount) AS total_amount FROM expenses WHERE user_id = ? GROUP BY category");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$query->close();
$conn->close();

echo json_encode($data);
?>
