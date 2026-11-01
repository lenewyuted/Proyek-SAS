<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TechHub - Login</title>
  
  <!-- Font & icons -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- CSS -->
  <link rel="stylesheet" href="../styles/base.css">
  <link rel="stylesheet" href="../../styles/auth/login.css">
</head>
<body>
  <div class="announcement-bar">
    Discover Premium PC Components Built for Performance, Speed, and Reliability
  </div>
  <div class="login-wrapper">
    <div class="login-container">
      <div class="login-form-section">
        <div class="login-header">
          <h2>Welcome Back</h2>
          <p>Login to your account</p>
        </div>
        <form action="../../actions/auth/login.php" method="POST" class="login-form">
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
          <button type="submit" name="login" class="btn-login">Login</button>
          <div class="register-prompt">
            <span>Don't have an account?</span>
            <a href="register.php" class="register-link">Register</a>
          </div>
        </form>
      </div>
      <div class="login-banner-section">
        <div class="banner-content">
          <h2>Upgrade Your Components,<br>Boost Your Performance.</h2>
        </div>
        <div class="banner-image">
          <img src="../assets/images/laptop-illustration.svg" alt="Laptop Illustration">
        </div>
      </div>

    </div>
  </div>
  <?php require_once __DIR__ . "/../../components/landing/footer.php"?>
</body>
</html>