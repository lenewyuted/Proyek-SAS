<?php
// Komponen kartu produk. Butuh variabel $product (hasil dari repository)
require_once __DIR__ . "/../../repositories/product-repository.php";
?>
<div class="product-card">
    <div class="product-img">
        <img src="../../asset/images/<?= htmlspecialchars($product['image'] ?? 'placeholder.svg') ?>"
            alt="<?= htmlspecialchars($product['title']) ?>"
            onerror="this.onerror=null;this.src='../../asset/images/<?= htmlspecialchars($product['category_icon'] ?? 'placeholder.svg') ?>'">
    </div>
    <h3 class="product-title"><?= htmlspecialchars($product['title']) ?></h3>
    <div class="product-rating">
        <span><?= number_format((float) ($product['rating'] ?? 5), 1) ?></span>
        <i class="fa-solid fa-star"></i>
    </div>
    <div class="product-footer">
        <span class="product-price"><?= formatRupiah($product['price']) ?></span>
        <button class="btn-add-cart" type="button" aria-label="Tambah ke keranjang">
            <i class="fa-solid fa-cart-shopping"></i>
        </button>
    </div>
</div>
