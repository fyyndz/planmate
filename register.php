<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - PlanMate</title>
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
        <div class="auth-card" style="max-width: 500px;">
            <div class="auth-header">
                <div class="auth-brand-logo">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2>Registrasi Akun PlanMate</h2>
                <p>Buat akun baru untuk mulai mengatur tugas &amp; aktivitas Anda.</p>
            </div>

            <div id="auth-alert" style="display: none;" class="alert alert-danger">
                <span id="auth-error-msg"></span>
            </div>

            <form id="registerForm">
                <div class="form-group">
                    <label for="name"><i class="fas fa-id-card"></i> Nama Lengkap Mahasiswa</label>
                    <input type="text" id="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i> Username / NIM</label>
                        <input type="text" id="username" class="form-control" placeholder="24051204001" required>
                    </div>
                    <div class="form-group">
                        <label for="jurusan"><i class="fas fa-graduation-cap"></i> Jurusan / Prodi</label>
                        <input type="text" id="jurusan" class="form-control" placeholder="Teknik Informatika" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Mahasiswa</label>
                    <input type="email" id="email" class="form-control" placeholder="budi@student.ac.id" required>
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="password"><i class="fas fa-lock"></i> Kata Sandi Baru</label>
                    <input type="password" id="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 0.95rem;">
                    <i class="fas fa-check-circle"></i> Daftar &amp; Masuk Dashboard
                </button>
            </form>

            <div style="margin-top: 24px; text-align: center; font-size: 0.85rem; color: var(--text-muted);">
                Sudah memiliki akun? <a href="login.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">Masuk di Sini</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const userData = {
                name: document.getElementById('name').value.trim(),
                username: document.getElementById('username').value.trim(),
                jurusan: document.getElementById('jurusan').value.trim(),
                email: document.getElementById('email').value.trim(),
                password: document.getElementById('password').value
            };

            const res = Store.registerUser(userData);
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
