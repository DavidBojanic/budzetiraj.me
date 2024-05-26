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
        $delete_type = $_POST['delete_type'];

        if ($delete_type === 'regular') {
            $stmt = $conn->prepare("DELETE FROM income WHERE id = ? AND user_id = ?");
        } else {
            $stmt = $conn->prepare("DELETE FROM recurring_incomes WHERE id = ? AND user_id = ?");
        }

        $stmt->bind_param("ii", $delete_id, $user_id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success' id='success-message'>Income deleted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } elseif (isset($_POST['stop_id'])) {
        $stop_id = $_POST['stop_id'];
        $current_date = date('Y-m-d');

        $stmt = $conn->prepare("UPDATE recurring_incomes SET end_date = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $current_date, $stop_id, $user_id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success' id='success-message'>Recurring income stopped successfully!</div>";
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
        $is_recurring = isset($_POST['is_recurring']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $interval = $_POST['interval'];

        if ($is_recurring) {
            $stmt = $conn->prepare("INSERT INTO recurring_incomes (user_id, category, amount, start_date, end_date, `interval`, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssss", $user_id, $category, $amount, $start_date, $end_date, $interval, $description);
        } else {
            $stmt = $conn->prepare("INSERT INTO income (user_id, amount, category, date, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("idsss", $user_id, $amount, $category, $date, $description);
        }

        if ($stmt->execute()) {
            echo "<div class='alert alert-success' id='success-message'>Income added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}

$income_entries = $conn->query("SELECT id, amount, category, date, description, 'regular' AS type, NULL AS `interval`, NULL AS end_date FROM income WHERE user_id = $user_id
UNION
SELECT id, amount, category, start_date AS date, description, 'recurring' AS type, `interval`, end_date FROM recurring_incomes WHERE user_id = $user_id
ORDER BY date DESC");

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
                <input type="date" id="date" name="date" class="form-control">
                <div class="invalid-feedback">
                    Please enter a valid date.
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="description">Description:</label>
                <textarea id="description" name="description" class="form-control" placeholder="Description"></textarea>
            </div>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="is_recurring" name="is_recurring">
            <label class="form-check-label" for="is_recurring">Recurring</label>
        </div>
        <div id="recurringOptions" style="display:none;">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control">
                    <div class="invalid-feedback">
                        Please enter a valid start date.
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control">
                    <div class="invalid-feedback">
                        End date cannot be before start date.
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="interval">Interval:</label>
                    <select id="interval" name="interval" class="form-control">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
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
                <th>Type</th>
                <th>End Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($entry = $income_entries->fetch_assoc()): ?>
            <tr>
                <td><?= number_format($entry['amount'], 2, ',', '.') ?> €</td>
                <td><?= $entry['category'] ?></td>
                <td><?= date('d-m-Y', strtotime($entry['date'])) ?></td>
                <td><?= $entry['description'] ?></td>
                <td><?= ucfirst($entry['type']) . ($entry['type'] == 'recurring' ? " ({$entry['interval']})" : '') ?></td>
                <td><?= $entry['type'] == 'recurring' && $entry['end_date'] ? date('d-m-Y', strtotime($entry['end_date'])) : '-' ?></td>
                <td>
                    <form action="income.php" method="post" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?= $entry['id'] ?>">
                        <input type="hidden" name="delete_type" value="<?= $entry['type'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    <?php if ($entry['type'] == 'recurring'): ?>
                        <?php if ($entry['end_date'] && new DateTime($entry['end_date']) < new DateTime()): ?>
                            <button type="button" class="btn btn-secondary btn-sm" disabled>Ended</button>
                        <?php else: ?>
                            <form action="income.php" method="post" style="display:inline;">
                                <input type="hidden" name="stop_id" value="<?= $entry['id'] ?>">
                                <button type="submit" class="btn btn-warning btn-sm">Stop</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
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

document.getElementById('is_recurring').addEventListener('change', function() {
    var recurringOptions = document.getElementById('recurringOptions');
    var dateField = document.getElementById('date');
    var startDateField = document.getElementById('start_date');
    if (this.checked) {
        recurringOptions.style.display = 'block';
        startDateField.value = dateField.value;  
        dateField.required = false;  
    } else {
        recurringOptions.style.display = 'none';
        dateField.required = true;  
    }
});

document.getElementById('start_date').addEventListener('change', function() {
    var startDate = this.value;
    var endDateField = document.getElementById('end_date');
    var endDate = endDateField.value;
    if (new Date(endDate) < new Date(startDate)) {
        endDateField.setCustomValidity("End date cannot be before start date.");
    } else {
        endDateField.setCustomValidity("");
    }
});

document.getElementById('end_date').addEventListener('change', function() {
    var startDate = document.getElementById('start_date').value;
    var endDate = this.value;
    if (new Date(endDate) < new Date(startDate)) {
        this.setCustomValidity("End date cannot be before start date.");
    } else {
        this.setCustomValidity("");
    }
});

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

window.setTimeout(function() {
    var successMessage = document.getElementById('success-message');
    if (successMessage) {
        successMessage.style.display = 'none';
    }
}, 3000);
</script>

<?php include 'templates/footer.php'; ?>
