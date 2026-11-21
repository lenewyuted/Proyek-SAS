<?php
session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../repositories/product-repository.php";
require_once __DIR__ . "/../../repositories/review-repository.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = $id > 0 ? getProduct($id) : null;

if (!$product) {
    header('Location: ../landing/index.php');
    exit;
}

$reviews       = getReviewsByProductId($id);
$ratingSummary = getProductRatingSummary($id);
$avgRating     = $ratingSummary['average_rating'];
$totalReviews  = $ratingSummary['total_reviews'];

$currentUserId = isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : 0;
$isAdminUser   = (($_SESSION['user']['role'] ?? '') === 'admin');

$flashSuccess = $_SESSION['flash_success'] ?? "";
$flashError   = $_SESSION['flash_error'] ?? "";
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

if (!function_exists('formatRupiah')) {
    function formatRupiah($amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}

function renderStars(float $rating): string
{
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($rating >= $i) {
            $html .= '<i class="fa-solid fa-star"></i>';
        } elseif ($rating >= ($i - 0.5)) {
            $html .= '<i class="fa-solid fa-star-half-stroke"></i>';
        } else {
            $html .= '<i class="fa-regular fa-star"></i>';
        }
    }
    return $html;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - <?= htmlspecialchars($product['name'] ?? 'Product Description') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS Kustom -->
    <link rel="stylesheet" href="../../styles/pages/products/show.css">
    <link rel="stylesheet" href="../../styles/components/header.css">
    <link rel="stylesheet" href="../../styles/components/footer.css">
</head>

<body>

    <?php require_once __DIR__ . "/../../components/landing/header.php" ?>

    <main class="product-main-container">
        <?php if ($flashSuccess !== ""): ?>
            <div class="alert-box alert-success">
                <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flashSuccess) ?>
            </div>
        <?php endif; ?>

        <?php if ($flashError !== ""): ?>
            <div class="alert-box alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($flashError) ?>
            </div>
        <?php endif; ?>

        <!-- Judul Halaman -->
        <div class="product-header-title">
            <div>
                <h1>Product Description</h1>
            </div>
            <a href="../landing/index.php" class="back-link">Back <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <!-- DETAIL PRODUK UTAMA -->
        <div class="product-detail-grid">
            <!-- Gambar Produk -->
            <div class="product-image-card">
                <img src="/assets/img/<?= htmlspecialchars($product['picture'] ?? 'default.png'); ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Product'); ?>">
            </div>

            <!-- Informasi Produk -->
            <div class="product-info-card">
                <h2 class="product-title"><?= htmlspecialchars($product['name'] ?? ''); ?></h2>
                <p class="product-description">
                    <?= nl2br(htmlspecialchars($product['description'] ?? '')); ?>
                </p>

                <div class="product-rating-overview">
                    <span class="rating-number"><?= number_format($avgRating, 1) ?></span>
                    <div class="stars">
                        <?= renderStars($avgRating) ?>
                    </div>
                    <span class="review-count-text">(<?= $totalReviews ?> Reviews)</span>
                </div>

                <div class="product-price-box">
                    <span class="price-amount">
                        <?= formatRupiah($product['price'] ?? 0); ?>
                    </span>
                </div>

                <div class="purchase-action-row">
                    <form action="../../actions/cart/add.php" method="POST" class="cart-form-inline">
                        <div class="quantity-selector">
                            <button type="button" class="qty-btn">-</button>
                            <span class="qty-value">1</span>
                            <button type="button" class="qty-btn">+</button>
                        </div>
                        <input type="hidden" name="product_id" value="<?= (int) ($product['id'] ?? 0) ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button name="add" type="submit" class="btn-add-to-cart" <?= $isAdminUser ? " disabled" : "" ?>>Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- BAGIAN RATING & ULASAN -->
        <section class="rating-reviews-section" id="reviews-section">
            <h3 class="section-heading">Rating & Reviews</h3>

            <div class="total-rating-box">
                <span class="total-rating-score"><?= number_format($avgRating, 1) ?>/5.0</span>
                <div class="stars large">
                    <?= renderStars($avgRating) ?>
                </div>
                <span class="total-reviews-label">Based on <?= $totalReviews ?> review<?= $totalReviews !== 1 ? 's' : '' ?></span>
            </div>

            <!-- Form Tambah Rating & Komentar -->
            <?php if ($currentUserId > 0): ?>
                <form action="../../actions/reviews/store.php" method="POST" class="review-form-card">
                    <h4>Write a Review</h4>
                    <input type="hidden" name="product_id" value="<?= (int) ($product['id'] ?? 0) ?>">

                    <div class="star-rating-input-group">
                        <label>Your Rating:</label>
                        <div class="star-rating-selector">
                            <input type="radio" id="star5" name="rating" value="5" required>
                            <label for="star5" title="5 stars"><i class="fa-solid fa-star"></i></label>

                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4" title="4 stars"><i class="fa-solid fa-star"></i></label>

                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3" title="3 stars"><i class="fa-solid fa-star"></i></label>

                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2" title="2 stars"><i class="fa-solid fa-star"></i></label>

                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1" title="1 star"><i class="fa-solid fa-star"></i></label>
                        </div>
                    </div>

                    <div class="review-textarea-group">
                        <label for="review-comment">Your Comment:</label>
                        <textarea id="review-comment" name="comment" rows="4" placeholder="Share your experience with this hardware..." required></textarea>
                    </div>

                    <button type="submit" class="btn-submit-review">
                        <i class="fa-solid fa-paper-plane"></i> Submit Review
                    </button>
                </form>
            <?php else: ?>
                <div class="login-to-review-box">
                    <p>Ingin memberikan rating dan ulasan untuk produk ini? <a href="../auth/login.php">Login sekarang</a>.</p>
                </div>
            <?php endif; ?>

            <!-- Daftar Ulasan dari Database -->
            <?php if (empty($reviews)): ?>
                <div class="empty-reviews-box">
                    <p>Belum ada ulasan untuk produk ini. Jadilah yang pertama memberikan ulasan!</p>
                </div>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <?php
                    $reviewId     = (int) ($review['id'] ?? 0);
                    $reviewUserId = (int) ($review['user_id'] ?? 0);
                    $canDelete    = ($currentUserId > 0 && ($currentUserId === $reviewUserId || $isAdminUser));
                    ?>
                    <div class="review-item">
                        <div class="reviewer-header">
                            <div class="reviewer-left">
                                <div class="reviewer-avatar">
                                    <i class="fa-regular fa-user"></i>
                                </div>
                                <div class="reviewer-meta">
                                    <span class="reviewer-name"><?= htmlspecialchars($review['user_name'] ?? 'Customer') ?></span>
                                    <div class="review-sub-meta">
                                        <div class="stars small">
                                            <?= renderStars((float) ($review['rating'] ?? 5)) ?>
                                        </div>
                                        <?php if (!empty($review['created_at'])): ?>
                                            <span class="review-date">
                                                &bull; <?= date('d M Y', strtotime($review['created_at'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if ($canDelete): ?>
                                <form action="../../actions/reviews/destroy.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?');">
                                    <input type="hidden" name="review_id" value="<?= $reviewId ?>">
                                    <input type="hidden" name="product_id" value="<?= (int) ($product['id'] ?? 0) ?>">
                                    <button type="submit" class="btn-delete-review" title="Hapus ulasan">
                                        <i class="fa-regular fa-trash-can"></i> Hapus
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>

                        <div class="review-box">
                            <p><?= nl2br(htmlspecialchars($review['comment'] ?? '')) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

    </main>

    <?php require_once __DIR__ . "/../../components/landing/footer.php" ?>

</body>

</html>