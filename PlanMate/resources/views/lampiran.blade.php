<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PlanMate - Lampiran Aktivitas</title>
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
            <div class="top-header">
                <div class="breadcrumb-label">PLANMATE / Lampiran Berkas</div>
            </div>

            <div class="card-section">
                <div class="card-header-flex" style="flex-wrap: wrap; gap: 14px;">
                    <div>
                        <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-dark);">Manajemen Lampiran &amp; Berkas Tugas</h2>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Unggah, tinjau, perbarui, dan kelola dokumen pendukung yang tertaut pada aktivitas kuliah.</p>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <select id="filterAktivitas" class="form-select" style="height: 40px; min-width: 210px; font-size: 0.85rem;" onchange="renderLampiranTable()">
                            <option value="">Semua Aktivitas</option>
                        </select>
                        <button onclick="openLampiranModal()" class="btn btn-primary">
                            <i class="fas fa-cloud-upload-alt"></i> Unggah Lampiran
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Judul &amp; Nama Berkas</th>
                                <th>Aktivitas Terkait</th>
                                <th>Format / Ukuran</th>
                                <th>Tanggal Unggah</th>
                                <th style="text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbl-lampiran-body">
                            <tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-muted);">Memuat data lampiran...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Create / Update Lampiran -->
    <div id="lampiranModal" class="pm-modal-overlay">
        <div class="pm-modal-card pm-modal-md" role="dialog" aria-modal="true" aria-labelledby="lampiranModalTitle">
            <div class="pm-modal-header">
                <div class="pm-modal-title-group">
                    <h3 class="pm-modal-title" id="lampiranModalTitle">
                        <i class="fas fa-paperclip" style="color: var(--primary-accent-hover);"></i> Unggah Lampiran Baru
                    </h3>
                    <p class="pm-modal-subtitle">Tautkan dokumen, gambar, atau arsip tugas ke dalam aktivitas.</p>
                </div>
                <button type="button" onclick="closeLampiranModal()" class="pm-modal-close" aria-label="Tutup">&times;</button>
            </div>
            <form id="lampiranForm" enctype="multipart/form-data">
                <div class="pm-modal-body">
                    <input type="hidden" id="lampiranId">

                    <div class="pm-form-group">
                        <label for="judulLampiran" class="pm-label">Judul Lampiran <span class="required">*</span></label>
                        <input type="text" id="judulLampiran" class="pm-input" required placeholder="Contoh: Modul Praktikum Bab 4 / Draft Laporan">
                    </div>

                    <div class="pm-form-group">
                        <label for="lampiranAktivitasId" class="pm-label">Tautkan ke Aktivitas</label>
                        <select id="lampiranAktivitasId" class="pm-select">
                            <option value="">-- Lampiran Umum (Tanpa Aktivitas Spesifik) --</option>
                        </select>
                    </div>

                    <div class="pm-form-group">
                        <label for="lampiranFile" class="pm-label">
                            Pilih Berkas <span class="required" id="fileRequiredMark">*</span>
                        </label>
                        <input type="file" id="lampiranFile" class="pm-input" style="padding: 9px 12px;" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip,.rar,.txt">
                        <span id="fileHelpText" style="font-size: 0.76rem; color: var(--text-muted); display: block; margin-top: 5px;">
                            Format: PDF, DOCX, XLSX, PPTX, JPG, PNG, ZIP, RAR, TXT (Maks. 10 MB).
                        </span>
                    </div>

                    <div class="pm-form-group" style="margin-bottom: 0;">
                        <label for="lampiranKeterangan" class="pm-label">Keterangan Tambahan</label>
                        <textarea id="lampiranKeterangan" class="pm-textarea" rows="2" placeholder="Catatan singkat mengenai isi berkas..."></textarea>
                    </div>
                </div>
                <div class="pm-modal-footer">
                    <button type="button" onclick="closeLampiranModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnLampiranSubmit"><i class="fas fa-save"></i> Simpan Lampiran</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Preview / Detail Lampiran -->
    <div id="viewLampiranModal" class="pm-modal-overlay">
        <div class="pm-modal-card pm-modal-md" role="dialog" aria-modal="true">
            <div class="pm-modal-header">
                <div class="pm-modal-title-group">
                    <h3 class="pm-modal-title"><i class="far fa-file-alt" style="color: var(--primary-accent-hover);"></i> Detail Lampiran</h3>
                    <p class="pm-modal-subtitle">Informasi metadata berkas dan pratinjau dokumen.</p>
                </div>
                <button type="button" onclick="closeViewLampiranModal()" class="pm-modal-close">&times;</button>
            </div>
            <div class="pm-modal-body" id="viewLampiranBody"></div>
            <div class="pm-modal-footer">
                <button type="button" onclick="closeViewLampiranModal()" class="btn btn-secondary">Tutup</button>
                <a href="#" id="btnDownloadLampiranDetail" class="btn btn-primary"><i class="fas fa-download"></i> Unduh Berkas</a>
            </div>
        </div>
    </div>

    <script>
        const LAMPIRAN_CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let lampiranList = [];
        let aktivitasList = [];

        document.addEventListener('DOMContentLoaded', async () => {
            await loadAktivitasOptions();

            const params = new URLSearchParams(window.location.search);
            const preActId = params.get('activity_id');
            if (preActId) {
                document.getElementById('filterAktivitas').value = preActId;
            }

            await loadLampiranData();

            if (params.get('action') === 'create') {
                openLampiranModal(null, preActId);
            }
        });

        async function loadAktivitasOptions() {
            try {
                const res = await fetch('/aktivitas/data', { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    const data = await res.json();
                    aktivitasList = Array.isArray(data) ? data : (data.data || []);
                }
            } catch (e) {
                console.error('Gagal memuat daftar aktivitas:', e);
            }
            populateAktivitasSelects();
        }

        function populateAktivitasSelects() {
            const filterSel = document.getElementById('filterAktivitas');
            const modalSel = document.getElementById('lampiranAktivitasId');
            const currentFilter = filterSel.value;

            filterSel.innerHTML = '<option value="">Semua Aktivitas</option>';
            modalSel.innerHTML = '<option value="">-- Lampiran Umum (Tanpa Aktivitas) --</option>';

            aktivitasList.forEach(act => {
                const title = act.judul || act.title || act.nama_aktivitas || `Aktivitas #${act.id}`;
                const opt = `<option value="${act.id}">${escapeHtml(title)}</option>`;
                filterSel.innerHTML += opt;
                modalSel.innerHTML += opt;
            });

            if (currentFilter) filterSel.value = currentFilter;
        }

        async function loadLampiranData() {
            try {
                const res = await fetch('/lampiran/data', { headers: { 'Accept': 'application/json' } });
                if (res.status === 401) { window.location.href = '/login'; return; }
                lampiranList = await res.json();
                renderLampiranTable();
            } catch (e) {
                console.error(e);
            }
        }

        function formatBytes(bytes) {
            if (!bytes || bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        function getFileIcon(ext) {
            const e = (ext || '').toUpperCase();
            if (e === 'PDF') return 'fa-file-pdf text-danger';
            if (['DOC', 'DOCX'].includes(e)) return 'fa-file-word';
            if (['XLS', 'XLSX'].includes(e)) return 'fa-file-excel';
            if (['PPT', 'PPTX'].includes(e)) return 'fa-file-powerpoint';
            if (['JPG', 'JPEG', 'PNG'].includes(e)) return 'fa-file-image';
            if (['ZIP', 'RAR'].includes(e)) return 'fa-file-archive';
            return 'fa-file-alt';
        }

        function resolveAktivitasTitle(item) {
            if (item.aktivitasJudul) return item.aktivitasJudul;
            const act = aktivitasList.find(a => Number(a.id) === Number(item.aktivitasId));
            return act ? (act.judul || act.title || act.nama_aktivitas) : null;
        }

        function renderLampiranTable() {
            const tbody = document.getElementById('tbl-lampiran-body');
            const filterActId = document.getElementById('filterAktivitas').value;

            const filtered = filterActId
                ? lampiranList.filter(item => Number(item.aktivitasId) === Number(filterActId))
                : lampiranList;

            tbody.innerHTML = '';

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:35px; color:var(--text-muted);">Belum ada berkas lampiran. Klik <strong>"Unggah Lampiran"</strong> untuk menambahkan dokumen.</td></tr>`;
                return;
            }

            filtered.forEach((item, idx) => {
                const actTitle = resolveAktivitasTitle(item);
                const actBadge = actTitle
                    ? `<a href="/aktivitas?highlight=${item.aktivitasId}" style="text-decoration:none; font-size:0.78rem; font-weight:700; background:#eff6ff; color:#2563eb; padding:4px 10px; border-radius:6px; border:1px solid #bfdbfe; display:inline-block;"><i class="fas fa-link"></i> ${escapeHtml(actTitle)}</a>`
                    : `<span class="badge badge-secondary">Lampiran Umum</span>`;

                const tgl = item.createdAt
                    ? new Date(item.createdAt).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
                    : '-';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${idx + 1}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:36px; height:36px; border-radius:8px; background:#f1f5f9; color:#334155; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0;">
                                <i class="fas ${getFileIcon(item.ekstensi)}"></i>
                            </div>
                            <div>
                                <strong style="color:var(--text-dark); display:block;">${escapeHtml(item.judul)}</strong>
                                <span style="font-size:0.78rem; color:var(--text-muted);">${escapeHtml(item.namaFileAsli)}</span>
                            </div>
                        </div>
                    </td>
                    <td>${actBadge}</td>
                    <td>
                        <span class="badge badge-secondary" style="font-weight:700;">${escapeHtml(item.ekstensi)}</span>
                        <span style="font-size:0.78rem; color:var(--text-muted); margin-left:4px;">${formatBytes(item.ukuranFile)}</span>
                    </td>
                    <td style="font-size:0.82rem; color:var(--text-muted);">${tgl}</td>
                    <td style="text-align:right;">
                        <div style="display:flex; gap:6px; justify-content:flex-end;">
                            <button onclick="viewLampiran(${item.id})" class="btn btn-secondary btn-icon btn-sm" title="Lihat Detail"><i class="fas fa-eye"></i></button>
                            <a href="/lampiran/${item.id}/download" class="btn btn-secondary btn-icon btn-sm" style="color:#2563eb;" title="Unduh Berkas"><i class="fas fa-download"></i></a>
                            <button onclick="openLampiranModal(${item.id})" class="btn btn-secondary btn-icon btn-sm" title="Ubah / Ganti File"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteLampiran(${item.id})" class="btn btn-secondary btn-icon btn-sm" style="color:#ef4444;" title="Hapus Lampiran"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function openLampiranModal(id = null, preActId = null) {
            document.getElementById('lampiranForm').reset();
            document.getElementById('lampiranId').value = '';
            populateAktivitasSelects();

            const fileInput = document.getElementById('lampiranFile');
            const reqMark = document.getElementById('fileRequiredMark');
            const helpText = document.getElementById('fileHelpText');

            if (id) {
                const item = lampiranList.find(x => Number(x.id) === Number(id));
                if (item) {
                    document.getElementById('lampiranModalTitle').innerHTML = '<i class="fas fa-edit" style="color: var(--primary-accent-hover);"></i> Edit / Ganti Lampiran';
                    document.getElementById('lampiranId').value = item.id;
                    document.getElementById('judulLampiran').value = item.judul;
                    document.getElementById('lampiranAktivitasId').value = item.aktivitasId || '';
                    document.getElementById('lampiranKeterangan').value = item.keterangan || '';

                    fileInput.required = false;
                    reqMark.style.display = 'none';
                    helpText.textContent = `Berkas saat ini: ${item.namaFileAsli} (Kosongkan jika tidak ingin mengganti berkas).`;
                    document.getElementById('btnLampiranSubmit').innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
                }
            } else {
                document.getElementById('lampiranModalTitle').innerHTML = '<i class="fas fa-cloud-upload-alt" style="color: var(--primary-accent-hover);"></i> Unggah Lampiran Baru';
                if (preActId) document.getElementById('lampiranAktivitasId').value = preActId;

                fileInput.required = true;
                reqMark.style.display = 'inline';
                helpText.textContent = 'Format: PDF, DOCX, XLSX, PPTX, JPG, PNG, ZIP, RAR, TXT (Maks. 10 MB).';
                document.getElementById('btnLampiranSubmit').innerHTML = '<i class="fas fa-save"></i> Simpan Lampiran';
            }

            document.getElementById('lampiranModal').style.display = 'flex';
        }

        function closeLampiranModal() {
            document.getElementById('lampiranModal').style.display = 'none';
        }

        document.getElementById('lampiranForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('lampiranId').value;
            const formData = new FormData();
            formData.append('judul_lampiran', document.getElementById('judulLampiran').value.trim());

            const actId = document.getElementById('lampiranAktivitasId').value;
            formData.append('aktivitas_id', actId || '');

            const ket = document.getElementById('lampiranKeterangan').value.trim();
            formData.append('keterangan', ket || '');

            const fileInput = document.getElementById('lampiranFile');
            if (fileInput.files.length > 0) {
                formData.append('file', fileInput.files[0]);
            }

            let url = '/lampiran';
            if (id) {
                url = `/lampiran/${id}`;
                formData.append('_method', 'PUT');
            }

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': LAMPIRAN_CSRF
                    },
                    body: formData
                });

                if (!res.ok) {
                    const errJson = await res.json();
                    const firstErr = errJson.errors ? Object.values(errJson.errors)[0][0] : (errJson.message || 'Gagal menyimpan lampiran.');
                    alert(firstErr);
                    return;
                }

                closeLampiranModal();
                await loadLampiranData();
            } catch (err) {
                alert('Terjadi kesalahan jaringan saat mengunggah berkas.');
            }
        });

        function viewLampiran(id) {
            const item = lampiranList.find(x => Number(x.id) === Number(id));
            if (!item) return;

            const actTitle = resolveAktivitasTitle(item);
            const isImage = ['JPG', 'JPEG', 'PNG'].includes(item.ekstensi);
            const isPdf = item.ekstensi === 'PDF';

            let previewHtml = '';
            if (isImage) {
                previewHtml = `<div style="margin-top:14px; text-align:center;"><img src="${item.fileUrl}" alt="${escapeHtml(item.judul)}" style="max-width:100%; max-height:280px; border-radius:10px; border:1px solid var(--border-color);"></div>`;
            } else if (isPdf) {
                previewHtml = `<div style="margin-top:14px;"><iframe src="${item.fileUrl}" style="width:100%; height:280px; border:1px solid var(--border-color); border-radius:10px;"></iframe></div>`;
            }

            document.getElementById('viewLampiranBody').innerHTML = `
                <div class="pm-detail-grid">
                    <div class="pm-detail-item">
                        <div class="pm-detail-label">Judul Lampiran</div>
                        <div class="pm-detail-value">${escapeHtml(item.judul)}</div>
                    </div>
                    <div class="pm-detail-item">
                        <div class="pm-detail-label">Aktivitas Terkait</div>
                        <div class="pm-detail-value">${actTitle ? escapeHtml(actTitle) : 'Lampiran Umum'}</div>
                    </div>
                    <div class="pm-detail-item">
                        <div class="pm-detail-label">Nama Berkas Asli</div>
                        <div class="pm-detail-value">${escapeHtml(item.namaFileAsli)}</div>
                    </div>
                    <div class="pm-detail-item">
                        <div class="pm-detail-label">Format &amp; Ukuran</div>
                        <div class="pm-detail-value">${escapeHtml(item.ekstensi)} (${formatBytes(item.ukuranFile)})</div>
                    </div>
                </div>
                <div class="pm-form-group" style="margin-bottom:0;">
                    <label class="pm-label">Keterangan</label>
                    <div style="font-size:0.88rem; color:#475569; background:#faf8f5; border:1px solid var(--border-color); border-radius:10px; padding:12px;">
                        ${escapeHtml(item.keterangan || 'Tidak ada keterangan tambahan.')}
                    </div>
                </div>
                ${previewHtml}
            `;
            document.getElementById('btnDownloadLampiranDetail').href = `/lampiran/${item.id}/download`;
            document.getElementById('viewLampiranModal').style.display = 'flex';
        }

        function closeViewLampiranModal() {
            document.getElementById('viewLampiranModal').style.display = 'none';
        }

        function deleteLampiran(id) {
            const item = lampiranList.find(x => Number(x.id) === Number(id));
            if (!item) return;

            const executeDelete = async () => {
                const res = await fetch(`/lampiran/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': LAMPIRAN_CSRF
                    }
                });
                if (res.ok) {
                    await loadLampiranData();
                }
            };

            if (typeof Store !== 'undefined' && Store.showConfirm) {
                Store.showConfirm({
                    title: 'Hapus Lampiran?',
                    message: `Apakah Anda yakin ingin menghapus berkas "${escapeHtml(item.judul)}"? File akan dihapus permanen dari penyimpanan.`,
                    icon: 'fas fa-trash-alt',
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal',
                    isDanger: true,
                    onConfirm: executeDelete
                });
            } else if (confirm(`Hapus berkas "${item.judul}"?`)) {
                executeDelete();
            }
        }

        ['lampiranModal', 'viewLampiranModal'].forEach(mId => {
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