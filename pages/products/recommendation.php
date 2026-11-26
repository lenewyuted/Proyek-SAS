<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$reviewRepo = __DIR__ . '/../../repositories/review-repository.php';
if (file_exists($reviewRepo)) {
    require_once $reviewRepo;
}

if (!function_exists('e')) {
    function e($s)
    {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }
}

$questions = [
    'need' => [
        'title'   => 'What are the primary requirements for the PC you want to build?',
        'options' => ['gaming' => 'Gaming', 'design' => 'Design & editing', 'study' => 'Study & work'],
        'default' => 'gaming',
    ],
    'budget' => [
        'title'   => 'What budget have you set aside for the purchase?',
        'options' => ['low' => '< Rp5.000.000,00', 'mid' => 'Rp5.000.000,00 - Rp10.000.000,00', 'high' => '> Rp10.000.000,00'],
        'default' => 'low',
    ],
    'apps' => [
        'title'   => 'What games or applications do you use often?',
        'options' => ['heavy' => 'Demanding games', 'light' => 'Light games', 'office' => 'Office applications'],
        'default' => 'heavy',
    ],
    'usage' => [
        'title'   => 'How often do you use a PC in your daily activities?',
        'options' => ['most' => 'Most of the day', 'few' => 'A few hours a day', 'rare' => 'Occasionally'],
        'default' => 'most',
    ],
];

$savedInput = $_SESSION['rec_input'] ?? [];
$errors     = $_SESSION['rec_errors'] ?? [];
$recResult  = $_SESSION['rec_result'] ?? null;

unset($_SESSION['rec_errors']);

$hasSubmitted = is_array($recResult) && !empty($recResult['submitted']);
$dbError      = $hasSubmitted && !empty($recResult['dbError']);
$matchedRows  = ($hasSubmitted && is_array($recResult['products'] ?? null)) ? $recResult['products'] : [];
$totalMatched = count($matchedRows);

$showAll     = isset($_GET['see_all']) && $_GET['see_all'] === '1';
$limit       = 10;
$visibleRows = $showAll ? $matchedRows : array_slice($matchedRows, 0, $limit);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommendation | TechHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../styles/base.css">
    <link rel="stylesheet" href="../../styles/components/header.css">
    <link rel="stylesheet" href="../../styles/components/footer.css">
    <link rel="stylesheet" href="../../styles/pages/recommendation.css">
</head>

<body class="recommendation-page">

    <?php require_once __DIR__ . '/../../components/landing/header.php'; ?>

    <main class="rec-main">

        <div class="rec-head">
            <div>
                <h1>Recommendation</h1>
                <p class="rec-sub">We will tailor our recommendations to your needs and the criteria you specify</p>
            </div>
            <a href="../landing-page/index.php" class="rec-back">Back <span>&rarr;</span></a>
        </div>

        <!-- Kuesioner -->
        <form class="rec-panel" id="rec-form" method="POST" action="../../actions/recommendation/filter.php" novalidate>
            <div class="question-grid">
                <?php foreach ($questions as $key => $q): ?>
                    <?php
                    $selectedVal = $savedInput[$key] ?? $q['default'];
                    $hasErr      = isset($errors[$key]);
                    ?>
                    <div class="question<?= $hasErr ? ' has-error' : '' ?>" data-q="<?= e($key) ?>" role="radiogroup" aria-labelledby="q-<?= e($key) ?>">
                        <h4 id="q-<?= e($key) ?>"><?= e($q['title']) ?></h4>
                        <?php foreach ($q['options'] as $val => $label): ?>
                            <label class="check">
                                <input type="radio"
                                    name="<?= e($key) ?>"
                                    value="<?= e($val) ?>"
                                    <?= $val === $selectedVal ? 'checked' : '' ?>>
                                <span class="box" aria-hidden="true"></span>
                                <span class="check-text"><?= e($label) ?></span>
                            </label>
                        <?php endforeach; ?>
                        <small class="q-error" <?= $hasErr ? '' : ' hidden' ?>><?= e($errors[$key] ?? 'Please choose an answer.') ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="rec-submit-row">
                <button type="submit" class="rec-submit" id="btn-submit">Submit</button>
            </div>
        </form>

        <!-- Hasil rekomendasi (muncul setelah form di-submit ke action PHP) -->
        <?php if ($hasSubmitted && empty($errors)): ?>
            <section class="rec-results" id="results">
                <h2>Your Best Choice</h2>
                <p class="rec-sub">The following options have been adapted to your criteria</p>

                <?php if ($dbError): ?>
                    <p class="rec-empty" id="empty">Failed to load products from the database.</p>
                <?php elseif ($totalMatched > 0): ?>
                    <p class="rec-count" id="count" aria-live="polite">
                        Showing <?= count($visibleRows) ?> of <?= $totalMatched ?> recommendation<?= $totalMatched > 1 ? 's' : '' ?>
                    </p>
                    <div class="rec-grid" id="grid">
                        <?php foreach ($visibleRows as $i => $r): ?>
                            <?php
                            $id    = (int) ($r['id'] ?? 0);
                            $name  = (string) ($r['name'] ?? '');
                            $price = (int) ($r['price'] ?? 0);
                            $stock = (int) ($r['stock'] ?? 0);
                            $img   = (string) ($r['picture'] ?? '');
                            if ($img !== '' && !preg_match('#^(https?:)?//|^/#', $img)) {
                                $img = '/assets/img/' . $img;
                            }

                            $rating = null;
                            if ($id > 0 && function_exists('getProductRatingSummary')) {
                                try {
                                    $summary = getProductRatingSummary($id);
                                    if (!empty($summary['total_reviews']) && (int) $summary['total_reviews'] > 0) {
                                        $rating = (float) $summary['average_rating'];
                                    }
                                } catch (Throwable $ex) {
                                    $rating = null;
                                }
                            }

                            $link = '../products/show.php?id=' . $id;
                            ?>
                            <article class="rec-card" style="--i:<?= $i ?>">
                                <div class="rec-card-top">
                                    <a class="rec-thumb" href="<?= e($link) ?>">
                                        <?php if ($img !== ''): ?>
                                            <img src="<?= e($img) ?>" alt="<?= e($name) ?>" loading="lazy">
                                        <?php else: ?>
                                            <span class="rec-fallback"><i class="fa-solid fa-desktop"></i></span>
                                        <?php endif; ?>
                                    </a>
                                    <a class="rec-name" href="<?= e($link) ?>"><?= e($name) ?></a>
                                </div>
                                <div class="rec-card-footer">
                                    <div class="rec-rating">
                                        <?php if ($rating !== null): ?>
                                            <?= number_format($rating, 1) ?> <span class="rec-star">&#9733;</span>
                                        <?php else: ?>
                                            <span class="rec-muted">No rating</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="rec-bottom">
                                        <strong class="rec-price">Rp <?= number_format($price, 0, ',', '.') ?></strong>
                                        <form method="POST" action="../../actions/cart/add.php" style="display:inline;">
                                            <input type="hidden" name="product_id" value="<?= $id ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="rec-cart-btn" aria-label="Add <?= e($name) ?> to cart" <?= $stock <= 0 ? 'disabled' : '' ?>>
                                                <i class="fa-solid fa-cart-shopping"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($totalMatched > $limit): ?>
                        <div class="rec-see-more-wrap" id="see-more-wrap">
                            <?php if ($showAll): ?>
                                <a href="?see_all=0#results" class="rec-see-more-btn" id="btn-see-more">Show Less</a>
                            <?php else: ?>
                                <a href="?see_all=1#results" class="rec-see-more-btn" id="btn-see-more">See More (<?= $totalMatched - $limit ?> more)</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="rec-empty" id="empty">No products match your selected budget and description criteria.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <?php require_once __DIR__ . '/../../components/landing/footer.php'; ?>
</body>

</html>