<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanMate - Target &amp; Goals</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script src="{{ asset('assets/js/store.js') }}"></script>
    <script>Store.requireAuth();</script>
    <style>
        /* ===== Goal Card ===== */
        .goal-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }
        .goal-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
            border-color: #cbd5e1;
        }
        .goal-progress-track {
            width: 100%;
            height: 6px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0 0;
        }
        .goal-progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.4s ease;
        }

        /* ===== Collapsible Subtask Section ===== */
        .subtask-accordion {
            margin-top: 16px;
            border-top: 1px solid var(--border-color);
            padding-top: 12px;
        }
        .subtask-accordion-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
            padding: 4px 0;
        }
        .subtask-accordion-toggle:hover .accordion-label {
            color: var(--text-dark);
        }
        .accordion-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            transition: color 0.15s;
        }
        .accordion-meta {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .accordion-count {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 20px;
        }
        .accordion-chevron {
            font-size: 0.75rem;
            color: #94a3b8;
            transition: transform 0.25s ease;
        }
        .accordion-chevron.open {
            transform: rotate(180deg);
        }
        .subtask-list-body {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease, opacity 0.25s ease;
            opacity: 0;
        }
        .subtask-list-body.open {
            max-height: 600px;
            opacity: 1;
        }
        .subtask-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 10px;
            border-radius: 8px;
            font-size: 0.84rem;
            color: var(--text-dark);
            cursor: pointer;
            transition: background 0.15s;
            margin-bottom: 4px;
        }
        .subtask-item:hover { background: #f8fafc; }
        .subtask-item.done {
            text-decoration: line-through;
            color: #94a3b8;
        }

        /* ===== Filter Pills ===== */
        .filter-pill {
            padding: 7px 16px;
            border-radius: 30px;
            font-size: 0.84rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--border-color);
            background: #ffffff;
            color: var(--text-muted);
            transition: var(--transition);
        }
        .filter-pill.active {
            background: #182531;
            color: #ffffff;
            border-color: #182531;
        }

        /* ===== Multi-Step Modal ===== */
        .goal-stepper {
            display: flex;
            align-items: center;
            gap: 0;
            margin-bottom: 28px;
        }
        .goal-step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }
        .goal-step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 16px;
            left: calc(50% + 16px);
            right: calc(-50% + 16px);
            height: 2px;
            background: #e2e8f0;
            transition: background 0.3s;
        }
        .goal-step-item.completed:not(:last-child)::after {
            background: var(--primary-accent);
        }
        .goal-step-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 800;
            color: #94a3b8;
            transition: all 0.3s;
            z-index: 1;
        }
        .goal-step-item.active .goal-step-dot {
            border-color: var(--primary-accent);
            background: var(--primary-accent);
            color: #14222d;
        }
        .goal-step-item.completed .goal-step-dot {
            border-color: var(--primary-accent-hover);
            background: var(--primary-accent-hover);
            color: #fff;
        }
        .goal-step-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: #94a3b8;
            margin-top: 6px;
            text-align: center;
            line-height: 1.3;
        }
        .goal-step-item.active .goal-step-label { color: var(--text-dark); }
        .goal-step-item.completed .goal-step-label { color: var(--primary-accent-hover); }

        /* Step Panels */
        .goal-step-panel {
            display: none;
            animation: stepFadeIn 0.25s ease;
        }
        .goal-step-panel.active { display: block; }
        @keyframes stepFadeIn {
            from { opacity: 0; transform: translateX(10px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* Subtarget builder in step 3 */
        .st-builder-row {
            display: flex;
            gap: 8px;
            align-items: center;
            padding: 8px 10px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
        .st-builder-row + .st-builder-row { margin-top: 8px; }

        /* Step navigation footer */
        .modal-step-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 28px 24px;
            border-top: 1px solid var(--border-color);
            background: #fff;
        }
    </style>
</head>
<body>
    <div class="app-shell">
        @include('partials.sidebar')

        <main class="app-main-workspace">
            <!-- Top Breadcrumb -->
            <div class="top-header">
                <div class="breadcrumb-label">PLANMATE / Target &amp; Goals</div>
                <div>
                    <i class="far fa-bell" style="font-size: 1.1rem; color: var(--text-muted); cursor: pointer;"></i>
                </div>
            </div>

            <!-- Header Section -->
            <div class="card-section" style="margin-bottom: 24px;">
                <div class="card-header-flex" style="flex-wrap: wrap; gap: 16px;">
                    <div>
                        <h2 style="font-size: 1.4rem; font-weight: 800; color: var(--text-dark); margin-bottom: 4px;">
                            Target &amp; Goals Mahasiswa
                        </h2>
                        <p style="font-size: 0.88rem; color: var(--text-muted); margin: 0;">
                            Tetapkan sasaran akademik &amp; pribadi, pantau pencapaian, dan bagi aktivitas menjadi tahapan yang terukur.
                        </p>
                    </div>
                    <button onclick="openGoalModal()" class="btn btn-primary" style="padding: 12px 20px;">
                        <i class="fas fa-plus"></i> Buat Target Baru
                    </button>
                </div>

                <!-- 4 Stat Mini Cards -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 24px;">
                    <div style="background: #faf8f5; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Target</span>
                        <div style="font-size: 1.8rem; font-weight: 800; color: var(--text-dark); margin-top: 4px;" id="stat-total-goals">0</div>
                    </div>
                    <div style="background: #faf8f5; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Sedang Berjalan</span>
                        <div style="font-size: 1.8rem; font-weight: 800; color: #2563eb; margin-top: 4px;" id="stat-ongoing-goals">0</div>
                    </div>
                    <div style="background: #faf8f5; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Tercapai (100%)</span>
                        <div style="font-size: 1.8rem; font-weight: 800; color: #10b981; margin-top: 4px;" id="stat-completed-goals">0</div>
                    </div>
                    <div style="background: #faf8f5; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Rata-rata Progres</span>
                        <div style="font-size: 1.8rem; font-weight: 800; color: var(--primary-accent-hover); margin-top: 4px;" id="stat-avg-progress">0%</div>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div style="display: flex; gap: 10px; margin-top: 24px; flex-wrap: wrap; align-items: center;">
                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-right: 4px;">Filter:</span>
                    <button class="filter-pill active" onclick="setGoalFilter('all', this)">Semua Target</button>
                    <button class="filter-pill" onclick="setGoalFilter('ongoing', this)">Sedang Berjalan</button>
                    <button class="filter-pill" onclick="setGoalFilter('done', this)">Sudah Tercapai</button>
                    <button class="filter-pill" onclick="setGoalFilter('linked', this)">Terkait Aktivitas</button>
                    <button class="filter-pill" onclick="setGoalFilter('independent', this)">Target Mandiri</button>
                </div>
            </div>

            <!-- Empty State -->
            <div id="goals-empty-state" style="display: none; background: #ffffff; border: 2px dashed var(--border-color); border-radius: 16px; padding: 50px 20px; text-align: center;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: #ecfdf5; color: var(--primary-accent-hover); display: inline-flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 16px;">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark); margin-bottom: 8px;">Belum Ada Target / Goals</h3>
                <p style="font-size: 0.92rem; color: var(--text-muted); max-width: 480px; margin: 0 auto 24px; line-height: 1.6;">
                    Tetapkan target perkuliahan atau hubungkan dengan aktivitas agar progres belajarmu terpantau dengan jelas.
                </p>
                <button onclick="openGoalModal()" class="btn btn-primary" style="padding: 12px 24px;">
                    <i class="fas fa-plus"></i> Buat Target Pertama
                </button>
            </div>

            <!-- Goals Grid Container -->
            <div id="goals-grid-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 24px;">
                <!-- Dynamically rendered -->
            </div>
        </main>
    </div>

    <!-- Modal Form Create / Edit Goal — Multi-Step -->
    <div id="goalModal" class="pm-modal-overlay">
        <div class="pm-modal-card pm-modal-lg" role="dialog" aria-modal="true" aria-labelledby="goalModalTitle">
            <!-- Header -->
            <div class="pm-modal-header">
                <div class="pm-modal-title-group">
                    <h3 class="pm-modal-title" id="goalModalTitle">
                        <i class="fas fa-bullseye" style="color: var(--primary-accent-hover);"></i> Buat Target Baru
                    </h3>
                    <p class="pm-modal-subtitle" id="goalModalSubtitle">Langkah 1 dari 3 — Informasi Target</p>
                </div>
                <button type="button" onclick="closeGoalModal()" class="pm-modal-close" aria-label="Tutup">&times;</button>
            </div>

            <!-- Stepper -->
            <div style="padding: 20px 28px 0;">
                <div class="goal-stepper">
                    <div class="goal-step-item active" id="step-indicator-1">
                        <div class="goal-step-dot" id="step-dot-1">1</div>
                        <div class="goal-step-label">Informasi</div>
                    </div>
                    <div class="goal-step-item" id="step-indicator-2">
                        <div class="goal-step-dot" id="step-dot-2">2</div>
                        <div class="goal-step-label">Pengaturan</div>
                    </div>
                    <div class="goal-step-item" id="step-indicator-3">
                        <div class="goal-step-dot" id="step-dot-3">3</div>
                        <div class="goal-step-label">Tahapan</div>
                    </div>
                </div>
            </div>

            <form id="goalForm">
                <input type="hidden" id="goalId">
                <div class="pm-modal-body" style="padding-top: 8px;">

                    <!-- STEP 1: Informasi Target -->
                    <div class="goal-step-panel active" id="goal-panel-1">
                        <div class="pm-form-group">
                            <label for="goalJudul" class="pm-label">Nama / Judul Target <span class="required">*</span></label>
                            <input type="text" id="goalJudul" class="pm-input" placeholder="Contoh: Menyelesaikan Laporan Praktikum Modul 1–4">
                        </div>
                        <div class="pm-form-group">
                            <label for="goalDeskripsi" class="pm-label">Deskripsi &amp; Sasaran <span style="font-weight: 400; color: var(--text-muted); font-size: 0.8rem;">(opsional)</span></label>
                            <textarea id="goalDeskripsi" class="pm-textarea" rows="3" placeholder="Catatan, detail, atau sasaran spesifik dari target ini..."></textarea>
                        </div>
                    </div>

                    <!-- STEP 2: Pengaturan -->
                    <div class="goal-step-panel" id="goal-panel-2">
                        <div class="pm-form-group">
                            <label for="goalAktivitasId" class="pm-label">Hubungkan dengan Aktivitas <span style="font-weight: 400; color: var(--text-muted); font-size: 0.8rem;">(opsional)</span></label>
                            <select id="goalAktivitasId" class="pm-select">
                                <option value="">— Target Mandiri (Tanpa Aktivitas) —</option>
                            </select>
                            <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 5px;">Pilih aktivitas untuk memantau progres tugas tersebut secara terperinci.</span>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="pm-form-group" style="margin-bottom: 0;">
                                <label for="goalDeadline" class="pm-label"><i class="far fa-calendar-alt"></i> Target Selesai (Deadline)</label>
                                <input type="date" id="goalDeadline" class="pm-input">
                            </div>
                            <div class="pm-form-group" style="margin-bottom: 0;">
                                <label class="pm-label"><i class="fas fa-chart-line"></i> Progres Saat Ini</label>
                                <div style="display: flex; align-items: center; gap: 12px; height: 42px;">
                                    <input type="range" id="goalProgresRange" min="0" max="100" value="0"
                                        style="flex: 1; accent-color: var(--primary-accent-hover);"
                                        oninput="document.getElementById('goalProgresText').textContent = this.value + '%'">
                                    <span id="goalProgresText" style="font-weight: 800; font-size: 0.95rem; min-width: 42px; text-align: right; color: var(--text-dark);">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: Tahapan & Milestone -->
                    <div class="goal-step-panel" id="goal-panel-3">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <div>
                                <div style="font-size: 0.9rem; font-weight: 700; color: var(--text-dark);">Tahapan &amp; Milestone</div>
                                <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">Bagi target menjadi langkah-langkah kecil yang terukur.</div>
                            </div>
                            <button type="button" onclick="addSubtargetRow()" class="btn btn-secondary btn-sm" style="font-weight: 700;">
                                <i class="fas fa-plus"></i> Tambah Tahap
                            </button>
                        </div>
                        <div id="subtargets-builder-list" style="display: flex; flex-direction: column; gap: 0;">
                            <!-- Subtarget rows inserted here -->
                        </div>
                        <div id="subtask-empty-hint" style="text-align: center; padding: 24px 16px; color: var(--text-muted); font-size: 0.85rem; border: 1px dashed var(--border-color); border-radius: 10px; display: none;">
                            <i class="fas fa-list-check" style="font-size: 1.3rem; margin-bottom: 8px; display: block; color: #cbd5e1;"></i>
                            Belum ada tahapan. Klik <strong>+ Tambah Tahap</strong> untuk menambahkan.
                        </div>
                    </div>

                </div>

                <!-- Step Navigation Footer -->
                <div class="modal-step-footer">
                    <button type="button" id="btnGoalBack" onclick="goalStepBack()" class="btn btn-secondary" style="display: none;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                    <div></div>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" onclick="closeGoalModal()" class="btn btn-secondary">Batal</button>
                        <button type="button" id="btnGoalNext" onclick="goalStepNext()" class="btn btn-primary">
                            Selanjutnya <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" class="btn btn-primary" id="btnGoalSubmit" style="display: none;">
                            <i class="fas fa-save"></i> Simpan Target
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Quick Update Progres -->
    <div id="progressModal" class="pm-modal-overlay">
        <div class="pm-modal-card pm-modal-sm" role="dialog" aria-modal="true" aria-labelledby="quickModalTitle">
            <div class="pm-modal-header">
                <div class="pm-modal-title-group">
                    <h3 class="pm-modal-title" id="quickModalTitle">
                        <i class="fas fa-tasks" style="color: var(--primary-accent-hover);"></i> Perbarui Progres Target
                    </h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 320px;" id="quick-goal-title">
                        Target Name
                    </p>
                </div>
                <button type="button" onclick="closeProgressModal()" class="pm-modal-close" aria-label="Tutup">&times;</button>
            </div>
            <div class="pm-modal-body">
                <input type="hidden" id="quickGoalId">
                <div style="margin-bottom: 12px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-weight: 800; font-size: 1.1rem;">
                        <span style="font-size: 0.9rem; color: #475569;">Persentase Capaian</span>
                        <span id="quickProgressVal" style="color: var(--primary-accent-hover);">50%</span>
                    </div>
                    <input type="range" id="quickProgressSlider" min="0" max="100" value="50" style="width: 100%; height: 8px; accent-color: var(--primary-accent-hover);" oninput="document.getElementById('quickProgressVal').textContent = this.value + '%'">
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted); margin-top: 6px;">
                        <span>0% (Belum Dimulai)</span>
                        <span>50%</span>
                        <span>100% (Tuntas)</span>
                    </div>
                </div>
            </div>
            <div class="pm-modal-footer">
                <button type="button" onclick="closeProgressModal()" class="btn btn-secondary">Batal</button>
                <button type="button" onclick="saveQuickProgress()" class="btn btn-primary"><i class="fas fa-check"></i> Simpan Progres</button>
            </div>
        </div>
    </div>

    <!-- Modal View Detail Goal -->
    <div id="detailGoalModal" class="pm-modal-overlay">
        <div class="pm-modal-card pm-modal-md" role="dialog" aria-modal="true" aria-labelledby="detailGoalTitle">
            <div class="pm-modal-header">
                <div class="pm-modal-title-group">
                    <h3 class="pm-modal-title" id="detailGoalTitle">
                        <i class="fas fa-bullseye" style="color: var(--primary-accent-hover);"></i> Detail Target
                    </h3>
                    <p class="pm-modal-subtitle">Informasi capaian dan penyelesaian tahapan kecil target.</p>
                </div>
                <button type="button" onclick="closeDetailGoalModal()" class="pm-modal-close" aria-label="Tutup">&times;</button>
            </div>
            
            <div class="pm-modal-body" id="detailGoalBody">
                <!-- Dynamically loaded -->
            </div>

            <div class="pm-modal-footer">
                <button type="button" onclick="closeDetailGoalModal()" class="btn btn-secondary">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        let currentGoalFilter = 'all';

        // ---------- Komunikasi dengan server (data goals tersimpan di MySQL) ----------
        const GOAL_CSRF = '{{ csrf_token() }}';

        async function goalApi(method, url, body) {
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': GOAL_CSRF
                },
                body: body ? JSON.stringify(body) : undefined
            });
            if (res.status === 401 || res.status === 419) {
                window.location.href = '/login';
                throw new Error('Sesi berakhir, silakan masuk lagi.');
            }
            if (!res.ok) {
                let msg = 'Terjadi kesalahan saat menyimpan data.';
                try {
                    const j = await res.json();
                    if (j.errors) msg = Object.values(j.errors)[0][0];
                    else if (j.message) msg = j.message;
                } catch (e) {}
                throw new Error(msg);
            }
            return res.status === 204 ? null : res.json();
        }

        // Ambil ulang daftar goals dari server lalu simpan ke localStorage (dipakai Store.getGoals)
        async function syncGoals() {
            const goals = await goalApi('GET', '/goals/data');
            try { localStorage.setItem('planmate_goals', JSON.stringify(goals)); } catch (e) {}
        }

        function goalError(e) {
            alert(e && e.message ? e.message : 'Terjadi kesalahan.');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const user = Store.getCurrentUser();
            if (user) {
                const name = user.name || user.username || 'Mahasiswa';
                document.getElementById('sidebar-user-name').textContent = name;
                document.getElementById('sidebar-user-role').textContent = user.jurusan || 'Mahasiswa';
                document.getElementById('sidebar-avatar').textContent = name.charAt(0).toUpperCase();
            }

            renderGoals();

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'create') {
                const linkedActId = urlParams.get('activity_id');
                openGoalModal(null, linkedActId);
            }
        });

        function populateActivitiesDropdown(selectedId = null) {
            const activities = Store.getActivities();
            const select = document.getElementById('goalAktivitasId');
            select.innerHTML = '<option value="">-- Target Mandiri (Tanpa Aktivitas) --</option>';
            activities.forEach(a => {
                const sel = (selectedId && Number(selectedId) === Number(a.id)) ? 'selected' : '';
                select.innerHTML += `<option value="${a.id}" ${sel}>${escapeHtml(a.judul)}</option>`;
            });
        }

        function setGoalFilter(filter, elem) {
            currentGoalFilter = filter;
            document.querySelectorAll('.filter-pill').forEach(btn => btn.classList.remove('active'));
            if (elem) elem.classList.add('active');
            renderGoals();
        }

        function renderGoals() {
            const allGoals = Store.getGoals();
            const total = allGoals.length;
            const completed = allGoals.filter(g => Number(g.progres) >= 100).length;
            const ongoing = total - completed;

            let avgProgress = 0;
            if (total > 0) {
                const sum = allGoals.reduce((acc, curr) => acc + (Number(curr.progres) || 0), 0);
                avgProgress = Math.round(sum / total);
            }

            document.getElementById('stat-total-goals').textContent = total;
            document.getElementById('stat-ongoing-goals').textContent = ongoing;
            document.getElementById('stat-completed-goals').textContent = completed;
            document.getElementById('stat-avg-progress').textContent = avgProgress + '%';

            // Filter logic
            let filtered = allGoals;
            if (currentGoalFilter === 'ongoing') {
                filtered = allGoals.filter(g => Number(g.progres) < 100);
            } else if (currentGoalFilter === 'done') {
                filtered = allGoals.filter(g => Number(g.progres) >= 100);
            } else if (currentGoalFilter === 'linked') {
                filtered = allGoals.filter(g => g.aktivitasId);
            } else if (currentGoalFilter === 'independent') {
                filtered = allGoals.filter(g => !g.aktivitasId);
            }

            const emptyState = document.getElementById('goals-empty-state');
            const grid = document.getElementById('goals-grid-container');

            if (total === 0) {
                emptyState.style.display = 'block';
                grid.style.display = 'none';
                return;
            } else {
                emptyState.style.display = 'none';
                grid.style.display = 'grid';
            }

            grid.innerHTML = '';
            if (filtered.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1 / -1; padding: 40px; background: #ffffff; border-radius: 16px; text-align: center; color: var(--text-muted); border: 1px dashed var(--border-color);">
                        <i class="fas fa-search" style="font-size: 1.5rem; margin-bottom: 8px; display: block;"></i>
                        Tidak ada target yang cocok dengan filter yang dipilih.
                    </div>
                `;
                return;
            }

            filtered.forEach(goal => {
                const prog = Number(goal.progres) || 0;
                const isComplete = prog >= 100;

                const card = document.createElement('div');
                card.className = 'goal-card';

                // Deadline HTML
                let deadlineHtml = '';
                if (goal.deadline) {
                    const dl = new Date(goal.deadline);
                    const now = new Date();
                    const diffMs = dl - now;
                    const diffDays = Math.ceil(diffMs / (1000 * 60 * 60 * 24));
                    const dlFormatted = dl.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                    let deadlineColor = 'var(--text-muted)';
                    if (!isComplete && diffDays <= 2 && diffDays >= 0) deadlineColor = '#ef4444';
                    else if (!isComplete && diffDays <= 7) deadlineColor = '#f59e0b';
                    deadlineHtml = `<span style="font-size: 0.78rem; color: ${deadlineColor}; font-weight: 600;"><i class="far fa-calendar-alt"></i> ${dlFormatted}</span>`;
                }

                // Linked activity badge
                let linkBadge = `<span style="font-size: 0.72rem; font-weight: 700; background: #f8fafc; color: #64748b; padding: 2px 8px; border-radius: 6px; border: 1px solid #e2e8f0;"><i class="fas fa-user"></i> Mandiri</span>`;
                if (goal.aktivitas) {
                    linkBadge = `<a href="/aktivitas?highlight=${goal.aktivitas.id}" style="text-decoration:none; font-size: 0.72rem; font-weight: 700; background: #eff6ff; color: #2563eb; padding: 2px 8px; border-radius: 6px; border: 1px solid #bfdbfe; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block;" title="${escapeHtml(goal.aktivitas.judul)}"><i class="fas fa-link"></i> ${escapeHtml(goal.aktivitas.judul)}</a>`;
                }

                // Collapsible subtargets
                let subtargetsHtml = '';
                if (Array.isArray(goal.targetKecil) && goal.targetKecil.length > 0) {
                    const doneCount = goal.targetKecil.filter(s => s.done).length;
                    const total = goal.targetKecil.length;
                    const accordionId = `subtask-body-${goal.id}`;
                    const chevronId = `subtask-chevron-${goal.id}`;

                    const subtaskItems = goal.targetKecil.map(st => `
                        <div class="subtask-item ${st.done ? 'done' : ''}" onclick="toggleSubtarget(${goal.id}, ${st.id})">
                            <i class="fas ${st.done ? 'fa-check-square' : 'fa-square'}" style="color: ${st.done ? '#10b981' : '#cbd5e1'}; font-size: 0.95rem; flex-shrink: 0;"></i>
                            <span style="flex: 1;">${escapeHtml(st.text)}</span>
                        </div>
                    `).join('');

                    subtargetsHtml = `
                        <div class="subtask-accordion">
                            <div class="subtask-accordion-toggle" onclick="toggleAccordion('${accordionId}', '${chevronId}')">
                                <span class="accordion-label">Tahapan Pencapaian</span>
                                <div class="accordion-meta">
                                    <span class="accordion-count">${doneCount}/${total} selesai</span>
                                    <i class="fas fa-chevron-down accordion-chevron" id="${chevronId}"></i>
                                </div>
                            </div>
                            <div class="subtask-list-body" id="${accordionId}">
                                <div style="margin-top: 10px; display: flex; flex-direction: column; gap: 2px;">
                                    ${subtaskItems}
                                </div>
                            </div>
                        </div>
                    `;
                }

                card.innerHTML = `
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 12px;">
                            ${linkBadge}
                            ${isComplete
                                ? '<span style="font-size: 0.72rem; font-weight: 800; background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 6px;"><i class="fas fa-check-circle"></i> Tercapai</span>'
                                : '<span style="font-size: 0.72rem; font-weight: 800; background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 6px;"><i class="fas fa-circle-notch fa-spin"></i> Berjalan</span>'}
                        </div>

                        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-dark); margin-bottom: 6px; line-height: 1.4;">
                            ${escapeHtml(goal.judul)}
                        </h3>

                        ${goal.deskripsi ? `<p style="font-size: 0.83rem; color: var(--text-muted); margin-bottom: 10px; line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${escapeHtml(goal.deskripsi)}</p>` : ''}
                        ${deadlineHtml ? `<div style="margin-bottom: 10px;">${deadlineHtml}</div>` : ''}

                        <!-- Progress -->
                        <div style="margin-top: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; font-weight: 700; margin-bottom: 4px;">
                                <span style="color: var(--text-muted);">Kemajuan</span>
                                <span style="color: ${isComplete ? '#10b981' : 'var(--primary-accent-hover)'};">${prog}%</span>
                            </div>
                            <div class="goal-progress-track">
                                <div class="goal-progress-fill" style="width: ${prog}%; background: ${isComplete ? '#10b981' : 'linear-gradient(90deg, #34d399, #059669)'};"></div>
                            </div>
                        </div>

                        ${subtargetsHtml}
                    </div>

                    <!-- Card Actions -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 14px; border-top: 1px solid var(--border-color); margin-top: 16px;">
                        <button onclick="openQuickProgress(${goal.id})" class="btn btn-secondary btn-sm" title="Perbarui progres">
                            <i class="fas fa-sliders-h"></i> Progres
                        </button>
                        <div style="display: flex; gap: 6px;">
                            <button onclick="viewGoalDetail(${goal.id})" class="btn btn-secondary btn-icon btn-sm" title="Lihat Detail"><i class="fas fa-eye"></i></button>
                            <button onclick="openGoalModal(${goal.id})" class="btn btn-secondary btn-icon btn-sm" title="Edit Target"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteGoalItem(${goal.id})" class="btn btn-secondary btn-icon btn-sm" style="color: #ef4444;" title="Hapus Target"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                `;

                grid.appendChild(card);
            });
        }

        // Accordion toggle for subtask list on card
        function toggleAccordion(bodyId, chevronId) {
            const body = document.getElementById(bodyId);
            const chevron = document.getElementById(chevronId);
            if (!body) return;
            const isOpen = body.classList.contains('open');
            body.classList.toggle('open', !isOpen);
            if (chevron) chevron.classList.toggle('open', !isOpen);
        }

        // Subtarget Toggle with automatic progress calculation
        async function toggleSubtarget(goalId, subtargetId) {
            try {
                await goalApi('PATCH', `/goals/${goalId}/tahapan/${subtargetId}/toggle`);
                await syncGoals();
                renderGoals();
            } catch (e) { goalError(e); }
        }

        // Quick Progress Modal
        function openQuickProgress(goalId) {
            const goal = Store.getGoal(goalId);
            if (!goal) return;

            document.getElementById('quickGoalId').value = goal.id;
            document.getElementById('quick-goal-title').textContent = goal.judul;
            const prog = Number(goal.progres) || 0;
            document.getElementById('quickProgressSlider').value = prog;
            document.getElementById('quickProgressVal').textContent = prog + '%';
            document.getElementById('progressModal').style.display = 'flex';
        }

        function closeProgressModal() {
            document.getElementById('progressModal').style.display = 'none';
        }

        async function saveQuickProgress() {
            const goalId = document.getElementById('quickGoalId').value;
            const prog = Number(document.getElementById('quickProgressSlider').value) || 0;
            try {
                await goalApi('PATCH', `/goals/${goalId}/progress`, { progres: prog });
                await syncGoals();
                closeProgressModal();
                renderGoals();
            } catch (e) { goalError(e); }
        }

        // ============================================================
        // Multi-Step Modal Logic
        // ============================================================
        let _goalCurrentStep = 1;
        const _goalTotalSteps = 3;

        const _stepSubtitles = [
            'Langkah 1 dari 3 — Informasi Target',
            'Langkah 2 dari 3 — Pengaturan Target',
            'Langkah 3 dari 3 — Tahapan & Milestone',
        ];

        function _goalGotoStep(step) {
            _goalCurrentStep = step;

            // Show/hide panels
            for (let i = 1; i <= _goalTotalSteps; i++) {
                const panel = document.getElementById(`goal-panel-${i}`);
                if (panel) panel.classList.toggle('active', i === step);
            }

            // Update stepper indicators
            for (let i = 1; i <= _goalTotalSteps; i++) {
                const indicator = document.getElementById(`step-indicator-${i}`);
                const dot = document.getElementById(`step-dot-${i}`);
                if (!indicator) continue;
                indicator.classList.remove('active', 'completed');
                if (i < step) {
                    indicator.classList.add('completed');
                    dot.innerHTML = '<i class="fas fa-check" style="font-size:0.65rem;"></i>';
                } else if (i === step) {
                    indicator.classList.add('active');
                    dot.textContent = i;
                } else {
                    dot.textContent = i;
                }
            }

            // Subtitle
            document.getElementById('goalModalSubtitle').textContent = _stepSubtitles[step - 1];

            // Back button
            document.getElementById('btnGoalBack').style.display = step > 1 ? 'inline-flex' : 'none';

            // Next / Submit
            const btnNext = document.getElementById('btnGoalNext');
            const btnSubmit = document.getElementById('btnGoalSubmit');
            if (step < _goalTotalSteps) {
                btnNext.style.display = 'inline-flex';
                btnSubmit.style.display = 'none';
            } else {
                btnNext.style.display = 'none';
                btnSubmit.style.display = 'inline-flex';
            }

            // Show/hide subtask empty hint
            if (step === 3) _updateSubtaskHint();
        }

        function goalStepNext() {
            // Validate step 1
            if (_goalCurrentStep === 1) {
                const judul = document.getElementById('goalJudul').value.trim();
                if (!judul) {
                    document.getElementById('goalJudul').focus();
                    document.getElementById('goalJudul').style.borderColor = '#ef4444';
                    document.getElementById('goalJudul').addEventListener('input', function h() {
                        this.style.borderColor = '';
                        this.removeEventListener('input', h);
                    });
                    return;
                }
            }
            if (_goalCurrentStep < _goalTotalSteps) {
                _goalGotoStep(_goalCurrentStep + 1);
            }
        }

        function goalStepBack() {
            if (_goalCurrentStep > 1) {
                _goalGotoStep(_goalCurrentStep - 1);
            }
        }

        function _updateSubtaskHint() {
            const list = document.getElementById('subtargets-builder-list');
            const hint = document.getElementById('subtask-empty-hint');
            if (!list || !hint) return;
            hint.style.display = list.children.length === 0 ? 'block' : 'none';
        }

        function addSubtargetRow(initialText = '', isDone = false) {
            const container = document.getElementById('subtargets-builder-list');
            const id = Date.now() + Math.floor(Math.random() * 1000);

            const row = document.createElement('div');
            row.id = `st-row-${id}`;
            row.className = 'st-builder-row';
            row.innerHTML = `
                <input type="checkbox" ${isDone ? 'checked' : ''} class="st-done-check" style="cursor: pointer; width: 16px; height: 16px; flex-shrink: 0; accent-color: var(--primary-accent-hover);">
                <input type="text" class="form-control st-text-input" value="${escapeHtml(initialText)}" placeholder="Tuliskan langkah atau milestone..." style="flex: 1; padding: 7px 12px; font-size: 0.84rem;">
                <button type="button" onclick="removeSubtargetRow('${id}')" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 4px 6px; font-size: 0.9rem; flex-shrink: 0;" title="Hapus tahapan">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(row);
            _updateSubtaskHint();
        }

        function removeSubtargetRow(rowId) {
            const row = document.getElementById(`st-row-${rowId}`);
            if (row) row.remove();
            _updateSubtaskHint();
        }

        function openGoalModal(goalId = null, preselectActId = null) {
            document.getElementById('goalForm').reset();
            document.getElementById('goalId').value = '';
            document.getElementById('subtargets-builder-list').innerHTML = '';
            document.getElementById('goalProgresRange').value = 0;
            document.getElementById('goalProgresText').textContent = '0%';

            populateActivitiesDropdown();
            _goalGotoStep(1);

            if (goalId) {
                const goal = Store.getGoal(goalId);
                if (goal) {
                    document.getElementById('goalModalTitle').innerHTML = '<i class="fas fa-edit" style="color: var(--primary-accent-hover);"></i> Edit Target';
                    document.getElementById('goalId').value = goal.id;
                    document.getElementById('goalJudul').value = goal.judul;
                    document.getElementById('goalAktivitasId').value = goal.aktivitasId || '';
                    document.getElementById('goalDeadline').value = goal.deadline ? goal.deadline.slice(0, 10) : '';
                    document.getElementById('goalDeskripsi').value = goal.deskripsi || '';

                    const prog = Number(goal.progres) || 0;
                    document.getElementById('goalProgresRange').value = prog;
                    document.getElementById('goalProgresText').textContent = prog + '%';

                    if (Array.isArray(goal.targetKecil) && goal.targetKecil.length > 0) {
                        goal.targetKecil.forEach(st => addSubtargetRow(st.text, st.done));
                    }
                }
            } else {
                document.getElementById('goalModalTitle').innerHTML = '<i class="fas fa-bullseye" style="color: var(--primary-accent-hover);"></i> Buat Target Baru';
                if (preselectActId) {
                    document.getElementById('goalAktivitasId').value = preselectActId;
                    const linkedAct = Store.getActivity(preselectActId);
                    if (linkedAct) {
                        document.getElementById('goalJudul').value = `Target: ${linkedAct.judul}`;
                        if (linkedAct.deadline) {
                            document.getElementById('goalDeadline').value = linkedAct.deadline.slice(0, 10);
                        }
                    }
                }
                // Default milestones
                addSubtargetRow('Tahap 1: Perencanaan & Persiapan');
                addSubtargetRow('Tahap 2: Eksekusi & Penyelesaian');
            }

            document.getElementById('goalModal').style.display = 'flex';
        }

        function closeGoalModal() {
            document.getElementById('goalModal').style.display = 'none';
        }

        document.getElementById('goalForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('goalId').value;
            const judul = document.getElementById('goalJudul').value.trim();
            const aktivitasId = document.getElementById('goalAktivitasId').value || null;
            const deadline = document.getElementById('goalDeadline').value;
            const deskripsi = document.getElementById('goalDeskripsi').value.trim();
            const progresManual = Number(document.getElementById('goalProgresRange').value) || 0;

            if (!judul) {
                _goalGotoStep(1);
                document.getElementById('goalJudul').focus();
                return;
            }

            // Kumpulkan tahapan kecil
            const subtargetRows = document.querySelectorAll('#subtargets-builder-list > div');
            const targetKecil = [];
            subtargetRows.forEach((row, idx) => {
                const text = row.querySelector('.st-text-input').value.trim();
                const done = row.querySelector('.st-done-check').checked;
                if (text) {
                    targetKecil.push({ id: idx + 1, text, done });
                }
            });

            const data = {
                judul,
                aktivitasId: aktivitasId ? Number(aktivitasId) : null,
                deadline,
                deskripsi,
                progres: progresManual,
                targetKecil
            };

            try {
                if (id) {
                    await goalApi('PUT', `/goals/${id}`, data);
                } else {
                    await goalApi('POST', '/goals', data);
                }
                await syncGoals();
                closeGoalModal();
                renderGoals();
            } catch (err) { goalError(err); }
        });

        function deleteGoalItem(id) {
            const goal = Store.getGoal(id);
            if (!goal) return;
            Store.showConfirm({
                title: 'Hapus Target / Goal?',
                message: `Apakah Anda yakin ingin menghapus target "${escapeHtml(goal.judul)}"? Seluruh milestone tahapan yang telah dicatat akan dihapus secara permanen.`,
                icon: 'fas fa-trash-alt',
                confirmText: 'Ya, Hapus',
                cancelText: 'Batal',
                isDanger: true,
                onConfirm: async () => {
                    try {
                        await goalApi('DELETE', `/goals/${id}`);
                        await syncGoals();
                        renderGoals();
                    } catch (e) { goalError(e); }
                }
            });
        }

        function viewGoalDetail(id) {
            const goal = Store.getGoal(id);
            if (!goal) return;

            const prog = Number(goal.progres) || 0;
            const isComplete = prog >= 100;
            document.getElementById('detailGoalTitle').textContent = goal.judul;

            let actDetailHtml = '<span style="color: var(--text-muted);">Target Mandiri (Tidak terkait aktivitas spesifik)</span>';
            if (goal.aktivitas) {
                actDetailHtml = `
                    <div style="padding: 10px 14px; background: #eff6ff; border-radius: 8px; border: 1px solid #bfdbfe; font-size: 0.88rem;">
                        <strong style="color: #1e40af;"><i class="fas fa-clipboard-check"></i> ${escapeHtml(goal.aktivitas.judul)}</strong>
                        <div style="font-size: 0.78rem; color: #3b82f6; margin-top: 4px;">
                            Status Aktivitas: <strong>${goal.aktivitas.status}</strong> | Deadline: ${goal.aktivitas.deadline || '-'}
                        </div>
                    </div>
                `;
            }

            let subtargetsHtml = '<p style="color: var(--text-muted); font-size: 0.85rem;">Tidak ada tahapan kecil yang didaftarkan.</p>';
            if (Array.isArray(goal.targetKecil) && goal.targetKecil.length > 0) {
                subtargetsHtml = `
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        ${goal.targetKecil.map(st => `
                            <div style="display: flex; align-items: center; gap: 10px; padding: 8px 12px; background: #f8fafc; border-radius: 8px; font-size: 0.88rem; ${st.done ? 'text-decoration: line-through; color: #94a3b8;' : ''}">
                                <i class="fas ${st.done ? 'fa-check-circle' : 'fa-circle'}" style="color: ${st.done ? '#10b981' : '#cbd5e1'};"></i>
                                <span>${escapeHtml(st.text)}</span>
                            </div>
                        `).join('')}
                    </div>
                `;
            }

            document.getElementById('detailGoalBody').innerHTML = `
                <div style="margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">STATUS TARGET:</span>
                        ${isComplete
                            ? '<span style="font-size: 0.8rem; font-weight: 800; background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 6px;"><i class="fas fa-check-circle"></i> Selesai (100%)</span>'
                            : '<span style="font-size: 0.8rem; font-weight: 800; background: #fef3c7; color: #b45309; padding: 4px 10px; border-radius: 6px;"><i class="fas fa-spinner fa-spin"></i> Dalam Pengerjaan</span>'}
                    </div>

                    <div style="margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem; font-weight: 800; margin-bottom: 6px;">
                            <span>Progres Capaian</span>
                            <span style="color: ${isComplete ? '#10b981' : 'var(--primary-accent-hover)'};">${prog}%</span>
                        </div>
                        <div class="goal-progress-track" style="margin: 0;">
                            <div class="goal-progress-fill" style="width: ${prog}%; background: ${isComplete ? '#10b981' : 'linear-gradient(90deg, #34d399, #059669)'};"></div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Aktivitas Terkait</span>
                    <div style="margin-top: 6px;">${actDetailHtml}</div>
                </div>

                ${goal.deskripsi ? `
                    <div style="margin-bottom: 16px;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Deskripsi & Catatan</span>
                        <p style="font-size: 0.9rem; color: var(--text-dark); margin-top: 4px; line-height: 1.6; background: #faf8f5; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color);">${escapeHtml(goal.deskripsi)}</p>
                    </div>
                ` : ''}

                <div style="margin-bottom: 16px;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Target Deadline</span>
                    <div style="font-size: 0.9rem; font-weight: 600; color: var(--text-dark); margin-top: 4px;">
                        ${goal.deadline ? new Date(goal.deadline).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) : 'Tidak ada batas waktu deadline'}
                    </div>
                </div>

                <div>
                    <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 8px;">Daftar Tahapan Target</span>
                    ${subtargetsHtml}
                </div>
            `;

            document.getElementById('detailGoalModal').style.display = 'flex';
        }

        function closeDetailGoalModal() {
            document.getElementById('detailGoalModal').style.display = 'none';
        }

        // Overlay click to close
        ['goalModal', 'progressModal', 'detailGoalModal'].forEach(mId => {
            const el = document.getElementById(mId);
            if (el) {
                el.addEventListener('click', (e) => {
                    if (e.target === el) el.style.display = 'none';
                });
            }
        });

        function escapeHtml(str) {
            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
</body>
</html>
