<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>budzetiraj.me</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="js/scripts.js" defer></script>
</head>
<body>
    <?php include 'templates/header.php'; ?>

    <header class="hero-section text-center text-white d-flex align-items-center justify-content-center">
        <div class="container">
            <h1 class="display-4">Take Control of Your Finances</h1>
            <p class="lead">Track your income, expenses, and budget easily with budzetiraj.me.</p>
            <a href="register.php" class="btn btn-primary btn-lg">Get Started</a>
            <a href="#features" class="btn btn-secondary btn-lg">Learn More</a>
        </div>
    </header>

    <div class="container">
        <section id="features" class="my-5">
            <h2 class="text-center">Features</h2>
            <div class="row text-center">
                <div class="col-md-3">
                    <i class="fas fa-wallet fa-3x mb-3"></i>
                    <h4>Track Income and Expenses</h4>
                    <p>Easily add and manage your financial transactions.</p>
                </div>
                <div class="col-md-3">
                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                    <h4>Budget Management</h4>
                    <p>Set budgets for different categories and monitor your spending.</p>
                </div>
                <div class="col-md-3">
                    <i class="fas fa-sync-alt fa-3x mb-3"></i>
                    <h4>Recurring Transactions</h4>
                    <p>Automate regular incomes and expenses.</p>
                </div>
                <div class="col-md-3">
                    <i class="fas fa-chart-pie fa-3x mb-3"></i>
                    <h4>Reports and Insights</h4>
                    <p>Gain insights into your spending habits with detailed reports and charts.</p>
                </div>
            </div>
        </section>

        <section id="how-it-works" class="my-5">
            <h2 class="text-center">How It Works</h2>
            <div class="row">
                <div class="col-md-3 text-center">
                    <i class="fas fa-user-plus fa-3x mb-3"></i>
                    <h4>Register</h4>
                    <p>Create an account to start tracking your finances.</p>
                </div>
                <div class="col-md-3 text-center">
                    <i class="fas fa-edit fa-3x mb-3"></i>
                    <h4>Add Transactions</h4>
                    <p>Log your income and expenses with ease.</p>
                </div>
                <div class="col-md-3 text-center">
                    <i class="fas fa-piggy-bank fa-3x mb-3"></i>
                    <h4>Set Budgets</h4>
                    <p>Define your budget categories and limits.</p>
                </div>
                <div class="col-md-3 text-center">
                    <i class="fas fa-chart-bar fa-3x mb-3"></i>
                    <h4>View Reports</h4>
                    <p>Analyze your financial data with comprehensive reports.</p>
                </div>
            </div>
        </section>

        <section id="testimonials" class="my-5">
            <h2 class="text-center">Reviews</h2>
            <div id="testimonialCarousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner text-center">
                    <div class="carousel-item active">
                        <p>"budzetiraj.me has transformed how I manage my finances. Highly recommended!" - User A</p>
                    </div>
                    <div class="carousel-item">
                        <p>"I love the easy-to-use interface and detailed reports. Fantastic tool!" - User B</p>
                    </div>
                </div>
                <a class="carousel-control-prev" href="#testimonialCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#testimonialCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </section>

        <section id="cta" class="text-center my-5">
            <h2>Ready to take control of your finances?</h2>
            <a href="register.php" class="btn btn-primary btn-lg">Sign Up Now</a>
        </section>
    </div>

    <?php include 'templates/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
