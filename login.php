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

        <!-- Header -->
        <div class="auth-header" style="text-align: center; margin-bottom: 24px;">
            <h2 style="font-size: 28px; font-weight: 800; margin: 0 0 8px; color: #192936; letter-spacing: -0.5px;">
                Masuk ke PlanMate
            </h2>
            <p style="font-size: 14px; line-height: 20px; color: #64748b; margin: 0;">
                Sistem Manajemen Tugas &amp; Aktivitas Mahasiswa
            </p>
        </div>

        <!-- Alert -->
        <div id="auth-alert" style="display: none; margin-bottom: 16px;" class="alert alert-danger">
            <span id="auth-error-msg"></span>
        </div>

        <!-- Form Login -->
        <form id="loginForm">
            <div class="form-group" style="margin-bottom: 16px;">
                <label for="identifier" style="font-size: 14px; font-weight: 600; color: #334155; display: block; margin-bottom: 8px;">
                    <i class="fas fa-user" style="margin-right: 4px;"></i>
                    Email
                </label>
                <input
                    type="email"
                    id="identifier"
                    class="form-control"
                    placeholder="mahasiswa@student.ac.id"
                    autocomplete="username"
                    required
                    style="height: 48px; border-radius: 8px;"
                >
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="password" style="font-size: 14px; font-weight: 600; color: #334155; display: block; margin-bottom: 8px;">
                    <i class="fas fa-lock" style="margin-right: 4px;"></i>
                    Kata Sandi
                </label>
                <input
                    type="password"
                    id="password"
                    class="form-control"
                    placeholder="Masukkan kata sandi"
                    autocomplete="current-password"
                    required
                    style="height: 48px; border-radius: 8px;"
                >
            </div>

            <!-- Tombol Utama -->
            <button
                type="submit"
                class="btn btn-primary"
                style="width: 100%; min-height: 48px; padding: 12px; font-size: 15px; font-weight: 700; border-radius: 8px;"
            >
                <i class="fas fa-sign-in-alt" style="margin-right: 4px;"></i>
                Masuk Sekarang
            </button>
        </form>

        <!-- Link Daftar -->
        <div style="margin-top: 20px; text-align: center; font-size: 13px; line-height: 20px; color: #64748b;">
            Belum punya akun mahasiswa?
            <a href="register.php" style="color: var(--primary); font-weight: 700; text-decoration: none; margin-left: 4px;">
                Daftar Akun Baru
            </a>
        </div>

        <!-- Informasi Pendukung -->
        <div style="margin-top: 20px; padding: 12px; background: #f1f5f9; border-radius: 8px; text-align: center;">
            <div style="font-size: 12px; line-height: 12px; font-weight: 700; color: #475569; margin-bottom: 8px;">
                <i class="far fa-lightbulb" style="margin-right: 4px;"></i>
                Demo Login Cepat
            </div>

            <div style="font-size: 12px; line-height: 16px; color: #64748b;">
                Username: <code>mahasiswa</code><br>
                Password: <code>password123</code>
            </div>
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
