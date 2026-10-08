<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanMate - Kategori &amp; Status</title>
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
                <div class="breadcrumb-label">PLANMATE / Kategori &amp; Status</div>
            </div>

            <div style="display: grid; grid-template-columns: 350px 1fr; gap: 24px;">
                <div class="card-section" style="height: fit-content;">
                    <div class="card-header-flex">
                        <h3 style="font-size: 1.1rem; font-weight: 800;" id="cat-form-title"><i class="fas fa-plus-circle"></i> Tambah Kategori</h3>
                        <button id="btn-cancel-edit" onclick="resetCatForm()" style="display: none;" class="btn btn-secondary btn-sm">Batal</button>
                    </div>
                    <form id="catForm">
                        <input type="hidden" id="catId">
                        <div class="form-group">
                            <label for="catName">Nama Kategori <span style="color: red;">*</span></label>
                            <input type="text" id="catName" class="form-control" required placeholder="Contoh: Praktikum, UKM">
                        </div>
                        <div class="form-group">
                            <label for="catColor">Warna Badge Identitas</label>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="color" id="catColor" value="#4f46e5" style="width: 50px; height: 38px; border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer;">
                                <span style="font-size: 0.8rem; color: var(--text-muted);">Pilih warna identitas visual</span>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="catDesc">Deskripsi Kategori</label>
                            <textarea id="catDesc" class="form-control" rows="3" placeholder="Keterangan singkat kategori..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-accent" style="width: 100%;">
                            <i class="fas fa-save"></i> <span id="cat-btn-text">Simpan Kategori</span>
                        </button>
                    </form>
                </div>

                <div class="card-section">
                    <div class="card-header-flex">
                        <div>
                            <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-dark);">Daftar Kategori Aktivitas</h2>
                            <p style="font-size: 0.85rem; color: var(--text-muted);">Kelompokkan aktivitas berdasarkan kategori dan pantau perkembangannya.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Total Tugas</th>
                                    <th style="text-align: right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cat-table-body"></tbody>
                        </table>
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
            }

            renderCategoryTable();
        });

        function renderCategoryTable() {
            const categories = Store.getCategories();
            const activities = Store.getActivities();
            const tbody = document.getElementById('cat-table-body');
            tbody.innerHTML = '';

            categories.forEach((c, index) => {
                const count = activities.filter(a => Number(a.kategoriId) === Number(c.id)).length;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>
                        <span class="cat-pill" style="background:${c.color}15; color:${c.color}; border:1px solid ${c.color}40; padding: 6px 12px;">
                            ${escapeHtml(c.name)}
                        </span>
                    </td>
                    <td style="color: var(--text-muted); font-size: 0.85rem;">${escapeHtml(c.description || '-')}</td>
                    <td><span class="badge badge-secondary">${count} Tugas</span></td>
                    <td style="text-align: right;">
                        <div style="display:flex; gap:6px; justify-content:flex-end;">
                            <button onclick="editCat(${c.id})" class="btn btn-secondary btn-icon btn-sm" title="Edit"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteCat(${c.id})" class="btn btn-secondary btn-icon btn-sm" style="color: #ef4444;" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function editCat(id) {
            const c = Store.getCategories().find(item => Number(item.id) === Number(id));
            if (c) {
                document.getElementById('catId').value = c.id;
                document.getElementById('catName').value = c.name;
                document.getElementById('catColor').value = c.color || '#4f46e5';
                document.getElementById('catDesc').value = c.description || '';

                document.getElementById('cat-form-title').innerHTML = '<i class="fas fa-edit"></i> Edit Kategori';
                document.getElementById('cat-btn-text').textContent = 'Simpan Perubahan';
                document.getElementById('btn-cancel-edit').style.display = 'inline-block';
            }
        }

        function resetCatForm() {
            document.getElementById('catForm').reset();
            document.getElementById('catId').value = '';
            document.getElementById('cat-form-title').innerHTML = '<i class="fas fa-plus-circle"></i> Tambah Kategori Baru';
            document.getElementById('cat-btn-text').textContent = 'Simpan Kategori';
            document.getElementById('btn-cancel-edit').style.display = 'none';
        }

        function deleteCat(id) {
            if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
                Store.deleteCategory(id);
                resetCatForm();
                renderCategoryTable();
            }
        }

        document.getElementById('catForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const id = document.getElementById('catId').value;
            const data = {
                name: document.getElementById('catName').value.trim(),
                color: document.getElementById('catColor').value,
                description: document.getElementById('catDesc').value.trim()
            };

            if (id) Store.updateCategory(id, data);
            else Store.addCategory(data);

            resetCatForm();
            renderCategoryTable();
        });

        function escapeHtml(str) {
            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
</body>
</html>
