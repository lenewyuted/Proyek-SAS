<?php
require_once __DIR__ . "/../../middleware/auth.php";
require_once __DIR__ . "/../../repositories/user-repository.php";

$user = getUser(getAuthUserId());

// Jika data admin tidak ditemukan, database belum diimport
if (!$user) {
    die('Data admin tidak ditemukan. Import database/techhub.sql terlebih dahulu.');
}

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
$success = $_SESSION['success'] ?? '';

unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['success']);

$user = array_merge($user, $old);

// Foto profil: pakai foto pengguna jika ada, kalau tidak pakai avatar default
$uploadDir = __DIR__ . "/../../asset/uploads/";
$photoUrl = (!empty($user['photo']) && is_file($uploadDir . $user['photo']))
    ? '../../asset/uploads/' . rawurlencode($user['photo'])
    : '../../asset/images/avatar-default.svg';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - TechHub</title>

    <!-- Font & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../asset/css/admin.css">
</head>

<body>
    <!-- Topbar -->
    <?php require_once __DIR__ . "/../../components/admin/topbar.php" ?>
    <div class="admin-layout">
        <!-- Sidebar -->
        <?php $activePage = 'profile'; ?>
        <?php require_once __DIR__ . "/../../components/admin/sidebar.php" ?>
        <main>
            <h1 class="page-title">Edit Profile</h1>
            <p class="page-sub">Update your profile information and photo.</p>

            <?php if ($success !== ''): ?>
                <div class="alert ok"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form action="../../actions/profile/update.php" method="POST" enctype="multipart/form-data" id="profileForm">
                <div class="profile-grid">
                    <div>
                        <div class="field">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" placeholder="Enter full name">
                            <?php if (isset($errors['full_name'])) : ?>
                                <p style="font-size: 12px; margin-top: 4px; color: red;">
                                    <?= $errors['full_name'] ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="field">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" placeholder="Enter username">
                            <?php if (isset($errors['username'])) : ?>
                                <p style="font-size: 12px; margin-top: 4px; color: red;">
                                    <?= $errors['username'] ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="field">
                            <label for="email">Email Address</label>
                            <input type="text" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="Enter email address">
                            <?php if (isset($errors['email'])) : ?>
                                <p style="font-size: 12px; margin-top: 4px; color: red;">
                                    <?= $errors['email'] ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="row-2">
                            <div class="field">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="Enter phone number">
                                <?php if (isset($errors['phone'])) : ?>
                                    <p style="font-size: 12px; margin-top: 4px; color: red;">
                                        <?= $errors['phone'] ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="field">
                                <label for="address">Address</label>
                                <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>" placeholder="Enter address">
                                <?php if (isset($errors['address'])) : ?>
                                    <p style="font-size: 12px; margin-top: 4px; color: red;">
                                        <?= $errors['address'] ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="field">
                            <label for="bio">Bio</label>
                            <textarea id="bio" name="bio" placeholder="Write your bio..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        </div>
                        <button name="update" type="submit" class="btn-save">Save Changes</button>
                    </div>

                    <!-- Profile Picture -->
                    <div class="photo-card">
                        <h3>Profile Picture</h3>
                        <div class="avatar"><img id="avatarPreview" src="<?= $photoUrl ?>" alt="Profile Picture"></div>
                        <input type="file" id="photoInput" name="photo" accept="image/png,image/jpeg,image/webp">
                        <?php if (isset($errors['photo'])) : ?>
                            <p style="font-size: 12px; margin-bottom: 8px; color: red; text-align: center;">
                                <?= $errors['photo'] ?>
                            </p>
                        <?php endif; ?>
                        <button type="button" class="btn-photo primary" onclick="document.getElementById('photoInput').click()">Change Photo</button>
                        <button name="delete_photo" type="submit" formaction="../../actions/profile/delete-photo.php" class="btn-photo outline"
                            onclick="return confirm('Hapus foto profil?')">Delete Photo</button>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <script>
        // Pratinjau foto sebelum disimpan
        const photoInput = document.getElementById('photoInput');
        const avatarPreview = document.getElementById('avatarPreview');

        photoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                avatarPreview.src = URL.createObjectURL(this.files[0]);
            }
        });
    </script>
</body>

</html>
