<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanMate - Profil Saya</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script src="{{ asset('assets/js/store.js') }}"></script>
    <script>Store.requireAuth();</script>
    <style>
        .avatar-upload-wrapper {
            position: relative;
            width: 80px;
            height: 80px;
            cursor: pointer;
            flex-shrink: 0;
        }
        .avatar-circle {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #253949;
            color: var(--primary-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 800;
            overflow: hidden;
            border: 2px solid var(--border-color);
        }
        .avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .avatar-camera-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 28px;
            height: 28px;
            background: var(--primary-accent, #2563eb);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            border: 2px solid #fff;
            transition: transform 0.2s ease;
        }
        .avatar-upload-wrapper:hover .avatar-camera-badge {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <div class="app-shell">
        @include('partials.sidebar')

        <main class="app-main-workspace">
            <div class="top-header">
                <div class="breadcrumb-label">PLANMATE / Profil Saya</div>
            </div>

            <div style="max-width: 800px; margin: 0 auto;">
                <div id="profile-alert" style="display: none;" class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Profil Anda telah berhasil diperbarui!
                </div>

                <div class="card-section">
                    <div class="card-header-flex">
                        <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-dark);">Informasi Profil Mahasiswa</h2>
                    </div>

                    <div style="display: flex; align-items: center; gap: 20px; padding-bottom: 24px; border-bottom: 1px solid var(--border-color); margin-bottom: 24px;">
                        <!-- Input File Tersembunyi -->
                        <input type="file" id="avatar-input" accept="image/png, image/jpeg, image/webp" style="display: none;">

                        <!-- Avatar Interaktif (Klik foto untuk ganti) -->
                        <div class="avatar-upload-wrapper" onclick="document.getElementById('avatar-input').click()" title="Klik untuk ganti foto profil">
                            <div id="profile-avatar" class="avatar-circle">RA</div>
                            <div class="avatar-camera-badge">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>

                        <div>
                            <h3 id="profile-header-name" style="font-size: 1.35rem; font-weight: 800; color: var(--text-dark);">Mahasiswa</h3>
                            <p id="profile-header-sub" style="font-size: 0.85rem; color: var(--text-muted);">Jurusan | Email</p>
                        </div>
                    </div>

                    <form id="profileForm">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" id="name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="username">Username / NIM</label>
                                <input type="text" id="username" class="form-control" readonly style="background: #f1f5f9; cursor: not-allowed;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label for="email">Email Mahasiswa</label>
                                <input type="email" id="email" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="jurusan">Jurusan / Program Studi</label>
                                <input type="text" id="jurusan" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 24px;">
                            <label for="bio">Bio / Catatan Diri</label>
                            <textarea id="bio" class="form-control" rows="3" placeholder="Tuliskan catatan motivasi Anda..."></textarea>
                        </div>

                        <div style="display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn btn-accent">
                                <i class="fas fa-save"></i> Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let currentAvatarBase64 = null;

            function renderAvatar(avatarData, name) {
                const initial = (name || 'M').charAt(0).toUpperCase();
                const profileAvatarEl = document.getElementById('profile-avatar');
                const sidebarAvatarEl = document.getElementById('sidebar-avatar');

                if (avatarData) {
                    profileAvatarEl.innerHTML = `<img src="${avatarData}" alt="Foto Profil">`;
                    if (sidebarAvatarEl) {
                        sidebarAvatarEl.innerHTML = `<img src="${avatarData}" alt="Foto" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                    }
                } else {
                    profileAvatarEl.textContent = initial;
                    if (sidebarAvatarEl) {
                        sidebarAvatarEl.textContent = initial;
                    }
                }
            }

            function compressImage(file, maxSize = 300) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = (event) => {
                        const img = new Image();
                        img.src = event.target.result;
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            let width = img.width;
                            let height = img.height;

                            if (width > height) {
                                if (width > maxSize) {
                                    height *= maxSize / width;
                                    width = maxSize;
                                }
                            } else {
                                if (height > maxSize) {
                                    width *= maxSize / height;
                                    height = maxSize;
                                }
                            }

                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);
                            resolve(canvas.toDataURL('image/jpeg', 0.8));
                        };
                        img.onerror = reject;
                    };
                    reader.onerror = reject;
                });
            }

            const user = Store.getCurrentUser();
            if (user) {
                const name = user.name || user.username;
                currentAvatarBase64 = user.avatar || null;

                document.getElementById('name').value = name;
                document.getElementById('username').value = user.username || '';
                document.getElementById('email').value = user.email || '';
                document.getElementById('jurusan').value = user.jurusan || '';
                document.getElementById('bio').value = user.bio || '';

                document.getElementById('sidebar-user-name').textContent = name;
                document.getElementById('sidebar-user-role').textContent = user.jurusan || 'Mahasiswa';
                document.getElementById('profile-header-name').textContent = name;
                document.getElementById('profile-header-sub').textContent = (user.jurusan || 'Mahasiswa') + ' • ' + (user.email || '');

                renderAvatar(currentAvatarBase64, name);
            }

            document.getElementById('avatar-input').addEventListener('change', async (e) => {
                const file = e.target.files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    alert('Harap pilih file gambar (JPG, PNG, atau WebP).');
                    return;
                }

                try {
                    currentAvatarBase64 = await compressImage(file);
                    const currentName = document.getElementById('name').value.trim();
                    renderAvatar(currentAvatarBase64, currentName);
                } catch (err) {
                    alert('Gagal memproses gambar. Silakan coba gambar lain.');
                }
            });

            document.getElementById('profileForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const updated = {
                    name: document.getElementById('name').value.trim(),
                    email: document.getElementById('email').value.trim(),
                    jurusan: document.getElementById('jurusan').value.trim(),
                    bio: document.getElementById('bio').value.trim(),
                    avatar: currentAvatarBase64
                };

                if (Store.updateCurrentUser(updated)) {
                    const alertEl = document.getElementById('profile-alert');
                    alertEl.style.display = 'flex';
                    
                    const curr = Store.getCurrentUser();
                    const name = curr.name || curr.username;
                    document.getElementById('sidebar-user-name').textContent = name;
                    document.getElementById('sidebar-user-role').textContent = curr.jurusan || 'Mahasiswa';
                    document.getElementById('profile-header-name').textContent = name;
                    document.getElementById('profile-header-sub').textContent = (curr.jurusan || 'Mahasiswa') + ' • ' + (curr.email || '');
                    
                    renderAvatar(curr.avatar, name);
                    setTimeout(() => alertEl.style.display = 'none', 3000);
                }
            });
        });
    </script>
</body>
</html>