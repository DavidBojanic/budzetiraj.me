<?php
include 'session.php';
include 'db.php';

$user_id = $_SESSION['user_id'];

// Query to get monthly income
$income_query = $conn->prepare("
    SELECT DATE_FORMAT(date, '%Y-%m') AS month, SUM(amount) AS total_amount
    FROM income
    WHERE user_id = ?
    GROUP BY DATE_FORMAT(date, '%Y-%m')
");
$income_query->bind_param("i", $user_id);
$income_query->execute();
$income_result = $income_query->get_result();

$income_data = [];
while ($row = $income_result->fetch_assoc()) {
    $income_data[$row['month']] = $row['total_amount'];
}

$expense_query = $conn->prepare("
    SELECT DATE_FORMAT(date, '%Y-%m') AS month, SUM(amount) AS total_amount
    FROM expenses
    WHERE user_id = ?
    GROUP BY DATE_FORMAT(date, '%Y-%m')
");
$expense_query->bind_param("i", $user_id);
$expense_query->execute();
$expense_result = $expense_query->get_result();

$expense_data = [];
while ($row = $expense_result->fetch_assoc()) {
    $expense_data[$row['month']] = $row['total_amount'];
}

$data = [
    'income' => $income_data,
    'expenses' => $expense_data
];

$income_query->close();
$expense_query->close();
$conn->close();

echo json_encode($data);
?>
