<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanMate - Catatan Materi Kuliah</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script src="{{ asset('assets/js/store.js') }}"></script>
    <script>Store.requireAuth();</script>

    <style>
        .notes-toolbar { display:flex; justify-content:space-between; align-items:center; gap:14px; flex-wrap:wrap; margin-bottom:22px; }
        .notes-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:18px; }
        .note-card { background:#fff; border:1px solid var(--border-color); border-radius:14px; padding:20px; display:flex; flex-direction:column; min-height:220px; box-shadow:0 3px 12px rgba(15,23,42,.035); }
        .note-card-top { display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:13px; }
        .note-subject { font-size:.75rem; font-weight:700; color:var(--primary-accent-hover); background:#f1f5f9; padding:6px 10px; border-radius:20px; }
        .note-date { font-size:.72rem; color:var(--text-muted); }
        .note-title { font-size:1rem; font-weight:800; color:var(--text-dark); margin:0 0 9px; overflow-wrap:anywhere; }
        .note-preview { font-size:.83rem; line-height:1.65; color:var(--text-muted); white-space:pre-wrap; overflow-wrap:anywhere; display:-webkit-box; -webkit-line-clamp:4; -webkit-box-orient:vertical; overflow:hidden; flex:1; }
        .note-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:17px; padding-top:13px; border-top:1px solid var(--border-color); }
        .note-action { border:1px solid var(--border-color); background:#fff; border-radius:8px; padding:7px 10px; cursor:pointer; color:var(--text-muted); font:inherit; font-size:.78rem; }
        .note-action:hover { background:#f8fafc; color:var(--text-dark); }
        .notes-empty { text-align:center; padding:55px 20px; color:var(--text-muted); background:#fff; border:1px dashed var(--border-color); border-radius:14px; grid-column:1/-1; }
        .notes-empty i { font-size:2rem; margin-bottom:12px; color:#94a3b8; }
        .note-form-group { margin-bottom:15px; }
        .note-form-group label { display:block; font-size:.82rem; font-weight:700; margin-bottom:7px; color:var(--text-dark); }
        .note-form-group input, .note-form-group textarea { width:100%; box-sizing:border-box; }
        .note-form-group textarea { min-height:190px; resize:vertical; }
        @media(max-width:600px) {
            .notes-toolbar { align-items:stretch; }
            .notes-toolbar .btn { width:100%; }
        }
    </style>
</head>

<body>

<div class="app-shell">

    @include('partials.sidebar')

    <main class="app-main-workspace">

        <div class="top-header">
            <div class="breadcrumb-label">PLANMATE / Catatan Materi Kuliah</div>
        </div>

        <div class="card-section">

            <div class="card-header-flex">
                <div>
                    <h2 style="font-size:1.35rem;font-weight:800;color:var(--text-dark);">Catatan Materi Kuliah</h2>
                    <p style="font-size:.85rem;color:var(--text-muted);">
                        Simpan rangkuman dan materi perkuliahan agar mudah dibaca kembali.
                    </p>
                </div>
            </div>

            <div class="notes-toolbar">
                <div style="font-size:.85rem;color:var(--text-muted);">
                    <i class="fas fa-book-open"></i>
                    <span id="note-count">0 catatan tersimpan</span>
                </div>

                <button type="button" class="btn btn-primary" onclick="openNoteModal()">
                    <i class="fas fa-plus"></i> Tambah Catatan
                </button>
            </div>

            <div id="notes-grid" class="notes-grid"></div>

        </div>

    </main>
</div>


<!-- MODAL TAMBAH DAN EDIT CATATAN -->
<div id="noteModal" class="pm-modal-overlay">
    <div class="pm-modal-card pm-modal-md" role="dialog" aria-modal="true" aria-labelledby="noteModalTitle">

        <div class="pm-modal-header">
            <div class="pm-modal-title-group">
                <h3 class="pm-modal-title" id="noteModalTitle">
                    <i class="fas fa-book" style="color:var(--primary-accent-hover);"></i>
                    Tambah Catatan Materi
                </h3>
                <p class="pm-modal-subtitle">Isi informasi mata kuliah dan materi yang ingin disimpan.</p>
            </div>
            <button type="button" onclick="closeNoteModal()" class="pm-modal-close">&times;</button>
        </div>

        <form id="noteForm">
            <div class="pm-modal-body">

                <input type="hidden" id="noteId">

                <div class="note-form-group">
                    <label for="noteSubject">Mata Kuliah *</label>
                    <input type="text" id="noteSubject" class="pm-input" required maxlength="100" placeholder="Contoh: Basis Data">
                </div>

                <div class="note-form-group">
                    <label for="noteTitle">Judul Materi *</label>
                    <input type="text" id="noteTitle" class="pm-input" required maxlength="150" placeholder="Contoh: Normalisasi Basis Data">
                </div>

                <div class="note-form-group">
                    <label for="noteDate">Tanggal Catatan</label>
                    <input type="date" id="noteDate" class="pm-input">
                </div>

                <div class="note-form-group" style="margin-bottom:0;">
                    <label for="noteContent">Isi Catatan *</label>
                    <textarea id="noteContent" class="pm-textarea" required
                        placeholder="Tuliskan rangkuman, poin penting, rumus, atau penjelasan materi di sini..."></textarea>
                </div>

            </div>

            <div class="pm-modal-footer">
                <button type="button" onclick="closeNoteModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Catatan</button>
            </div>
        </form>

    </div>
</div>


<!-- MODAL MEMBACA CATATAN -->
<div id="readNoteModal" class="pm-modal-overlay">
    <div class="pm-modal-card pm-modal-md" role="dialog" aria-modal="true">

        <div class="pm-modal-header">
            <div class="pm-modal-title-group">
                <h3 class="pm-modal-title" id="readNoteTitle">Detail Catatan</h3>
                <p class="pm-modal-subtitle" id="readNoteMeta"></p>
            </div>
            <button type="button" onclick="closeReadModal()" class="pm-modal-close">&times;</button>
        </div>

        <div class="pm-modal-body">
            <div id="readNoteContent"
                style="white-space:pre-wrap;line-height:1.8;font-size:.9rem;color:var(--text-dark);overflow-wrap:anywhere;"></div>
        </div>

        <div class="pm-modal-footer">
            <button type="button" onclick="closeReadModal()" class="btn btn-secondary">Tutup</button>
        </div>

    </div>
</div>


<script>

const CSRF = document.querySelector('meta[name="csrf-token"]').content;

let currentNotes = [];


async function api(url, method = 'GET', body = null) {

    const res = await fetch(url, {
        method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF
        },
        body: body ? JSON.stringify(body) : null
    });

    if (!res.ok) {
        let msg = 'Terjadi kesalahan, coba lagi.';
        try {
            const json = await res.json();
            msg = json.errors ? Object.values(json.errors)[0][0] : (json.message || msg);
        } catch (e) {}
        throw new Error(msg);
    }

    return res.json();
}


function escapeNoteHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}


async function loadNotes() {

    const user = Store.getCurrentUser();

    if (user) {
        const name = user.name || user.username || 'Mahasiswa';
        const nameEl = document.getElementById('sidebar-user-name');
        const roleEl = document.getElementById('sidebar-user-role');
        const avatarEl = document.getElementById('sidebar-avatar');

        if (nameEl) nameEl.textContent = name;
        if (roleEl) roleEl.textContent = user.jurusan || 'Mahasiswa';
        if (avatarEl) avatarEl.textContent = name.charAt(0).toUpperCase();
    }

    try {
        currentNotes = await api('/catatan/data');
    } catch (error) {
        currentNotes = [];
        alert(error.message);
    }

    renderNotes();
}


function renderNotes() {

    const grid = document.getElementById('notes-grid');

    document.getElementById('note-count').textContent = `${currentNotes.length} catatan tersimpan`;

    if (!currentNotes.length) {
        grid.innerHTML = `
            <div class="notes-empty">
                <i class="fas fa-book-open"></i>
                <div style="font-weight:700;color:var(--text-dark);margin-bottom:5px;">Belum ada catatan materi</div>
                <div style="font-size:.83rem;">Klik tombol Tambah Catatan untuk menyimpan materi kuliah pertamamu.</div>
            </div>
        `;
        return;
    }

    grid.innerHTML = currentNotes.map(note => `
        <article class="note-card">

            <div class="note-card-top">
                <span class="note-subject">${escapeNoteHtml(note.subject)}</span>
                <span class="note-date">${escapeNoteHtml(formatNoteDate(note.date))}</span>
            </div>

            <h3 class="note-title">${escapeNoteHtml(note.title)}</h3>

            <div class="note-preview">${escapeNoteHtml(note.content)}</div>

            <div class="note-actions">
                <button type="button" class="note-action" onclick="readNote(${Number(note.id)})">
                    <i class="fas fa-eye"></i> Baca
                </button>
                <button type="button" class="note-action" onclick="editNote(${Number(note.id)})">
                    <i class="fas fa-pen"></i> Edit
                </button>
                <button type="button" class="note-action" onclick="deleteNote(${Number(note.id)})">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>

        </article>
    `).join('');
}


function formatNoteDate(date) {

    if (!date) return 'Tanpa tanggal';

    const parsed = new Date(date + 'T00:00:00');

    return Number.isNaN(parsed.getTime())
        ? date
        : parsed.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}


function openNoteModal(note = null) {

    document.getElementById('noteForm').reset();

    document.getElementById('noteId').value = note ? note.id : '';
    document.getElementById('noteSubject').value = note ? note.subject : '';
    document.getElementById('noteTitle').value = note ? note.title : '';
    document.getElementById('noteDate').value = note ? (note.date || '') : new Date().toISOString().slice(0, 10);
    document.getElementById('noteContent').value = note ? note.content : '';

    document.getElementById('noteModalTitle').innerHTML = note
        ? '<i class="fas fa-edit"></i> Edit Catatan Materi'
        : '<i class="fas fa-book"></i> Tambah Catatan Materi';

    document.getElementById('noteModal').style.display = 'flex';
}


function closeNoteModal() {
    document.getElementById('noteModal').style.display = 'none';
}


function editNote(id) {
    const note = currentNotes.find(item => Number(item.id) === Number(id));
    if (note) openNoteModal(note);
}


function readNote(id) {

    const note = currentNotes.find(item => Number(item.id) === Number(id));
    if (!note) return;

    document.getElementById('readNoteTitle').textContent = note.title;
    document.getElementById('readNoteMeta').textContent = `${note.subject} • ${formatNoteDate(note.date)}`;
    document.getElementById('readNoteContent').textContent = note.content;

    document.getElementById('readNoteModal').style.display = 'flex';
}


function closeReadModal() {
    document.getElementById('readNoteModal').style.display = 'none';
}


async function deleteNote(id) {

    if (!confirm('Yakin ingin menghapus catatan ini?')) return;

    try {
        await api('/catatan/' + id, 'DELETE');
        await loadNotes();
    } catch (error) {
        alert(error.message);
    }
}


document.getElementById('noteForm').addEventListener('submit', async function (event) {

    event.preventDefault();

    const id = document.getElementById('noteId').value;

    const payload = {
        subject: document.getElementById('noteSubject').value.trim(),
        title:   document.getElementById('noteTitle').value.trim(),
        date:    document.getElementById('noteDate').value || null,
        content: document.getElementById('noteContent').value.trim()
    };

    if (!payload.subject || !payload.title || !payload.content) return;

    try {
        if (id) {
            await api('/catatan/' + id, 'PUT', payload);
        } else {
            await api('/catatan', 'POST', payload);
        }

        closeNoteModal();
        await loadNotes();

    } catch (error) {
        alert(error.message);
    }
});


document.addEventListener('DOMContentLoaded', loadNotes);

</script>

</body>
</html>