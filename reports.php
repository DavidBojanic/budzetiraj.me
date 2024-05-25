<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php include 'templates/header.php'; ?>

<h2>Expense Breakdown by Category</h2>
<div class="chart-container" style="position: relative; height:40vh; width:40vw">
    <canvas id="expenseChart"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
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
                    maintainAspectRatio: false, 
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Expense Breakdown by Category'
                        }
                    }
                }
            });
        });
});
</script>

<?php include 'templates/footer.php'; ?>
