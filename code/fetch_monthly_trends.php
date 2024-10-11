<?php
include 'db.php';
include 'session.php';

$user_id = $_SESSION['user_id'];

$query = "
    SELECT DATE_FORMAT(date, '%Y-%m') AS month, 
           SUM(CASE WHEN type = 'Income' THEN amount ELSE 0 END) AS income, 
           SUM(CASE WHEN type = 'Expense' THEN amount ELSE 0 END) AS expense
    FROM (
        SELECT amount, 'Income' AS type, date FROM income WHERE user_id = $user_id
        UNION ALL
        SELECT amount, 'Expense' AS type, date FROM expenses WHERE user_id = $user_id
    ) AS combined
    GROUP BY month
    ORDER BY month";

$result = $conn->query($query);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>
