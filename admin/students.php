<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['add'])) {
    $name   = mysqli_real_escape_string($conn, $_POST['name']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    mysqli_query($conn, "INSERT INTO students (name, email, mobile, course) VALUES ('$name', '$email', '$mobile', '$course')");
    header("Location: students.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM students WHERE id='$id'");
    header("Location: students.php");
    exit();
}

if (isset($_POST['update'])) {
    $id     = mysqli_real_escape_string($conn, $_POST['id']);
    $name   = mysqli_real_escape_string($conn, $_POST['name']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    mysqli_query($conn, "UPDATE students SET name='$name', email='$email', mobile='$mobile', course='$course' WHERE id='$id'");
    header("Location: students.php");
    exit();
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM students WHERE id='$id'");
    $editData = mysqli_fetch_assoc($res);
}

$students = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");

$pageTitle = $editData ? 'Edit Student' : 'Students';
$pageSubtitle = 'Register and manage student records';
$activePage = 'students';

include("includes/header.php");
?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="content-card">
            <div class="card-header-custom">
                <h3>
                    <i class="bi bi-<?= $editData ? 'pencil-square' : 'person-plus-fill' ?> text-primary"></i>
                    <?= $editData ? 'Edit Student' : 'Add New Student' ?>
                </h3>
            </div>
            <div class="card-body-custom">
                <form method="POST">
                    <?php if ($editData): ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label-custom">Student Name</label>
                        <input type="text" name="name" class="form-control form-control-custom" placeholder="John Doe" required
                            value="<?= htmlspecialchars($editData['name'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Email Address</label>
                        <input type="email" name="email" class="form-control form-control-custom" placeholder="john@example.com" required
                            value="<?= htmlspecialchars($editData['email'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Mobile Number</label>
                        <input type="text" name="mobile" class="form-control form-control-custom" placeholder="9876543210" required
                            value="<?= htmlspecialchars($editData['mobile'] ?? '') ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Course</label>
                        <input type="text" name="course" class="form-control form-control-custom" placeholder="B.Tech Computer Science" required
                            value="<?= htmlspecialchars($editData['course'] ?? '') ?>">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="<?= $editData ? 'update' : 'add' ?>"
                            class="btn btn-action btn-<?= $editData ? 'success' : 'indigo' ?>">
                            <i class="bi bi-<?= $editData ? 'check-lg' : 'plus-lg' ?> me-1"></i>
                            <?= $editData ? 'Update Student' : 'Save Student' ?>
                        </button>
                        <?php if ($editData): ?>
                            <a href="students.php" class="btn btn-action btn-light text-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="content-card">
            <div class="card-header-custom">
                <h3><i class="bi bi-people text-primary"></i> All Students</h3>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                    <?= mysqli_num_rows($students) ?> Records
                </span>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Course</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($students) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($students)): ?>
                                    <tr>
                                        <td class="text-muted fw-bold">#<?= htmlspecialchars($row['id']) ?></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($row['name']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['mobile']) ?></td>
                                        <td><span class="badge-course"><?= htmlspecialchars($row['course']) ?></span></td>
                                        <td class="text-center">
                                            <a href="?edit=<?= $row['id'] ?>" class="btn btn-table btn-outline-primary me-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-table btn-outline-danger"
                                                onclick="return confirm('Delete this student record?')">
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
                                            No students found. Add your first student.
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
