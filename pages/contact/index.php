<?php
session_start();
require_once __DIR__ . "/../../repositories/user-repository.php";

if (!function_exists('hEsc')) {
    function hEsc($s): string
    {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }
}

// ===== Data panduan (isi modal "See more") =====
// Setiap langkah: [judul, penjelasan]
$guides = [
    'order' => [
        'question' => 'How to order?',
        'short'    => 'Learn easy steps..',
        'title'    => 'How to order',
        'intro'    => 'Learn easy steps to place your order:',
        'steps'    => [
            ['Select Product', 'Find and select the product you wish to purchase.'],
            ['Add to Cart / Checkout', 'Click the buy button or add the item to your shopping cart.'],
            ['Fill Shipping Address', 'Complete your personal details and full delivery address.'],
            ['Make Payment', 'Select your payment method and complete the transaction.'],
            ['Order Processing', 'Wait for confirmation, and your order will be shipped shortly.'],
        ],
    ],
    'profile' => [
        'question' => 'How to edit profile?',
        'short'    => 'Learn easy steps..',
        'title'    => 'How to edit profile',
        'intro'    => 'Learn easy steps to update your personal profile:',
        'steps'    => [
            ['Log In to Your Account', 'Sign in using your registered email and password.'],
            ['Go to Profile Settings', 'Click on your avatar/profile picture at the top corner and select "Profile" or "Account Settings".'],
            ['Edit Information', 'Click the "Edit" button to update your name, phone number, shipping address, or profile picture.'],
            ['Save Changes', 'Click "Save" or "Update" to apply your new details.'],
        ],
    ],
    'pay' => [
        'question' => 'How to pay for an order?',
        'short'    => 'Learn easy steps..',
        'title'    => 'How to pay an order',
        'intro'    => 'Learn easy steps to pay for your order:',
        'steps'    => [
            ['Open Checkout', 'Review your cart, fill in your shipping address, and choose a shipping method.'],
            ['Choose Payment Method', 'Select Credit / Debit Card, Bank Transfer (Virtual Account), or E-Wallet.'],
            ['Complete the Payment', 'Click "Bayar Sekarang", fill in the payment details in the pop-up, then click "Pay Now".'],
            ['Confirm Payment', 'Wait for the confirmation, or upload your transfer receipt if you pay by bank transfer.'],
        ],
    ],
    'checkout' => [
        'question' => 'How to check out?',
        'short'    => 'Learn how to....',
        'title'    => 'How to checkout',
        'intro'    => 'Learn how to complete your order checkout in a few simple steps:',
        'steps'    => [
            ['Open Shopping Cart', 'Click on the Cart icon at the top right of the page to review your selected items.'],
            ['Review Your Order', 'Check the quantity, colors, or options of your items.'],
            ['Apply Vouchers / Coupons', 'Enter any promo codes or select available discount vouchers if you have them.'],
            ['Proceed to Checkout', 'Click the "Checkout" button to proceed.'],
            ['Fill Shipping & Payment Details', 'Enter your shipping address, choose your delivery service, select a payment method, and confirm your order.'],
        ],
    ],
    'compare' => [
        'question' => 'Comparing products?',
        'short'    => 'Check our ...',
        'title'    => 'Comparing Products?',
        'intro'    => 'Check our quick guide to compare items effectively and choose the best one:',
        'steps'    => [
            ['Select Products', 'Click the "Compare" button on any product card or listing page to add items to your comparison list.'],
            ['Open Comparison View', 'Go to the comparison page to view specifications, prices, and features side-by-side.'],
            ['Analyze Differences', 'Review key factors such as materials, dimensions, or technical specifications highlighted in the comparison table.'],
            ['Make your best choice', ''],
        ],
    ],
];

// ===== Autofill nama & email jika user sudah login (sama seperti halaman checkout) =====
$currentUser = null;
if (isset($_SESSION['user']['id'])) {
    $currentUser = getUser($_SESSION['user']['id']);
}

$old = [
    'name'    => $currentUser['name'] ?? '',
    'email'   => $currentUser['email'] ?? '',
    'subject' => '',
    'message' => '',
];
$errors = [];

// ===== Proses form kontak =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $_) {
        $old[$key] = trim((string)($_POST[$key] ?? ''));
    }

    if ($old['name'] === '') {
        $errors['name'] = 'Full name is required.';
    }
    if ($old['email'] === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if ($old['subject'] === '') {
        $errors['subject'] = 'Subject is required.';
    }
    if ($old['message'] === '') {
        $errors['message'] = 'Message is required.';
    } elseif (mb_strlen($old['message']) > 500) {
        $errors['message'] = 'Message may not exceed 500 characters.';
    }

    if (!$errors) {
        // TODO: simpan ke database (mis. lewat repository) atau kirim email di sini

        // Post/Redirect/Get: mencegah form terkirim ulang saat halaman di-refresh
        $_SESSION['flash_success'] = 'Thank you! Your message has been sent. We will reply as soon as possible.';
        header('Location: index.php#contact');
        exit;
    }
}

$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub - Help &amp; Contact</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap 4.6 CSS (dimuat lebih dulu agar CSS kustom di bawahnya tidak tertimpa) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <!-- CSS Kustom -->
    <link rel="stylesheet" href="../../styles/pages/contact.css">
    <link rel="stylesheet" href="../../styles/components/header.css">
    <link rel="stylesheet" href="../../styles/components/footer.css">
</head>

<body>

    <?php require_once __DIR__ . "/../../components/landing/header.php"; ?>

    <!-- MAIN CONTENT / HELP & CONTACT SECTION -->
    <main class="help-main">
        <div class="help-header-title">
            <h1>Help &amp; Contact</h1>
            <a href="../../index.php" class="back-link" onclick="if (history.length > 1) { history.back(); return false; }">Back <span><i class="fa-solid fa-arrow-right"></i></span></a>
        </div>

        <section class="hero">
            <div class="hero-text">
                <h2>Help &amp; Contact</h2>
                <p>We are ready to help with your PC component needs</p>
            </div>
            <div class="hero-art" aria-hidden="true">
                <img src="../../assets/img/hero-laptop.png" alt="">
                <div class="bubble">?</div>
            </div>
        </section>

        <!-- HELP CENTER -->
        <section class="panel help-center" id="help-center">
            <h2>Help Center</h2>
            <div class="help-grid">
                <?php foreach ($guides as $id => $guide): ?>
                    <article class="help-card">
                        <span class="help-icon"><i class="fa-regular fa-circle-question"></i></span>
                        <h3><?= hEsc($guide['question']) ?></h3>
                        <p><?= hEsc($guide['short']) ?></p>
                        <a href="#guide-<?= hEsc($id) ?>" role="button" data-guide="<?= hEsc($id) ?>">See more</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- CONTACT -->
        <section class="panel contact" id="contact">
            <div class="contact-form">
                <h2>Contact us</h2>

                <?php if ($flashSuccess): ?>
                    <div class="alert alert-success" role="status">
                        <i class="fa-solid fa-circle-check"></i> <?= hEsc($flashSuccess) ?>
                    </div>
                <?php endif; ?>

                <form action="index.php#contact" method="POST" id="contactForm" novalidate>
                    <div class="form-group">
                        <label for="name">Full name</label>
                        <input type="text" id="name" name="name" placeholder="Enter your full name"
                               value="<?= hEsc($old['name']) ?>" class="<?= isset($errors['name']) ? 'invalid' : '' ?>">
                        <?php if (isset($errors['name'])): ?><p class="error-text"><?= hEsc($errors['name']) ?></p><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email"
                               value="<?= hEsc($old['email']) ?>" class="<?= isset($errors['email']) ? 'invalid' : '' ?>">
                        <?php if (isset($errors['email'])): ?><p class="error-text"><?= hEsc($errors['email']) ?></p><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="Enter message subject"
                               value="<?= hEsc($old['subject']) ?>" class="<?= isset($errors['subject']) ? 'invalid' : '' ?>">
                        <?php if (isset($errors['subject'])): ?><p class="error-text"><?= hEsc($errors['subject']) ?></p><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" maxlength="500" placeholder="Write your message here.."
                                  class="<?= isset($errors['message']) ? 'invalid' : '' ?>"><?= hEsc($old['message']) ?></textarea>
                        <div class="char-count"><span id="charNow"><?= mb_strlen($old['message']) ?></span>/500</div>
                        <?php if (isset($errors['message'])): ?><p class="error-text"><?= hEsc($errors['message']) ?></p><?php endif; ?>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">Submit</button>
                </form>
            </div>

            <aside class="contact-info">
                <h3>Contact Information</h3>

                <div class="info-card">
                    <span class="info-icon"><i class="fa-solid fa-headset"></i></span>
                    <div>
                        <strong>Customer Service</strong>
                        <p>08.00 AM &ndash; 10.00 PM</p>
                    </div>
                </div>

                <a class="info-card" href="https://wa.me/6281234567890" target="_blank" rel="noopener">
                    <span class="info-icon"><i class="fa-brands fa-whatsapp"></i></span>
                    <div>
                        <strong>WhatsApp</strong>
                        <p>+62 812 3456 7890</p>
                    </div>
                </a>

                <a class="info-card" href="mailto:support@laptoppro.id">
                    <span class="info-icon"><i class="fa-regular fa-envelope"></i></span>
                    <div>
                        <strong>Email</strong>
                        <p>support@laptoppro.id</p>
                    </div>
                </a>
            </aside>
        </section>
    </main>

    <!-- MODAL PANDUAN (dibuka lewat JavaScript saat "See more" ditekan) -->
    <div class="btp-backdrop" id="guideModal" aria-hidden="true">
        <div class="btp-modal" role="dialog" aria-modal="true" aria-labelledby="guideTitle">
            <div class="btp-modal-header">
                <h3 id="guideTitle"></h3>
                <button type="button" class="btp-close-btn" data-close-modal="guideModal" aria-label="Close">&times;</button>
            </div>

            <p class="btp-modal-intro" id="guideIntro"></p>
            <div id="guideSteps"></div>

            <div class="btp-modal-footer">
                <button type="button" class="btp-btn-pay" data-close-modal="guideModal">Got it</button>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . "/../../components/landing/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

    <script>
        // ===== Data panduan dari PHP =====
        const guides = <?= json_encode($guides, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

        const guideModal = document.getElementById('guideModal');
        const guideTitle = document.getElementById('guideTitle');
        const guideIntro = document.getElementById('guideIntro');
        const guideSteps = document.getElementById('guideSteps');
        let lastTrigger = null;

        // 1. Isi modal dengan data panduan yang dipilih
        function renderGuide(guide) {
            guideTitle.textContent = guide.title;
            guideIntro.textContent = guide.intro;
            guideSteps.innerHTML = '';

            guide.steps.forEach(function(step, index) {
                const section = document.createElement('div');
                section.className = 'btp-section';

                const header = document.createElement('div');
                header.className = 'btp-section-header';

                const badge = document.createElement('span');
                badge.className = 'btp-step-badge';
                badge.textContent = index + 1;

                const title = document.createElement('h4');
                title.className = 'btp-section-title';
                title.textContent = step[0];

                header.appendChild(badge);
                header.appendChild(title);
                section.appendChild(header);

                if (step[1]) {
                    const desc = document.createElement('p');
                    desc.className = 'btp-step-desc';
                    desc.textContent = step[1];
                    section.appendChild(desc);
                }
                guideSteps.appendChild(section);
            });
        }

        // 2. Fungsi Tutup Semua Modal
        function closeAllModals() {
            document.querySelectorAll('.btp-backdrop').forEach(function(modal) {
                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
            });
            document.body.classList.remove('btp-lock');

            if (location.hash.indexOf('#guide-') === 0) {
                history.replaceState(null, '', location.pathname + location.search);
            }
            if (lastTrigger) {
                lastTrigger.focus();
                lastTrigger = null;
            }
        }

        // 3. Buka modal sesuai kartu "See more" yang ditekan
        function openGuide(id, trigger) {
            const guide = guides[id];
            if (!guide) return;
            renderGuide(guide);
            lastTrigger = trigger || lastTrigger;
            guideModal.classList.add('show');
            guideModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('btp-lock');
            guideModal.querySelector('.btp-close-btn').focus();
        }

        document.querySelectorAll('[data-guide]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                openGuide(link.getAttribute('data-guide'), link);
            });
        });

        // 4. Tutup modal jika klik tombol silang / "Got it" atau klik area gelap (backdrop)
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btp-backdrop') || e.target.closest('.btp-close-btn, [data-close-modal]')) {
                closeAllModals();
            }
        });

        // 5. Tombol Esc menutup modal, Tab tetap berputar di dalam modal
        document.addEventListener('keydown', function(e) {
            if (!guideModal.classList.contains('show')) return;
            if (e.key === 'Escape') {
                closeAllModals();
                return;
            }
            if (e.key === 'Tab') {
                const items = guideModal.querySelectorAll('.btp-modal button');
                const first = items[0];
                const last = items[items.length - 1];
                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        });

        // 6. Link dari footer/halaman lain (index.php#guide-order) langsung membuka modal
        function openFromHash() {
            if (location.hash.indexOf('#guide-') === 0) {
                openGuide(location.hash.slice(7));
            }
        }
        window.addEventListener('hashchange', openFromHash);
        openFromHash();

        // ===== Form kontak =====
        const contactForm = document.getElementById('contactForm');
        const submitBtn = document.getElementById('submitBtn');
        const messageInput = document.getElementById('message');
        const charNow = document.getElementById('charNow');

        messageInput.addEventListener('input', function() {
            charNow.textContent = messageInput.value.length;
        });

        function setError(input, text) {
            const group = input.closest('.form-group');
            const old = group.querySelector('.error-text');
            if (old) old.remove();
            input.classList.toggle('invalid', !!text);
            if (text) {
                const p = document.createElement('p');
                p.className = 'error-text';
                p.textContent = text;
                group.appendChild(p);
            }
        }

        contactForm.querySelectorAll('input, textarea').forEach(function(el) {
            el.addEventListener('input', function() {
                setError(el, '');
            });
        });

        contactForm.addEventListener('submit', function(e) {
            const f = contactForm.elements;
            const rules = [
                [f.name, !f.name.value.trim(), 'Full name is required.'],
                [f.email, !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.email.value.trim()), 'Please enter a valid email address.'],
                [f.subject, !f.subject.value.trim(), 'Subject is required.'],
                [f.message, !f.message.value.trim(), 'Message is required.']
            ];

            let firstInvalid = null;
            rules.forEach(function(rule) {
                setError(rule[0], rule[1] ? rule[2] : '');
                if (rule[1] && !firstInvalid) firstInvalid = rule[0];
            });

            if (firstInvalid) {
                e.preventDefault();
                firstInvalid.focus();
                return;
            }

            submitBtn.classList.add('is-loading');
            submitBtn.textContent = 'Sending...';
            setTimeout(function() {
                submitBtn.disabled = true;
            }, 0);
        });

        // ===== Alert sukses hilang sendiri =====
        const alertBox = document.querySelector('.alert');
        if (alertBox) {
            setTimeout(function() {
                alertBox.classList.add('is-hiding');
                setTimeout(function() {
                    alertBox.remove();
                }, 400);
            }, 5000);
        }
    </script>
</body>

</html>
