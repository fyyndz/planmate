<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanMate - Dashboard</title>
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
                <div class="breadcrumb-label">PLANMATE / Dashboard</div>
                <div>
                    <i class="far fa-bell" style="font-size: 1.1rem; color: var(--text-muted); cursor: pointer;" title="Notifikasi"></i>
                </div>
            </div>

            <!-- Banner Peringatan Jadwal Padat (Muncul jika ada >= 3 deadline dalam 24 jam) -->
            <div id="dense-schedule-banner" style="display: none; margin-bottom: 24px; padding: 16px 20px; background: #fff1f2; border: 1px solid #fecdd3; border-left: 5px solid #ef4444; border-radius: 14px; color: #9f1239; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 42px; height: 42px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <strong style="font-size: 1rem; display: block;" id="dense-banner-title">Peringatan Jadwal Padat!</strong>
                        <span style="font-size: 0.85rem; color: #be123c;" id="dense-banner-desc">Terdapat beberapa aktivitas penting yang mendekati batas waktu dalam 24 jam ke depan.</span>
                    </div>
                </div>
                <button onclick="filterDeadlineTab('urgent')" class="btn btn-sm" style="background: #e11d48; color: #ffffff; border-radius: 8px; font-weight: 700; padding: 8px 14px; border: none; cursor: pointer;">
                    Periksa Deadline
                </button>
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

            <!-- Main Integrated Dashboard Workspace -->
            <div id="dashboard-content-grid" style="display: grid; grid-template-columns: 1.35fr 1fr; gap: 24px; align-items: start;">
                
                <!-- Left Column: Kalender Interaktif & Pencarian / Filter Aktivitas -->
                <div style="display: flex; flex-direction: column; gap: 24px;">

                    <!-- SECTION 1: Kalender & Agenda Interaktif Harian -->
                    <div class="card-section" style="margin-bottom: 0;">
                        <div class="card-header-flex">
                            <div>
                                <div class="card-heading-title">KALENDER AKTIVITAS</div>
                                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-dark); margin-top: 2px;" id="dash-cal-month-title">
                                    Kalender Bulan Ini
                                </h3>
                            </div>
                            <div style="display: flex; gap: 6px; align-items: center;">
                                <button type="button" onclick="changeDashMonth(-1)" class="btn btn-secondary btn-icon btn-sm" title="Bulan Sebelumnya">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" onclick="goDashToday()" class="btn btn-secondary btn-sm" style="font-weight: 700;">
                                    Hari Ini
                                </button>
                                <button type="button" onclick="changeDashMonth(1)" class="btn btn-secondary btn-icon btn-sm" title="Bulan Berikutnya">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Calendar Sub-layout: Mini Calendar + Selected Date Agenda -->
                        <div class="dash-cal-subgrid" style="display: grid; grid-template-columns: 1.15fr 1fr; gap: 18px; align-items: start;">
                            <!-- Calendar Grid Container -->
                            <div class="dash-cal-wrapper">
                                <div class="dash-cal-grid" style="margin-bottom: 8px;">
                                    <div class="dash-cal-head">Sen</div>
                                    <div class="dash-cal-head">Sel</div>
                                    <div class="dash-cal-head">Rab</div>
                                    <div class="dash-cal-head">Kam</div>
                                    <div class="dash-cal-head">Jum</div>
                                    <div class="dash-cal-head" style="color: #ef4444;">Sab</div>
                                    <div class="dash-cal-head" style="color: #ef4444;">Min</div>
                                </div>
                                <div class="dash-cal-grid" id="dash-cal-days-grid">
                                    <!-- Populated via renderDashCalendar() -->
                                </div>
                            </div>

                            <!-- Selected Date Agenda Panel -->
                            <div style="background: #faf8f5; border: 1px solid var(--border-color); border-radius: 14px; padding: 18px; display: flex; flex-direction: column; min-height: 290px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                                    <div>
                                        <div style="font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">AGENDA TANGGAL</div>
                                        <strong style="font-size: 0.95rem; color: var(--text-dark);" id="dash-selected-date-str">-</strong>
                                    </div>
                                    <span id="dash-selected-count-badge" class="badge badge-secondary">0 Tugas</span>
                                </div>
                                <div id="dash-selected-agenda-list" style="display: flex; flex-direction: column; gap: 8px; flex: 1; overflow-y: auto; max-height: 230px;">
                                    <!-- Agenda items dynamically populated -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: Pencarian & Filter Cepat Aktivitas -->
                    <div class="card-section" style="margin-bottom: 0;">
                        <div class="card-header-flex" style="margin-bottom: 14px;">
                            <div>
                                <div class="card-heading-title">PENCARIAN &amp; FILTER AKTIVITAS</div>
                                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-dark); margin-top: 2px;">
                                    Temukan Aktivitas
                                </h3>
                            </div>
                            <button type="button" onclick="resetDashSearch()" class="btn btn-secondary btn-sm" title="Kembalikan ke pengaturan awal">
                                <i class="fas fa-undo"></i> Reset Filter
                            </button>
                        </div>

                        <!-- Search Controls Bar -->
                        <div class="dash-search-grid" style="display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr 1.1fr; gap: 8px; margin-bottom: 16px;">
                            <div>
                                <div style="position: relative;">
                                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 12px; color: var(--text-muted); font-size: 0.85rem;"></i>
                                    <input type="text" id="dashSearchKeyword" class="form-control" placeholder="Cari judul / deskripsi..." style="padding-left: 34px; height: 40px; font-size: 0.86rem;" oninput="executeDashSearch()">
                                </div>
                            </div>
                            <div>
                                <select id="dashSearchCat" class="form-select" style="height: 40px; font-size: 0.84rem;" onchange="executeDashSearch()"></select>
                            </div>
                            <div>
                                <select id="dashSearchStatus" class="form-select" style="height: 40px; font-size: 0.84rem;" onchange="executeDashSearch()">
                                    <option value="">Semua Status</option>
                                    <option value="Belum Dimulai">Belum Dimulai</option>
                                    <option value="Sedang Dikerjakan">Sedang Dikerjakan</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </div>
                            <div>
                                <select id="dashSearchPrio" class="form-select" style="height: 40px; font-size: 0.84rem;" onchange="executeDashSearch()">
                                    <option value="">Semua Prioritas</option>
                                    <option value="Tinggi">Tinggi</option>
                                    <option value="Sedang">Sedang</option>
                                    <option value="Rendah">Rendah</option>
                                </select>
                            </div>
                            <div>
                                <select id="dashSearchDeadline" class="form-select" style="height: 40px; font-size: 0.84rem;" onchange="executeDashSearch()">
                                    <option value="">Semua Deadline</option>
                                    <option value="today">Hari Ini</option>
                                    <option value="week">7 Hari ke Depan</option>
                                    <option value="overdue">Terlambat</option>
                                    <option value="upcoming">Mendatang</option>
                                </select>
                            </div>
                        </div>

                        <!-- Live Search Results Table -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 0.8rem; color: var(--text-muted);">
                            <span id="dashSearchResultCount">Menampilkan seluruh aktivitas</span>
                            <a href="aktivitas.php" class="link-action" style="font-size: 0.78rem;">Buka Semua Aktivitas &rarr;</a>
                        </div>
                        <div class="table-responsive" style="max-height: 310px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 12px;">
                            <table class="table" style="margin: 0;">
                                <thead>
                                    <tr>
                                        <th>Judul Aktivitas</th>
                                        <th>Kategori</th>
                                        <th>Deadline</th>
                                        <th>Prioritas</th>
                                        <th>Status</th>
                                        <th style="text-align: right;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="dashSearchTableBody">
                                    <!-- Loaded via executeDashSearch() -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Deadline & Prioritas + Hari Ini + Progress Goals -->
                <div style="display: flex; flex-direction: column; gap: 24px;">

                    <!-- SECTION 3: Deadline & Prioritas Aktivitas Terdekat -->
                    <div class="card-section" style="margin-bottom: 0;">
                        <div class="card-header-flex" style="margin-bottom: 12px;">
                            <div>
                                <div class="card-heading-title">DEADLINE &amp; PRIORITAS</div>
                                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-dark); margin-top: 2px;">
                                    Tenggat Terdekat
                                </h3>
                            </div>
                            <a href="aktivitas.php" class="link-action" title="Kelola semua aktivitas">Kelola</a>
                        </div>

                        <!-- Quick Filter Tabs for Deadlines -->
                        <div class="filter-pills-row" style="margin-bottom: 16px;">
                            <button type="button" class="filter-pill active" id="flt-pill-all" onclick="filterDeadlineTab('all')">Semua</button>
                            <button type="button" class="filter-pill" id="flt-pill-urgent" onclick="filterDeadlineTab('urgent')">Mendekati Tenggat</button>
                            <button type="button" class="filter-pill" id="flt-pill-today" onclick="filterDeadlineTab('today')">Hari Ini</button>
                            <button type="button" class="filter-pill" id="flt-pill-high" onclick="filterDeadlineTab('high')">Prioritas Tinggi</button>
                            <button type="button" class="filter-pill" id="flt-pill-overdue" onclick="filterDeadlineTab('overdue')">Terlambat</button>
                        </div>

                        <!-- Deadline Tasks List -->
                        <div id="dash-deadline-list" style="display: flex; flex-direction: column; gap: 10px; max-height: 410px; overflow-y: auto;">
                            <!-- Populated via renderDashDeadlines() -->
                        </div>
                    </div>

                    <!-- SECTION 4: Status Jadwal Hari Ini -->
                    <div class="card-section" style="margin-bottom: 0;">
                        <div class="card-header-flex">
                            <div class="card-heading-title">RINGKASAN HARI INI</div>
                        </div>
                        <h3 style="font-size: 1.05rem; font-weight: 800; margin-bottom: 8px;" id="today-heading-status">Tidak ada jadwal padat</h3>
                        <div style="font-size: 0.85rem; color: var(--text-muted);" id="today-sub-status">
                            Nikmati harimu atau mulai kerjakan tugas prioritas!
                        </div>
                    </div>

                    <!-- SECTION 5: Progress Target & Goals -->
                    <div class="card-section" style="margin-bottom: 0;">
                        <div class="card-header-flex" style="margin-bottom: 12px;">
                            <div style="font-size: 0.8rem; font-weight: 800; letter-spacing: 0.5px; color: var(--text-muted);">
                                <i class="fas fa-bullseye" style="color: var(--primary-accent-hover); margin-right: 6px;"></i> TARGET &amp; GOALS
                            </div>
                            <a href="goals.php" class="link-action">Kelola Goals</a>
                        </div>
                        <div id="dashboard-goals-container">
                            <!-- Populated via renderDashboardGoals() -->
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- Modal Minimalis Modern: Detail Aktivitas di Dashboard -->
    <div id="dashDetailModal" class="pm-modal-overlay">
        <div class="pm-modal-card pm-modal-md" role="dialog" aria-modal="true" aria-labelledby="dashModalTitle">
            <div class="pm-modal-header">
                <div class="pm-modal-title-group">
                    <h3 class="pm-modal-title" id="dashModalTitle">
                        <i class="far fa-file-alt" style="color: var(--primary-accent-hover);"></i> Detail Aktivitas
                    </h3>
                    <p class="pm-modal-subtitle" id="dashModalSubtitle">Informasi rinci jadwal dan prioritas tugas.</p>
                </div>
                <button type="button" class="pm-modal-close" onclick="closeDashDetailModal()" aria-label="Tutup">&times;</button>
            </div>
            <div class="pm-modal-body">
                <div style="margin-bottom: 18px;">
                    <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 8px;" id="dashModalBadges">
                        <!-- Badges loaded dynamically -->
                    </div>
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark); margin: 0 0 6px;" id="dashModalHeading">
                        Judul Aktivitas
                    </h2>
                </div>

                <div class="pm-detail-grid">
                    <div class="pm-detail-item">
                        <div class="pm-detail-label"><i class="far fa-calendar-alt"></i> Target Deadline</div>
                        <div class="pm-detail-value" id="dashModalDeadline">-</div>
                    </div>
                    <div class="pm-detail-item">
                        <div class="pm-detail-label"><i class="fas fa-hourglass-half"></i> Urgency / Sisa Waktu</div>
                        <div class="pm-detail-value" id="dashModalUrgency">-</div>
                    </div>
                    <div class="pm-detail-item">
                        <div class="pm-detail-label"><i class="fas fa-tags"></i> Kategori</div>
                        <div class="pm-detail-value" id="dashModalCategory">-</div>
                    </div>
                    <div class="pm-detail-item">
                        <div class="pm-detail-label"><i class="fas fa-flag"></i> Prioritas &amp; Status</div>
                        <div class="pm-detail-value" id="dashModalStatusPrio">-</div>
                    </div>
                </div>

                <div class="pm-form-group">
                    <label class="pm-label">Deskripsi &amp; Catatan</label>
                    <div id="dashModalDesc" style="font-size: 0.88rem; color: #475569; background: #faf8f5; border: 1px solid var(--border-color); border-radius: 10px; padding: 14px; min-height: 80px; line-height: 1.6; white-space: pre-wrap;">-</div>
                </div>
            </div>
            <div class="pm-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDashDetailModal()">Tutup</button>
                <a href="#" id="dashModalEditLink" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Kelola di Aktivitas
                </a>
            </div>
        </div>
    </div>

    <script>
        // State Dashboard
        let dashCalendarDate = new Date();
        let dashSelectedDateStr = '';
        let currentDeadlineTab = 'all';

        document.addEventListener('DOMContentLoaded', () => {
            const user = Store.getCurrentUser();
            if (user) {
                const name = user.name || user.username || 'Mahasiswa';
                document.getElementById('sidebar-user-name').textContent = name;
                document.getElementById('sidebar-user-role').textContent = user.jurusan || 'Mahasiswa';
                document.getElementById('sidebar-avatar').textContent = name.charAt(0).toUpperCase();
                document.getElementById('hero-user-name').textContent = name + '.';
            }

            // Set Hari Ini String (Bahasa Indonesia)
            const days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
            const months = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
            const now = new Date();
            document.getElementById('hero-date-str').textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;

            // Inisialisasi tanggal terpilih kalender ke hari ini (YYYY-MM-DD)
            dashSelectedDateStr = formatDateKey(now);

            // Populate category filter di pencarian
            populateDashCategoryDropdown();

            // Render Dashboard
            renderDashboard();

            // Backdrop click close for dashDetailModal
            const modalOverlay = document.getElementById('dashDetailModal');
            if (modalOverlay) {
                modalOverlay.addEventListener('click', (e) => {
                    if (e.target === modalOverlay) closeDashDetailModal();
                });
            }
        });

        function formatDateKey(dateObj) {
            const y = dateObj.getFullYear();
            const m = String(dateObj.getMonth() + 1).padStart(2, '0');
            const d = String(dateObj.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        function renderDashboard() {
            const activities = Store.getActivities();
            const categories = Store.getCategories();
            const now = new Date();

            const total = activities.length;
            const pending = activities.filter(a => a.status !== 'Selesai').length;

            // Deadline hari ini
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
                return;
            } else {
                emptyStateElem.style.display = 'none';
                gridElem.style.display = 'grid';
            }

            // Peringatan Jadwal Padat (>= 3 deadline dalam 24 jam)
            checkDenseSchedule(activities, now);

            // Render 3 Fitur Terintegrasi
            renderDashCalendar();
            renderDashAgendaForDate(dashSelectedDateStr);
            executeDashSearch();
            renderDashDeadlines();
            renderDashboardGoals();
        }

        // ==========================================================
        // 1. FITUR DEADLINE & PRIORITAS DI DASHBOARD
        // ==========================================================
        function filterDeadlineTab(tab) {
            currentDeadlineTab = tab;
            ['all', 'urgent', 'today', 'high', 'overdue'].forEach(t => {
                const el = document.getElementById(`flt-pill-${t}`);
                if (el) {
                    if (t === tab) el.classList.add('active');
                    else el.classList.remove('active');
                }
            });
            renderDashDeadlines();
        }

        function renderDashDeadlines() {
            const activities = Store.getActivities();
            const categories = Store.getCategories();
            const now = new Date();
            const limit24h = new Date(now.getTime() + 24 * 60 * 60 * 1000);

            let filtered = [...activities];

            if (currentDeadlineTab === 'urgent') {
                filtered = filtered.filter(a => a.status !== 'Selesai' && new Date(a.deadline) <= limit24h);
            } else if (currentDeadlineTab === 'today') {
                filtered = filtered.filter(a => {
                    if (a.status === 'Selesai') return false;
                    const dl = new Date(a.deadline);
                    return dl.getFullYear() === now.getFullYear() &&
                           dl.getMonth() === now.getMonth() &&
                           dl.getDate() === now.getDate();
                });
            } else if (currentDeadlineTab === 'high') {
                filtered = filtered.filter(a => a.prioritas === 'Tinggi');
            } else if (currentDeadlineTab === 'overdue') {
                filtered = filtered.filter(a => a.status !== 'Selesai' && new Date(a.deadline) < now);
            }

            // Urutkan deadline terdekat dahulu (kecuali sudah selesai ditaruh di bawah)
            filtered.sort((a, b) => {
                if (a.status === 'Selesai' && b.status !== 'Selesai') return 1;
                if (a.status !== 'Selesai' && b.status === 'Selesai') return -1;
                return new Date(a.deadline) - new Date(b.deadline);
            });

            const container = document.getElementById('dash-deadline-list');
            container.innerHTML = '';

            if (filtered.length === 0) {
                container.innerHTML = `
                    <div style="padding: 24px 16px; background: #faf8f5; border-radius: 12px; text-align: center; color: var(--text-muted); font-size: 0.86rem; border: 1px dashed var(--border-color);">
                        <i class="fas fa-check-circle" style="color: #10b981; font-size: 1.4rem; margin-bottom: 6px; display: block;"></i>
                        Tidak ada aktivitas yang sesuai dengan filter deadline ini.
                    </div>`;
                return;
            }

            filtered.forEach(act => {
                const cat = categories.find(c => Number(c.id) === Number(act.kategoriId)) || { name: 'Umum', color: '#64748b' };
                const dlDate = new Date(act.deadline);
                const dlFormatted = dlDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
                const diffMs = dlDate - now;

                // Urgency badge calculation & visual indicators
                const isToday = dlDate.getFullYear() === now.getFullYear() &&
                                dlDate.getMonth() === now.getMonth() &&
                                dlDate.getDate() === now.getDate();

                let urgencyBadge = '';
                let itemBorderHighlight = '';

                if (act.status === 'Selesai') {
                    urgencyBadge = '<span class="badge badge-done"><i class="fas fa-check"></i> Selesai</span>';
                } else if (diffMs < 0) {
                    urgencyBadge = '<span class="badge" style="background:#fee2e2; color:#dc2626; border: 1px solid #fca5a5;"><i class="fas fa-times-circle"></i> Terlambat</span>';
                    itemBorderHighlight = 'border-left: 4px solid #ef4444;';
                } else if (isToday) {
                    urgencyBadge = '<span class="deadline-today-indicator"><i class="far fa-clock"></i> Hari Ini</span>';
                    itemBorderHighlight = 'border-left: 4px solid #f59e0b;';
                } else if (diffMs <= 24 * 3600 * 1000) {
                    urgencyBadge = '<span class="deadline-approaching-indicator"><i class="fas fa-fire"></i> &lt; 24 Jam</span>';
                    itemBorderHighlight = 'border-left: 4px solid #ef4444;';
                } else {
                    urgencyBadge = '<span class="badge badge-secondary"><i class="far fa-calendar"></i> Mendatang</span>';
                }

                let prioBadge = '<span class="badge badge-medium">Sedang</span>';
                if (act.prioritas === 'Tinggi') prioBadge = '<span class="badge badge-high">Tinggi</span>';
                if (act.prioritas === 'Rendah') prioBadge = '<span class="badge badge-low">Rendah</span>';

                const item = document.createElement('div');
                item.style.cssText = `display: flex; flex-direction: column; gap: 8px; padding: 12px 14px; background: #faf8f5; border-radius: 12px; border: 1px solid var(--border-color); ${itemBorderHighlight} transition: transform 0.15s ease;`;
                item.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px; flex-wrap: wrap;">
                                <span class="cat-pill" style="background:${cat.color}15; color:${cat.color}; border:1px solid ${cat.color}40; font-size: 0.72rem; padding: 2px 7px;">${escapeHtml(cat.name)}</span>
                                ${urgencyBadge}
                                ${prioBadge}
                            </div>
                            <strong style="font-size: 0.92rem; color: var(--text-dark); display: block; line-height: 1.35;">${escapeHtml(act.judul)}</strong>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 6px; border-top: 1px dashed var(--border-color); font-size: 0.78rem; color: var(--text-muted);">
                        <span><i class="far fa-clock"></i> ${dlFormatted}</span>
                        <div style="display: flex; gap: 6px;">
                            <button type="button" onclick="openDashDetailModal(${act.id})" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.76rem;" title="Lihat detail">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                            <a href="aktivitas.php?highlight=${act.id}" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.76rem;" title="Buka di halaman aktivitas">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                `;
                container.appendChild(item);
            });
        }

        // ==========================================================
        // 2. FITUR KALENDER INTERAKTIF DI DASHBOARD
        // ==========================================================
        function changeDashMonth(delta) {
            dashCalendarDate = new Date(dashCalendarDate.getFullYear(), dashCalendarDate.getMonth() + delta, 1);
            renderDashCalendar();
        }

        function goDashToday() {
            dashCalendarDate = new Date();
            dashSelectedDateStr = formatDateKey(dashCalendarDate);
            renderDashCalendar();
            renderDashAgendaForDate(dashSelectedDateStr);
        }

        function selectDashCalendarDate(dateKey) {
            dashSelectedDateStr = dateKey;
            renderDashCalendar();
            renderDashAgendaForDate(dateKey);
        }

        function renderDashCalendar() {
            const year = dashCalendarDate.getFullYear();
            const month = dashCalendarDate.getMonth();
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            document.getElementById('dash-cal-month-title').innerHTML = `${monthNames[month]} ${year}`;

            const firstDay = new Date(year, month, 1, 12, 0, 0);
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const dayOfWeek = firstDay.getDay(); // 0 = Minggu
            const emptyOffset = (dayOfWeek + 6) % 7; // Monday-first

            const activities = Store.getActivities();
            const now = new Date();
            const todayKey = formatDateKey(now);

            const grid = document.getElementById('dash-cal-days-grid');
            grid.innerHTML = '';

            // Leading empty cells
            for (let i = 0; i < emptyOffset; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'dash-cal-cell empty';
                grid.appendChild(emptyCell);
            }

            // Month days
            for (let day = 1; day <= daysInMonth; day++) {
                const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const isToday = (dateKey === todayKey);
                const isSelected = (dateKey === dashSelectedDateStr);

                // Temukan aktivitas pada tanggal ini
                const dayActs = activities.filter(a => a.deadline && a.deadline.startsWith(dateKey));
                const hasPendingUrgent = dayActs.some(a => a.status !== 'Selesai' && a.prioritas === 'Tinggi');

                const cell = document.createElement('div');
                cell.className = `dash-cal-cell ${isToday ? 'today' : ''} ${isSelected ? 'selected' : ''}`;
                cell.title = `${day} ${monthNames[month]} ${year} (${dayActs.length} aktivitas)`;
                cell.onclick = () => selectDashCalendarDate(dateKey);

                let dotsHtml = '';
                if (dayActs.length > 0) {
                    const dotCount = Math.min(dayActs.length, 3);
                    let dots = '';
                    for (let d = 0; d < dotCount; d++) {
                        const isUrg = (d === 0 && hasPendingUrgent);
                        dots += `<span class="dash-cal-dot ${isUrg ? 'urgent' : ''}"></span>`;
                    }
                    dotsHtml = `<div class="dash-cal-dots">${dots}</div>`;
                }

                cell.innerHTML = `
                    <span class="dash-cal-num">${day}</span>
                    ${dotsHtml}
                `;
                grid.appendChild(cell);
            }

            // Trailing empty cells
            const totalCells = emptyOffset + daysInMonth;
            const trailingCount = (7 - (totalCells % 7)) % 7;
            for (let i = 0; i < trailingCount; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'dash-cal-cell empty';
                grid.appendChild(emptyCell);
            }
        }

        function renderDashAgendaForDate(dateKey) {
            const activities = Store.getActivities();
            const categories = Store.getCategories();
            const container = document.getElementById('dash-selected-agenda-list');
            const countBadge = document.getElementById('dash-selected-count-badge');
            const titleDate = document.getElementById('dash-selected-date-str');

            // Format tanggal terpilih ke bahasa Indonesia
            const parts = dateKey.split('-');
            const selDateObj = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            titleDate.textContent = `${days[selDateObj.getDay()]}, ${selDateObj.getDate()} ${months[selDateObj.getMonth()]} ${selDateObj.getFullYear()}`;

            const dayActs = activities.filter(a => a.deadline && a.deadline.startsWith(dateKey));
            countBadge.textContent = `${dayActs.length} Tugas`;

            container.innerHTML = '';

            if (dayActs.length === 0) {
                container.innerHTML = `
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 24px 10px; color: var(--text-muted); font-size: 0.85rem;">
                        <i class="far fa-calendar-check" style="font-size: 1.8rem; color: #cbd5e1; margin-bottom: 8px;"></i>
                        Tidak ada aktivitas yang dijadwalkan pada tanggal ini.
                        <a href="aktivitas.php?action=create" style="margin-top: 10px; font-size: 0.78rem; font-weight: 700; color: var(--primary-accent-hover); text-decoration: none;">
                            <i class="fas fa-plus"></i> Tambah Jadwal Baru
                        </a>
                    </div>
                `;
                return;
            }

            dayActs.forEach(act => {
                const cat = categories.find(c => Number(c.id) === Number(act.kategoriId)) || { name: 'Umum', color: '#64748b' };
                const dl = new Date(act.deadline);
                const jamStr = !isNaN(dl.getTime()) ? dl.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '';
                const isDone = (act.status === 'Selesai');

                const item = document.createElement('div');
                item.style.cssText = `background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 10px 12px; display: flex; justify-content: space-between; align-items: center; gap: 8px; border-left: 3px solid ${isDone ? '#10b981' : cat.color};`;
                item.innerHTML = `
                    <div style="min-width: 0; flex: 1;">
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 0.73rem; color: var(--text-muted); margin-bottom: 2px;">
                            <span style="font-weight: 700; color: #182531;"><i class="far fa-clock"></i> ${jamStr || 'Sepanjang Hari'}</span>
                            <span>&bull;</span>
                            <span style="color: ${cat.color}; font-weight: 700;">${escapeHtml(cat.name)}</span>
                        </div>
                        <strong style="font-size: 0.86rem; color: var(--text-dark); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            ${escapeHtml(act.judul)}
                        </strong>
                    </div>
                    <button type="button" onclick="openDashDetailModal(${act.id})" class="btn btn-secondary btn-icon btn-sm" style="flex-shrink: 0;" title="Lihat detail">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                `;
                container.appendChild(item);
            });
        }

        // ==========================================================
        // 3. FITUR PENCARIAN & FILTER DI DASHBOARD
        // ==========================================================
        function populateDashCategoryDropdown() {
            const categories = Store.getCategories();
            const select = document.getElementById('dashSearchCat');
            if (!select) return;
            select.innerHTML = '<option value="">Semua Kategori</option>';
            categories.forEach(c => {
                select.innerHTML += `<option value="${c.id}">${escapeHtml(c.name)}</option>`;
            });
        }

        function resetDashSearch() {
            document.getElementById('dashSearchKeyword').value = '';
            document.getElementById('dashSearchCat').value = '';
            document.getElementById('dashSearchStatus').value = '';
            document.getElementById('dashSearchPrio').value = '';
            document.getElementById('dashSearchDeadline').value = '';
            executeDashSearch();
        }

        function executeDashSearch() {
            const keyword = (document.getElementById('dashSearchKeyword').value || '').trim().toLowerCase();
            const catId = document.getElementById('dashSearchCat').value;
            const status = document.getElementById('dashSearchStatus').value;
            const priority = document.getElementById('dashSearchPrio').value;
            const deadlineFilter = document.getElementById('dashSearchDeadline').value;

            const activities = Store.getActivities();
            const categories = Store.getCategories();
            const now = new Date();
            const nowTime = now.getTime();
            const limit7d = nowTime + 7 * 24 * 60 * 60 * 1000;

            const filtered = activities.filter(act => {
                const matchKey = !keyword || 
                    (act.judul && act.judul.toLowerCase().includes(keyword)) || 
                    (act.deskripsi && act.deskripsi.toLowerCase().includes(keyword));
                const matchCat = !catId || Number(act.kategoriId) === Number(catId);
                const matchStat = !status || act.status === status;
                const matchPrio = !priority || act.prioritas === priority;

                let matchDeadline = true;
                if (deadlineFilter && act.deadline) {
                    const dlDate = new Date(act.deadline);
                    const dlTime = dlDate.getTime();
                    if (deadlineFilter === 'today') {
                        matchDeadline = (dlDate.getFullYear() === now.getFullYear() &&
                                         dlDate.getMonth() === now.getMonth() &&
                                         dlDate.getDate() === now.getDate());
                    } else if (deadlineFilter === 'week') {
                        matchDeadline = (dlTime >= nowTime && dlTime <= limit7d);
                    } else if (deadlineFilter === 'overdue') {
                        matchDeadline = (dlTime < nowTime && act.status !== 'Selesai');
                    } else if (deadlineFilter === 'upcoming') {
                        matchDeadline = (dlTime >= nowTime);
                    }
                }

                return matchKey && matchCat && matchStat && matchPrio && matchDeadline;
            });

            // Urutkan deadline terdekat
            filtered.sort((a, b) => new Date(a.deadline) - new Date(b.deadline));

            const tbody = document.getElementById('dashSearchTableBody');
            const countLabel = document.getElementById('dashSearchResultCount');
            tbody.innerHTML = '';

            countLabel.textContent = `Menampilkan ${filtered.length} dari ${activities.length} aktivitas`;

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:28px 16px; color:var(--text-muted); font-size:0.86rem;">Tidak ada aktivitas yang sesuai dengan kriteria pencarian Anda.</td></tr>`;
                return;
            }

            filtered.forEach(act => {
                const cat = categories.find(c => Number(c.id) === Number(act.kategoriId)) || { name: 'Umum', color: '#64748b' };
                const deadlineDate = new Date(act.deadline);
                const dateFormatted = deadlineDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });

                let prioBadge = '<span class="badge badge-medium">Sedang</span>';
                if (act.prioritas === 'Tinggi') prioBadge = '<span class="badge badge-high">Tinggi</span>';
                if (act.prioritas === 'Rendah') prioBadge = '<span class="badge badge-low">Rendah</span>';

                let statusBadge = '<span class="badge badge-todo">Belum Dimulai</span>';
                if (act.status === 'Sedang Dikerjakan') statusBadge = '<span class="badge badge-wip">Sedang Dikerjakan</span>';
                if (act.status === 'Selesai') statusBadge = '<span class="badge badge-done">Selesai</span>';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <strong style="font-size:0.88rem; color:var(--text-dark); display:block;">${escapeHtml(act.judul)}</strong>
                        ${act.deskripsi ? `<div style="font-size:0.75rem; color:var(--text-muted); max-width:240px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${escapeHtml(act.deskripsi)}</div>` : ''}
                    </td>
                    <td><span class="cat-pill" style="background:${cat.color}15; color:${cat.color}; border:1px solid ${cat.color}40; font-size: 0.74rem;">${escapeHtml(cat.name)}</span></td>
                    <td style="font-size:0.80rem; font-weight:600; white-space:nowrap;">${dateFormatted}</td>
                    <td>${prioBadge}</td>
                    <td>${statusBadge}</td>
                    <td style="text-align: right; white-space:nowrap;">
                        <button type="button" onclick="openDashDetailModal(${act.id})" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size:0.75rem;" title="Lihat detail">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        <a href="aktivitas.php?highlight=${act.id}" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size:0.75rem;" title="Kelola di Semua Aktivitas">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // ==========================================================
        // 4. JADWAL PADAT & GOALS WIDGET
        // ==========================================================
        function checkDenseSchedule(activities, now) {
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
                denseBanner.style.display = 'flex';
                document.getElementById('dense-banner-title').textContent = `Peringatan Jadwal Padat (${acts24h.length} Deadline)`;
                document.getElementById('dense-banner-desc').textContent = `Terdapat ${acts24h.length} aktivitas yang harus diselesaikan dalam waktu kurang dari 24 jam!`;

                todayHeading.textContent = "Jadwal padat";
                todayHeading.style.color = "#ef4444";
                todaySub.innerHTML = `
                    <div style="margin-bottom: 10px; color: var(--text-dark); font-weight: 700; font-size: 0.88rem;">
                        Ada <span style="color: #ef4444;">${acts24h.length} deadline</span> dalam 24 jam ke depan!
                    </div>
                    ${acts24h.map(a => {
                        const dl = new Date(a.deadline);
                        const jamStr = dl.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                        const isDone = (a.status === 'Selesai');
                        if (isDone) {
                            return `<div style="font-size: 0.78rem; padding: 7px 10px; background: #f0fdf4; border-radius: 8px; margin-bottom: 6px; color: #15803d; border-left: 3px solid #16a34a; display: flex; justify-content: space-between; align-items: center;">
                                <div><strong>${escapeHtml(a.judul)}</strong> (${jamStr})</div>
                                <span style="font-size: 0.70rem; font-weight: 700; background: #dcfce7; color: #166534; padding: 2px 5px; border-radius: 4px;"><i class="fas fa-check"></i> Selesai</span>
                            </div>`;
                        } else {
                            return `<div style="font-size: 0.78rem; padding: 7px 10px; background: #fef2f2; border-radius: 8px; margin-bottom: 6px; color: #991b1b; border-left: 3px solid #ef4444;">
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
        }

        function renderDashboardGoals() {
            const goals = Store.getGoals();
            const container = document.getElementById('dashboard-goals-container');
            if (!container) return;

            if (goals.length === 0) {
                container.innerHTML = `
                    <div style="font-size: 0.85rem; color: var(--text-muted); padding: 8px 0;">
                        Belum ada target yang dibuat. <a href="goals.php" style="color: var(--primary-accent-hover); font-weight: 700; text-decoration: none;">Buat Target Baru</a>
                    </div>
                `;
                return;
            }

            const activeGoals = goals.slice(0, 2);
            container.innerHTML = activeGoals.map(g => {
                const prog = g.progres || 0;
                return `
                    <div style="margin-bottom: 10px; background: #faf8f5; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-size: 0.86rem; font-weight: 700; color: var(--text-dark); max-width: 72%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
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

        // ==========================================================
        // 5. DETAIL MODAL DI DASHBOARD
        // ==========================================================
        function openDashDetailModal(actId) {
            const act = Store.getActivity(actId);
            if (!act) return;

            const categories = Store.getCategories();
            const cat = categories.find(c => Number(c.id) === Number(act.kategoriId)) || { name: 'Umum', color: '#64748b' };
            const now = new Date();
            const dl = new Date(act.deadline);

            document.getElementById('dashModalHeading').textContent = act.judul;
            document.getElementById('dashModalCategory').innerHTML = `<span class="cat-pill" style="background:${cat.color}15; color:${cat.color}; border:1px solid ${cat.color}40;">${escapeHtml(cat.name)}</span>`;
            document.getElementById('dashModalDeadline').textContent = dl.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Status and Urgency
            const diffMs = dl - now;
            let urgText = '';
            if (act.status === 'Selesai') {
                urgText = '<span style="color:#10b981; font-weight:700;"><i class="fas fa-check-circle"></i> Sudah Diselesaikan</span>';
            } else if (diffMs < 0) {
                urgText = '<span style="color:#ef4444; font-weight:700;"><i class="fas fa-exclamation-circle"></i> Melewati Tenggat Waktu</span>';
            } else if (diffMs <= 24 * 3600 * 1000) {
                const jamSisa = Math.max(1, Math.round(diffMs / (3600 * 1000)));
                urgText = `<span style="color:#f59e0b; font-weight:700;"><i class="fas fa-clock"></i> Tersisa ~${jamSisa} jam lagi</span>`;
            } else {
                const hariSisa = Math.round(diffMs / (24 * 3600 * 1000));
                urgText = `<span style="color:#10b981; font-weight:700;"><i class="far fa-calendar-alt"></i> Tersisa ${hariSisa} hari lagi</span>`;
            }
            document.getElementById('dashModalUrgency').innerHTML = urgText;

            let prioBadge = '<span class="badge badge-medium">Sedang</span>';
            if (act.prioritas === 'Tinggi') prioBadge = '<span class="badge badge-high">Tinggi</span>';
            if (act.prioritas === 'Rendah') prioBadge = '<span class="badge badge-low">Rendah</span>';

            let statusBadge = '<span class="badge badge-todo">Belum Dimulai</span>';
            if (act.status === 'Sedang Dikerjakan') statusBadge = '<span class="badge badge-wip">Sedang Dikerjakan</span>';
            if (act.status === 'Selesai') statusBadge = '<span class="badge badge-done">Selesai</span>';

            document.getElementById('dashModalBadges').innerHTML = `${statusBadge} ${prioBadge}`;
            document.getElementById('dashModalStatusPrio').innerHTML = `${act.status} &bull; Prioritas ${act.prioritas}`;
            document.getElementById('dashModalDesc').textContent = act.deskripsi || 'Tidak ada catatan atau instruksi tambahan.';
            document.getElementById('dashModalEditLink').href = `aktivitas.php?highlight=${act.id}`;

            document.getElementById('dashDetailModal').style.display = 'flex';
        }

        function closeDashDetailModal() {
            document.getElementById('dashDetailModal').style.display = 'none';
        }

        function escapeHtml(str) {
            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
</body>
</html>