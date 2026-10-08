<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PlanMate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/store.js"></script>
    <script>Store.requireGuest();</script>
</head>
<body style="background: #0f172a;">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-brand-logo">
                    <i class="fas fa-tasks"></i>
                </div>
                <h2>Masuk ke PlanMate</h2>
                <p>Sistem Manajemen Tugas &amp; Aktivitas Mahasiswa</p>
            </div>

            <div id="auth-alert" style="display: none;" class="alert alert-danger">
                <span id="auth-error-msg"></span>
            </div>

            <form id="loginForm">
                <div class="form-group">
                    <label for="identifier"><i class="fas fa-user"></i> Email atau Username</label>
                    <input type="text" id="identifier" class="form-control" placeholder="Contoh: mahasiswa@student.ac.id" required>
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="password"><i class="fas fa-lock"></i> Kata Sandi</label>
                    <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 0.95rem;">
                    <i class="fas fa-sign-in-alt"></i> Masuk Sekarang
                </button>
            </form>

            <div style="margin-top: 24px; text-align: center; font-size: 0.85rem; color: var(--text-muted);">
                Belum punya akun mahasiswa? <a href="register.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">Daftar Akun Baru</a>
            </div>

            <div style="margin-top: 20px; padding: 12px; background: #f1f5f9; border-radius: 8px; font-size: 0.78rem; color: #475569; text-align: center;">
                <i class="far fa-lightbulb"></i> <strong>Demo Login Cepat:</strong><br>
                Username: <code>mahasiswa</code> | Password: <code>password123</code>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const identifier = document.getElementById('identifier').value.trim();
            const password = document.getElementById('password').value;

            const res = Store.loginUser(identifier, password);
            if (res.success) {
                window.location.href = 'dashboard.php';
            } else {
                const alertElem = document.getElementById('auth-alert');
                document.getElementById('auth-error-msg').textContent = res.message;
                alertElem.style.display = 'flex';
            }
        });
    </script>
</body>
</html>
