<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../repositories/user-repository.php";

$sessionUser = $_SESSION["user"] ?? null;
$userId      = $sessionUser["id"] ?? $_SESSION["user_id"] ?? $_SESSION["id"] ?? null;

if (!$userId) {
    header("Location: ../auth/login.php");
    exit;
}

$user = function_exists("getProfile") ? getProfile((int) $userId) : $sessionUser;

if (!$user || !is_array($user)) {
    echo "Data user tidak ditemukan.";
    exit;
}

$old           = $_SESSION["old"] ?? $user;
$errors        = $_SESSION["errors"] ?? [];
$flashSuccess  = $_SESSION["flash_success"] ?? "";
$flashError    = $_SESSION["flash_error"] ?? "";
unset($_SESSION["old"], $_SESSION["errors"], $_SESSION["flash_success"], $_SESSION["flash_error"]);

$currentGender = strtolower(trim((string) ($old["gender"] ?? "")));
$photoFile     = trim((string) ($old["picture"] ?? $user["picture"] ?? $user["avatar"] ?? ""));
$hasPhoto      = ($photoFile !== "");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Profile</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="../../styles/pages/landing/style.css">
    <link rel="stylesheet" href="../../styles/components/header.css">
    <link rel="stylesheet" href="../../styles/components/footer.css">
    <link rel="stylesheet" href="../../styles/pages/profile.css">
</head>

<body>

    <!-- MAIN HEADER -->
    <?php require_once __DIR__ . "/../../components/landing/header.php"; ?>

    <main class="user-profile-wrapper">
        <div class="user-profile-topbar">
            <h1>Profile</h1>
            <a href="../landing-page/index.php" class="user-profile-back">
                Back <i class="fa-solid fa-arrow-right-long"></i>
            </a>
        </div>

        <?php if ($flashSuccess !== ""): ?>
            <div class="profile-alert profile-alert-success">
                <?= htmlspecialchars($flashSuccess) ?>
            </div>
        <?php endif; ?>

        <?php if ($flashError !== ""): ?>
            <div class="profile-alert profile-alert-error">
                <?= htmlspecialchars($flashError) ?>
            </div>
        <?php endif; ?>

        <form action="../../actions/profile-user/update.php" method="POST" enctype="multipart/form-data" id="userProfileForm" class="user-profile-grid">
            <input type="file" id="avatarInput" name="picture" accept="image/*" hidden>
            <input type="hidden" name="redirect_source" value="user">

            <!-- PANEL KIRI: DATA PROFIL -->
            <div class="profile-left-card">
                <div class="profile-field-row">
                    <label for="name">Name</label>
                    <div class="profile-input-wrap">
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?= htmlspecialchars((string) ($old["name"] ?? "")) ?>"
                            placeholder="Enter your name">
                        <?php if (!empty($errors["name"])): ?>
                            <small class="profile-error-text"><?= htmlspecialchars($errors["name"]) ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-field-row">
                    <label for="email">Email</label>
                    <div class="profile-input-wrap">
                        <input
                            type="email"
                            id="email"
                            value="<?= htmlspecialchars((string) ($user["email"] ?? "")) ?>"
                            disabled
                            readonly
                            aria-disabled="true"
                            class="is-readonly">
                    </div>
                </div>

                <div class="profile-field-row">
                    <label for="phone">Phone</label>
                    <div class="profile-input-wrap">
                        <input
                            type="text"
                            id="phone"
                            name="phone_number"
                            value="<?= htmlspecialchars((string) ($old["phone_number"] ?? $old["phone"] ?? "")) ?>"
                            placeholder="08xxxxxxxxxx">
                        <?php if (!empty($errors["phone_number"])): ?>
                            <small class="profile-error-text"><?= htmlspecialchars($errors["phone_number"]) ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-gender-block">
                    <span class="profile-block-label">Gender</span>
                    <div class="profile-gender-options">
                        <label class="gender-check-item">
                            <input type="radio" name="gender" value="female" <?= $currentGender === "female" ? "checked" : "" ?>>
                            <span class="gender-box"></span>
                            <span>Female</span>
                        </label>

                        <label class="gender-check-item">
                            <input type="radio" name="gender" value="male" <?= $currentGender === "male" ? "checked" : "" ?>>
                            <span class="gender-box"></span>
                            <span>Male</span>
                        </label>

                        <label class="gender-check-item">
                            <input type="radio" name="gender" value="secret" <?= $currentGender === "secret" ? "checked" : "" ?>>
                            <span class="gender-box"></span>
                            <span>Secret</span>
                        </label>
                    </div>
                    <?php if (!empty($errors["gender"])): ?>
                        <small class="profile-error-text"><?= htmlspecialchars($errors["gender"]) ?></small>
                    <?php endif; ?>
                </div>

                <div class="profile-location-grid">
                    <div class="profile-location-col">
                        <label for="province">Province</label>
                        <input
                            type="text"
                            id="province"
                            name="province"
                            value="<?= htmlspecialchars((string) ($old["province"] ?? "")) ?>"
                            placeholder="Province">
                        <?php if (!empty($errors["province"])): ?>
                            <small class="profile-error-text"><?= htmlspecialchars($errors["province"]) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="profile-location-col">
                        <label for="city">City</label>
                        <input
                            type="text"
                            id="city"
                            name="city"
                            value="<?= htmlspecialchars((string) ($old["city"] ?? "")) ?>"
                            placeholder="City">
                        <?php if (!empty($errors["city"])): ?>
                            <small class="profile-error-text"><?= htmlspecialchars($errors["city"]) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="profile-location-col">
                        <label for="address">Address</label>
                        <input
                            type="text"
                            id="address"
                            name="address"
                            value="<?= htmlspecialchars((string) ($old["address"] ?? "")) ?>"
                            placeholder="Street / Address">
                        <?php if (!empty($errors["address"])): ?>
                            <small class="profile-error-text"><?= htmlspecialchars($errors["address"]) ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-actions-row">
                    <button type="submit" name="update" class="btn-save-profile">Save Profile</button>
                    <button type="submit" form="resetProfileForm" class="btn-reset-profile" id="btnResetProfile">Reset Profile</button>
                </div>
            </div>
        </form>

        <form action="../../actions/profile-user/reset.php" method="POST" id="resetProfileForm" hidden></form>
    </main>

    <!-- MAIN FOOTER -->
    <?php require_once __DIR__ . "/../../components/landing/footer.php"; ?>

    <script>
        (function() {
            const fileInput = document.getElementById("avatarInput");
            const previewImg = document.getElementById("avatarPreview");
            const plusIcon = document.getElementById("avatarPlusIcon");
            const btnEditPhoto = document.getElementById("btnEditPhoto");
            const avatarCircleBtn = document.getElementById("avatarCircleBtn");
            const btnReset = document.getElementById("btnResetProfile");
            const form = document.getElementById("userProfileForm");

            function openPicker() {
                if (fileInput) fileInput.click();
            }

            if (btnEditPhoto) btnEditPhoto.addEventListener("click", openPicker);
            if (avatarCircleBtn) avatarCircleBtn.addEventListener("click", openPicker);

            if (fileInput) {
                fileInput.addEventListener("change", function() {
                    const file = this.files && this.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (previewImg) {
                            previewImg.src = e.target.result;
                            previewImg.style.display = "block";
                        }
                        if (plusIcon) {
                            plusIcon.style.display = "none";
                        }
                    };
                    reader.readAsDataURL(file);
                });
            }

            if (btnReset && form) {
                btnReset.addEventListener("click", function() {
                    ["phone", "province", "city", "address"].forEach(function(id) {
                        const el = document.getElementById(id);
                        if (el) el.value = "";
                    });
                    form.querySelectorAll('input[type="radio"][name="gender"]').forEach(function(radio) {
                        radio.checked = false;
                    });
                    if (fileInput) fileInput.value = "";
                });
            }
        })();
    </script>
</body>

</html>