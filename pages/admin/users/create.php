<?php
require_once __DIR__ . "/../../../repositories/user-repository.php";
require_once __DIR__ . "/../../../middleware/auth.php";
requireRole(["admin"]);

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub Admin - Add New Product</title>

    <!-- Font & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../../styles/base.css">
    <link rel="stylesheet" href="../../../styles/admin/sidebar.css">
    <link rel="stylesheet" href="../../../styles/admin/topbar.css">
    <link rel="stylesheet" href="../../../styles/admin/products.css">
</head>

<body>
    <!-- Topbar -->
    <?php require_once __DIR__ . "/../../../components/admin/topbar.php" ?>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php require_once __DIR__ . "/../../../components/admin/sidebar.php" ?>
        <main class="main-content">
            <div class="page-header-flex" style="margin-bottom: 20px;">
                <div class="header-col">
                    <h1>Add New User</h1>
                    <p>Add a new user account to your system</p>
                </div>
                <div>
                    <a href="index.php" class="back-link" style="padding: 10px 10px;">
                        Back <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <form action="../../../actions/users/store.php" method="POST" enctype="multipart/form-data" class="product-form-create">
                <div class="form-group full-width">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input value="<?= $old['name'] ?? '' ?>" type="text" id="name" name="name" placeholder="Enter name">
                        <?php if (isset($errors['name'])) : ?>
                            <p style="font-size: 12px; margin-top: 4px; color: red;">
                                <?= $errors['name'] ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group full-width">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input value="<?= $old['email'] ?? '' ?>" type="text" id="email" name="email" placeholder="Enter email">
                        <?php if (isset($errors['email'])) : ?>
                            <p style="font-size: 12px; margin-top: 4px; color: red;">
                                <?= $errors['email'] ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group full-width">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input value="<?= $old['password'] ?? '' ?>" type="password" id="password" name="password" placeholder="Enter password">
                        <?php if (isset($errors['password'])) : ?>
                            <p style="font-size: 12px; margin-top: 4px; color: red;">
                                <?= $errors['password'] ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group full-width">
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role">
                            <option value="admin" <?= (isset($old['role']) && $old['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                            <option value="customer" <?= (isset($old['role']) && $old['role'] === 'customer') ? 'selected' : '' ?>>Customer</option>
                        </select>
                        <?php if (isset($errors['role'])) : ?>
                            <p style="font-size: 12px; margin-top: 4px; color: red;">
                                <?= $errors['role'] ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <div class="form-actions-center" style="margin-top: 25px;">
                    <button name="store" type="submit" class="btn-submit-add">Add User</button>
                    <a href="index.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>