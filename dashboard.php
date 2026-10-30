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
        <?php include_once __DIR__ . '/sidebar.php'; ?>

        <main class="app-main-workspace">
            <!-- Top Breadcrumb -->
            <div class="top-header">
                <div class="breadcrumb-label">PLANMATE / Ringkasan</div>
                <div>
                    <i class="far fa-bell" style="font-size: 1.1rem; color: var(--text-muted); cursor: pointer;"></i>
                </div>
            </div>

            <!-- Banner Peringatan Jadwal Padat (Muncul jika ada >= 3 deadline dalam 24 jam) -->
            <div id="dense-schedule-banner" style="display: none; margin-bottom: 24px; padding: 16px 20px; background: #fff1f2; border: 1px solid #fecdd3; border-left: 5px solid #ef4444; border-radius: 12px; color: #9f1239; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 42px; height: 42px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <strong style="font-size: 1rem; display: block;" id="dense-banner-title">Peringatan Jadwal Padat!</strong>
                        <span style="font-size: 0.85rem; color: #be123c;" id="dense-banner-desc">Terdapat beberapa aktivitas penting yang mendekati batas waktu dalam 24 jam ke depan.</span>
                    </div>
                </div>
                <a href="deadline.php?filter=urgent" class="btn btn-sm" style="background: #e11d48; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 700; padding: 8px 14px;">
                    Periksa Deadline
                </a>
            </div>

            <!-- Hero Welcome Card -->
            <div class="hero-card">
                <div class="hero-date" id="hero-date-str">RABU, 7 OKTOBER</div>
                <h1 class="hero-heading">
                    Halo, <span id="hero-user-name">Mahasiswa.</span><br>
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

            <!-- Empty State jika pengguna belum memiliki aktivitas -->
            <div id="dashboard-empty-state" style="display: none; background: #ffffff; border: 2px dashed var(--border-color); border-radius: 16px; padding: 48px 24px; text-align: center; margin-bottom: 28px;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: #ecfdf5; color: var(--primary-accent-hover); display: inline-flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 16px;">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark); margin-bottom: 8px;">Belum Ada Aktivitas Tercatat</h3>
                <p style="font-size: 0.92rem; color: var(--text-muted); max-width: 520px; margin: 0 auto 24px; line-height: 1.6;">
                    Mulai susun tugas perkuliahan, jadwal praktikum, dan agenda pribadimu di PlanMate agar harimu lebih terencana dan tidak ada deadline yang terlewat!
                </p>
                <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                    <a href="aktivitas.php?action=create" class="btn btn-primary" style="padding: 12px 24px; font-size: 0.95rem;">
                        <i class="fas fa-plus"></i> Buat Aktivitas Pertama
                    </a>
                    <a href="goals.php?action=create" class="btn btn-secondary" style="padding: 12px 24px; font-size: 0.95rem;">
                        <i class="fas fa-bullseye"></i> Tetapkan Target / Goals
                    </a>
                </div>
            </div>

            <!-- Sub Sections: Prioritas, Jadwal Hari Ini, & Widget Goals -->
            <div id="dashboard-content-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
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

                    <!-- Mini Goals Section Inside Left Column -->
                    <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                        <div class="card-header-flex" style="margin-bottom: 12px;">
                            <div style="font-size: 0.8rem; font-weight: 800; letter-spacing: 0.5px; color: var(--text-muted);">
                                <i class="fas fa-bullseye" style="color: var(--primary-accent-hover); margin-right: 6px;"></i> PROGRESS TARGET &amp; GOALS
                            </div>
                            <a href="goals.php" class="link-action">Kelola Goals</a>
                        </div>
                        <div id="dashboard-goals-container">
                            <!-- Loaded dynamically -->
                        </div>
                    </div>
                </div>

                <!-- Today Schedule -->
                <div class="card-section" style="height: fit-content;">
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
                const name = user.name || user.username || 'Mahasiswa';
                document.getElementById('sidebar-user-name').textContent = name;
                document.getElementById('sidebar-user-role').textContent = user.jurusan || 'Mahasiswa';
                document.getElementById('sidebar-avatar').textContent = name.charAt(0).toUpperCase();

                document.getElementById('hero-user-name').textContent = name + '.';
            }

            // Tanggal Hari Ini (Bahasa Indonesia)
            const days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
            const months = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
            const now = new Date();
            document.getElementById('hero-date-str').textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;

            renderRingkasan();
        });

        function renderRingkasan() {
            const activities = Store.getActivities();
            const categories = Store.getCategories();
            const now = new Date();

            const total = activities.length;
            const done = activities.filter(a => a.status === 'Selesai').length;
            const pending = activities.filter(a => a.status !== 'Selesai').length;

            // Perhitungan Deadline Hari Ini (Berdasarkan tanggal kalender lokal hari ini)
            const todayCount = activities.filter(a => {
                if (a.status === 'Selesai') return false;
                if (!a.deadline) return false;
                const dl = new Date(a.deadline);
                return dl.getFullYear() === now.getFullYear() &&
                       dl.getMonth() === now.getMonth() &&
                       dl.getDate() === now.getDate();
            }).length;

            document.getElementById('box-total-val').textContent = total;
            document.getElementById('box-pending-val').textContent = pending;
            document.getElementById('box-today-val').textContent = todayCount;

            // Empty state handling
            const emptyStateElem = document.getElementById('dashboard-empty-state');
            const gridElem = document.getElementById('dashboard-content-grid');

            if (total === 0) {
                emptyStateElem.style.display = 'block';
                gridElem.style.display = 'none';
            } else {
                emptyStateElem.style.display = 'none';
                gridElem.style.display = 'grid';
            }

            // Logika Jadwal Padat: Minimal 3 deadline dalam rentang 24 jam ke depan
            const nowTime = now.getTime();
            const limit24hTime = nowTime + 24 * 60 * 60 * 1000;

            const acts24h = activities.filter(a => {
                if (!a.deadline) return false;
                const dlTime = new Date(a.deadline).getTime();
                return dlTime >= nowTime && dlTime <= limit24hTime;
            });

            const denseBanner = document.getElementById('dense-schedule-banner');
            const todayHeading = document.getElementById('today-heading-status');
            const todaySub = document.getElementById('today-sub-status');

            if (acts24h.length >= 3) {
                // Tampilkan banner peringatan
                denseBanner.style.display = 'flex';
                document.getElementById('dense-banner-title').textContent = `Peringatan Jadwal Padat (${acts24h.length} Deadline)`;
                document.getElementById('dense-banner-desc').textContent = `Terdapat ${acts24h.length} aktivitas yang harus diselesaikan dalam waktu kurang dari 24 jam!`;

                todayHeading.textContent = "Jadwal padat";
                todayHeading.style.color = "#ef4444";
                todaySub.innerHTML = `
                    <div style="margin-bottom: 12px; color: var(--text-dark); font-weight: 700; font-size: 0.9rem;">
                        Ada <span style="color: #ef4444;">${acts24h.length} deadline</span> dalam 24 jam ke depan!
                    </div>
                    ${acts24h.map(a => {
                        const dl = new Date(a.deadline);
                        const jamStr = dl.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                        const isDone = (a.status === 'Selesai');
                        if (isDone) {
                            return `<div style="font-size: 0.8rem; padding: 8px 12px; background: #f0fdf4; border-radius: 8px; margin-bottom: 8px; color: #15803d; border-left: 3px solid #16a34a; display: flex; justify-content: space-between; align-items: center;">
                                <div><strong>${escapeHtml(a.judul)}</strong> (${jamStr})</div>
                                <span style="font-size: 0.72rem; font-weight: 700; background: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 4px;"><i class="fas fa-check"></i> Selesai</span>
                            </div>`;
                        } else {
                            return `<div style="font-size: 0.8rem; padding: 8px 12px; background: #fef2f2; border-radius: 8px; margin-bottom: 8px; color: #991b1b; border-left: 3px solid #ef4444;">
                                <strong>${escapeHtml(a.judul)}</strong> (${jamStr})
                            </div>`;
                        }
                    }).join('')}
                `;
            } else {
                denseBanner.style.display = 'none';
                todayHeading.textContent = "Tidak ada jadwal padat";
                todayHeading.style.color = "var(--text-dark)";
                todaySub.textContent = "Nikmati harimu atau mulai kerjakan tugas prioritas!";
            }

            // Render Prioritas Berikutnya (Maksimal 3 aktivitas belum selesai dengan deadline terdekat)
            const highPrioList = activities
                .filter(a => a.status !== 'Selesai')
                .sort((a, b) => new Date(a.deadline) - new Date(b.deadline))
                .slice(0, 3);

            const container = document.getElementById('priority-list-container');
            container.innerHTML = '';

            if (highPrioList.length === 0) {
                container.innerHTML = `
                    <div style="padding: 24px; background: #faf8f5; border-radius: 12px; text-align: center; color: var(--text-muted); font-size: 0.88rem; border: 1px dashed var(--border-color);">
                        <i class="fas fa-check-circle" style="color: #10b981; font-size: 1.5rem; margin-bottom: 8px; display: block;"></i>
                        Semua tugas prioritas telah tuntas! Tidak ada tugas tertunda saat ini.
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
                            <a href="aktivitas.php?highlight=${act.id}" class="btn btn-secondary btn-sm" title="Lihat detail aktivitas ini">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                    `;
                    container.appendChild(item);
                });
            }

            // Render Ringkasan Goals Pengguna
            renderDashboardGoals();
        }

        function renderDashboardGoals() {
            const goals = Store.getGoals();
            const container = document.getElementById('dashboard-goals-container');
            if (!container) return;

            if (goals.length === 0) {
                container.innerHTML = `
                    <div style="font-size: 0.85rem; color: var(--text-muted); padding: 10px 0;">
                        Belum ada target yang dibuat. <a href="goals.php" style="color: var(--primary-accent-hover); font-weight: 700; text-decoration: none;">Buat Target Baru</a>
                    </div>
                `;
                return;
            }

            const activeGoals = goals.slice(0, 2);
            container.innerHTML = activeGoals.map(g => {
                const prog = g.progres || 0;
                return `
                    <div style="margin-bottom: 12px; background: #faf8f5; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-size: 0.88rem; font-weight: 700; color: var(--text-dark); max-width: 70%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${escapeHtml(g.judul)}
                            </span>
                            <span style="font-size: 0.78rem; font-weight: 800; color: ${prog === 100 ? '#10b981' : 'var(--text-dark)'};">
                                ${prog}%
                            </span>
                        </div>
                        <div style="height: 6px; background: #e2e8f0; border-radius: 6px; overflow: hidden;">
                            <div style="width: ${prog}%; height: 100%; background: ${prog === 100 ? '#10b981' : 'linear-gradient(90deg, #34d399, #059669)'}; transition: width 0.3s ease;"></div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function escapeHtml(str) {
            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
</body>
</html>
