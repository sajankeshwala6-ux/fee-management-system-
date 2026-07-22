<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$payment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($payment_id > 0) {
    $query = "
        SELECT 
            payments.*,
            students.name, students.email, students.mobile, students.course,
            fees.total_fee, fees.paid_fee, fees.due_fee
        FROM payments
        JOIN students ON payments.student_id = students.id
        LEFT JOIN fees ON fees.student_id = students.id
        WHERE payments.id = ?
        LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $payment_id);
} else {
    $query = "
        SELECT 
            payments.*,
            students.name, students.email, students.mobile, students.course,
            fees.total_fee, fees.paid_fee, fees.due_fee
        FROM payments
        JOIN students ON payments.student_id = students.id
        LEFT JOIN fees ON fees.student_id = students.id
        ORDER BY payments.id DESC
        LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Receipt not found.");
}

$pageTitle = 'Receipt #' . $row['id'];
$pageSubtitle = 'Payment receipt for ' . $row['name'];
$activePage = 'receipts';

include("includes/header.php");
?>

<div class="d-flex justify-content-center">
    <div class="card receipt-card shadow-sm w-100 bg-white">
        <div class="receipt-accent"></div>

        <div class="card-body p-4 p-sm-5">
            <div class="text-center mb-4 receipt-header">
                <div class="login-brand mx-auto mb-3" style="width:48px;height:48px;font-size:1.2rem;">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <h1>British Accreditation Council</h1>
                <p class="receipt-meta mb-0">
                    Receipt No: <strong class="text-dark">RCPT-<?= htmlspecialchars($row['id']) ?></strong>
                    <span class="mx-2 text-muted">|</span>
                    Date: <strong class="text-dark"><?= date("d M Y", strtotime($row['payment_date'])) ?></strong>
                </p>
            </div>

            <hr class="text-muted my-4">

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <p class="mb-1"><span class="fw-semibold text-dark">Student:</span> <?= htmlspecialchars($row['name']) ?></p>
                    <p class="mb-1"><span class="fw-semibold text-dark">Email:</span> <?= htmlspecialchars($row['email']) ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1"><span class="fw-semibold text-dark">Mobile:</span> <?= htmlspecialchars($row['mobile']) ?></p>
                    <p class="mb-1"><span class="fw-semibold text-dark">Course:</span> <span class="badge-course"><?= htmlspecialchars($row['course']) ?></span></p>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered receipt-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Total Fee</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-medium">Tuition & Academic Fees</td>
                            <td class="text-end text-muted">₹<?= number_format($row['total_fee'], 2) ?></td>
                            <td class="text-end text-success">₹<?= number_format($row['paid_fee'], 2) ?></td>
                            <td class="text-end fw-bold text-danger">₹<?= number_format($row['due_fee'], 2) ?></td>
                        </tr>
                        <tr class="table-light fw-bold">
                            <td>This Payment</td>
                            <td class="text-end" colspan="3">
                                <span class="text-success fs-5">₹<?= number_format($row['amount'], 2) ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row g-4 pt-2">
                <div class="col-md-7">
                    <h5 class="h6 fw-bold mb-2">Terms & Conditions</h5>
                    <p class="small text-secondary mb-0">
                        All fee payments are non-refundable. For any discrepancies, please contact the administration office.
                    </p>
                </div>
                <div class="col-md-5 text-md-end">
                    <h5 class="h6 fw-bold mb-2">Transaction Details</h5>
                    <p class="small text-secondary mb-1">
                        Method: <span class="badge-method"><?= htmlspecialchars($row['payment_method']) ?></span>
                    </p>
                    <p class="small text-secondary mb-0">
                        Reference: <strong>TXN-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT) ?></strong>
                    </p>
                </div>
            </div>

            <div class="text-center d-print-none mt-5 pt-3 border-top">
                <button onclick="window.print()" class="btn btn-action btn-indigo me-2">
                    <i class="bi bi-printer me-1"></i> Print Receipt
                </button>
                <a href="payments.php" class="btn btn-action btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Payments
                </a>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>