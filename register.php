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
        <div class="auth-card" style="max-width: 520px; padding: 40px;">
 
            <!-- Header: judul > subjudul, rata tengah seperti login -->
            <div class="auth-header" style="margin-bottom: 32px; text-align: center;">
                <h2 style="margin: 0 0 8px; font-size: 32px; line-height: 40px;">Daftar ke PlanMate</h2>
                <p style="margin: 0; font-size: 16px; line-height: 24px;">Buat akun untuk mulai mengatur tugas &amp; aktivitasmu</p>
            </div>
 
            <div id="auth-alert" style="display: none; margin-bottom: 24px;" class="alert alert-danger">
                <span id="auth-error-msg"></span>
            </div>
 
            <form id="registerForm" style="display: flex; flex-direction: column; gap: 20px;">
                <div class="form-group" style="margin: 0;">
                    <label for="name" style="display: block; margin-bottom: 8px; font-size: 16px;"><i class="fas fa-id-card"></i> Nama Lengkap</label>
                    <input type="text" id="name" class="form-control" placeholder="Contoh: Budi Santoso" autocomplete="name" required>
                </div>
 
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group" style="margin: 0;">
                        <label for="username" style="display: block; margin-bottom: 8px; font-size: 16px;"><i class="fas fa-user"></i> Username / NIM</label>
                        <input type="text" id="username" class="form-control" placeholder="24051204001" autocomplete="username" required>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label for="jurusan" style="display: block; margin-bottom: 8px; font-size: 16px;"><i class="fas fa-graduation-cap"></i> Jurusan</label>
                        <input type="text" id="jurusan" class="form-control" placeholder="Teknik Informatika" required>
                    </div>
                </div>
 
                <div class="form-group" style="margin: 0;">
                    <label for="email" style="display: block; margin-bottom: 8px; font-size: 16px;"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" class="form-control" placeholder="budi@student.ac.id" autocomplete="email" required>
                </div>
 
                <div class="form-group" style="margin: 0;">
                    <label for="password" style="display: block; margin-bottom: 8px; font-size: 16px;"><i class="fas fa-lock"></i> Kata Sandi</label>
                    <input type="password" id="password" class="form-control" placeholder="Minimal 6 karakter" autocomplete="new-password" required minlength="6">
                </div>
 
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 4px; padding: 16px; font-size: 16px;">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </button>
            </form>
 
            <div style="margin-top: 24px; text-align: center; font-size: 16px; color: var(--text-muted);">
                Sudah punya akun? <a href="login.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">Masuk di Sini</a>
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
                window.location.href = 'login.php?registered=1';
            } else {
                const alertElem = document.getElementById('auth-alert');
                document.getElementById('auth-error-msg').textContent = res.message;
                alertElem.style.display = 'flex';
                alertElem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    </script>
</body>
</html>
