<?php

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$helpItems = [
    ['title' => 'How to order?',            'desc' => 'Learn easy steps…', 'link' => '../products/index.php'],
    ['title' => 'How to edit profile?',     'desc' => 'Learn easy steps…', 'link' => '../profile/index.php'],
    ['title' => 'How to pay for an order?', 'desc' => 'Learn easy steps…', 'link' => '../checkout/index.php'],
    ['title' => 'How to check out?',        'desc' => 'Learn how to….',    'link' => '../checkout/index.php'],
    ['title' => 'Comparing products?',      'desc' => 'Check our…',        'link' => '../compare/index.php'],
];

// 'href' (opsional): kalau diisi, kartu bisa diklik. 'external' = buka tab baru.
$contactInfo = [
    ['icon' => '🎧', 'title' => 'Customer Service', 'text' => '08:00 AM – 10:00 PM',   'href' => null],
    ['icon' => '💬', 'title' => 'WhatsApp',         'text' => '+62 812 3456 7890',     'href' => 'https://wa.me/6281234567890', 'external' => true],
    ['icon' => '✉️', 'title' => 'Email',            'text' => 'support@laptoppro.id',  'href' => 'mailto:support@laptoppro.id'],
];

$errors  = [];
$success = isset($_GET['sent']);   // true setelah redirect dari pengiriman form
$old     = ['fullname' => '', 'email' => '', 'subject' => '', 'message' => ''];
$isAjax  = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['fullname'] = trim($_POST['fullname'] ?? '');
    $old['email']    = trim($_POST['email'] ?? '');
    $old['subject']  = trim($_POST['subject'] ?? '');
    $old['message']  = trim($_POST['message'] ?? '');

    if ($old['fullname'] === '' || mb_strlen($old['fullname']) < 3) {
        $errors['fullname'] = 'Full name must be at least 3 characters.';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if ($old['subject'] === '' || mb_strlen($old['subject']) < 3) {
        $errors['subject'] = 'Subject must be at least 3 characters.';
    }
    if ($old['message'] === '' || mb_strlen($old['message']) < 10) {
        $errors['message'] = 'Message must be at least 10 characters.';
    }

    if (!$errors) {
        // TODO: simpan pesan ke database atau kirim ke email di sini
    }

    // Dikirim lewat JavaScript (tanpa reload): balas JSON
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => !$errors, 'errors' => $errors]);
        exit;
    }

    // Tanpa JavaScript: redirect supaya refresh tidak mengirim ulang form
    if (!$errors) {
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?sent=1#contact');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help &amp; Contact | TechHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../styles/base.css">
    <link rel="stylesheet" href="../../../styles/pages/contact.css">
</head>
<body class="contact-page">

    <div class="top-banner">
        Discover Premium PC Components Built for Performance, Speed, and Reliability
    </div>

    <header class="site-header">
        <a href="../../../index.php" class="logo">
            <img src="logo.png" alt="TechHub - Power Your Build">
        </a>
        <form class="search-box" action="../products/index.php" method="get">
            <input type="text" name="q" placeholder="Search for parts to build your dream PC...">
            <button type="submit" aria-label="Search">🔍</button>
        </form>
        <nav class="header-icons">
            <a href="../products/index.php" aria-label="Recommendation">👍</a>
            <a href="../compare/index.php" aria-label="Compare">⇄</a>
            <a href="../cart/index.php" aria-label="Cart">🛒</a>
            <a href="../profile/index.php" aria-label="Profile">👤</a>
        </nav>
    </header>

    <main class="container">

        <div class="page-title">
            <h1>Help &amp; Contact</h1>
            <a href="javascript:history.back()" class="back-link">Back <span>→</span></a>
        </div>

        <section class="hero">
            <div class="hero-text">
                <h2>Help &amp; Contact</h2>
                <p>We are ready to help with your PC component needs</p>
            </div>
            <div class="hero-illustration" aria-hidden="true">
                <div class="laptop"></div>
                <div class="bubble">?</div>
            </div>
        </section>

        <section class="panel help-center" id="help-center">
            <h3 class="panel-title">Help Center</h3>
            <div class="help-grid">
                <?php foreach ($helpItems as $item): ?>
                    <article class="help-card">
                        <span class="help-icon">?</span>
                        <h4><?= e($item['title']) ?></h4>
                        <p><?= e($item['desc']) ?></p>
                        <a href="<?= e($item['link']) ?>">See more</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel contact" id="contact">
            <h3 class="panel-title">Contact us</h3>

            <div class="contact-wrapper">

                <form class="contact-form" method="post" action="" novalidate>
                    <?php if ($success): ?>
                        <div class="alert alert-success" role="status">Thank you! Your message has been sent. We will get back to you soon.</div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="fullname">Full name</label>
                        <input type="text" id="fullname" name="fullname"
                               placeholder="Enter your full name"
                               value="<?= e($old['fullname']) ?>"
                               class="<?= isset($errors['fullname']) ? 'invalid' : '' ?>">
                        <?php if (isset($errors['fullname'])): ?><small class="error-text"><?= e($errors['fullname']) ?></small><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                               placeholder="Enter your email"
                               value="<?= e($old['email']) ?>"
                               class="<?= isset($errors['email']) ? 'invalid' : '' ?>">
                        <?php if (isset($errors['email'])): ?><small class="error-text"><?= e($errors['email']) ?></small><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject"
                               placeholder="Enter message subject"
                               value="<?= e($old['subject']) ?>"
                               class="<?= isset($errors['subject']) ? 'invalid' : '' ?>">
                        <?php if (isset($errors['subject'])): ?><small class="error-text"><?= e($errors['subject']) ?></small><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="6"
                                  placeholder="Write your message here..."
                                  class="<?= isset($errors['message']) ? 'invalid' : '' ?>"><?= e($old['message']) ?></textarea>
                        <?php if (isset($errors['message'])): ?><small class="error-text"><?= e($errors['message']) ?></small><?php endif; ?>
                    </div>

                    <button type="submit" class="btn-submit">Submit</button>
                </form>

                <!-- Contact info -->
                <aside class="contact-info">
                    <h4>Contact Information</h4>
                    <?php foreach ($contactInfo as $info):
                        $href  = $info['href'] ?? null;
                        $tag   = $href ? 'a' : 'div';
                        $attrs = $href
                            ? ' href="' . e($href) . '"' . (!empty($info['external']) ? ' target="_blank" rel="noopener"' : '')
                            : '';
                    ?>
                        <<?= $tag ?> class="info-card"<?= $attrs ?>>
                            <span class="info-icon"><?= $info['icon'] ?></span>
                            <div>
                                <strong><?= e($info['title']) ?></strong>
                                <p><?= e($info['text']) ?></p>
                            </div>
                        </<?= $tag ?>>
                    <?php endforeach; ?>
                </aside>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="footer-brand">
            <a href="../../../index.php" class="logo">
                <img src="logo.png" alt="TechHub - Power Your Build">
            </a>
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
            <a href="#help-center">How to Order</a>
            <a href="#contact">Shipping</a>
            <a href="#help-center">Payment Methods</a>
            <a href="#help-center">FAQ</a>
            <a href="#contact">Contact Us</a>
        </div>

        <div class="footer-col">
            <h5>My Account</h5>
            <a href="../profile/index.php">My Profile</a>
            <a href="../products/index.php">Recommendation</a>
            <a href="../compare/index.php">Compare Products</a>
            <a href="../cart/index.php">Shopping Cart</a>
            <a href="../products/index.php">Search Products</a>
        </div>

        <div class="footer-col">
            <h5>Follow Us</h5>
            <div class="social">
                <a href="#" aria-label="Instagram">IG</a>
                <a href="#" aria-label="Facebook">f</a>
                <a href="#" aria-label="YouTube">▶</a>
                <a href="#" aria-label="WhatsApp">WA</a>
                <a href="#" aria-label="TikTok">TT</a>
                <a href="#" aria-label="X">X</a>
            </div>
        </div>
    </footer>

    <script>
    (function () {
        var form = document.querySelector('.contact-form');
        if (!form) return;

        var rules = {
            fullname: function (v) { return v.length < 3 ? 'Full name must be at least 3 characters.' : ''; },
            email:    function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? '' : 'Please enter a valid email address.'; },
            subject:  function (v) { return v.length < 3 ? 'Subject must be at least 3 characters.' : ''; },
            message:  function (v) { return v.length < 10 ? 'Message must be at least 10 characters.' : ''; }
        };
        var fields  = Object.keys(rules);
        var btn     = form.querySelector('.btn-submit');
        var btnText = btn.textContent;
        var hideTimer;

        // ----- Tampilkan / hapus pesan error di bawah input -----
        function setError(name, msg) {
            var input = form.elements[name];
            var box   = input.closest('.form-group');
            var err   = box.querySelector('.error-text');

            if (msg) {
                if (!err) {
                    err = document.createElement('small');
                    err.className = 'error-text';
                    box.appendChild(err);
                }
                err.id = err.id || name + '-error';
                err.textContent = msg;
                input.classList.add('invalid');
                input.setAttribute('aria-invalid', 'true');
                input.setAttribute('aria-describedby', err.id);
            } else {
                if (err) err.remove();
                input.classList.remove('invalid');
                input.removeAttribute('aria-invalid');
                input.removeAttribute('aria-describedby');
            }
        }

        function validate(name) {
            var msg = rules[name](form.elements[name].value.trim());
            setError(name, msg);
            return !msg;
        }

        // Cek saat keluar dari input; setelah error muncul, cek ulang tiap ketikan
        fields.forEach(function (name) {
            var input = form.elements[name];
            input.addEventListener('blur', function () {
                if (input.value !== '' || input.classList.contains('invalid')) validate(name);
            });
            input.addEventListener('input', function () {
                if (input.classList.contains('invalid')) validate(name);
            });
        });

        // ----- Penghitung karakter pesan -----
        var msgBox  = form.elements.message;
        var counter = document.createElement('small');
        counter.className = 'char-count';
        msgBox.insertAdjacentElement('afterend', counter);

        function updateCount() {
            var n = msgBox.value.trim().length;
            counter.textContent = n + ' characters' + (n < 10 ? ' (min. 10)' : '');
        }
        msgBox.addEventListener('input', updateCount);
        updateCount();

        // ----- Alert sukses / gagal -----
        function autoHide(alertEl) {
            clearTimeout(hideTimer);
            hideTimer = setTimeout(function () {
                alertEl.classList.add('is-hiding');
                setTimeout(function () { alertEl.remove(); }, 400);
            }, 6000);
        }

        function showAlert(type, text) {
            var a = form.querySelector('.alert');
            if (!a) {
                a = document.createElement('div');
                form.insertBefore(a, form.firstChild);
            }
            a.className = 'alert alert-' + type;
            a.setAttribute('role', type === 'error' ? 'alert' : 'status');
            a.textContent = text;
            a.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            if (type === 'success') autoHide(a);
        }

        var existing = form.querySelector('.alert-success');
        if (existing) autoHide(existing);

        function setLoading(on) {
            btn.disabled = on;
            btn.classList.toggle('is-loading', on);
            btn.textContent = on ? 'Sending...' : btnText;
        }

        // ----- Kirim form tanpa reload -----
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var firstInvalid = null;
            fields.forEach(function (name) {
                if (!validate(name) && !firstInvalid) firstInvalid = name;
            });
            if (firstInvalid) {
                form.elements[firstInvalid].focus();
                return;
            }

            // Browser lama tanpa fetch: kirim biasa
            if (!window.fetch || !window.FormData) { form.submit(); return; }

            setLoading(true);
            fetch(form.action || window.location.href, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'fetch', 'Accept': 'application/json' }
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.ok) {
                    fields.forEach(function (name) {
                        form.elements[name].value = '';
                        setError(name, '');
                    });
                    updateCount();
                    showAlert('success', 'Thank you! Your message has been sent. We will get back to you soon.');
                } else {
                    var first = null;
                    Object.keys(data.errors || {}).forEach(function (name) {
                        setError(name, data.errors[name]);
                        if (!first) first = name;
                    });
                    if (first) form.elements[first].focus();
                }
            })
            .catch(function () {
                showAlert('error', 'Something went wrong. Please try again.');
            })
            .then(function () { setLoading(false); });
        });
    })();
    </script>

</body>
</html>
