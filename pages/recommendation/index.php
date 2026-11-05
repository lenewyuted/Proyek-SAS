<?php
require_once __DIR__ . "/../../config/recommendation.php";
require_once __DIR__ . "/../../repositories/recommendation-repository.php";

// Jawaban diambil dari URL (GET) supaya hasilnya bisa dibagikan lewat link
$answers = getRecommendationAnswers($_GET, $questions);
$submitted = isset($_GET['submit']);

$products = getRecommendedProducts($answers, $submitted, $budgetRange, $purposeCategories, 5);

// Muncul ketika sudah submit tetapi tidak ada produk yang cocok dengan anggaran
$noMatch = $submitted && empty($products);

// Mengecek apakah sebuah opsi sedang dipilih, supaya checkbox tetap tercentang setelah submit
$isChecked = fn($key, $value) => in_array($value, $answers[$key], true) ? 'checked' : '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommendation - TechHub</title>

    <!-- Font & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../asset/css/style.css">
</head>

<body>
    <!-- Header -->
    <?php require_once __DIR__ . "/../../components/user/header.php" ?>

    <main class="reco-page">
        <div class="reco-head">
            <h1>Recommendation</h1>
            <a href="../home/index.php" class="reco-back">Back <i class="fa-solid fa-arrow-right-long"></i></a>
        </div>
        <p class="reco-sub">We will tailor our recommendations to your needs and the criteria you specify</p>

        <form method="GET" action="index.php" class="reco-form">
            <div class="reco-questions">
                <?php foreach ($questions as $key => $question): ?>
                    <fieldset class="reco-q">
                        <legend><?= htmlspecialchars($question['title']) ?></legend>
                        <?php foreach ($question['options'] as $value => $label): ?>
                            <label>
                                <input type="checkbox" name="<?= $key ?>[]" value="<?= $value ?>" <?= $isChecked($key, $value) ?>>
                                <?= htmlspecialchars($label) ?>
                            </label>
                        <?php endforeach; ?>
                    </fieldset>
                <?php endforeach; ?>
            </div>
            <div class="reco-submit">
                <button type="submit" name="submit" value="1">Submit</button>
            </div>
        </form>

        <section class="reco-result">
            <h2>Your Best Choice</h2>
            <p class="reco-sub">The following options have been adapted to your criteria</p>

            <?php if ($noMatch): ?>
                <p class="reco-note">Tidak ada produk yang cocok dengan anggaran tersebut. Coba pilih rentang anggaran lain.</p>
            <?php endif; ?>

            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <?php require __DIR__ . "/../../components/user/product-card.php" ?>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php require_once __DIR__ . "/../../components/user/footer.php" ?>
</body>

</html>
