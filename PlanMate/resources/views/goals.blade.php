<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PlanMate - Target &amp; Goals</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/goals.css') }}">
    {{-- store.js tetap dimuat hanya untuk Store.showConfirm & data user sidebar --}}
    <script src="{{ asset('assets/js/store.js') }}"></script>
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
                <!-- Dirender oleh goals.js -->
            </div>
        </main>
    </div>

    <!-- Modal Form Create / Edit Goal — Multi-Step -->
    <div id="goalModal" class="pm-modal-overlay">
        <div class="pm-modal-card pm-modal-lg" role="dialog" aria-modal="true" aria-labelledby="goalModalTitle">
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

                    <!-- STEP 1 -->
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

                    <!-- STEP 2 -->
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

                    <!-- STEP 3 -->
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
                        <div id="subtargets-builder-list" style="display: flex; flex-direction: column; gap: 0;"></div>
                        <div id="subtask-empty-hint" style="text-align: center; padding: 24px 16px; color: var(--text-muted); font-size: 0.85rem; border: 1px dashed var(--border-color); border-radius: 10px; display: none;">
                            <i class="fas fa-list-check" style="font-size: 1.3rem; margin-bottom: 8px; display: block; color: #cbd5e1;"></i>
                            Belum ada tahapan. Klik <strong>+ Tambah Tahap</strong> untuk menambahkan.
                        </div>
                    </div>

                </div>

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

            <div class="pm-modal-body" id="detailGoalBody"></div>

            <div class="pm-modal-footer">
                <button type="button" onclick="closeDetailGoalModal()" class="btn btn-secondary">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        window.GOALS_CONFIG = {
            aktivitas: @json($aktivitas ?? [])
        };
    </script>
    <script src="{{ asset('assets/js/goals.js') }}"></script>
</body>
</html>