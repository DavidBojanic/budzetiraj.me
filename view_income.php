<?php include 'templates/header.php'; ?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT amount, category, date, description FROM income WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Income Entries</h2>
<table>
    <tr>
        <th>Amount</th>
        <th>Category</th>
        <th>Date</th>
        <th>Description</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['amount']; ?></td>
            <td><?php echo $row['category']; ?></td>
            <td><?php echo $row['date']; ?></td>
            <td><?php echo $row['description']; ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
$stmt->close();
$conn->close();
include 'templates/footer.php';
?>
