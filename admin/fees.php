<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['save'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $total_fee  = floatval($_POST['total_fee']);
    $paid_fee   = floatval($_POST['paid_fee']);
    $due_fee    = $total_fee - $paid_fee;
    mysqli_query($conn, "INSERT INTO fees (student_id, total_fee, paid_fee, due_fee) VALUES ('$student_id', '$total_fee', '$paid_fee', '$due_fee')");
    header("Location: fees.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM fees WHERE id='$id'");
    header("Location: fees.php");
    exit();
}

if (isset($_POST['update'])) {
    $id         = mysqli_real_escape_string($conn, $_POST['id']);
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $total_fee  = floatval($_POST['total_fee']);
    $paid_fee   = floatval($_POST['paid_fee']);
    $due_fee    = $total_fee - $paid_fee;
    mysqli_query($conn, "UPDATE fees SET student_id='$student_id', total_fee='$total_fee', paid_fee='$paid_fee', due_fee='$due_fee' WHERE id='$id'");
    header("Location: fees.php");
    exit();
}

$edit = null;
if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM fees WHERE id='$id'");
    $edit = mysqli_fetch_assoc($res);
}

$studentData = mysqli_query($conn, "SELECT id, name FROM students ORDER BY name ASC");
$fees = mysqli_query($conn, "SELECT fees.*, students.name FROM fees JOIN students ON fees.student_id = students.id ORDER BY fees.id DESC");

$pageTitle = $edit ? 'Edit Fee' : 'Fees';
$pageSubtitle = 'Assign and track student fee records';
$activePage = 'fees';

include("includes/header.php");
?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="content-card">
            <div class="card-header-custom">
                <h3>
                    <i class="bi bi-<?= $edit ? 'pencil-square' : 'plus-circle' ?> text-primary"></i>
                    <?= $edit ? 'Update Fee' : 'Assign Fee' ?>
                </h3>
            </div>
            <div class="card-body-custom">
                <form method="POST">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($edit['id']) ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label-custom">Select Student</label>
                        <select name="student_id" class="form-select form-select-custom" required>
                            <option value="">-- Choose Student --</option>
                            <?php
                            mysqli_data_seek($studentData, 0);
                            while ($s = mysqli_fetch_assoc($studentData)):
                            ?>
                                <option value="<?= $s['id'] ?>" <?= ($edit && $edit['student_id'] == $s['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Total Fee (₹)</label>
                        <input type="number" step="0.01" name="total_fee" class="form-control form-control-custom" placeholder="0.00" required
                            value="<?= htmlspecialchars($edit['total_fee'] ?? '') ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Paid Amount (₹)</label>
                        <input type="number" step="0.01" name="paid_fee" class="form-control form-control-custom" placeholder="0.00" required
                            value="<?= htmlspecialchars($edit['paid_fee'] ?? '') ?>">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="<?= $edit ? 'update' : 'save' ?>"
                            class="btn btn-action btn-<?= $edit ? 'success' : 'indigo' ?>">
                            <i class="bi bi-<?= $edit ? 'check-lg' : 'save' ?> me-1"></i>
                            <?= $edit ? 'Update Fee' : 'Save Fee' ?>
                        </button>
                        <?php if ($edit): ?>
                            <a href="fees.php" class="btn btn-action btn-light text-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header-custom">
                <h3><i class="bi bi-journal-text text-primary"></i> Fee Ledger</h3>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($fees) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($fees)): ?>
                                    <tr>
                                        <td class="text-muted fw-bold">#<?= htmlspecialchars($row['id']) ?></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($row['name']) ?></td>
                                        <td class="text-primary fw-medium">₹<?= number_format($row['total_fee'], 2) ?></td>
                                        <td class="text-success fw-medium">₹<?= number_format($row['paid_fee'], 2) ?></td>
                                        <td class="fw-bold <?= $row['due_fee'] > 0 ? 'text-danger' : 'text-muted' ?>">
                                            ₹<?= number_format($row['due_fee'], 2) ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="?edit=<?= $row['id'] ?>" class="btn btn-table btn-outline-primary me-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-table btn-outline-danger"
                                                onclick="return confirm('Delete this fee record?')">
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
                                            No fee records yet.
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
