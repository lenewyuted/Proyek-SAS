<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../repositories/product-repository.php';

// Repository rating (dari tim). Sesuaikan nama file kalau berbeda.
$reviewRepo = __DIR__ . '/../../repositories/review-repository.php';
if (file_exists($reviewRepo)) {
    require_once $reviewRepo;
}

$cartUrl   = '../../actions/cart/add.php';
$imageBase = '/assets/img/';

if (!function_exists('e')) {
    function e($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('pick')) {
    function pick(array $row, array $keys, $default = '')
    {
        foreach ($keys as $k) {
            if (isset($row[$k]) && $row[$k] !== '') return $row[$k];
        }
        return $default;
    }
}

// ---------- Ambil data produk (fungsi tim) ----------
$raw = [];
$dbError = false;
try {
    $raw = getAllProducts();
} catch (Throwable $ex) {
    $dbError = true;
}

// Isi keranjang saat ini (untuk cek stok di browser, sama seperti add.php)
$cartSession = $_SESSION['cart'] ?? [];
$loggedIn    = isset($_SESSION['user']['id']);

$products = array_map(function ($r) use ($imageBase, $cartSession) {
    $id  = (int) $r['id'];
    $img = (string) ($r['picture'] ?? '');
    if ($img !== '' && !preg_match('#^(https?:)?//|^/#', $img)) {
        $img = $imageBase . $img;
    }

    // Rating dari getProductRatingSummary() milik tim
    $rating = null;
    if (function_exists('getProductRatingSummary')) {
        try {
            $summary = getProductRatingSummary($id);
            if ($summary['total_reviews'] > 0) {
                $rating = $summary['average_rating'];
            }
        } catch (Throwable $ex) {
            $rating = null;
        }
    }

    $p = [
        'id'       => $id,
        'name'     => (string) $r['name'],
        'category' => (string) ($r['category_name'] ?? ''),
        'price'    => (int) $r['price'],
        'stock'    => (int) ($r['stock'] ?? 0),
        'image'    => $img,
        'rating'   => $rating,
        'inCart'   => (int) ($cartSession[$id]['quantity'] ?? 0),
    ];
    // Teks untuk pencocokan kata kunci (tidak ditampilkan)
    $extra = pick($r, ['brand', 'specs', 'specification', 'description', 'deskripsi']);
    $p['search'] = mb_strtolower($p['name'] . ' ' . $p['category'] . ' ' . $extra);
    return $p;
}, $raw);

// ---------- Pertanyaan ----------
$questions = [
    'need' => [
        'title'   => 'What are the primary requirements for the PC you want to build?',
        'type'    => 'checkbox',
        'options' => ['gaming' => 'Gaming', 'design' => 'Design & editing', 'study' => 'Study & work'],
        'default' => ['gaming'],
    ],
    'budget' => [
        'title'   => 'What budget have you set aside for the purchase?',
        'type'    => 'radio',
        'options' => ['low' => '< Rp5.000.000,00', 'mid' => 'Rp5.000.000,00 - Rp10.000.000,00', 'high' => '> Rp10.000.000,00'],
        'default' => ['low'],
    ],
    'apps' => [
        'title'   => 'What games or applications do you use often?',
        'type'    => 'checkbox',
        'options' => ['heavy' => 'Demanding games', 'light' => 'Light games', 'office' => 'Office applications'],
        'default' => ['heavy'],
    ],
    'usage' => [
        'title'   => 'How often do you use a PC in your daily activities?',
        'type'    => 'radio',
        'options' => ['most' => 'Most of the day', 'few' => 'A few hours a day', 'rare' => 'Occasionally'],
        'default' => ['most'],
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recommendation | TechHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Ikon yang dipakai header -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../styles/base.css">
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
            <a href="javascript:history.back()" class="rec-back">Back <span>→</span></a>
        </div>

        <!-- Kuesioner -->
        <form class="rec-panel" id="rec-form" novalidate>
            <div class="question-grid">
                <?php foreach ($questions as $key => $q):
                    $isCheck = $q['type'] === 'checkbox';
                ?>
                    <div class="question" data-q="<?= e($key) ?>" role="group" aria-labelledby="q-<?= e($key) ?>">
                        <h4 id="q-<?= e($key) ?>"><?= e($q['title']) ?></h4>
                        <?php foreach ($q['options'] as $val => $label): ?>
                            <label class="check">
                                <input type="<?= $q['type'] ?>"
                                       name="<?= e($key) . ($isCheck ? '[]' : '') ?>"
                                       value="<?= e($val) ?>"
                                       <?= in_array($val, $q['default'], true) ? 'checked' : '' ?>>
                                <span class="box" aria-hidden="true"></span>
                                <span class="check-text"><?= e($label) ?></span>
                            </label>
                        <?php endforeach; ?>
                        <small class="q-error" hidden>Please choose at least one answer.</small>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="rec-submit" id="btn-submit">Submit</button>
        </form>

        <!-- Hasil rekomendasi -->
        <section class="rec-results" id="results">
            <h2>Your Best Choice</h2>
            <p class="rec-sub">The following options have been adapted to your criteria</p>
            <p class="rec-count" id="count" aria-live="polite"></p>
            <div class="rec-grid" id="grid"></div>
            <p class="rec-empty" id="empty" hidden></p>
        </section>
    </main>

    <?php require_once __DIR__ . '/../../components/landing/footer.php'; ?>

    <div class="rec-toast" id="toast" role="status" hidden></div>

    <script>
    (function () {
        var PRODUCTS  = <?= json_encode($products, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
        var DB_ERROR  = <?= $dbError ? 'true' : 'false' ?>;
        var LOGGED_IN = <?= $loggedIn ? 'true' : 'false' ?>;
        var CART_URL  = <?= json_encode($cartUrl) ?>;
        var MAX_RESULTS = 5;

        var form    = document.getElementById('rec-form');
        var grid    = document.getElementById('grid');
        var empty   = document.getElementById('empty');
        var count   = document.getElementById('count');
        var toastEl = document.getElementById('toast');
        var btn     = document.getElementById('btn-submit');
        var toastTimer;

        // Cari produk berdasarkan id
        var byId = {};
        PRODUCTS.forEach(function (p) { byId[p.id] = p; });

        function esc(s) {
            return String(s).replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }
        function rupiah(n) { return 'Rp.' + Number(n).toLocaleString('id-ID'); }

        // ----- Kata kunci untuk mencocokkan produk dengan kebutuhan -----
        // Penilaian sederhana berdasarkan nama, kategori, dan deskripsi produk.
        var KW = {
            heavy:  ['rtx', 'gtx', 'radeon', 'geforce', 'gaming', 'victus', 'aorus', 'nitro', 'legion', 'rog', 'tuf', 'ryzen 7', 'ryzen 9', 'i7', 'i9'],
            design: ['rtx', 'creator', 'oled', 'studio', 'ryzen 7', 'ryzen 9', 'i7', 'i9', '16gb', '32gb', '16 gb', '32 gb'],
            office: ['ryzen 3', 'ryzen 5', 'i3', 'i5', 'vivobook', 'ideapad', 'aspire', 'thinkpad', 'office', 'slim', '8gb', '8 gb'],
            light:  ['gtx', 'ryzen 5', 'i5', 'aspire', 'vivobook']
        };
        function hits(text, list) {
            return list.filter(function (k) { return text.indexOf(k) !== -1; }).length;
        }

        // ----- Jawaban dan validasi -----
        function answers() {
            var fd = new FormData(form);
            return {
                need:   fd.getAll('need[]'),
                budget: fd.get('budget'),
                apps:   fd.getAll('apps[]'),
                usage:  fd.get('usage')
            };
        }

        function validate() {
            var a = answers();
            var checks = { need: a.need.length > 0, budget: !!a.budget, apps: a.apps.length > 0, usage: !!a.usage };
            var first = null;
            Object.keys(checks).forEach(function (key) {
                var box = form.querySelector('[data-q="' + key + '"]');
                var bad = !checks[key];
                box.classList.toggle('has-error', bad);
                box.querySelector('.q-error').hidden = !bad;
                if (bad && !first) first = box;
            });
            if (first) {
                first.classList.remove('shake');
                void first.offsetWidth;
                first.classList.add('shake');
                first.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return !first;
        }

        form.addEventListener('change', function (e) {
            var box = e.target.closest('.question');
            if (box) {
                box.classList.remove('has-error');
                box.querySelector('.q-error').hidden = true;
            }
        });

        // ----- Logika rekomendasi -----
        function inBudget(price, b) {
            if (b === 'low')  return price < 5000000;
            if (b === 'mid')  return price >= 5000000 && price <= 10000000;
            if (b === 'high') return price > 10000000;
            return true;
        }

        function recommend(a) {
            return PRODUCTS
                .filter(function (p) { return inBudget(p.price, a.budget) && p.stock > 0; })
                .map(function (p) {
                    var s = 0, t = p.search;
                    if (a.need.indexOf('gaming') > -1) s += hits(t, KW.heavy);
                    if (a.need.indexOf('design') > -1) s += hits(t, KW.design);
                    if (a.need.indexOf('study')  > -1) s += hits(t, KW.office);
                    if (a.apps.indexOf('heavy')  > -1) s += hits(t, KW.heavy);
                    if (a.apps.indexOf('light')  > -1) s += hits(t, KW.light);
                    if (a.apps.indexOf('office') > -1) s += hits(t, KW.office);
                    var ratingWeight = a.usage === 'most' ? 0.6 : 0.3;   // pemakaian lama: utamakan rating
                    return { p: p, score: s * 2 + (p.rating || 0) * ratingWeight };
                })
                .sort(function (x, y) {
                    return y.score - x.score || (y.p.rating || 0) - (x.p.rating || 0) || x.p.price - y.p.price;
                })
                .slice(0, MAX_RESULTS)
                .map(function (x) { return x.p; });
        }

        // ----- Tampilan -----
        function cardHtml(p, i) {
            var link  = 'show.php?id=' + p.id;
            var thumb = p.image
                ? '<img src="' + esc(p.image) + '" alt="' + esc(p.name) + '" loading="lazy">'
                : '<span class="rec-fallback">🖥️</span>';
            var rating = p.rating !== null
                ? Number(p.rating).toFixed(1) + ' <span class="rec-star">★</span>'
                : '<span class="rec-muted">No rating</span>';
            return '<article class="rec-card" style="--i:' + i + '">' +
                '<a class="rec-thumb" href="' + link + '">' + thumb + '</a>' +
                '<a class="rec-name" href="' + link + '">' + esc(p.name) + '</a>' +
                '<div class="rec-rating">' + rating + '</div>' +
                '<div class="rec-bottom">' +
                    '<strong class="rec-price">' + rupiah(p.price) + '</strong>' +
                    '<button type="button" class="rec-cart-btn" data-id="' + p.id + '" aria-label="Add ' + esc(p.name) + ' to cart">🛒</button>' +
                '</div>' +
            '</article>';
        }

        function render(list) {
            grid.innerHTML = list.map(cardHtml).join('');
            empty.hidden = list.length > 0;
            count.textContent = list.length ? list.length + ' recommendation' + (list.length > 1 ? 's' : '') + ' found' : '';
            if (!list.length) {
                empty.textContent = DB_ERROR
                    ? 'Failed to load products from the database.'
                    : PRODUCTS.length === 0
                        ? 'There are no products in the database yet.'
                        : 'No products match this budget. Try choosing a different budget.';
            }
        }

        function showSkeleton() {
            var html = '';
            for (var i = 0; i < MAX_RESULTS; i++) html += '<div class="rec-card rec-skeleton" aria-hidden="true"></div>';
            grid.innerHTML = html;
            empty.hidden = true;
            count.textContent = '';
        }

        // Gambar yang gagal dimuat diganti ikon
        grid.addEventListener('error', function (e) {
            if (e.target.tagName === 'IMG') {
                var span = document.createElement('span');
                span.className = 'rec-fallback';
                span.textContent = '🖥️';
                e.target.replaceWith(span);
            }
        }, true);

        // ----- Submit -----
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!validate()) return;

            btn.disabled = true;
            btn.textContent = 'Searching...';
            showSkeleton();
            document.getElementById('results').scrollIntoView({ behavior: 'smooth', block: 'start' });

            setTimeout(function () {
                render(recommend(answers()));
                btn.disabled = false;
                btn.textContent = 'Submit';
            }, 450);
        });

        // ----- Notifikasi -----
        function toast(msg, type) {
            clearTimeout(toastTimer);
            toastEl.textContent = msg;
            toastEl.className = 'rec-toast' + (type ? ' ' + type : '');
            toastEl.hidden = false;
            toastTimer = setTimeout(function () { toastEl.hidden = true; }, 2800);
        }

        // ----- Angka di ikon keranjang pada header (sama seperti header.php: hanya untuk user login) -----
        function updateBadge() {
            if (!LOGGED_IN) return;
            var link = document.querySelector('.cart-icon-link');
            if (!link) return;
            var total = PRODUCTS.reduce(function (sum, p) { return sum + p.inCart; }, 0);
            var badge = link.querySelector('.cart-badge');
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'cart-badge';
                link.appendChild(badge);
            }
            badge.textContent = total;
        }

        // ----- Tambah ke keranjang lewat actions/cart/add.php (tanpa diubah) -----
        grid.addEventListener('click', function (e) {
            var b = e.target.closest('.rec-cart-btn');
            if (!b || b.disabled) return;

            var p = byId[b.dataset.id];
            if (!p) return;

            // Aturan yang sama dengan add.php: jumlah di keranjang + 1 tidak boleh melebihi stok
            if (p.inCart + 1 > p.stock) {
                toast('Stock limit reached for ' + p.name + ' (stock: ' + p.stock + ').', 'error');
                return;
            }

            b.disabled = true;
            fetch(CART_URL, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'product_id=' + encodeURIComponent(p.id) + '&quantity=1'
            })
            .then(function (res) {
                // add.php selalu me-redirect. Berhasil kalau berakhir di halaman detail produk.
                if (!res.ok || !/show\.php/.test(res.url)) throw new Error('failed');
                p.inCart += 1;
                b.classList.add('added');
                updateBadge();
                toast(p.name + ' added to cart');
            })
            .catch(function () {
                toast('Could not add to cart. Please try again.', 'error');
            })
            .then(function () { b.disabled = false; });
        });

        // ----- Hasil awal sesuai pilihan bawaan -----
        render(recommend(answers()));
    })();
    </script>
</body>
</html>