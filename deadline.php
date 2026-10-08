<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanMate - Deadline &amp; Prioritas</title>
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
                <div class="breadcrumb-label">PLANMATE / Deadline &amp; Prioritas</div>
            </div>

            <div class="card-section">
                <div class="card-header-flex">
                    <div>
                        <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-dark);">Deadline &amp; Prioritas Aktivitas</h2>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Pantau tanggal tenggat dan tingkat prioritas tugas yang terhitung otomatis berdasarkan deadline.</p>
                    </div>
                </div>

                <!-- Filter Buttons Inside Card Section (Matching pencarian.php layout) -->
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; width: 100%;">
                    <button onclick="filterDeadline('all')" class="btn btn-secondary" id="flt-all" style="text-align: center; justify-content: center;">Semua Deadline</button>
                    <button onclick="filterDeadline('urgent')" class="btn btn-secondary" id="flt-urgent" style="text-align: center; justify-content: center;">Urgent (&lt; 24 Jam)</button>
                    <button onclick="filterDeadline('high')" class="btn btn-secondary" id="flt-high" style="text-align: center; justify-content: center;">Prioritas Tinggi</button>
                    <button onclick="filterDeadline('overdue')" class="btn btn-secondary" id="flt-overdue" style="color: #ef4444; text-align: center; justify-content: center;">Terlambat</button>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Judul Aktivitas</th>
                                <th>Kategori</th>
                                <th>Target Deadline</th>
                                <th>Status Urgency</th>
                                <th>Tingkat Prioritas</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbl-deadline-body"></tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        let currentFilter = 'all';

        document.addEventListener('DOMContentLoaded', () => {
            const user = Store.getCurrentUser();
            if (user) {
                const name = user.name || user.username;
                document.getElementById('sidebar-user-name').textContent = name;
                document.getElementById('sidebar-user-role').textContent = user.jurusan || 'Mahasiswa';
                document.getElementById('sidebar-avatar').textContent = name.charAt(0).toUpperCase();
            }

            renderDeadlineTable();
        });

        function filterDeadline(type) {
            currentFilter = type;
            renderDeadlineTable();
        }

        function renderDeadlineTable() {
            const activities = Store.getActivities();
            const categories = Store.getCategories();
            const now = new Date();
            const limit24h = new Date(now.getTime() + 24 * 60 * 60 * 1000);

            let filtered = [...activities];
            if (currentFilter === 'urgent') filtered = filtered.filter(a => a.status !== 'Selesai' && new Date(a.deadline) <= limit24h);
            if (currentFilter === 'high') filtered = filtered.filter(a => a.prioritas === 'Tinggi');
            if (currentFilter === 'overdue') filtered = filtered.filter(a => a.status !== 'Selesai' && new Date(a.deadline) < now);

            filtered.sort((a, b) => new Date(a.deadline) - new Date(b.deadline));
            const tbody = document.getElementById('tbl-deadline-body');
            tbody.innerHTML = '';

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-muted);">Tidak ada aktivitas yang sesuai dengan filter ini.</td></tr>`;
                return;
            }

            filtered.forEach(act => {
                const cat = categories.find(c => Number(c.id) === Number(act.kategoriId)) || { name: 'Umum', color: '#64748b' };
                const deadlineDate = new Date(act.deadline);
                const dateFormatted = deadlineDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

                const diffMs = deadlineDate - now;
                let urgencyTag = '<span class="badge" style="background:#f1f5f9; color:#64748b;">Aman</span>';
                if (act.status === 'Selesai') urgencyTag = '<span class="badge badge-done">Tuntas</span>';
                else if (diffMs < 0) urgencyTag = '<span class="badge" style="background:#fee2e2; color:#dc2626;">Terlambat</span>';
                else if (diffMs <= 24 * 3600 * 1000) urgencyTag = '<span class="badge" style="background:#fef3c7; color:#d97706;">Urgent</span>';

                let prioBadge = '<span class="badge badge-medium">Sedang</span>';
                if (act.prioritas === 'Tinggi') prioBadge = '<span class="badge badge-high">Tinggi</span>';
                if (act.prioritas === 'Rendah') prioBadge = '<span class="badge badge-low">Rendah</span>';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${escapeHtml(act.judul)}</strong></td>
                    <td><span class="cat-pill" style="background:${cat.color}15; color:${cat.color}; border:1px solid ${cat.color}40;">${escapeHtml(cat.name)}</span></td>
                    <td style="font-size:0.85rem; font-weight:600;">${dateFormatted}</td>
                    <td>${urgencyTag}</td>
                    <td>${prioBadge}</td>
                    <td style="text-align: right;">
                        <a href="aktivitas.php" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i> Kelola Tugas</a>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function changePrio(id, newPrio) {
            const act = Store.getActivity(id);
            if (act) {
                act.prioritas = newPrio;
                Store.updateActivity(id, act);
                renderDeadlineTable();
            }
        }

        function escapeHtml(str) {
            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
</body>
</html>
