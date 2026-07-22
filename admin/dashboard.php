<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$students = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students");
$totalStudents = mysqli_fetch_assoc($students)['total'] ?? 0;

$fee_query = mysqli_query($conn, "SELECT SUM(total_fee) AS total, SUM(paid_fee) AS paid, SUM(due_fee) AS due FROM fees");
$fee_data = mysqli_fetch_assoc($fee_query);

$totalFee  = $fee_data['total'] ?? 0;
$totalPaid = $fee_data['paid'] ?? 0;
$totalDue  = $fee_data['due'] ?? 0;

$pageTitle = 'Dashboard';
$pageSubtitle = 'Overview of students and fee collection';
$activePage = 'dashboard';

include("includes/header.php");
?>

<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">Total Students</div>
            <div class="stat-value"><?= $totalStudents ?></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-label">Total Fee</div>
            <div class="stat-value">₹<?= number_format($totalFee, 2) ?></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-label">Collected Fee</div>
            <div class="stat-value text-success">₹<?= number_format($totalPaid, 2) ?></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon red"><i class="bi bi-exclamation-circle-fill"></i></div>
            <div class="stat-label">Pending Fee</div>
            <div class="stat-value text-danger">₹<?= number_format($totalDue, 2) ?></div>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="card-header-custom">
        <h3><i class="bi bi-table text-primary"></i> Recent Summary</h3>
    </div>
    <div class="card-body-custom p-0">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Students</th>
                        <th>Total Fee</th>
                        <th>Paid</th>
                        <th>Due</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-semibold"><?= $totalStudents ?></td>
                        <td class="fw-bold text-primary">₹<?= number_format($totalFee, 2) ?></td>
                        <td class="fw-bold text-success">₹<?= number_format($totalPaid, 2) ?></td>
                        <td class="fw-bold text-danger">₹<?= number_format($totalDue, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
