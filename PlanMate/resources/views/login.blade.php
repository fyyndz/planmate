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
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script src="{{ asset('assets/js/store.js') }}"></script>
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
        <div id="register-success" style="display: none; margin-bottom: 16px;" class="alert alert-success">
            Akun berhasil dibuat. Silakan masuk.
        </div>
        <script>
        if (new URLSearchParams(location.search).get('registered')) {
        document.getElementById('register-success').style.display = 'flex';
        }
        </script>

        <!-- Alert -->
        @if ($errors->any())
<div class="alert alert-danger" style="margin-bottom: 16px;">
    <span>{{ $errors->first() }}</span>
</div>
@endif

        <!-- Form Login -->
        <form id="loginForm" method="POST" action="/login">
            @csrf
            <div class="form-group" style="margin-bottom: 16px;">
                <label for="identifier" style="font-size: 14px; font-weight: 600; color: #334155; display: block; margin-bottom: 8px;">
                    <i class="fas fa-user" style="margin-right: 4px;"></i>
                    Email atau Username
                </label>
                <input
                    type="text"
                    id="identifier"
                    name="identifier"
                    value="{{ old('identifier') }}"
                    class="form-control"
                    placeholder="mahasiswa@student.ac.id atau username"
                    autocomplete="username"
                    required
                    autofocus
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
                    name="password"
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

        <!-- Akun Demo Info Box -->
        <div style="margin-top: 18px; padding: 12px 14px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <span style="font-weight: 700; color: #1e293b; font-size: 12px;">
                    <i class="fas fa-key" style="color: #2563eb; margin-right: 4px;"></i> Akun Demo Mahasiswa
                </span>
                <button type="button" onclick="fillDemoAccount()" class="btn btn-sm" style="background: #e2e8f0; color: #0f172a; padding: 3px 8px; font-size: 11px; border-radius: 5px; border: none; cursor: pointer; font-weight: 700;">
                    Isi Otomatis
                </button>
            </div>
            <div style="font-size: 12px; color: #64748b; line-height: 1.4;">
                User: <code style="color: #0284c7; background: #e0f2fe; padding: 1px 4px; border-radius: 3px;">mahasiswa</code> &nbsp;|&nbsp; Sandi: <code style="color: #0284c7; background: #e0f2fe; padding: 1px 4px; border-radius: 3px;">password123</code>
            </div>
        </div>

        <!-- Link Daftar -->
        <div style="margin-top: 18px; text-align: center; font-size: 13px; line-height: 20px; color: #64748b;">
            Belum punya akun mahasiswa?
            <a href="/register" style="color: var(--primary); font-weight: 700; text-decoration: none; margin-left: 4px;">
                Daftar Akun Baru
            </a>
        </div>

    </div>
</div>



    <script>
        function fillDemoAccount() {
            document.getElementById('identifier').value = 'mahasiswa';
            document.getElementById('password').value = 'password123';
        }
    </script>
</body>
</html>
