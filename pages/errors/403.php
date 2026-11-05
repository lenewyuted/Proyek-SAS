<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Redirect otomatis setelah 5 detik ke halaman dashboard -->
    <meta http-equiv="refresh" content="5;url=../../../pages/admin/dashboard.php">
    <title>TechHub - 403 Forbidden</title>
    
    <!-- Google Fonts & Bootstrap Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #1e293b;
        }

        .error-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 40px 32px;
            text-align: center;
            max-width: 440px;
            width: 90%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .error-icon {
            font-size: 3rem;
            color: #0d6efd;
            margin-bottom: 12px;
        }

        .error-badge {
            display: inline-block;
            font-size: 0.85rem;
            font-weight: 600;
            background-color: #e7f1ff;
            color: #0d6efd;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 16px;
            letter-spacing: 0.5px;
        }

        .error-card h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .error-card p {
            font-size: 0.95rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .btn-primary {
            background-color: #0d6efd;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <span class="error-badge">403 FORBIDDEN</span>
        <h1>Access Denied</h1>
        <p>Maaf, Anda tidak memiliki hak akses untuk halaman ini. Anda akan dialihkan secara otomatis dalam beberapa detik.</p>
        
    </div>

</body>
</html>