<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../repositories/product-repository.php';

// Repository rating
$reviewRepo = __DIR__ . '/../../repositories/review-repository.php';
if (file_exists($reviewRepo)) {
    require_once $reviewRepo;
}

$cartUrl   = '../../actions/cart/add.php';
$imageBase = '/assets/img/';

if (!function_exists('e')) {
    function e($s)
    {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }
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

// ---------- Ambil data produk ----------
$raw = [];
$dbError = false;
try {
    $raw = getAllProducts();
} catch (Throwable $ex) {
    $dbError = true;
}

$cartSession = $_SESSION['cart'] ?? [];
$loggedIn    = isset($_SESSION['user']['id']);

$products = array_map(function ($r) use ($imageBase, $cartSession) {
    $id  = (int) $r['id'];
    $img = (string) ($r['picture'] ?? '');
    if ($img !== '' && !preg_match('#^(https?:)?//|^/#', $img)) {
        $img = $imageBase . $img;
    }

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
    $extra = pick($r, ['brand', 'specs', 'specification', 'description', 'deskripsi']);
    $p['search'] = mb_strtolower($p['name'] . ' ' . $p['category'] . ' ' . $extra);
    return $p;
}, $raw);

// ---------- Semua Pertanyaan Bertipe Radio (1 Pilihan Aktif) ----------
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
            <a href="javascript:history.back()" class="rec-back">Back <span>&rarr;</span></a>
        </div>

        <!-- Kuesioner -->
        <form class="rec-panel" id="rec-form" novalidate>
            <div class="question-grid">
                <?php foreach ($questions as $key => $q): ?>
                    <div class="question" data-q="<?= e($key) ?>" role="radiogroup" aria-labelledby="q-<?= e($key) ?>">
                        <h4 id="q-<?= e($key) ?>"><?= e($q['title']) ?></h4>
                        <?php foreach ($q['options'] as $val => $label): ?>
                            <label class="check">
                                <input type="radio"
                                    name="<?= e($key) ?>"
                                    value="<?= e($val) ?>"
                                    <?= $val === $q['default'] ? 'checked' : '' ?>>
                                <span class="box" aria-hidden="true"></span>
                                <span class="check-text"><?= e($label) ?></span>
                            </label>
                        <?php endforeach; ?>
                        <small class="q-error" hidden>Please choose an answer.</small>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="rec-submit-row">
                <button type="submit" class="rec-submit" id="btn-submit">Submit</button>
            </div>
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
        (function() {
            var PRODUCTS = <?= json_encode($products, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
            var DB_ERROR = <?= $dbError ? 'true' : 'false' ?>;
            var LOGGED_IN = <?= $loggedIn ? 'true' : 'false' ?>;
            var CART_URL = <?= json_encode($cartUrl) ?>;
            var MAX_RESULTS = 5;

            var form = document.getElementById('rec-form');
            var grid = document.getElementById('grid');
            var empty = document.getElementById('empty');
            var count = document.getElementById('count');
            var toastEl = document.getElementById('toast');
            var btn = document.getElementById('btn-submit');
            var toastTimer;

            var byId = {};
            PRODUCTS.forEach(function(p) {
                byId[p.id] = p;
            });

            function esc(s) {
                return String(s).replace(/[&<>"']/g, function(c) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    } [c];
                });
            }

            function rupiah(n) {
                return 'Rp ' + Number(n).toLocaleString('id-ID');
            }

            var KW = {
                heavy: ['rtx', 'gtx', 'radeon', 'geforce', 'gaming', 'victus', 'aorus', 'nitro', 'legion', 'rog', 'tuf', 'ryzen 7', 'ryzen 9', 'i7', 'i9'],
                design: ['rtx', 'creator', 'oled', 'studio', 'ryzen 7', 'ryzen 9', 'i7', 'i9', '16gb', '32gb', '16 gb', '32 gb'],
                office: ['ryzen 3', 'ryzen 5', 'i3', 'i5', 'vivobook', 'ideapad', 'aspire', 'thinkpad', 'office', 'slim', '8gb', '8 gb'],
                light: ['gtx', 'ryzen 5', 'i5', 'aspire', 'vivobook']
            };

            function hits(text, list) {
                return list.filter(function(k) {
                    return text.indexOf(k) !== -1;
                }).length;
            }

            // ----- Jawaban tunggal (radio) -----
            function answers() {
                var fd = new FormData(form);
                return {
                    need: fd.get('need'),
                    budget: fd.get('budget'),
                    apps: fd.get('apps'),
                    usage: fd.get('usage')
                };
            }

            function validate() {
                var a = answers();
                var checks = {
                    need: !!a.need,
                    budget: !!a.budget,
                    apps: !!a.apps,
                    usage: !!a.usage
                };
                var first = null;
                Object.keys(checks).forEach(function(key) {
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
                    first.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
                return !first;
            }

            // Otomatis perbarui hasil saat opsi dipilih + hapus error
            form.addEventListener('change', function(e) {
                var box = e.target.closest('.question');
                if (box) {
                    box.classList.remove('has-error');
                    box.querySelector('.q-error').hidden = true;
                }
                if (validate()) {
                    render(recommend(answers()));
                }
            });

            // ----- Logika rekomendasi -----
            function inBudget(price, b) {
                if (b === 'low') return price < 5000000;
                if (b === 'mid') return price >= 5000000 && price <= 10000000;
                if (b === 'high') return price > 10000000;
                return true;
            }

            function recommend(a) {
                return PRODUCTS
                    .filter(function(p) {
                        return inBudget(p.price, a.budget) && p.stock > 0;
                    })
                    .map(function(p) {
                        var s = 0,
                            t = p.search;
                        if (a.need === 'gaming') s += hits(t, KW.heavy);
                        if (a.need === 'design') s += hits(t, KW.design);
                        if (a.need === 'study') s += hits(t, KW.office);
                        if (a.apps === 'heavy') s += hits(t, KW.heavy);
                        if (a.apps === 'light') s += hits(t, KW.light);
                        if (a.apps === 'office') s += hits(t, KW.office);
                        var ratingWeight = a.usage === 'most' ? 0.6 : 0.3;
                        return {
                            p: p,
                            score: s * 2 + (p.rating || 0) * ratingWeight
                        };
                    })
                    .sort(function(x, y) {
                        return y.score - x.score || (y.p.rating || 0) - (x.p.rating || 0) || x.p.price - y.p.price;
                    })
                    .slice(0, MAX_RESULTS)
                    .map(function(x) {
                        return x.p;
                    });
            }

            // ----- Tampilan Kartu Produk Sesuai Referensi -----
            function cardHtml(p, i) {
                var link = '../products/show.php?id=' + p.id;
                var thumb = p.image ?
                    '<img src="' + esc(p.image) + '" alt="' + esc(p.name) + '" loading="lazy">' :
                    '<span class="rec-fallback"><i class="fa-solid fa-desktop"></i></span>';
                var rating = p.rating !== null ?
                    Number(p.rating).toFixed(1) + ' <span class="rec-star">&#9733;</span>' :
                    '<span class="rec-muted">No rating</span>';
                return '<article class="rec-card" style="--i:' + i + '">' +
                    '<div class="rec-card-top">' +
                    '<a class="rec-thumb" href="' + link + '">' + thumb + '</a>' +
                    '<a class="rec-name" href="' + link + '">' + esc(p.name) + '</a>' +
                    '</div>' +
                    '<div class="rec-card-footer">' +
                    '<div class="rec-rating">' + rating + '</div>' +
                    '<div class="rec-bottom">' +
                    '<strong class="rec-price">' + rupiah(p.price) + '</strong>' +
                    '<button type="button" class="rec-cart-btn" data-id="' + p.id + '" aria-label="Add ' + esc(p.name) + ' to cart">' +
                    '<i class="fa-solid fa-cart-shopping"></i>' +
                    '</button>' +
                    '</div>' +
                    '</div>' +
                    '</article>';
            }

            function render(list) {
                grid.innerHTML = list.map(cardHtml).join('');
                empty.hidden = list.length > 0;
                count.textContent = list.length ? list.length + ' recommendation' + (list.length > 1 ? 's' : '') + ' found' : '';
                if (!list.length) {
                    empty.textContent = DB_ERROR ?
                        'Failed to load products from the database.' :
                        PRODUCTS.length === 0 ?
                        'There are no products in the database yet.' :
                        'No products match this budget. Try choosing a different budget.';
                }
            }

            function showSkeleton() {
                var html = '';
                for (var i = 0; i < MAX_RESULTS; i++) html += '<div class="rec-card rec-skeleton" aria-hidden="true"></div>';
                grid.innerHTML = html;
                empty.hidden = true;
                count.textContent = '';
            }

            grid.addEventListener('error', function(e) {
                if (e.target.tagName === 'IMG') {
                    var span = document.createElement('span');
                    span.className = 'rec-fallback';
                    span.innerHTML = '<i class="fa-solid fa-desktop"></i>';
                    e.target.replaceWith(span);
                }
            }, true);

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!validate()) return;

                btn.disabled = true;
                btn.textContent = 'Searching...';
                showSkeleton();
                document.getElementById('results').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

                setTimeout(function() {
                    render(recommend(answers()));
                    btn.disabled = false;
                    btn.textContent = 'Submit';
                }, 400);
            });

            function toast(msg, type) {
                clearTimeout(toastTimer);
                toastEl.textContent = msg;
                toastEl.className = 'rec-toast' + (type ? ' ' + type : '');
                toastEl.hidden = false;
                toastTimer = setTimeout(function() {
                    toastEl.hidden = true;
                }, 2800);
            }

            function updateBadge() {
                if (!LOGGED_IN) return;
                var link = document.querySelector('.cart-icon-link');
                if (!link) return;
                var total = PRODUCTS.reduce(function(sum, p) {
                    return sum + p.inCart;
                }, 0);
                var badge = link.querySelector('.cart-badge');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'cart-badge';
                    link.appendChild(badge);
                }
                badge.textContent = total;
            }

            grid.addEventListener('click', function(e) {
                var b = e.target.closest('.rec-cart-btn');
                if (!b || b.disabled) return;

                var p = byId[b.dataset.id];
                if (!p) return;

                if (p.inCart + 1 > p.stock) {
                    toast('Stock limit reached for ' + p.name + ' (stock: ' + p.stock + ').', 'error');
                    return;
                }

                b.disabled = true;
                fetch(CART_URL, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'product_id=' + encodeURIComponent(p.id) + '&quantity=1'
                    })
                    .then(function(res) {
                        if (!res.ok || !/show\.php/.test(res.url)) throw new Error('failed');
                        p.inCart += 1;
                        b.classList.add('added');
                        updateBadge();
                        toast(p.name + ' added to cart');
                    })
                    .catch(function() {
                        toast('Could not add to cart. Please try again.', 'error');
                    })
                    .then(function() {
                        b.disabled = false;
                    });
            });

            render(recommend(answers()));
        })();
    </script>
</body>

</html>