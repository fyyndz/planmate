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
        <?php include_once __DIR__ . '/includes/sidebar.php'; ?>

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
    <div id="actModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 200; align-items: center; justify-content: center; padding: 20px;">
        <div class="card-section" style="width: 100%; max-width: 580px; margin: 0; box-shadow: 0 20px 40px rgba(0,0,0,0.25);">
            <div class="card-header-flex">
                <h3 style="font-size: 1.1rem; font-weight: 800;" id="actModalTitle"><i class="fas fa-plus-circle"></i> Tambah Aktivitas</h3>
                <button onclick="closeActModal()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-muted);">&times;</button>
            </div>
            <form id="actForm">
                <input type="hidden" id="actId">
                <div class="form-group">
                    <label for="judul">Judul Kegiatan <span style="color: red;">*</span></label>
                    <input type="text" id="judul" class="form-control" required placeholder="Contoh: Laporan Praktikum PAP Modul 4">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label for="kategoriId">Kategori <span style="color: red;">*</span></label>
                        <select id="kategoriId" class="form-select" required></select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status Pengerjaan</label>
                        <select id="status" class="form-select">
                            <option value="Belum Dimulai">Belum Dimulai</option>
                            <option value="Sedang Dikerjakan">Sedang Dikerjakan</option>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label for="deadline_date"><i class="far fa-calendar-alt"></i> Tanggal Deadline <span style="color: red;">*</span></label>
                        <input type="date" id="deadline_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="deadline_time"><i class="far fa-clock"></i> Jam Deadline <span style="color: red;">*</span></label>
                        <input type="time" id="deadline_time" class="form-control" required value="23:59">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="deskripsi">Deskripsi &amp; Catatan</label>
                    <textarea id="deskripsi" class="form-control" rows="3" placeholder="Rincian atau instruksi tugas..."></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeActModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-accent"><i class="fas fa-save"></i> Simpan Data</button>
                </div>
            </form>
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
                        <strong>${escapeHtml(act.judul)}</strong>
                        ${act.deskripsi ? `<div style="font-size:0.78rem; color:var(--text-muted); max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${escapeHtml(act.deskripsi)}</div>` : ''}
                    </td>
                    <td><span class="cat-pill" style="background:${cat.color}15; color:${cat.color}; border:1px solid ${cat.color}40;">${escapeHtml(cat.name)}</span></td>
                    <td style="font-size:0.82rem; font-weight:600;">${dateFormatted}</td>
                    <td>${prioBadge}</td>
                    <td>${statusBadge}</td>
                    <td style="text-align: right;">
                        <div style="display:flex; gap:6px; justify-content:flex-end;">
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
                alert(`Tugas "${act.judul}" sudah dalam status Selesai.`);
                return;
            }
            if (confirm(`Apakah Anda yakin tugas "${act.judul}" sudah selesai dikerjakan?`)) {
                Store.updateActivity(id, { status: 'Selesai' });
                renderAktivitas();
            }
        }

        function openActModal(actId = null) {
            document.getElementById('actForm').reset();
            document.getElementById('actId').value = '';

            if (actId) {
                const act = Store.getActivity(actId);
                if (act) {
                    document.getElementById('actModalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Aktivitas';
                    document.getElementById('actId').value = act.id;
                    document.getElementById('judul').value = act.judul;
                    document.getElementById('kategoriId').value = act.kategoriId;
                    document.getElementById('status').value = act.status;
                    document.getElementById('deskripsi').value = act.deskripsi || '';

                    if (act.deadline) {
                        document.getElementById('deadline_date').value = act.deadline.slice(0, 10);
                        document.getElementById('deadline_time').value = act.deadline.slice(11, 16) || '23:59';
                    }
                }
            } else {
                document.getElementById('actModalTitle').innerHTML = '<i class="fas fa-plus-circle"></i> Tambah Aktivitas Baru';
                const tomorrow = new Date(Date.now() + 24 * 60 * 60 * 1000);
                document.getElementById('deadline_date').value = tomorrow.toISOString().slice(0, 10);
                document.getElementById('deadline_time').value = '23:59';
            }
            document.getElementById('actModal').style.display = 'flex';
        }

        function closeActModal() { document.getElementById('actModal').style.display = 'none'; }
        function editAct(id) { openActModal(id); }
        function deleteAct(id) {
            if (confirm('Hapus aktivitas ini?')) {
                Store.deleteActivity(id);
                renderAktivitas();
            }
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
                status: document.getElementById('status').value,
                deskripsi: document.getElementById('deskripsi').value.trim()
            };

            if (id) Store.updateActivity(id, data);
            else Store.addActivity(data);

            closeActModal();
            renderAktivitas();
        });

        function escapeHtml(str) {
            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
</body>
</html>
