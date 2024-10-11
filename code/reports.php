<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'templates/header.php'; ?>

<div class="container">
    <h2>Reports</h2>

    <div class="chart-row">
        <div class="chart-half">
            <div class="chart-section">
                <h3>Expense Breakdown by Category</h3>
                <div class="chart-container">
                    <canvas id="expenseChart"></canvas>
                </div>
            </div>
        </div>
        <div class="chart-half">
            <div class="chart-section">
                <h3>Monthly Trends</h3>
                <div class="chart-container">
                    <canvas id="monthlyTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <h3>Comparative Reports</h3>
        <form id="comparisonForm" class="comparative-form">
            <label for="startPeriod">Start Period:</label>
            <input type="month" id="startPeriod" name="startPeriod" required>
            <label for="endPeriod">End Period:</label>
            <input type="month" id="endPeriod" name="endPeriod" required>
            <button type="submit">Compare</button>
        </form>
        <div class="center-chart-container">
            <div class="chart-container">
                <canvas id="comparativeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let comparativeChartInstance;

    // Expense Breakdown by Category
    fetch('fetch_expense_data.php')
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.category);
            const amounts = data.map(item => item.total_amount);

            const ctx = document.getElementById('expenseChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: amounts,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(255, 159, 64, 0.6)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Expense Breakdown by Category'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    return `${label}: ${value} €`;
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching expense data:', error));

    // Monthly Trends
    fetch('fetch_monthly_trends.php')
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.month);
            const incomeData = data.map(item => item.income);
            const expenseData = data.map(item => item.expense);

            const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Income',
                            data: incomeData,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            fill: true
                        },
                        {
                            label: 'Expenses',
                            data: expenseData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Monthly Trends'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.raw || 0;
                                    return `${label}: ${value} €`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' €';
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching monthly trends data:', error));

    // Comparative Reports
    document.getElementById('comparisonForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const startPeriod = document.getElementById('startPeriod').value;
        const endPeriod = document.getElementById('endPeriod').value;

        fetch(`fetch_comparative_data.php?startPeriod=${startPeriod}&endPeriod=${endPeriod}`)
            .then(response => response.json())
            .then(data => {
                if (comparativeChartInstance) {
                    comparativeChartInstance.destroy();
                }

                const labels = data.map(item => item.period);
                const incomeData = data.map(item => item.income);
                const expenseData = data.map(item => item.expense);

                const ctx = document.getElementById('comparativeChart').getContext('2d');
                comparativeChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Income',
                                data: incomeData,
                                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Expenses',
                                data: expenseData,
                                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Comparative Reports'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.dataset.label || '';
                                        const value = context.raw || 0;
                                        return `${label}: ${value} €`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value + ' €';
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching comparative data:', error));
    });
});
</script>

<?php include 'templates/footer.php'; ?>
</body>
</html>
