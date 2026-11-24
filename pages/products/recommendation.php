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

    $description = (string) pick($r, ['description', 'deskripsi'], '');

    return [
        'id'          => $id,
        'name'        => (string) $r['name'],
        'category'    => (string) ($r['category_name'] ?? ''),
        'description' => $description,
        'descLower'   => mb_strtolower($description),
        'price'       => (int) $r['price'],
        'stock'       => (int) ($r['stock'] ?? 0),
        'image'       => $img,
        'rating'      => $rating,
        'inCart'      => (int) ($cartSession[$id]['quantity'] ?? 0),
    ];
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
    <style>
        .rec-see-more-wrap {
            display: flex;
            justify-content: center;
            margin-top: 24px;
        }

        .rec-see-more-btn {
            padding: 10px 24px;
            border: 2px solid #0056c6;
            border-radius: 8px;
            background: #ffffff;
            color: #0056c6;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }

        .rec-see-more-btn:hover {
            background: #0056c6;
            color: #ffffff;
        }
    </style>
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

        <!-- Hasil rekomendasi (muncul setelah Submit ditekan) -->
        <section class="rec-results" id="results" hidden>
            <h2>Your Best Choice</h2>
            <p class="rec-sub">The following options have been adapted to your criteria</p>
            <p class="rec-count" id="count" aria-live="polite"></p>
            <div class="rec-grid" id="grid"></div>
            <div class="rec-see-more-wrap" id="see-more-wrap" hidden>
                <button type="button" class="rec-see-more-btn" id="btn-see-more">See More</button>
            </div>
            <p class="rec-empty" id="empty" hidden></p>
        </section>
    </main>

    <?php require_once __DIR__ . '/../../components/landing/footer.php'; ?>

    <div class="rec-toast" id="toast" role="status" hidden></div>

    <script>
        (function() {
            const PRODUCTS = <?= json_encode($products, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
            const DB_ERROR = <?= $dbError ? 'true' : 'false' ?>;
            const LOGGED_IN = <?= $loggedIn ? 'true' : 'false' ?>;
            const CART_URL = <?= json_encode($cartUrl) ?>;
            const INITIAL_LIMIT = 12;

            const form = document.getElementById('rec-form');
            const resultsSection = document.getElementById('results');
            const grid = document.getElementById('grid');
            const empty = document.getElementById('empty');
            const count = document.getElementById('count');
            const toastEl = document.getElementById('toast');
            const btn = document.getElementById('btn-submit');
            const seeMoreWrap = document.getElementById('see-more-wrap');
            const btnSeeMore = document.getElementById('btn-see-more');

            let toastTimer;
            let currentMatches = [];
            let showAll = false;

            const PHRASES = {
                need: {
                    gaming: 'gaming',
                    design: 'design & editing',
                    study: 'study & work'
                },
                apps: {
                    heavy: 'demanding games',
                    light: 'light games',
                    office: 'office applications'
                },
                usage: {
                    most: 'most of the day',
                    few: 'a few hours a day',
                    rare: 'occasionally'
                }
            };

            function esc(text) {
                return String(text || '').replace(/[&<>"']/g, (ch) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [ch]));
            }

            function rupiah(amount) {
                return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
            }

            function checkPrice(price, op, target) {
                const p = Number(price);
                const t = Number(target);
                if (op === '<') return p < t;
                if (op === '<=') return p <= t;
                if (op === '=') return p === t;
                if (op === '>=') return p >= t;
                if (op === '>') return p > t;
                return true;
            }

            function matchBudget(price, budget) {
                if (budget === 'low') return checkPrice(price, '<', 5000000);
                if (budget === 'mid') return checkPrice(price, '>=', 5000000) && checkPrice(price, '<=', 10000000);
                if (budget === 'high') return checkPrice(price, '>', 10000000);
                return true;
            }

            function likeDesc(desc, phrase) {
                if (!desc || !phrase) return false;
                return desc.includes(phrase.toLowerCase());
            }

            function getAnswers() {
                const data = new FormData(form);
                return {
                    need: data.get('need'),
                    budget: data.get('budget'),
                    apps: data.get('apps'),
                    usage: data.get('usage')
                };
            }

            function validateAnswers(ans) {
                let isValid = true;
                let firstInvalidBox = null;

                ['need', 'budget', 'apps', 'usage'].forEach((key) => {
                    const box = form.querySelector(`[data-q="${key}"]`);
                    const missing = !ans[key];
                    if (missing) isValid = false;

                    box.classList.toggle('has-error', missing);
                    box.querySelector('.q-error').hidden = !missing;

                    if (missing && !firstInvalidBox) {
                        firstInvalidBox = box;
                    }
                });

                if (firstInvalidBox) {
                    firstInvalidBox.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }

                return isValid;
            }

            function getRecommendations(ans) {
                const needPhrase = PHRASES.need[ans.need] || '';
                const appsPhrase = PHRASES.apps[ans.apps] || '';
                const usagePhrase = PHRASES.usage[ans.usage] || '';

                const validProducts = PRODUCTS.filter((p) => p.stock > 0 && matchBudget(p.price, ans.budget));

                const scored = validProducts
                    .map((p) => {
                        const d = p.descLower || '';
                        const matchNeed = likeDesc(d, needPhrase);
                        const matchApps = likeDesc(d, appsPhrase);
                        const matchUsage = likeDesc(d, usagePhrase);
                        const matchCount = (matchNeed ? 1 : 0) + (matchApps ? 1 : 0) + (matchUsage ? 1 : 0);

                        return {
                            product: p,
                            matchCount: matchCount,
                            rating: Number(p.rating || 0)
                        };
                    })
                    .filter((item) => item.matchCount > 0);

                scored.sort((a, b) => {
                    if (b.matchCount !== a.matchCount) return b.matchCount - a.matchCount;
                    if (b.rating !== a.rating) return b.rating - a.rating;
                    return a.product.price - b.product.price;
                });

                return scored.map((item) => item.product);
            }

            function renderCard(p, index) {
                const link = `../products/show.php?id=${p.id}`;
                const thumb = p.image ?
                    `<img src="${esc(p.image)}" alt="${esc(p.name)}" loading="lazy">` :
                    `<span class="rec-fallback"><i class="fa-solid fa-desktop"></i></span>`;

                const rating = p.rating !== null ?
                    `${Number(p.rating).toFixed(1)} <span class="rec-star">&#9733;</span>` :
                    `<span class="rec-muted">No rating</span>`;

                return `
                    <article class="rec-card" style="--i:${index}">
                        <div class="rec-card-top">
                            <a class="rec-thumb" href="${link}">${thumb}</a>
                            <a class="rec-name" href="${link}">${esc(p.name)}</a>
                        </div>
                        <div class="rec-card-footer">
                            <div class="rec-rating">${rating}</div>
                            <div class="rec-bottom">
                                <strong class="rec-price">${rupiah(p.price)}</strong>
                                <button type="button" class="rec-cart-btn" data-id="${p.id}" aria-label="Add ${esc(p.name)} to cart">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </button>
                            </div>
                        </div>
                    </article>
                `;
            }

            function renderResults() {
                resultsSection.hidden = false;
                const total = currentMatches.length;
                const visibleList = showAll ? currentMatches : currentMatches.slice(0, INITIAL_LIMIT);

                grid.innerHTML = visibleList.map((p, i) => renderCard(p, i)).join('');
                empty.hidden = total > 0;

                if (total > 0) {
                    count.textContent = `Showing ${visibleList.length} of ${total} recommendation${total > 1 ? 's' : ''}`;
                } else {
                    count.textContent = '';
                    if (DB_ERROR) {
                        empty.textContent = 'Failed to load products from the database.';
                    } else if (PRODUCTS.length === 0) {
                        empty.textContent = 'There are no products in the database yet.';
                    } else {
                        empty.textContent = 'No products match your selected budget and description criteria.';
                    }
                }

                if (total > INITIAL_LIMIT) {
                    seeMoreWrap.hidden = false;
                    btnSeeMore.textContent = showAll ? 'Show Less' : `See More (${total - INITIAL_LIMIT} more)`;
                } else {
                    seeMoreWrap.hidden = true;
                }
            }

            btnSeeMore.addEventListener('click', () => {
                showAll = !showAll;
                renderResults();
            });

            function showToast(msg, type) {
                clearTimeout(toastTimer);
                toastEl.textContent = msg;
                toastEl.className = 'rec-toast' + (type ? ` ${type}` : '');
                toastEl.hidden = false;
                toastTimer = setTimeout(() => {
                    toastEl.hidden = true;
                }, 2800);
            }

            function updateCartBadge() {
                if (!LOGGED_IN) return;
                const link = document.querySelector('.cart-icon-link');
                if (!link) return;

                const total = PRODUCTS.reduce((sum, p) => sum + Number(p.inCart || 0), 0);
                let badge = link.querySelector('.cart-badge');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'cart-badge';
                    link.appendChild(badge);
                }
                badge.textContent = total;
            }

            form.addEventListener('change', (e) => {
                const box = e.target.closest('.question');
                if (box) {
                    box.classList.remove('has-error');
                    box.querySelector('.q-error').hidden = true;
                }
            });

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const ans = getAnswers();
                if (!validateAnswers(ans)) return;

                btn.disabled = true;
                btn.textContent = 'Searching...';

                currentMatches = getRecommendations(ans);
                showAll = false;
                renderResults();

                btn.disabled = false;
                btn.textContent = 'Submit';

                resultsSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });

            grid.addEventListener('error', (e) => {
                if (e.target.tagName === 'IMG') {
                    const span = document.createElement('span');
                    span.className = 'rec-fallback';
                    span.innerHTML = '<i class="fa-solid fa-desktop"></i>';
                    e.target.replaceWith(span);
                }
            }, true);

            grid.addEventListener('click', (e) => {
                const cartBtn = e.target.closest('.rec-cart-btn');
                if (!cartBtn || cartBtn.disabled) return;

                const productId = Number(cartBtn.dataset.id);
                const product = PRODUCTS.find((p) => p.id === productId);
                if (!product) return;

                if (product.inCart + 1 > product.stock) {
                    showToast(`Stock limit reached for ${product.name} (stock: ${product.stock}).`, 'error');
                    return;
                }

                cartBtn.disabled = true;

                fetch(CART_URL, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `product_id=${encodeURIComponent(product.id)}&quantity=1`
                    })
                    .then((res) => {
                        if (!res.ok || !/show\.php/.test(res.url)) throw new Error('failed');
                        product.inCart += 1;
                        cartBtn.classList.add('added');
                        updateCartBadge();
                        showToast(`${product.name} added to cart`);
                    })
                    .catch(() => {
                        showToast('Could not add to cart. Please try again.', 'error');
                    })
                    .finally(() => {
                        cartBtn.disabled = false;
                    });
            });
        })();
    </script>
</body>

</html>