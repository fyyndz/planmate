<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanMate - Pencarian &amp; Filter</title>
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
                <div class="breadcrumb-label">PLANMATE / Pencarian &amp; Filter</div>
            </div>

            <div class="card-section">
                <div class="card-header-flex">
                    <div>
                        <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-dark);">Pencarian &amp; Filter Aktivitas</h2>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Temukan tugas spesifik berdasarkan kata kunci, kategori, status, dan prioritas.</p>
                    </div>
                    <button onclick="resetSearchFilters()" class="btn btn-secondary btn-sm">
                        <i class="fas fa-undo"></i> Reset Filter
                    </button>
                </div>

                <div style="display: grid; grid-template-columns: 2fr repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
                    <div class="form-group" style="margin: 0;">
                        <label for="srcKey">Kata Kunci</label>
                        <input type="text" id="srcKey" class="form-control" placeholder="Cari judul/deskripsi..." oninput="doSearch()">
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label for="srcCat">Kategori</label>
                        <select id="srcCat" class="form-select" onchange="doSearch()"></select>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label for="srcStat">Status</label>
                        <select id="srcStat" class="form-select" onchange="doSearch()">
                            <option value="">Semua Status</option>
                            <option value="Belum Dimulai">Belum Dimulai</option>
                            <option value="Sedang Dikerjakan">Sedang Dikerjakan</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label for="srcPrio">Prioritas</label>
                        <select id="srcPrio" class="form-select" onchange="doSearch()">
                            <option value="">Semua Prioritas</option>
                            <option value="Tinggi">Tinggi</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Rendah">Rendah</option>
                        </select>
                    </div>
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
                        <tbody id="tbl-search-body"></tbody>
                    </table>
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
            }

            populateCategoryFilter();
            doSearch();
        });

        function populateCategoryFilter() {
            const categories = Store.getCategories();
            const select = document.getElementById('srcCat');
            select.innerHTML = '<option value="">Semua Kategori</option>';
            categories.forEach(c => {
                select.innerHTML += `<option value="${c.id}">${escapeHtml(c.name)}</option>`;
            });
        }

        function resetSearchFilters() {
            document.getElementById('srcKey').value = '';
            document.getElementById('srcCat').value = '';
            document.getElementById('srcStat').value = '';
            document.getElementById('srcPrio').value = '';
            doSearch();
        }

        function doSearch() {
            const keyword = document.getElementById('srcKey').value.trim().toLowerCase();
            const catId = document.getElementById('srcCat').value;
            const status = document.getElementById('srcStat').value;
            const priority = document.getElementById('srcPrio').value;

            const activities = Store.getActivities();
            const categories = Store.getCategories();

            const filtered = activities.filter(act => {
                const matchKey = !keyword || act.judul.toLowerCase().includes(keyword) || (act.deskripsi && act.deskripsi.toLowerCase().includes(keyword));
                const matchCat = !catId || Number(act.kategoriId) === Number(catId);
                const matchStat = !status || act.status === status;
                const matchPrio = !priority || act.prioritas === priority;
                return matchKey && matchCat && matchStat && matchPrio;
            });

            const tbody = document.getElementById('tbl-search-body');
            tbody.innerHTML = '';

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:35px; color:var(--text-muted);">Tidak ada aktivitas yang sesuai dengan kriteria pencarian Anda.</td></tr>`;
                return;
            }

            filtered.forEach((act, idx) => {
                const cat = categories.find(c => Number(c.id) === Number(act.kategoriId)) || { name: 'Umum', color: '#64748b' };
                const deadlineDate = new Date(act.deadline);
                const dateFormatted = deadlineDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

                let prioBadge = '<span class="badge badge-medium">Sedang</span>';
                if (act.prioritas === 'Tinggi') prioBadge = '<span class="badge badge-high">Tinggi</span>';
                if (act.prioritas === 'Rendah') prioBadge = '<span class="badge badge-low">Rendah</span>';

                let statusBadge = '<span class="badge badge-todo">Belum Dimulai</span>';
                if (act.status === 'Sedang Dikerjakan') statusBadge = '<span class="badge badge-wip">Sedang Dikerjakan</span>';
                if (act.status === 'Selesai') statusBadge = '<span class="badge badge-done">Selesai</span>';

                const tr = document.createElement('tr');
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
                        <a href="aktivitas.php" class="btn btn-secondary btn-sm"><i class="fas fa-external-link-alt"></i> Kelola</a>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function escapeHtml(str) {
            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
</body>
</html>
