<?php
// Database belum ada -> daftar produk kosong.
// Ubah $useDemoData jadi true untuk mencoba dengan data contoh.
$useDemoData = false;
 
$products = [];
if ($useDemoData) {
    $products = [
        ['id' => 1, 'name' => 'AMD Ryzen 5 5600X',        'brand' => 'AMD',    'category' => 'Processor',     'price' => 2450000, 'specs' => '6 Core / 12 Thread, 3.7 GHz', 'warranty' => '3 Tahun',  'stock' => 12],
        ['id' => 2, 'name' => 'Intel Core i5-12400F',     'brand' => 'Intel',  'category' => 'Processor',     'price' => 2300000, 'specs' => '6 Core / 12 Thread, 4.4 GHz', 'warranty' => '3 Tahun',  'stock' => 8],
        ['id' => 3, 'name' => 'NVIDIA GeForce RTX 4060',  'brand' => 'NVIDIA', 'category' => 'Graphics Card', 'price' => 5600000, 'specs' => '8 GB GDDR6',                  'warranty' => '2 Tahun',  'stock' => 5],
        ['id' => 4, 'name' => 'Corsair Vengeance 16GB',   'brand' => 'Corsair', 'category' => 'RAM',          'price' => 780000,  'specs' => '2 x 8 GB DDR4, 3200 MHz',     'warranty' => 'Lifetime', 'stock' => 20],
    ];
}
 
// Baris tabel: key => label
$rows = [
    'price'    => 'Harga',
    'brand'    => 'Brand',
    'category' => 'Kategori',
    'specs'    => 'Spesifikasi',
    'warranty' => 'Garansi',
    'stock'    => 'Stok',
];
$slots = 3;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perbandingan Produk | TechHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../styles/base.css">
    <link rel="stylesheet" href="../../../styles/pages/compare.css">
</head>
<body class="compare-page">
 
    <div class="top-banner">Discover Premium PC Components Built for Performance, Speed, and Reliability</div>
 
    <header class="site-header">
        <a href="../../../index.php" class="logo"><img src="logo.png" alt="TechHub"></a>
        <form class="search-box" action="../products/index.php" method="get">
            <input type="text" name="q" placeholder="Search for parts to build your dream PC...">
            <button type="submit" aria-label="Search">🔍</button>
        </form>
        <nav class="header-icons">
            <a href="../products/index.php">👍</a>
            <a href="index.php" class="active">⇄</a>
            <a href="../cart/index.php">🛒</a>
            <a href="../profile/index.php">👤</a>
        </nav>
    </header>
 
    <main class="container">
        <div class="page-title">
            <h1>Perbandingan Produk</h1>
            <a href="javascript:history.back()" class="back-link">Back <span>→</span></a>
        </div>
 
        <!-- Slot produk -->
        <section class="compare-slots">
            <div class="slot-spacer"></div>
            <?php for ($i = 0; $i < $slots; $i++): ?>
                <div class="slot-col" data-slot="<?= $i ?>">
                    <button type="button" class="slot-empty" onclick="openModal(<?= $i ?>)">
                        <span class="plus-icon">+</span>
                        <strong>Add Product</strong>
                        <span>Choose Product</span>
                    </button>
 
                    <div class="slot-filled" hidden>
                        <button type="button" class="slot-remove" onclick="removeProduct(<?= $i ?>)">✕</button>
                        <div class="slot-thumb">🖥️</div>
                        <strong class="slot-name"></strong>
                        <span class="slot-price"></span>
                        <button type="button" class="slot-change" onclick="openModal(<?= $i ?>)">Ganti produk</button>
                    </div>
 
                    <button type="button" class="btn-compare" onclick="compare()" disabled>Compare</button>
                </div>
            <?php endfor; ?>
        </section>
 
        <!-- Tabel -->
        <section class="compare-table-wrap">
            <table class="compare-table">
                <?php foreach ($rows as $key => $label): ?>
                    <tr data-row="<?= $key ?>">
                        <th><?= $label ?></th>
                        <?php for ($i = 0; $i < $slots; $i++): ?><td>-</td><?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>
    </main>
 
    <footer class="site-footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="../../../index.php" class="logo"><img src="logo.png" alt="TechHub"></a>
                <p>Your Hub for Genuine, Warrantied PC Components. The Ultimate Solution for Builders, Gamers, and Creators!</p>
            </div>
            <div class="footer-col">
                <h5>Shop</h5>
                <a href="../products/index.php">Processors</a>
                <a href="../products/index.php">Motherboards</a>
                <a href="../products/index.php">Graphics Cards</a>
                <a href="../products/index.php">RAM &amp; Storage</a>
                <a href="../products/index.php">Power Supplies</a>
            </div>
            <div class="footer-col">
                <h5>Support</h5>
                <a href="../contact/index.php">How to Order</a>
                <a href="../contact/index.php">Shipping</a>
                <a href="../contact/index.php">Payment Methods</a>
                <a href="../contact/index.php">FAQ</a>
                <a href="../contact/index.php">Contact Us</a>
            </div>
            <div class="footer-col">
                <h5>My Account</h5>
                <a href="../profile/index.php">My Profile</a>
                <a href="../products/index.php">Recommendation</a>
                <a href="index.php">Compare Products</a>
                <a href="../cart/index.php">Shopping Cart</a>
                <a href="../products/index.php">Search Products</a>
            </div>
            <div class="footer-col">
                <h5>Follow Us</h5>
                <div class="social">
                    <a href="#">IG</a><a href="#">f</a><a href="#">▶</a>
                    <a href="#">WA</a><a href="#">TT</a><a href="#">X</a>
                </div>
            </div>
        </div>
        <p class="copyright">© <?= date('Y') ?> TechHub. All rights reserved.</p>
    </footer>
 
    <!-- Popup pilih produk -->
    <div class="modal" id="modal" hidden>
        <div class="modal-backdrop" onclick="closeModal()"></div>
        <div class="modal-box">
            <div class="modal-head">
                <h2>Pilih Produk</h2>
                <button type="button" class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <input type="search" id="search" class="modal-search" placeholder="Cari produk..." oninput="renderList()">
            <ul class="modal-list" id="list"></ul>
            <p class="modal-empty" id="empty" hidden></p>
        </div>
    </div>
 
    <div class="toast" id="toast" hidden></div>
 
    <script>
    const PRODUCTS = <?= json_encode($products, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const ROWS = <?= json_encode(array_keys($rows)) ?>;
    const selected = [null, null, null];
    let activeSlot = null;
 
    const $ = (id) => document.getElementById(id);
    const rupiah = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');
    const esc = (s) => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
 
    function show(key, value) {
        if (value === undefined || value === '') return '-';
        if (key === 'price') return rupiah(value);
        if (key === 'stock') return value > 0 ? value + ' unit' : 'Habis';
        return value;
    }
 
    function toast(msg) {
        const t = $('toast');
        t.textContent = msg;
        t.hidden = false;
        setTimeout(() => t.hidden = true, 2500);
    }
 
    // ----- Popup -----
    function openModal(slot) {
        activeSlot = slot;
        $('search').value = '';
        renderList();
        $('modal').hidden = false;
    }
 
    function closeModal() {
        $('modal').hidden = true;
    }
 
    function renderList() {
        const keyword = $('search').value.toLowerCase();
        const chosen = selected.filter(Boolean).map(p => p.id);
        const result = PRODUCTS.filter(p => (p.name + p.brand + p.category).toLowerCase().includes(keyword));
 
        $('search').hidden = PRODUCTS.length === 0;
        $('empty').hidden = result.length > 0;
        $('empty').textContent = PRODUCTS.length === 0
            ? 'Belum ada produk tersedia. Produk akan muncul setelah database terhubung.'
            : 'Produk tidak ditemukan.';
 
        $('list').innerHTML = result.map(p => `
            <li>
                <button type="button" class="modal-item" onclick="selectProduct(${p.id})" ${chosen.includes(p.id) ? 'disabled' : ''}>
                    <span class="item-thumb">🖥️</span>
                    <span class="item-info">
                        <strong>${esc(p.name)}</strong>
                        <small>${esc(p.category)} • ${rupiah(p.price)}</small>
                    </span>
                    ${chosen.includes(p.id) ? '<em>Dipilih</em>' : ''}
                </button>
            </li>`).join('');
    }
 
    // ----- Pilih / hapus -----
    function selectProduct(id) {
        selected[activeSlot] = PRODUCTS.find(p => p.id === id);
        closeModal();
        render();
    }
 
    function removeProduct(slot) {
        selected[slot] = null;
        render();
    }
 
    // ----- Tampilkan slot dan tabel -----
    function render() {
        document.querySelectorAll('.slot-col').forEach((col, i) => {
            const p = selected[i];
            col.querySelector('.slot-empty').hidden = !!p;
            col.querySelector('.slot-filled').hidden = !p;
            col.querySelector('.btn-compare').disabled = !p;
            if (p) {
                col.querySelector('.slot-name').textContent = p.name;
                col.querySelector('.slot-price').textContent = rupiah(p.price);
            }
        });
 
        ROWS.forEach(key => {
            document.querySelectorAll(`tr[data-row="${key}"] td`).forEach((td, i) => {
                td.textContent = selected[i] ? show(key, selected[i][key]) : '-';
                td.className = '';
            });
        });
    }
 
    // ----- Compare: kuning = beda, hijau = sama -----
    function compare() {
        const chosen = selected.filter(Boolean);
        if (chosen.length < 2) return toast('Pilih minimal 2 produk untuk dibandingkan.');
 
        ROWS.forEach(key => {
            const same = chosen.every(p => p[key] === chosen[0][key]);
            document.querySelectorAll(`tr[data-row="${key}"] td`).forEach((td, i) => {
                td.className = selected[i] ? (same ? 'same' : 'diff') : '';
            });
        });
        toast('Kolom kuning menandakan perbedaan.');
    }
 
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
    render();
    </script>
</body>
</html>
 