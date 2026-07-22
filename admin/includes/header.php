<?php
if (!isset($pageTitle)) $pageTitle = 'Admin Panel';
if (!isset($activePage)) $activePage = '';
if (!isset($pageSubtitle)) $pageSubtitle = 'Fee Management System';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Fee Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h2>
                <span class="brand-icon"><i class="bi bi-mortarboard-fill"></i></span>
                Fee System
            </h2>
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="students.php" class="<?= $activePage === 'students' ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i> Students
            </a>
            <a href="fees.php" class="<?= $activePage === 'fees' ? 'active' : '' ?>">
                <i class="bi bi-cash-stack"></i> Fees
            </a>
            <a href="payments.php" class="<?= $activePage === 'payments' ? 'active' : '' ?>">
                <i class="bi bi-credit-card-fill"></i> Payments
            </a>
            <a href="reports.php" class="<?= $activePage === 'reports' ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-fill"></i> Reports
            </a>
            <a href="receipts.php" class="<?= $activePage === 'receipts' ? 'active' : '' ?>">
                <i class="bi bi-receipt"></i> Receipts
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-info">
                Signed in as
                <strong><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></strong>
            </div>
            <a href="../logout.php" class="btn btn-sm btn-outline-light w-100">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>
    </aside>

    <div class="main-content">
        <header class="top-bar">
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle menu">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <h1><?= htmlspecialchars($pageTitle) ?></h1>
                    <p class="page-subtitle"><?= htmlspecialchars($pageSubtitle) ?></p>
                </div>
            </div>
            <div class="d-none d-md-flex align-items-center gap-2">
                <span class="badge rounded-pill text-bg-light border px-3 py-2">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>
                </span>
            </div>
        </header>

        <main class="page-body">
