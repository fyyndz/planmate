<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanMate - Ringkasan</title>
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
            <!-- Top Breadcrumb -->
            <div class="top-header">
                <div class="breadcrumb-label">PLANMATE / Ringkasan</div>
                <div>
                    <i class="far fa-bell" style="font-size: 1.1rem; color: var(--text-muted); cursor: pointer;"></i>
                </div>
            </div>

            <!-- Hero Welcome Card (Exact screenshot design) -->
            <div class="hero-card">
                <div class="hero-date" id="hero-date-str">RABU, 7 OKTOBER</div>
                <h1 class="hero-heading">
                    Halo, <span id="hero-user-name">rifky achmad a.</span><br>
                    Mari rapikan harimu.
                </h1>
                <p class="hero-subtitle">
                    Satu per satu, rencana kecilmu sedang bergerak menjadi kemajuan yang nyata.
                </p>

                <a href="aktivitas.php?action=create" class="btn-dark-pill">
                    <i class="fas fa-plus"></i> Buat aktivitas
                </a>
            </div>

            <!-- 3 Stat Boxes -->
            <div class="stats-row">
                <!-- Stat 1: Total Aktivitas -->
                <div class="stat-box">
                    <div class="stat-box-top">
                        <span class="stat-box-title">Total Aktivitas</span>
                        <div class="stat-box-icon icon-gray">
                            <i class="far fa-clipboard"></i>
                        </div>
                    </div>
                    <div class="stat-box-val" id="box-total-val">0</div>
                    <div class="stat-box-desc">Semua hal yang sedang kamu bawa</div>
                </div>

                <!-- Stat 2: Belum Selesai -->
                <div class="stat-box">
                    <div class="stat-box-top">
                        <span class="stat-box-title">Belum Selesai</span>
                        <div class="stat-box-icon icon-teal">
                            <i class="far fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-box-val" id="box-pending-val">0</div>
                    <div class="stat-box-desc">Masih perlu perhatianmu</div>
                </div>

                <!-- Stat 3: Deadline Hari Ini -->
                <div class="stat-box">
                    <div class="stat-box-top">
                        <span class="stat-box-title">Deadline Hari Ini</span>
                        <div class="stat-box-icon icon-amber">
                            <i class="far fa-bell"></i>
                        </div>
                    </div>
                    <div class="stat-box-val" id="box-today-val">0</div>
                    <div class="stat-box-desc">Jangan sampai terlewat</div>
                </div>
            </div>

            <!-- Sub Sections: Prioritas & Today Schedule -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                <!-- Next Priorities Section -->
                <div class="card-section">
                    <div class="card-header-flex">
                        <div class="card-heading-title">PRIORITAS BERIKUTNYA</div>
                        <a href="deadline.php" class="link-action">Lihat semua</a>
                    </div>
                    <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 16px;">Jangan sampai terlewat</h3>

                    <div id="priority-list-container">
                        <!-- Loaded dynamically -->
                    </div>
                </div>

                <!-- Today Schedule -->
                <div class="card-section">
                    <div class="card-header-flex">
                        <div class="card-heading-title">HARI INI</div>
                    </div>
                    <h3 style="font-size: 1.05rem; font-weight: 800; margin-bottom: 16px;" id="today-heading-status">Tidak ada jadwal padat</h3>
                    <div style="font-size: 0.85rem; color: var(--text-muted);" id="today-sub-status">
                        Nikmati harimu atau mulai kerjakan tugas prioritas!
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const user = Store.getCurrentUser();
            if (user) {
                const name = user.name || user.username;
                document.getElementById('sidebar-user-name').textContent = name;
                document.getElementById('sidebar-user-role').textContent = user.jurusan || 'Mahasiswa';
                document.getElementById('sidebar-avatar').textContent = name.charAt(0).toUpperCase();

                document.getElementById('hero-user-name').textContent = name.toLowerCase() + '.';
            }

            // Set Today Date String (e.g. RABU, 7 OKTOBER)
            const days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
            const months = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
            const now = new Date();
            document.getElementById('hero-date-str').textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]}`;

            renderRingkasan();
        });

        function renderRingkasan() {
            const activities = Store.getActivities();
            const categories = Store.getCategories();
            const now = new Date();
            const todayStr = now.toISOString().slice(0, 10);

            const total = activities.length;
            const done = activities.filter(a => a.status === 'Selesai').length;
            const pending = total - done;

            // Deadline Today Count
            const todayCount = activities.filter(a => a.status !== 'Selesai' && a.deadline.startsWith(todayStr)).length;

            document.getElementById('box-total-val').textContent = total;
            document.getElementById('box-pending-val').textContent = pending;
            document.getElementById('box-today-val').textContent = todayCount;

            // Jadwal Padat logic: minimal 3 deadline dalam 24 jam ke depan (termasuk tugas selesai)
            const limit24h = new Date(now.getTime() + 24 * 60 * 60 * 1000);
            const acts24h = activities.filter(a => {
                const dl = new Date(a.deadline);
                return dl >= now && dl <= limit24h;
            });

            const todayHeading = document.getElementById('today-heading-status');
            const todaySub = document.getElementById('today-sub-status');

            if (acts24h.length >= 3) {
                todayHeading.textContent = "Jadwal padat";
                todayHeading.style.color = "#ef4444";
                todaySub.innerHTML = `
                    <div style="margin-bottom: 10px; color: var(--text-dark); font-weight: 600;">
                        Ada <strong>${acts24h.length} deadline</strong> dalam 24 jam ke depan!
                    </div>
                    ${acts24h.map(a => {
                        const dl = new Date(a.deadline);
                        const jamStr = dl.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                        const isDone = (a.status === 'Selesai');
                        if (isDone) {
                            return `<div style="font-size: 0.8rem; padding: 6px 10px; background: #f0fdf4; border-radius: 6px; margin-bottom: 6px; color: #15803d; border-left: 3px solid #16a34a; display: flex; justify-content: space-between; align-items: center;">
                                <div><strong>${escapeHtml(a.judul)}</strong> (${jamStr})</div>
                                <span style="font-size: 0.72rem; font-weight: 700; background: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 4px;"><i class="fas fa-check"></i> Selesai</span>
                            </div>`;
                        } else {
                            return `<div style="font-size: 0.8rem; padding: 6px 10px; background: #fef2f2; border-radius: 6px; margin-bottom: 6px; color: #991b1b; border-left: 3px solid #ef4444;">
                                <strong>${escapeHtml(a.judul)}</strong> (${jamStr})
                            </div>`;
                        }
                    }).join('')}
                `;
            } else {
                todayHeading.textContent = "Tidak ada jadwal padat";
                todayHeading.style.color = "var(--text-dark)";
                todaySub.textContent = "Nikmati harimu atau mulai kerjakan tugas prioritas!";
            }

            // Render Priority list
            const highPrioList = activities
                .filter(a => a.status !== 'Selesai')
                .sort((a, b) => new Date(a.deadline) - new Date(b.deadline))
                .slice(0, 3);

            const container = document.getElementById('priority-list-container');
            container.innerHTML = '';

            if (highPrioList.length === 0) {
                container.innerHTML = `
                    <div style="padding: 20px; background: #faf8f5; border-radius: 12px; text-align: center; color: var(--text-muted); font-size: 0.88rem;">
                        Tidak ada tugas tertunda. Semua prioritas telah tuntas!
                    </div>`;
            } else {
                highPrioList.forEach(act => {
                    const cat = categories.find(c => Number(c.id) === Number(act.kategoriId)) || { name: 'Umum', color: '#64748b' };
                    const dl = new Date(act.deadline);
                    const dlFormatted = dl.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });

                    const item = document.createElement('div');
                    item.style.cssText = 'display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; background: #faf8f5; border-radius: 12px; margin-bottom: 10px; border: 1px solid var(--border-color);';
                    item.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span class="cat-pill" style="background:${cat.color}15; color:${cat.color}; border:1px solid ${cat.color}40;">${escapeHtml(cat.name)}</span>
                            <div>
                                <strong style="font-size: 0.92rem; color: var(--text-dark); display: block;">${escapeHtml(act.judul)}</strong>
                                <span style="font-size: 0.78rem; color: var(--text-muted);"><i class="far fa-clock"></i> Deadline: ${dlFormatted}</span>
                            </div>
                        </div>
                        <div>
                            <a href="aktivitas.php" class="btn btn-secondary btn-sm">Lihat Detail</a>
                        </div>
                    `;
                    container.appendChild(item);
                });
            }
        }

        function escapeHtml(str) {
            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
</body>
</html>
