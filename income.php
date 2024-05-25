<?php
include 'session.php';
include 'templates/header.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$default_categories = ['Salary', 'Freelance', 'Investments', 'Gifts', 'Other'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM income WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $delete_id, $user_id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Income deleted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        $amount = $_POST['amount'];
        $category = $_POST['category'];
        if ($category == 'custom') {
            $category = $_POST['custom_category'];
        }
        $date = $_POST['date'];
        $description = $_POST['description'];

        $stmt = $conn->prepare("INSERT INTO income (user_id, amount, category, date, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("idsss", $user_id, $amount, $category, $date, $description);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Income added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}

$income_entries = $conn->query("SELECT * FROM income WHERE user_id = $user_id ORDER BY date DESC");

$conn->close();
?>

<div class="container mt-4">
    <h2>Add Income</h2>
    <form action="income.php" method="post" class="needs-validation" novalidate>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="amount">Amount:</label>
                <input type="number" step="0.01" id="amount" name="amount" class="form-control" placeholder="Amount" required>
                <div class="invalid-feedback">
                    Please enter a valid amount.
                </div>
            </div>
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
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" class="form-control" required>
                <div class="invalid-feedback">
                    Please enter a valid date.
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="description">Description:</label>
                <textarea id="description" name="description" class="form-control" placeholder="Description"></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add Income</button>
    </form>

    <h3 class="mt-4">Income Entries</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Amount</th>
                <th>Category</th>
                <th>Date</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($entry = $income_entries->fetch_assoc()): ?>
            <tr>
                <td><?= $entry['amount'] ?></td>
                <td><?= $entry['category'] ?></td>
                <td><?= $entry['date'] ?></td>
                <td><?= $entry['description'] ?></td>
                <td>
                    <form action="income.php" method="post" style="display:inline;">
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
