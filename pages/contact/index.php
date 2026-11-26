<?php
session_start();
require_once __DIR__ . "/../../repositories/user-repository.php";

// Isi panduan untuk modal "See more": judul langkah => penjelasan
$guides = [
    'order' => [
        'question' => 'How to order?',
        'short'    => 'Learn easy steps..',
        'title'    => 'How to order',
        'intro'    => 'Learn easy steps to place your order:',
        'steps'    => [
            'Select Product'         => 'Find and select the product you wish to purchase.',
            'Add to Cart / Checkout' => 'Click the buy button or add the item to your shopping cart.',
            'Fill Shipping Address'  => 'Complete your personal details and full delivery address.',
            'Make Payment'           => 'Select your payment method and complete the transaction.',
            'Order Processing'       => 'Wait for confirmation, and your order will be shipped shortly.',
        ],
    ],
    'profile' => [
        'question' => 'How to edit profile?',
        'short'    => 'Learn easy steps..',
        'title'    => 'How to edit profile',
        'intro'    => 'Learn easy steps to update your personal profile:',
        'steps'    => [
            'Log In to Your Account' => 'Sign in using your registered email and password.',
            'Go to Profile Settings' => 'Click on your avatar/profile picture at the top corner and select "Profile" or "Account Settings".',
            'Edit Information'       => 'Click the "Edit" button to update your name, phone number, shipping address, or profile picture.',
            'Save Changes'           => 'Click "Save" or "Update" to apply your new details.',
        ],
    ],
    'pay' => [
        'question' => 'How to pay for an order?',
        'short'    => 'Learn easy steps..',
        'title'    => 'How to pay an order',
        'intro'    => 'Learn easy steps to pay for your order:',
        'steps'    => [
            'Open Checkout'         => 'Review your cart, fill in your shipping address, and choose a shipping method.',
            'Choose Payment Method' => 'Select Credit / Debit Card, Bank Transfer (Virtual Account), or E-Wallet.',
            'Complete the Payment'  => 'Click "Bayar Sekarang", fill in the payment details in the pop-up, then click "Pay Now".',
            'Confirm Payment'       => 'Wait for the confirmation, or upload your transfer receipt if you pay by bank transfer.',
        ],
    ],
    'checkout' => [
        'question' => 'How to check out?',
        'short'    => 'Learn how to....',
        'title'    => 'How to checkout',
        'intro'    => 'Learn how to complete your order checkout in a few simple steps:',
        'steps'    => [
            'Open Shopping Cart'              => 'Click on the Cart icon at the top right of the page to review your selected items.',
            'Review Your Order'               => 'Check the quantity, colors, or options of your items.',
            'Apply Vouchers / Coupons'        => 'Enter any promo codes or select available discount vouchers if you have them.',
            'Proceed to Checkout'             => 'Click the "Checkout" button to proceed.',
            'Fill Shipping & Payment Details' => 'Enter your shipping address, choose your delivery service, select a payment method, and confirm your order.',
        ],
    ],
    'compare' => [
        'question' => 'Comparing products?',
        'short'    => 'Check our ...',
        'title'    => 'Comparing Products?',
        'intro'    => 'Check our quick guide to compare items effectively and choose the best one:',
        'steps'    => [
            'Select Products'       => 'Click the "Compare" button on any product card or listing page to add items to your comparison list.',
            'Open Comparison View'  => 'Go to the comparison page to view specifications, prices, and features side-by-side.',
            'Analyze Differences'   => 'Review key factors such as materials, dimensions, or technical specifications highlighted in the comparison table.',
            'Make your best choice' => '',
        ],
    ],
];

// Kalau sudah login, nama & email langsung terisi
$user = isset($_SESSION['user']['id']) ? getUser($_SESSION['user']['id']) : null;

$old = [
    'name'    => $user['name'] ?? '',
    'email'   => $user['email'] ?? '',
    'subject' => '',
    'message' => '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $field => $value) {
        $old[$field] = trim($_POST[$field] ?? '');
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

    if (empty($errors)) {
        // TODO: simpan pesan ke database atau kirim lewat email

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

    <!-- Bootstrap dimuat dulu supaya CSS kustom di bawahnya tidak tertimpa -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <!-- CSS Kustom -->
    <link rel="stylesheet" href="../../styles/pages/contact.css">
    <link rel="stylesheet" href="../../styles/components/header.css">
    <link rel="stylesheet" href="../../styles/components/footer.css">
</head>

<body>

    <?php require_once __DIR__ . "/../../components/landing/header.php"; ?>

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
            <div class="hero-art">
                <img src="../../assets/img/hero-laptop.png" alt="">
                <div class="bubble">?</div>
            </div>
        </section>

        <!-- Help Center -->
        <section class="panel help-center" id="help-center">
            <h2>Help Center</h2>
            <div class="help-grid">
                <?php foreach ($guides as $id => $guide): ?>
                    <article class="help-card">
                        <span class="help-icon"><i class="fa-regular fa-circle-question"></i></span>
                        <h3><?= htmlspecialchars($guide['question']) ?></h3>
                        <p><?= htmlspecialchars($guide['short']) ?></p>
                        <a href="#guide-<?= $id ?>" data-guide="<?= $id ?>">See more</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Contact -->
        <section class="panel contact" id="contact">
            <div class="contact-form">
                <h2>Contact us</h2>

                <?php if ($flashSuccess): ?>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flashSuccess) ?>
                    </div>
                <?php endif; ?>

                <form action="index.php#contact" method="POST" id="contactForm" novalidate>
                    <div class="form-group">
                        <label for="name">Full name</label>
                        <input type="text" id="name" name="name" placeholder="Enter your full name" value="<?= htmlspecialchars($old['name']) ?>" class="<?= isset($errors['name']) ? 'invalid' : '' ?>">
                        <?php if (isset($errors['name'])): ?><p class="error-text"><?= $errors['name'] ?></p><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($old['email']) ?>" class="<?= isset($errors['email']) ? 'invalid' : '' ?>">
                        <?php if (isset($errors['email'])): ?><p class="error-text"><?= $errors['email'] ?></p><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="Enter message subject" value="<?= htmlspecialchars($old['subject']) ?>" class="<?= isset($errors['subject']) ? 'invalid' : '' ?>">
                        <?php if (isset($errors['subject'])): ?><p class="error-text"><?= $errors['subject'] ?></p><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" maxlength="500" placeholder="Write your message here.." class="<?= isset($errors['message']) ? 'invalid' : '' ?>"><?= htmlspecialchars($old['message']) ?></textarea>
                        <div class="char-count"><span id="charNow"><?= mb_strlen($old['message']) ?></span>/500</div>
                        <?php if (isset($errors['message'])): ?><p class="error-text"><?= $errors['message'] ?></p><?php endif; ?>
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

    <!-- Modal panduan, isinya diisi lewat JavaScript -->
    <div class="btp-backdrop" id="guideModal">
        <div class="btp-modal" role="dialog" aria-modal="true" aria-labelledby="guideTitle">
            <div class="btp-modal-header">
                <h3 id="guideTitle"></h3>
                <button type="button" class="btp-close-btn" data-close-modal aria-label="Close">&times;</button>
            </div>

            <p class="btp-modal-intro" id="guideIntro"></p>
            <div id="guideSteps"></div>

            <div class="btp-modal-footer">
                <button type="button" class="btp-btn-pay" data-close-modal>Got it</button>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . "/../../components/landing/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

    <script>
        const guides = <?= json_encode($guides, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

        const guideModal = document.getElementById('guideModal');

        // Modal panduan
        function openGuide(id) {
            const guide = guides[id];
            if (!guide) return;

            document.getElementById('guideTitle').textContent = guide.title;
            document.getElementById('guideIntro').textContent = guide.intro;

            let html = '';
            let no = 1;
            for (const title in guide.steps) {
                const desc = guide.steps[title];
                html += `
                    <div class="btp-section">
                        <div class="btp-section-header">
                            <span class="btp-step-badge">${no++}</span>
                            <h4 class="btp-section-title">${title}</h4>
                        </div>
                        ${desc ? `<p class="btp-step-desc">${desc}</p>` : ''}
                    </div>`;
            }
            document.getElementById('guideSteps').innerHTML = html;

            guideModal.classList.add('show');
            document.body.classList.add('btp-lock');
        }

        function closeModal() {
            guideModal.classList.remove('show');
            document.body.classList.remove('btp-lock');
            if (location.hash.startsWith('#guide-')) {
                history.replaceState(null, '', location.pathname + location.search);
            }
        }

        // Klik "See more"
        document.querySelectorAll('[data-guide]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                openGuide(this.dataset.guide);
            });
        });

        // Tutup modal: tombol silang, "Got it", klik area gelap, atau tombol Esc
        guideModal.addEventListener('click', function(e) {
            if (e.target === guideModal || e.target.closest('[data-close-modal]')) {
                closeModal();
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        // Link dari footer (index.php#guide-order) langsung membuka modal
        function openFromHash() {
            if (location.hash.startsWith('#guide-')) {
                openGuide(location.hash.replace('#guide-', ''));
            }
        }
        window.addEventListener('hashchange', openFromHash);
        openFromHash();


        // Form kontak
        const contactForm = document.getElementById('contactForm');
        const submitBtn = document.getElementById('submitBtn');
        const messageInput = document.getElementById('message');

        messageInput.addEventListener('input', function() {
            document.getElementById('charNow').textContent = this.value.length;
        });

        function showError(input, text) {
            const group = input.closest('.form-group');
            const oldError = group.querySelector('.error-text');
            if (oldError) oldError.remove();

            input.classList.toggle('invalid', text !== '');

            if (text !== '') {
                group.insertAdjacentHTML('beforeend', `<p class="error-text">${text}</p>`);
            }
        }

        contactForm.querySelectorAll('input, textarea').forEach(function(input) {
            input.addEventListener('input', function() {
                showError(this, '');
            });
        });

        const errorMessages = {
            name: 'Full name is required.',
            email: 'Please enter a valid email address.',
            subject: 'Subject is required.',
            message: 'Message is required.'
        };

        contactForm.addEventListener('submit', function(e) {
            let firstInvalid = null;

            for (const field in errorMessages) {
                const input = contactForm.elements[field];
                const value = input.value.trim();

                let valid = value !== '';
                if (field === 'email') {
                    valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                }

                showError(input, valid ? '' : errorMessages[field]);
                if (!valid && !firstInvalid) firstInvalid = input;
            }

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

        // Alert sukses hilang sendiri setelah 5 detik
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
