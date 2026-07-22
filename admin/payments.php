<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['pay'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $amount     = floatval($_POST['amount']);
    $method     = mysqli_real_escape_string($conn, $_POST['method']);
    $date       = date("Y-m-d");

    $fee_res = mysqli_query($conn, "SELECT * FROM fees WHERE student_id='$student_id'");

    if ($row = mysqli_fetch_assoc($fee_res)) {
        $total   = floatval($row['total_fee']);
        $paid    = floatval($row['paid_fee']);
        $newPaid = $paid + $amount;
        $due     = $total - $newPaid;

        mysqli_query($conn, "UPDATE fees SET paid_fee='$newPaid', due_fee='$due' WHERE student_id='$student_id'");
        mysqli_query($conn, "INSERT INTO payments (student_id, amount, payment_method, payment_date) VALUES ('$student_id', '$amount', '$method', '$date')");
    }

    header("Location: payments.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM payments WHERE id='$id'");
    header("Location: payments.php");
    exit();
}

$students = mysqli_query($conn, "SELECT id, name FROM students ORDER BY name ASC");
$list = mysqli_query($conn, "SELECT payments.*, students.name FROM payments JOIN students ON payments.student_id = students.id ORDER BY payments.id DESC");

$pageTitle = 'Payments';
$pageSubtitle = 'Record and view payment transactions';
$activePage = 'payments';

include("includes/header.php");
?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="content-card">
            <div class="card-header-custom">
                <h3><i class="bi bi-credit-card text-primary"></i> New Payment</h3>
            </div>
            <div class="card-body-custom">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label-custom">Select Student</label>
                        <select name="student_id" class="form-select form-select-custom" required>
                            <option value="">-- Choose Student --</option>
                            <?php while ($s = mysqli_fetch_assoc($students)): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Amount (₹)</label>
                        <input type="number" step="0.01" name="amount" class="form-control form-control-custom" placeholder="0.00" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Payment Method</label>
                        <select name="method" class="form-select form-select-custom" required>
                            <option value="">-- Select Method --</option>
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="Card">Credit/Debit Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="pay" class="btn btn-action btn-indigo">
                            <i class="bi bi-check-circle me-1"></i> Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header-custom">
                <h3><i class="bi bi-clock-history text-primary"></i> Payment History</h3>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($list) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($list)): ?>
                                    <tr>
                                        <td class="text-muted fw-bold">#<?= htmlspecialchars($row['id']) ?></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($row['name']) ?></td>
                                        <td class="text-success fw-bold">₹<?= number_format($row['amount'], 2) ?></td>
                                        <td><span class="badge-method"><?= htmlspecialchars($row['payment_method']) ?></span></td>
                                        <td class="text-secondary"><?= htmlspecialchars($row['payment_date']) ?></td>
                                        <td class="text-center">
                                            <a href="receipts.php?id=<?= $row['id'] ?>" class="btn btn-table btn-outline-primary me-1" title="View Receipt">
                                                <i class="bi bi-receipt"></i>
                                            </a>
                                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-table btn-outline-danger"
                                                onclick="return confirm('Delete this payment record?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox"></i>
                                            No payments recorded yet.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
