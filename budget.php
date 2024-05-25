<?php
include 'session.php';
include 'templates/header.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        // Handle deletion
        $delete_id = $_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM budgets WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $delete_id, $user_id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Budget entry deleted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        $category = $_POST['category'];
        if ($category == 'custom') {
            $category = $_POST['custom_category'];
        }
        $amount = $_POST['amount'];
        $month = $_POST['month'];
        $year = $_POST['year'];

        $stmt = $conn->prepare("INSERT INTO budgets (user_id, category, amount, month, year) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdii", $user_id, $category, $amount, $month, $year);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Budget added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}

$budget_entries = $conn->query("SELECT * FROM budgets WHERE user_id = $user_id ORDER BY year DESC, month DESC");
$default_categories = ['Rent', 'Utilities', 'Groceries', 'Transportation', 'Entertainment', 'Healthcare', 'Savings'];
$conn->close();
?>

<div class="container mt-4">
    <h2>Manage Budget</h2>
    <form action="budget.php" method="post" class="needs-validation" novalidate>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="category">Category:</label>
                <select id="category" name="category" class="form-control" onchange="toggleCustomCategory(this)" required>
                    <?php foreach ($default_categories as $category): ?>
                        <option value="<?= $category ?>"><?= $category ?></option>
                    <?php endforeach; ?>
                    <option value="custom">Custom</option>
                </select>
                <div class="invalid-feedback">
                    Please select a category.
                </div>
            </div>
            <div class="form-group col-md-4" id="customCategoryDiv" style="display:none;">
                <label for="custom_category">Custom Category:</label>
                <input type="text" id="custom_category" name="custom_category" class="form-control" placeholder="Custom Category">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="amount">Amount:</label>
                <input type="number" step="0.01" id="amount" name="amount" class="form-control" placeholder="Amount" required>
                <div class="invalid-feedback">
                    Please enter a valid amount.
                </div>
            </div>
            <div class="form-group col-md-2">
                <label for="month">Month:</label>
                <input type="number" id="month" name="month" class="form-control" min="1" max="12" required>
                <div class="invalid-feedback">
                    Please enter a valid month.
                </div>
            </div>
            <div class="form-group col-md-2">
                <label for="year">Year:</label>
                <input type="number" id="year" name="year" class="form-control" min="2020" required>
                <div class="invalid-feedback">
                    Please enter a valid year.
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add Budget</button>
    </form>

    <h3 class="mt-4">Budget Entries</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Category</th>
                <th>Amount</th>
                <th>Month</th>
                <th>Year</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($entry = $budget_entries->fetch_assoc()): ?>
            <tr>
                <td><?= $entry['category'] ?></td>
                <td><?= $entry['amount'] ?></td>
                <td><?= $entry['month'] ?></td>
                <td><?= $entry['year'] ?></td>
                <td>
                    <form action="budget.php" method="post" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?= $entry['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function toggleCustomCategory(select) {
    var customCategoryDiv = document.getElementById('customCategoryDiv');
    if (select.value == 'custom') {
        customCategoryDiv.style.display = 'block';
    } else {
        customCategoryDiv.style.display = 'none';
    }
}

(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

<?php include 'templates/footer.php'; ?>
