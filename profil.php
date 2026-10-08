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
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/store.js"></script>
    <script>Store.requireAuth();</script>
</head>
<body>
    <div class="app-shell">
        <?php include_once __DIR__ . '/includes/sidebar.php'; ?>

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
                        <div id="profile-avatar" style="width: 72px; height: 72px; border-radius: 50%; background: #253949; color: var(--primary-accent); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800;">
                            RA
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
            const user = Store.getCurrentUser();
            if (user) {
                const name = user.name || user.username;
                document.getElementById('name').value = name;
                document.getElementById('username').value = user.username || '';
                document.getElementById('email').value = user.email || '';
                document.getElementById('jurusan').value = user.jurusan || '';
                document.getElementById('bio').value = user.bio || '';

                document.getElementById('sidebar-user-name').textContent = name;
                document.getElementById('sidebar-user-role').textContent = user.jurusan || 'Mahasiswa';
                document.getElementById('sidebar-avatar').textContent = name.charAt(0).toUpperCase();

                document.getElementById('profile-header-name').textContent = name;
                document.getElementById('profile-header-sub').textContent = (user.jurusan || 'Mahasiswa') + ' • ' + (user.email || '');
                document.getElementById('profile-avatar').textContent = name.charAt(0).toUpperCase();
            }

            document.getElementById('profileForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const updated = {
                    name: document.getElementById('name').value.trim(),
                    email: document.getElementById('email').value.trim(),
                    jurusan: document.getElementById('jurusan').value.trim(),
                    bio: document.getElementById('bio').value.trim()
                };

                if (Store.updateCurrentUser(updated)) {
                    const alert = document.getElementById('profile-alert');
                    alert.style.display = 'flex';
                    const curr = Store.getCurrentUser();
                    const name = curr.name || curr.username;
                    document.getElementById('sidebar-user-name').textContent = name;
                    document.getElementById('profile-header-name').textContent = name;
                    setTimeout(() => alert.style.display = 'none', 3000);
                }
            });
        });
    </script>
</body>
</html>
