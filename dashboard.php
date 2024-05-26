<?php
include 'session.php';
include 'templates/header.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$current_month = date('Y-m');
$total_income_result = $conn->query("
    SELECT SUM(amount) AS total_income FROM income 
    WHERE user_id = $user_id AND DATE_FORMAT(date, '%Y-%m') = '$current_month'
    UNION
    SELECT SUM(amount) AS total_income FROM recurring_incomes
    WHERE user_id = $user_id AND (end_date IS NULL OR end_date >= '$current_month-01') AND start_date <= '$current_month-31'
");
$total_income = $total_income_result->fetch_assoc()['total_income'];

$total_expense_result = $conn->query("
    SELECT SUM(amount) AS total_expense FROM expenses 
    WHERE user_id = $user_id AND DATE_FORMAT(date, '%Y-%m') = '$current_month'
    UNION
    SELECT SUM(amount) AS total_expense FROM recurring_expenses
    WHERE user_id = $user_id AND (end_date IS NULL OR end_date >= '$current_month-01') AND start_date <= '$current_month-31'
");
$total_expense = $total_expense_result->fetch_assoc()['total_expense'];

$net_balance = $total_income - $total_expense;

$recent_transactions = $conn->query("
    (SELECT 'Income' AS type, amount, category, date, description FROM income WHERE user_id = $user_id AND DATE_FORMAT(date, '%Y-%m') = '$current_month')
    UNION
    (SELECT 'Income' AS type, amount, category, start_date AS date, description FROM recurring_incomes WHERE user_id = $user_id AND (end_date IS NULL OR end_date >= '$current_month-01') AND start_date <= '$current_month-31')
    UNION
    (SELECT 'Expense' AS type, amount, category, date, description FROM expenses WHERE user_id = $user_id AND DATE_FORMAT(date, '%Y-%m') = '$current_month')
    UNION
    (SELECT 'Expense' AS type, amount, category, start_date AS date, description FROM recurring_expenses WHERE user_id = $user_id AND (end_date IS NULL OR end_date >= '$current_month-01') AND start_date <= '$current_month-31')
    ORDER BY date DESC
    LIMIT 10
");

$budget_data = $conn->query("SELECT category, amount FROM budgets WHERE user_id = $user_id AND DATE_FORMAT(CONCAT(year, '-', month, '-01'), '%Y-%m') = '$current_month'");
$actual_spending_data = $conn->query("SELECT category, SUM(amount) AS total_amount FROM expenses WHERE user_id = $user_id AND DATE_FORMAT(date, '%Y-%m') = '$current_month' GROUP BY category");

$budget = [];
$actual_spending = [];
while ($row = $budget_data->fetch_assoc()) {
    $budget[$row['category']] = $row['amount'];
}
while ($row = $actual_spending_data->fetch_assoc()) {
    $actual_spending[$row['category']] = $row['total_amount'];
}

$categories = array_unique(array_merge(array_keys($budget), array_keys($actual_spending)));
$chart_data = [];
foreach ($categories as $category) {
    $chart_data[] = [
        'category' => $category,
        'budget' => $budget[$category] ?? 0,
        'actual_spending' => $actual_spending[$category] ?? 0
    ];
}

$conn->close();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white mb-3" style="background-color: #1E90FF;">
                <div class="card-header">This Month's Income</div>
                <div class="card-body">
                    <h5 class="card-title"><?= number_format($total_income, 2, ',', '.') ?> €</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white mb-3" style="background-color: #FF6347;">
                <div class="card-header">This Month's Expenses</div>
                <div class="card-body">
                    <h5 class="card-title"><?= number_format($total_expense, 2, ',', '.') ?> €</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white mb-3" style="background-color: #20B2AA;">
                <div class="card-header">This Month's Net Balance</div>
                <div class="card-body">
                    <h5 class="card-title"><?= number_format($net_balance, 2, ',', '.') ?> €</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3>Budget vs. Spending</h3>
            <div class="chart-container" style="position: relative; height: 60vh; width: 100%;">
                <canvas id="budgetVsSpendingChart"></canvas>
            </div>
        </div>
    </div>

    <h3 class="mt-4">Recent Transactions</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Type</th>
                <th>Amount</th>
                <th>Category</th>
                <th>Date</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($entry = $recent_transactions->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php if ($entry['type'] == 'Income'): ?>
                        <span class="text-success"><i class="fas fa-arrow-up"></i> <?= $entry['type'] ?></span>
                    <?php else: ?>
                        <span class="text-danger"><i class="fas fa-arrow-down"></i> <?= $entry['type'] ?></span>
                    <?php endif; ?>
                </td>
                <td><?= number_format($entry['amount'], 2, ',', '.') ?> €</td>
                <td><?= $entry['category'] ?></td>
                <td><?= date('d-m-Y', strtotime($entry['date'])) ?></td>
                <td><?= $entry['description'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3 class="mt-4">Quick Actions</h3>
    <div class="d-flex justify-content-center mb-4">
        <div class="btn-group">
            <a href="income.php" class="btn btn-primary mx-2">Add Income</a>
            <a href="expenses.php" class="btn btn-secondary mx-2">Add Expense</a>
            <a href="budget.php" class="btn btn-info mx-2">Manage Budget</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = <?php echo json_encode($chart_data); ?>;
    const categories = chartData.map(item => item.category);
    const budgetData = chartData.map(item => item.budget);
    const actualSpendingData = chartData.map(item => item.actual_spending);

    const ctx = document.getElementById('budgetVsSpendingChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: categories,
            datasets: [
                {
                    label: 'Budget',
                    data: budgetData,
                    backgroundColor: 'rgba(30, 144, 255, 0.8)',  
                    borderColor: 'rgba(30, 144, 255, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Actual Spending',
                    data: actualSpendingData,
                    backgroundColor: 'rgba(255, 99, 71, 0.8)',  
                    borderColor: 'rgba(255, 99, 71, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' €';
                        }
                    }
                },
                x: {
                    grid: {
                        display: true,
                        color: "rgba(0, 0, 0, 0.1)"
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Budget vs. Actual Spending'
                }
            }
        }
    });
});
</script>

<style>
.card-with-texture {
    background-image: url('https://www.transparenttextures.com/patterns/asfalt-dark.png'); 
    background-size: cover;
}
.chart-container {
    position: relative;
    margin: auto;
    height: 60vh;
    width: 100%;
}
</style>

<?php include 'templates/footer.php'; ?>
