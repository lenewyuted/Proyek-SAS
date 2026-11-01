<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TechHub - Register</title>
  
  <!-- Font & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../styles/base.css">
  <link rel="stylesheet" href="../../styles/auth/register.css">
</head>
<body>

  <!-- Top Announcement Bar -->
  <div class="announcement-bar">
    Discover Premium PC Components Built for Performance, Speed, and Reliability
  </div>

  <!-- Main Register Section -->
  <div class="register-wrapper">
    <div class="register-container">
      
      <!-- Left Column: Register Form -->
      <div class="register-form-section">
        <div class="register-header">
          <h2>Create Your Account</h2>
          <p>Join Techhub for better shopping!</p>
        </div>

        <form action="../../actions/auth/register.php" method="POST" class="register-form">
          
          <div class="form-group">
            <label for="fullname">Full name</label>
            <div class="input-with-icon">
              <i class="bi bi-person"></i>
              <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <div class="input-with-icon">
              <i class="bi bi-envelope"></i>
              <input type="email" id="email" name="email" placeholder="example@gmail.com" required>
            </div>
          </div>

          <!-- <div class="form-group">
            <label for="phone">Number Phone</label>
            <div class="input-with-icon">
              <i class="bi bi-telephone"></i>
              <input type="tel" id="phone" name="phone" placeholder="+62 812 3465 2309" required>
            </div>
          </div> -->

          <div class="form-group">
            <label for="password">Password</label>
            <div class="input-with-icon">
              <i class="bi bi-lock"></i>
              <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
          </div>

          <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <div class="input-with-icon">
              <i class="bi bi-lock"></i>
              <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Password" required>
            </div>
          </div>

          <button type="submit" name="register" class="btn-register">Register</button>

          <div class="login-prompt">
            <span>Already have an account?</span>
            <a href="login.php" class="login-link">Login</a>
          </div>

        </form>
      </div>

      <!-- Right Column: Banner & Illustration -->
      <div class="register-banner-section">
        <div class="banner-image">
          <img src="../assets/images/laptop-illustration.svg" alt="Laptop Illustration">
        </div>
      </div>

    </div>
  </div>

  <!-- Footer TechHub -->
  <footer class="techhub-footer">
    <div class="footer-content">
      
      <div class="footer-col brand-col">
        <img src="../assets/images/logo-techhub.png" alt="TechHub Logo" class="footer-logo">
        <p>Your Hub for Genuine, Warrantied PC Components. The Ultimate Solution for Builders, Gamers, and Creators!</p>
      </div>

      <div class="footer-col">
        <h4>Shop</h4>
        <ul>
          <li><a href="#">Processors</a></li>
          <li><a href="#">Motherboards</a></li>
          <li><a href="#">Graphics Cards</a></li>
          <li><a href="#">RAM & Storage</a></li>
          <li><a href="#">Power Supplies</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Support</h4>
        <ul>
          <li><a href="#">How to Order</a></li>
          <li><a href="#">Shipping</a></li>
          <li><a href="#">Payment Methods</a></li>
          <li><a href="#">FAQ</a></li>
          <li><a href="#">Contact Us</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>My Account</h4>
        <ul>
          <li><a href="#">My Profile</a></li>
          <li><a href="#">Recommendation</a></li>
          <li><a href="#">Compare Products</a></li>
          <li><a href="#">Shopping Cart</a></li>
          <li><a href="#">Search Products</a></li>
        </ul>
      </div>

      <div class="footer-col social-col">
        <h4>Follow Us</h4>
        <div class="social-grid">
          <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
          <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
          <a href="#" class="social-icon"><i class="bi bi-youtube"></i></a>
          <a href="#" class="social-icon"><i class="bi bi-whatsapp"></i></a>
          <a href="#" class="social-icon"><i class="bi bi-tiktok"></i></a>
          <a href="#" class="social-icon"><i class="bi bi-twitter-x"></i></a>
        </div>
      </div>

    </div>

    <div class="footer-bottom">
      <p>&copy; 2026 TechHub. All rights reserved.</p>
    </div>
  </footer>

</body>
</html>