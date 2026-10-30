<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanMate - Semua Aktivitas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/store.js"></script>
    <script>Store.requireAuth();</script>
    <style>
        @keyframes rowPulse {
            0% { background-color: #fef08a; transform: scale(1.005); }
            50% { background-color: #fde047; }
            100% { background-color: transparent; transform: scale(1); }
        }
        .highlight-row {
            animation: rowPulse 2.5s ease-in-out;
            border-left: 4px solid var(--primary-accent) !important;
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <?php include_once __DIR__ . '/sidebar.php'; ?>

        <main class="app-main-workspace">
            <div class="top-header">
                <div class="breadcrumb-label">PLANMATE / Semua Aktivitas</div>
            </div>

            <div class="card-section">
                <div class="card-header-flex">
                    <div>
                        <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-dark);">Manajemen Aktivitas</h2>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Kelola, tambah, ubah, dan hapus data tugas perkuliahan &amp; kegiatan Anda.</p>
                    </div>
                    <button onclick="openActModal()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Aktivitas Baru
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Judul Aktivitas</th>
                                <th>Kategori</th>
                                <th>Deadline</th>
                                <th>Prioritas</th>
                                <th>Status</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbl-aktivitas-body"></tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Form Create / Edit Activity -->
    <div id="actModal" class="pm-modal-overlay">
        <div class="pm-modal-card pm-modal-md" role="dialog" aria-modal="true" aria-labelledby="actModalTitle">
            <div class="pm-modal-header">
                <div class="pm-modal-title-group">
                    <h3 class="pm-modal-title" id="actModalTitle">
                        <i class="fas fa-plus-circle" style="color: var(--primary-accent-hover);"></i> Tambah Aktivitas
                    </h3>
                    <p class="pm-modal-subtitle">Isi rincian kegiatan dan tentukan tenggat waktu penyelesaian.</p>
                </div>
                <button type="button" onclick="closeActModal()" class="pm-modal-close" aria-label="Tutup">&times;</button>
            </div>
            <form id="actForm">
                <div class="pm-modal-body">
                    <input type="hidden" id="actId">
                    
                    <div class="pm-form-group">
                        <label for="judul" class="pm-label">Judul Aktivitas <span class="required">*</span></label>
                        <input type="text" id="judul" class="pm-input" required placeholder="Contoh: Laporan Praktikum PAP Modul 4">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px;" class="pm-form-group">
                        <div>
                            <label for="kategoriId" class="pm-label">Kategori <span class="required">*</span></label>
                            <select id="kategoriId" class="pm-select" required></select>
                        </div>
                        <div>
                            <label for="status" class="pm-label">Status</label>
                            <select id="status" class="pm-select">
                                <option value="Belum Dimulai">Belum Dimulai</option>
                                <option value="Sedang Dikerjakan">Sedang Dikerjakan</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                        <div>
                            <label for="prioritas" class="pm-label">Prioritas</label>
                            <select id="prioritas" class="pm-select">
                                <option value="Tinggi">Tinggi</option>
                                <option value="Sedang" selected>Sedang</option>
                                <option value="Rendah">Rendah</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 14px;" class="pm-form-group">
                        <div>
                            <label for="deadline_date" class="pm-label"><i class="far fa-calendar-alt"></i> Tanggal Deadline <span class="required">*</span></label>
                            <input type="date" id="deadline_date" class="pm-input" required>
                        </div>
                        <div>
                            <label for="deadline_time" class="pm-label"><i class="far fa-clock"></i> Jam Deadline <span class="required">*</span></label>
                            <input type="time" id="deadline_time" class="pm-input" required value="23:59">
                        </div>
                    </div>

                    <div class="pm-form-group" style="margin-bottom: 0;">
                        <label for="deskripsi" class="pm-label">Deskripsi &amp; Catatan</label>
                        <textarea id="deskripsi" class="pm-textarea" rows="3" placeholder="Rincian catatan, instruksi tugas, atau link referensi..."></textarea>
                    </div>
                </div>
                <div class="pm-modal-footer">
                    <button type="button" onclick="closeActModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnActSubmit"><i class="fas fa-save"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal View Detail Aktivitas -->
    <div id="viewActModal" class="pm-modal-overlay">
        <div class="pm-modal-card pm-modal-md" role="dialog" aria-modal="true" aria-labelledby="viewActHeading">
            <div class="pm-modal-header">
                <div class="pm-modal-title-group">
                    <h3 class="pm-modal-title">
                        <i class="far fa-file-alt" style="color: var(--primary-accent-hover);"></i> Rincian Aktivitas
                    </h3>
                    <p class="pm-modal-subtitle">Detail informasi tugas perkuliahan dan statusnya.</p>
                </div>
                <button type="button" onclick="closeViewActModal()" class="pm-modal-close" aria-label="Tutup">&times;</button>
            </div>
            <div class="pm-modal-body">
                <div style="margin-bottom: 16px;">
                    <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 8px;" id="viewActBadges"></div>
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark); margin: 0 0 6px;" id="viewActHeading"></h2>
                </div>

                <div class="pm-detail-grid">
                    <div class="pm-detail-item">
                        <div class="pm-detail-label"><i class="far fa-calendar-alt"></i> Target Deadline</div>
                        <div class="pm-detail-value" id="viewActDeadline">-</div>
                    </div>
                    <div class="pm-detail-item">
                        <div class="pm-detail-label"><i class="fas fa-tags"></i> Kategori</div>
                        <div class="pm-detail-value" id="viewActCategory">-</div>
                    </div>
                    <div class="pm-detail-item">
                        <div class="pm-detail-label"><i class="fas fa-flag"></i> Prioritas</div>
                        <div class="pm-detail-value" id="viewActPrio">-</div>
                    </div>
                    <div class="pm-detail-item">
                        <div class="pm-detail-label"><i class="fas fa-tasks"></i> Status Pengerjaan</div>
                        <div class="pm-detail-value" id="viewActStatus">-</div>
                    </div>
                </div>

                <div class="pm-form-group">
                    <label class="pm-label">Deskripsi Lengkap</label>
                    <div id="viewActDesc" style="font-size: 0.88rem; color: #475569; background: #faf8f5; border: 1px solid var(--border-color); border-radius: 10px; padding: 14px; min-height: 80px; line-height: 1.6; white-space: pre-wrap;">-</div>
                </div>
            </div>
            <div class="pm-modal-footer">
                <button type="button" onclick="closeViewActModal()" class="btn btn-secondary">Tutup</button>
                <button type="button" id="btnEditFromDetail" class="btn btn-primary"><i class="fas fa-edit"></i> Edit Data</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const user = Store.getCurrentUser();
            if (user) {
                const name = user.name || user.username;
                document.getElementById('sidebar-user-name').textContent = name;
                document.getElementById('sidebar-user-role').textContent = user.jurusan || 'Mahasiswa';
                document.getElementById('sidebar-avatar').textContent = name.charAt(0).toUpperCase();
            }

            populateCategoryDropdown();
            renderAktivitas();

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'create') {
                openActModal();
            }

            const highlightId = urlParams.get('highlight') || urlParams.get('id');
            if (highlightId) {
                setTimeout(() => {
                    const targetRow = document.getElementById(`act-row-${highlightId}`);
                    if (targetRow) {
                        targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        targetRow.classList.add('highlight-row');
                        setTimeout(() => targetRow.classList.remove('highlight-row'), 3000);
                    }
                }, 150);
            }
        });

        function populateCategoryDropdown() {
            const categories = Store.getCategories();
            const select = document.getElementById('kategoriId');
            select.innerHTML = '<option value="">-- Pilih Kategori --</option>';
            categories.forEach(c => {
                select.innerHTML += `<option value="${c.id}">${escapeHtml(c.name)}</option>`;
            });
        }

        function renderAktivitas() {
            const activities = Store.getActivities();
            const categories = Store.getCategories();
            const tbody = document.getElementById('tbl-aktivitas-body');
            tbody.innerHTML = '';

            if (activities.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:35px; color:var(--text-muted);">Belum ada aktivitas. Klik "Tambah Aktivitas Baru".</td></tr>`;
                return;
            }

            activities.forEach((act, idx) => {
                const cat = categories.find(c => Number(c.id) === Number(act.kategoriId)) || { name: 'Umum', color: '#64748b' };
                const deadlineDate = new Date(act.deadline);
                const dateFormatted = deadlineDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

                let prioBadge = '<span class="badge badge-medium">Sedang</span>';
                if (act.prioritas === 'Tinggi') prioBadge = '<span class="badge badge-high">Tinggi</span>';
                if (act.prioritas === 'Rendah') prioBadge = '<span class="badge badge-low">Rendah</span>';

                let statusBadge = '<span class="badge badge-todo">Belum Dimulai</span>';
                if (act.status === 'Sedang Dikerjakan') statusBadge = '<span class="badge badge-wip">Sedang Dikerjakan</span>';
                if (act.status === 'Selesai') statusBadge = '<span class="badge badge-done">Selesai</span>';

                const isDone = (act.status === 'Selesai');
                const doneBtnStyle = isDone 
                    ? 'color: #10b981; background: #dcfce7; border-color: #86efac;' 
                    : 'color: #10b981;';

                const tr = document.createElement('tr');
                tr.id = `act-row-${act.id}`;
                tr.innerHTML = `
                    <td>${idx + 1}</td>
                    <td>
                        <a href="javascript:void(0)" onclick="openViewActModal(${act.id})" style="text-decoration:none; color:inherit;">
                            <strong style="color:var(--text-dark);">${escapeHtml(act.judul)}</strong>
                        </a>
                        ${act.deskripsi ? `<div style="font-size:0.78rem; color:var(--text-muted); max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${escapeHtml(act.deskripsi)}</div>` : ''}
                    </td>
                    <td><span class="cat-pill" style="background:${cat.color}15; color:${cat.color}; border:1px solid ${cat.color}40;">${escapeHtml(cat.name)}</span></td>
                    <td style="font-size:0.82rem; font-weight:600;">${dateFormatted}</td>
                    <td>${prioBadge}</td>
                    <td>${statusBadge}</td>
                    <td style="text-align: right;">
                        <div style="display:flex; gap:6px; justify-content:flex-end;">
                            <button onclick="openViewActModal(${act.id})" class="btn btn-secondary btn-icon btn-sm" title="Lihat Rincian"><i class="fas fa-eye"></i></button>
                            <a href="goals.php?action=create&activity_id=${act.id}" class="btn btn-secondary btn-icon btn-sm" title="Buat Target / Goals dari Aktivitas ini" style="color:#2563eb;"><i class="fas fa-bullseye"></i></a>
                            <button onclick="markDone(${act.id})" class="btn btn-secondary btn-icon btn-sm" style="${doneBtnStyle}" title="${isDone ? 'Aktivitas Selesai' : 'Tandai Sudah Selesai'}"><i class="fas fa-check-circle"></i></button>
                            <button onclick="editAct(${act.id})" class="btn btn-secondary btn-icon btn-sm" title="Edit"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteAct(${act.id})" class="btn btn-secondary btn-icon btn-sm" style="color:#ef4444;" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function markDone(id) {
            const act = Store.getActivity(id);
            if (!act) return;
            if (act.status === 'Selesai') {
                return;
            }
            Store.showConfirm({
                title: 'Selesaikan Aktivitas?',
                message: `Tandai tugas "${escapeHtml(act.judul)}" sebagai telah selesai dikerjakan?`,
                icon: 'fas fa-check-circle',
                confirmText: 'Ya, Selesai',
                cancelText: 'Batal',
                isDanger: false,
                onConfirm: () => {
                    Store.updateActivity(id, { status: 'Selesai' });
                    renderAktivitas();
                }
            });
        }

        function openActModal(actId = null) {
            document.getElementById('actForm').reset();
            document.getElementById('actId').value = '';

            if (actId) {
                const act = Store.getActivity(actId);
                if (act) {
                    document.getElementById('actModalTitle').innerHTML = '<i class="fas fa-edit" style="color: var(--primary-accent-hover);"></i> Edit Aktivitas';
                    document.getElementById('actId').value = act.id;
                    document.getElementById('judul').value = act.judul;
                    document.getElementById('kategoriId').value = act.kategoriId;
                    document.getElementById('status').value = act.status;
                    document.getElementById('prioritas').value = act.prioritas || 'Sedang';
                    document.getElementById('deskripsi').value = act.deskripsi || '';

                    if (act.deadline) {
                        document.getElementById('deadline_date').value = act.deadline.slice(0, 10);
                        document.getElementById('deadline_time').value = act.deadline.slice(11, 16) || '23:59';
                    }
                    document.getElementById('btnActSubmit').innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
                }
            } else {
                document.getElementById('actModalTitle').innerHTML = '<i class="fas fa-plus-circle" style="color: var(--primary-accent-hover);"></i> Tambah Aktivitas Baru';
                const tomorrow = new Date(Date.now() + 24 * 60 * 60 * 1000);
                document.getElementById('deadline_date').value = tomorrow.toISOString().slice(0, 10);
                document.getElementById('deadline_time').value = '23:59';
                document.getElementById('btnActSubmit').innerHTML = '<i class="fas fa-save"></i> Simpan Data';
            }
            document.getElementById('actModal').style.display = 'flex';
        }

        function closeActModal() { document.getElementById('actModal').style.display = 'none'; }
        
        function openViewActModal(actId) {
            const act = Store.getActivity(actId);
            if (!act) return;
            const categories = Store.getCategories();
            const cat = categories.find(c => Number(c.id) === Number(act.kategoriId)) || { name: 'Umum', color: '#64748b' };
            const dl = new Date(act.deadline);

            document.getElementById('viewActHeading').textContent = act.judul;
            document.getElementById('viewActCategory').innerHTML = `<span class="cat-pill" style="background:${cat.color}15; color:${cat.color}; border:1px solid ${cat.color}40;">${escapeHtml(cat.name)}</span>`;
            document.getElementById('viewActDeadline').textContent = dl.toLocaleDateString('id-ID', {
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
            });

            let prioBadge = '<span class="badge badge-medium">Sedang</span>';
            if (act.prioritas === 'Tinggi') prioBadge = '<span class="badge badge-high">Tinggi</span>';
            if (act.prioritas === 'Rendah') prioBadge = '<span class="badge badge-low">Rendah</span>';

            let statusBadge = '<span class="badge badge-todo">Belum Dimulai</span>';
            if (act.status === 'Sedang Dikerjakan') statusBadge = '<span class="badge badge-wip">Sedang Dikerjakan</span>';
            if (act.status === 'Selesai') statusBadge = '<span class="badge badge-done">Selesai</span>';

            document.getElementById('viewActBadges').innerHTML = `${statusBadge} ${prioBadge}`;
            document.getElementById('viewActPrio').textContent = act.prioritas || 'Sedang';
            document.getElementById('viewActStatus').textContent = act.status;
            document.getElementById('viewActDesc').textContent = act.deskripsi || 'Tidak ada catatan khusus.';

            document.getElementById('btnEditFromDetail').onclick = () => {
                closeViewActModal();
                openActModal(act.id);
            };

            document.getElementById('viewActModal').style.display = 'flex';
        }

        function closeViewActModal() {
            document.getElementById('viewActModal').style.display = 'none';
        }

        function editAct(id) { openActModal(id); }
        
        function deleteAct(id) {
            const act = Store.getActivity(id);
            const title = act ? act.judul : 'aktivitas ini';
            Store.showConfirm({
                title: 'Hapus Aktivitas?',
                message: `Apakah Anda yakin ingin menghapus "${escapeHtml(title)}"? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'fas fa-trash-alt',
                confirmText: 'Ya, Hapus',
                cancelText: 'Batal',
                isDanger: true,
                onConfirm: () => {
                    Store.deleteActivity(id);
                    renderAktivitas();
                }
            });
        }

        document.getElementById('actForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const id = document.getElementById('actId').value;
            const dateVal = document.getElementById('deadline_date').value;
            const timeVal = document.getElementById('deadline_time').value || '23:59';
            const combinedDeadline = `${dateVal}T${timeVal}`;

            const data = {
                judul: document.getElementById('judul').value.trim(),
                kategoriId: document.getElementById('kategoriId').value,
                deadline: combinedDeadline,
                prioritas: document.getElementById('prioritas').value,
                status: document.getElementById('status').value,
                deskripsi: document.getElementById('deskripsi').value.trim()
            };

            if (id) Store.updateActivity(id, data);
            else Store.addActivity(data);

            closeActModal();
            renderAktivitas();
        });

        // Overlay click to close
        ['actModal', 'viewActModal'].forEach(mId => {
            const el = document.getElementById(mId);
            if (el) {
                el.addEventListener('click', (e) => {
                    if (e.target === el) {
                        el.style.display = 'none';
                    }
                });
            }
        });

        function escapeHtml(str) {
            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
        }
    </script>
</body>
</html>
