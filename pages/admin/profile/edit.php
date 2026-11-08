<?php
require_once __DIR__ . "/../../../repositories/user-repository.php";
require_once __DIR__ . "/../../../middleware/auth.php";
requireRole(["admin"]);

$loggedInUser = $_SESSION["user"] ?? '';
$userId = $loggedInUser['id'] ?? $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;

if (!$userId) {
    header('location: ../auth/login.php');
    exit;
}

// Ambil data user dari database
$user = getProfile($userId);

if (!$user) {
    echo "Data user tidak ditemukan.";
    exit;
}

// Jika ada error validasi sebelumnya, gunakan data session 'old'. 
// Jika tidak ada error (setelah berhasil save atau baru dibuka), gunakan data terbaru dari database ($user).
$old = $_SESSION['old'] ?? $user;
$errors = $_SESSION['errors'] ?? [];

// Bersihkan session error & old setelah diambil
unset($_SESSION['errors'], $_SESSION['old']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub Admin - Edit Profile</title>

    <!-- Font & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="../../../styles/base.css">
    <link rel="stylesheet" href="../../../styles/admin/sidebar.css">
    <link rel="stylesheet" href="../../../styles/admin/topbar.css">
    <link rel="stylesheet" href="../../../styles/admin/products.css">
    <link rel="stylesheet" href="../../../styles/admin/profile.css">
    <link rel="stylesheet" href="../../../styles/admin/pagination.css">
</head>

<body>

    <!-- Topbar -->
    <?php require_once __DIR__ . "/../../../components/admin/topbar.php" ?>

    <div class="admin-container">
        <!-- Sidebar -->
        <?php require_once __DIR__ . "/../../../components/admin/sidebar.php" ?>

        <main class="main-content">
            <div class="page-header" style="margin-bottom: 25px;">
                <h1>Edit Profile</h1>
                <p>Update your personal information and account details.</p>
            </div>


            <!-- Form Edit Profile -->
            <form action="../../../actions/profile/update.php" method="POST" enctype="multipart/form-data" class="product-form-create">

                <div class="profile-form-section" style="background: var(--white); border: 1.5px solid var(--primary-blue); border-radius: 12px; padding: 30px; display: flex; flex-direction: column; gap: 20px;">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input value="<?= htmlspecialchars($old['name'] ?? ''); ?>" type="text" id="name" name="name" placeholder="Enter your name">
                            <?php if (isset($errors['name'])) : ?>
                                <p style="font-size: 12px; margin-top: 4px; color: red;">
                                    <?= $errors['name'] ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="name">Role</label>
                            <input value="<?= htmlspecialchars($user['role'] ?? ''); ?>" type="text" id="role" name="role" readonly style="background-color: #f1f5f9; cursor: not-allowed; color: var(--text-muted);">
                            <?php if (isset($errors['role'])) : ?>
                                <p style="font-size: 12px; margin-top: 4px; color: red;">
                                    <?= $errors['role'] ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input value="<?= htmlspecialchars($user['email'] ?? ''); ?>" type="email" id="email" name="email" readonly style="background-color: #f1f5f9; cursor: not-allowed; color: var(--text-muted);">
                        <?php if (isset($errors['email'])) : ?>
                            <p style="font-size: 12px; margin-top: 4px; color: red;">
                                <?= $errors['email'] ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input value="<?= htmlspecialchars($old['phone_number'] ?? ''); ?>" type="text" id="phone_number" name="phone_number" placeholder="Enter your phone number">
                            <?php if (isset($errors['phone_number'])) : ?>
                                <p style="font-size: 12px; margin-top: 4px; color: red;">
                                    <?= $errors['phone_number'] ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <input value="<?= htmlspecialchars($old['address'] ?? ''); ?>" type="text" id="address" name="address" placeholder="Enter your address">
                            <?php if (isset($errors['address'])) : ?>
                                <p style="font-size: 12px; margin-top: 4px; color: red;">
                                    <?= $errors['address'] ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="5" placeholder="Write your bio..."><?= htmlspecialchars($old['bio'] ?? ''); ?></textarea>
                        <?php if (isset($errors['bio'])) : ?>
                            <p style="font-size: 12px; margin-top: 4px; color: red;">
                                <?= $errors['bio'] ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tombol Aksi di Bawah Form -->
                <div class="form-actions-center" style="display: flex; justify-content: center; gap: 20px; margin-top: 30px;">
                    <button name="update" type="submit" class="btn-submit-add">Save Changes</button>
                    <a href="edit.php" class="btn-cancel">Cancel</a>
                </div>

            </form>
        </main>
    </div>

</body>

</html>