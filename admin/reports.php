<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$summary_query = "
    SELECT 
        (SELECT COUNT(*) FROM students) AS total_students,
        SUM(total_fee) AS total_fee,
        SUM(paid_fee) AS total_paid,
        SUM(due_fee) AS total_due
    FROM fees";
$summary_result = mysqli_query($conn, $summary_query);
$summary = mysqli_fetch_assoc($summary_result);

$totalStudents = $summary['total_students'] ?? 0;
$totalFee      = $summary['total_fee'] ?? 0;
$totalPaid     = $summary['total_paid'] ?? 0;
$totalDue      = $summary['total_due'] ?? 0;

$report = mysqli_query($conn, "
    SELECT 
        students.name,
        students.course,
        fees.total_fee,
        fees.paid_fee,
        fees.due_fee
    FROM students
    LEFT JOIN fees ON students.id = fees.student_id
    ORDER BY students.name ASC
");

$pageTitle = 'Reports';
$pageSubtitle = 'Financial summary and student balances';
$activePage = 'reports';

include("includes/header.php");
?>

<div class="d-flex justify-content-end mb-4 d-print-none">
    <button onclick="window.print()" class="btn btn-action btn-success me-2">
        <i class="bi bi-printer me-1"></i> Print Report
    </button>
</div>

<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card-gradient blue">
            <div class="stat-label">Students Enrolled</div>
            <div class="stat-value"><?= $totalStudents ?></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card-gradient dark">
            <div class="stat-label">Total Fee</div>
            <div class="stat-value">₹<?= number_format($totalFee, 2) ?></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card-gradient green">
            <div class="stat-label">Collected</div>
            <div class="stat-value">₹<?= number_format($totalPaid, 2) ?></div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card-gradient red">
            <div class="stat-label">Outstanding</div>
            <div class="stat-value">₹<?= number_format($totalDue, 2) ?></div>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="card-header-custom">
        <h3><i class="bi bi-file-earmark-bar-graph text-primary"></i> Student Account Balances</h3>
    </div>
    <div class="card-body-custom p-0">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Total Fee</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($report) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($report)):
                            $isClear = (($row['due_fee'] ?? 0) <= 0 && ($row['total_fee'] ?? 0) > 0);
                        ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($row['name']) ?></td>
                                <td><span class="badge-course"><?= htmlspecialchars($row['course'] ?? 'Unassigned') ?></span></td>
                                <td class="text-muted">₹<?= number_format($row['total_fee'] ?? 0, 2) ?></td>
                                <td class="text-success">₹<?= number_format($row['paid_fee'] ?? 0, 2) ?></td>
                                <td class="fw-bold <?= ($row['due_fee'] ?? 0) > 0 ? 'text-danger' : 'text-muted' ?>">
                                    ₹<?= number_format($row['due_fee'] ?? 0, 2) ?>
                                </td>
                                <td class="text-center">
                                    <span class="<?= $isClear ? 'badge-status-settled' : 'badge-status-pending' ?>">
                                        <?= $isClear ? 'Settled' : 'Pending' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    No report data available.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
