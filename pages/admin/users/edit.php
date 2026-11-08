<?php
require_once __DIR__ . "/../../../repositories/user-repository.php";
require_once __DIR__ . "/../../../middleware/auth.php";
requireRole(["admin"]);


$id = $_GET['id'] ?? null;

if (!$id) {
    header('location: index.php');
    exit;
}

$user = getUser($id);

if (!$user) {
    header('location: index.php');
    exit;
}

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

// Menggabungkan data dari database dengan inputan lama jika ada validasi yang gagal
$user = array_merge($user, $old);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub Admin - Edit User</title>

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
                    <h1>Edit User</h1>
                    <p>Update user information and account details</p>
                </div>
                <div>
                    <a href="index.php" class="back-link" style="padding: 10px 10px;">
                        Back <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <form action="../../../actions/users/update.php" method="POST" class="product-form-create">
                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id'] ?? '') ?>">
                <div class="form-group full-width">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input value="<?= htmlspecialchars($user['name'] ?? '') ?>" type="text" id="name" name="name" placeholder="Enter name">
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
                        <input value="<?= htmlspecialchars($user['email'] ?? '') ?>" type="email" id="email" name="email" placeholder="Enter email">
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
                        <input type="password" id="password" name="password" placeholder="Leave blank if you don't want to change password">
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
                            <option value="admin" <?= (isset($user['role']) && $user['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                            <option value="customer" <?= (isset($user['role']) && $user['role'] === 'customer') ? 'selected' : '' ?>>Customer</option>
                        </select>
                        <?php if (isset($errors['role'])) : ?>
                            <p style="font-size: 12px; margin-top: 4px; color: red;">
                                <?= $errors['role'] ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div> 
                <div class="form-actions-center" style="margin-top: 25px;">
                    <button name="update" type="submit" class="btn-submit-add">Save Changes</button>
                    <a href="index.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </main>
    </div>
</body>

</html>