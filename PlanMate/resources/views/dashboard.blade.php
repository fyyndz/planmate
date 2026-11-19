<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PlanMate - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script src="{{ asset('assets/js/store.js') }}"></script>
</head>
<body>
    <div class="app-shell">
        @include('partials.sidebar')

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
                <button type="button" onclick="filterDeadlineTab('urgent')" class="btn btn-sm" style="background: #e11d48; color: #ffffff; border-radius: 8px; font-weight: 700; padding: 8px 14px; border: none; cursor: pointer;">
                    Periksa Deadline
                </button>
            </div>

            <!-- Hero Welcome Card -->
            <div class="hero-card">
                <div class="hero-date" id="hero-date-str">HARI INI</div>
                <h1 class="hero-heading">
                    Halo, <span id="hero-user-name">{{ ($user->name ?? $user->username ?? 'Mahasiswa') }}.</span><br>
                    Mari rapikan harimu.
                </h1>
                <p class="hero-subtitle">
                    Satu per satu, rencana kecilmu sedang bergerak menjadi kemajuan yang nyata.
                </p>

                <a href="/aktivitas?action=create" class="btn-dark-pill">
                    <i class="fas fa-plus"></i> Buat aktivitas
                </a>
            </div>

            <!-- 3 Stat Boxes -->
            <div class="stats-row">
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
                    <a href="/aktivitas?action=create" class="btn btn-primary" style="padding: 12px 24px; font-size: 0.95rem;">
                        <i class="fas fa-plus"></i> Buat Aktivitas Pertama
                    </a>
                    <a href="/goals?action=create" class="btn btn-secondary" style="padding: 12px 24px; font-size: 0.95rem;">
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

                        <div class="dash-cal-subgrid" style="display: grid; grid-template-columns: 1.15fr 1fr; gap: 18px; align-items: start;">
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
                                <div class="dash-cal-grid" id="dash-cal-days-grid"></div>
                            </div>

                            <div style="background: #faf8f5; border: 1px solid var(--border-color); border-radius: 14px; padding: 18px; display: flex; flex-direction: column; min-height: 290px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                                    <div>
                                        <div style="font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">AGENDA TANGGAL</div>
                                        <strong style="font-size: 0.95rem; color: var(--text-dark);" id="dash-selected-date-str">-</strong>
                                    </div>
                                    <span id="dash-selected-count-badge" class="badge badge-secondary">0 Tugas</span>
                                </div>
                                <div id="dash-selected-agenda-list" style="display: flex; flex-direction: column; gap: 8px; flex: 1; overflow-y: auto; max-height: 230px;"></div>
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

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 0.8rem; color: var(--text-muted);">
                            <span id="dashSearchResultCount">Menampilkan seluruh aktivitas</span>
                            <a href="/aktivitas" class="link-action" style="font-size: 0.78rem;">Buka Semua Aktivitas &rarr;</a>
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
                                <tbody id="dashSearchTableBody"></tbody>
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
                            <a href="/aktivitas" class="link-action" title="Kelola semua aktivitas">Kelola</a>
                        </div>

                        <div class="filter-pills-row" style="margin-bottom: 16px;">
                            <button type="button" class="filter-pill active" id="flt-pill-all" onclick="filterDeadlineTab('all')">Semua</button>
                            <button type="button" class="filter-pill" id="flt-pill-urgent" onclick="filterDeadlineTab('urgent')">Mendekati Tenggat</button>
                            <button type="button" class="filter-pill" id="flt-pill-today" onclick="filterDeadlineTab('today')">Hari Ini</button>
                            <button type="button" class="filter-pill" id="flt-pill-high" onclick="filterDeadlineTab('high')">Prioritas Tinggi</button>
                            <button type="button" class="filter-pill" id="flt-pill-overdue" onclick="filterDeadlineTab('overdue')">Terlambat</button>
                        </div>

                        <div id="dash-deadline-list" style="display: flex; flex-direction: column; gap: 10px; max-height: 410px; overflow-y: auto;"></div>
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
                            <a href="/goals" class="link-action">Kelola Goals</a>
                        </div>
                        <div id="dashboard-goals-container"></div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- Modal Detail Aktivitas di Dashboard -->
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
                    <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 8px;" id="dashModalBadges"></div>
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
        window.DASHBOARD_USER = @json($user ? [
            'name' => $user->name ?? $user->username ?? 'Mahasiswa',
            'jurusan' => $user->jurusan ?? 'Mahasiswa',
        ] : null);
    </script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
</body>
</html>