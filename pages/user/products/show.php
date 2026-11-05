<?php


session_start();


$product = [
    'id'          => 1,
    'name'        => 'POWER SUPPLY MSI MAG A650BN',
    'description' => 'Reliable 650W power supply with 80 PLUS Bronze, DC-to-DC design, active PFC, and quiet 120mm fan. Ideal for entry to mid-range gaming PCs!',
    'price'       => 880000,
    'rating'      => 5.0,
    'image'       => 'msi-mag-a650bn.png',
];

$reviews = [
    [
        'name'    => 'Lynzz Leyn',
        'comment' => 'Great performance! Paired with Ryzen 7 7800X3D, VRM temperatures stay cool during heavy gaming. Wi-Fi 6E works fast and the M.2 heatsink slot helps a lot. Fast shipping and secure packaging.',
    ],
    [
        'name'    => 'Whoss Song',
        'comment' => 'Solid build quality, thick heatsinks, MSI BIOS is easy to understand. DDR5 Boost feature makes EXPO RAM run smoothly without issues. Highly recommended for mid-to-high-end gaming PC builds!',
    ],
];

// ====== PATH ======
$baseUrl  = '/Proyek-SAS';
$cssUrl   = $baseUrl . '/styles/product-description.css';
$imageUrl = $baseUrl . '/assets/images/products/' . $product['image'];

// ====== HELPER ======
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function rupiah(int $amount): string
{
    return 'Rp.' . number_format($amount, 0, ',', '.');
}

function stars(float $rating): string
{
    $full = (int) round($rating);
    return str_repeat('★', $full) . str_repeat('☆', 5 - $full);
}

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($product['name']) ?> - TechHub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $cssUrl ?>">
</head>
<body>

<?php require_once __DIR__ . '/../../../components/landing.php/header.php'; ?>

  <main class="container">

    <?php if ($flashSuccess): ?>
      <p class="alert alert-success"><?= e($flashSuccess) ?></p>
    <?php endif; ?>
    <?php if ($flashError): ?>
      <p class="alert alert-error"><?= e($flashError) ?></p>
    <?php endif; ?>

    <div class="page-head">
      <h1>Product Description</h1>
      <a href="javascript:history.back()" class="back-link">Back
        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12h17M14 6l6 6-6 6"/></svg>
      </a>
    </div>

    <section class="product">
      <div class="product-image">
        <img src="<?= e($imageUrl) ?>" alt="<?= e($product['name']) ?>">
      </div>

      <div class="product-info">
        <h2><?= e($product['name']) ?></h2>
        <p class="desc"><?= e($product['description']) ?></p>

        <div class="stars-row">
          <span class="score"><?= number_format($product['rating'], 1) ?></span>
          <span class="stars"><?= stars($product['rating']) ?></span>
        </div>

        <div class="price"><?= rupiah($product['price']) ?></div>

        <form class="buy-row" action="<?= $baseUrl ?>/actions/cart/add.php" method="post">
          <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">

          <div class="qty">
            <button type="button" id="minus" aria-label="Kurangi">−</button>
            <span id="qty-value">1</span>
            <input type="hidden" name="quantity" id="qty-input" value="1">
            <button type="button" id="plus" aria-label="Tambah">+</button>
          </div>

          <button type="submit" class="btn-cart">Add to Cart</button>
        </form>
      </div>
    </section>

    <section class="rating">
      <h3>Rating</h3>
      <div class="rating-summary">
        <span class="score"><?= number_format($product['rating'], 1) ?>/5.0</span>
        <span class="stars big"><?= stars($product['rating']) ?></span>
      </div>

      <?php if (empty($reviews)): ?>
        <p class="review-text">Belum ada ulasan untuk produk ini.</p>
      <?php else: ?>
        <?php foreach ($reviews as $review): ?>
          <div class="review">
            <div class="reviewer">
              <svg viewBox="0 0 24 24" width="38" height="38" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3.5"/><path d="M5.5 18.5c1.5-3 4-4 6.5-4s5 1 6.5 4"/></svg>
              <span><?= e($review['name']) ?></span>
            </div>
            <p class="review-text"><?= e($review['comment']) ?></p>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>

  <?php require_once __DIR__ . '/../../../components/footer.php'; ?>

  <script>
    const qtyEl = document.getElementById('qty-value');
    const qtyInput = document.getElementById('qty-input');

    function setQty(n) {
      qtyEl.textContent = n;
      qtyInput.value = n;
    }

    document.getElementById('plus').addEventListener('click', () => {
      setQty(parseInt(qtyEl.textContent) + 1);
    });
    document.getElementById('minus').addEventListener('click', () => {
      const n = parseInt(qtyEl.textContent);
      if (n > 1) setQty(n - 1);
    });
  </script>
</body>
</html>