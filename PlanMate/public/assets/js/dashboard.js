/* =====================================================
   PlanMate - Dashboard
   Data diambil dari server (/dashboard/data).
   ===================================================== */

const DASH_CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

let dashActivities = [];
let dashCategories = [];
let dashGoals = [];

let dashCalendarDate = new Date();
let dashSelectedDateStr = '';
let currentDeadlineTab = 'all';

function escapeHtml(str) {
    return String(str || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function formatDateKey(dateObj) {
    const y = dateObj.getFullYear();
    const m = String(dateObj.getMonth() + 1).padStart(2, '0');
    const d = String(dateObj.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

async function loadDashboardData() {
    const res = await fetch('/dashboard/data', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': DASH_CSRF,
        },
    });

    if (res.status === 401 || res.status === 419) {
        window.location.href = '/login';
        return;
    }

    if (!res.ok) {
        throw new Error('Gagal memuat data dashboard.');
    }

    const payload = await res.json();
    dashActivities = Array.isArray(payload.activities) ? payload.activities : [];
    dashCategories = Array.isArray(payload.categories) ? payload.categories : [];
    dashGoals = Array.isArray(payload.goals) ? payload.goals : [];
}

document.addEventListener('DOMContentLoaded', async () => {
    const user = window.DASHBOARD_USER || (window.Store && Store.getCurrentUser ? Store.getCurrentUser() : null);
    if (user) {
        const name = user.name || user.username || 'Mahasiswa';
        const sbName = document.getElementById('sidebar-user-name');
        const sbRole = document.getElementById('sidebar-user-role');
        const sbAvatar = document.getElementById('sidebar-avatar');
        const heroName = document.getElementById('hero-user-name');

        if (sbName) sbName.textContent = name;
        if (sbRole) sbRole.textContent = user.jurusan || 'Mahasiswa';
        if (sbAvatar) sbAvatar.textContent = name.charAt(0).toUpperCase();
        if (heroName) heroName.textContent = name + '.';
    }

    // Set Hari Ini String (Bahasa Indonesia)
    const days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
    const months = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
    const now = new Date();
    const heroDateEl = document.getElementById('hero-date-str');
    if (heroDateEl) {
        heroDateEl.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
    }

    dashSelectedDateStr = formatDateKey(now);

    try {
        await loadDashboardData();
    } catch (e) {
        console.error(e);
    }

    populateDashCategoryDropdown();
    renderDashboard();

    const modalOverlay = document.getElementById('dashDetailModal');
    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) closeDashDetailModal();
        });
    }
});

function renderDashboard() {
    const now = new Date();
    const total = dashActivities.length;
    const pending = dashActivities.filter(a => a.status !== 'Selesai').length;

    const todayCount = dashActivities.filter(a => {
        if (a.status === 'Selesai' || !a.deadline) return false;
        const dl = new Date(a.deadline);
        return dl.getFullYear() === now.getFullYear() &&
               dl.getMonth() === now.getMonth() &&
               dl.getDate() === now.getDate();
    }).length;

    document.getElementById('box-total-val').textContent = total;
    document.getElementById('box-pending-val').textContent = pending;
    document.getElementById('box-today-val').textContent = todayCount;

    const emptyStateElem = document.getElementById('dashboard-empty-state');
    const gridElem = document.getElementById('dashboard-content-grid');

    if (total === 0) {
        emptyStateElem.style.display = 'block';
        gridElem.style.display = 'none';
        return;
    }

    emptyStateElem.style.display = 'none';
    gridElem.style.display = 'grid';

    checkDenseSchedule(dashActivities, now);
    renderDashCalendar();
    renderDashAgendaForDate(dashSelectedDateStr);
    executeDashSearch();
    renderDashDeadlines();
    renderDashboardGoals();
}

// ==========================================================
// 1. FITUR DEADLINE & PRIORITAS DI DASHBOARD
// ==========================================================
function filterDeadlineTab(tab) {
    currentDeadlineTab = tab;
    ['all', 'urgent', 'today', 'high', 'overdue'].forEach(t => {
        const el = document.getElementById(`flt-pill-${t}`);
        if (el) el.classList.toggle('active', t === tab);
    });
    renderDashDeadlines();
}

function renderDashDeadlines() {
    const now = new Date();
    const limit24h = new Date(now.getTime() + 24 * 60 * 60 * 1000);

    let filtered = [...dashActivities];

    if (currentDeadlineTab === 'urgent') {
        filtered = filtered.filter(a => a.status !== 'Selesai' && a.deadline && new Date(a.deadline) <= limit24h);
    } else if (currentDeadlineTab === 'today') {
        filtered = filtered.filter(a => {
            if (a.status === 'Selesai' || !a.deadline) return false;
            const dl = new Date(a.deadline);
            return dl.getFullYear() === now.getFullYear() &&
                   dl.getMonth() === now.getMonth() &&
                   dl.getDate() === now.getDate();
        });
    } else if (currentDeadlineTab === 'high') {
        filtered = filtered.filter(a => a.prioritas === 'Tinggi');
    } else if (currentDeadlineTab === 'overdue') {
        filtered = filtered.filter(a => a.status !== 'Selesai' && a.deadline && new Date(a.deadline) < now);
    }

    filtered.sort((a, b) => {
        if (a.status === 'Selesai' && b.status !== 'Selesai') return 1;
        if (a.status !== 'Selesai' && b.status === 'Selesai') return -1;
        return new Date(a.deadline || 0) - new Date(b.deadline || 0);
    });

    const container = document.getElementById('dash-deadline-list');
    container.innerHTML = '';

    if (filtered.length === 0) {
        container.innerHTML = `
            <div style="padding: 24px 16px; background: #faf8f5; border-radius: 12px; text-align: center; color: var(--text-muted); font-size: 0.86rem; border: 1px dashed var(--border-color);">
                <i class="fas fa-check-circle" style="color: #10b981; font-size: 1.4rem; margin-bottom: 6px; display: block;"></i>
                Tidak ada aktivitas yang sesuai dengan filter deadline ini.
            </div>`;
        return;
    }

    filtered.forEach(act => {
        const cat = dashCategories.find(c => Number(c.id) === Number(act.kategoriId)) || { name: 'Umum', color: '#64748b' };
        const dlDate = new Date(act.deadline);
        const dlFormatted = !isNaN(dlDate.getTime())
            ? dlDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
            : '-';
        const diffMs = dlDate - now;

        const isToday = dlDate.getFullYear() === now.getFullYear() &&
                        dlDate.getMonth() === now.getMonth() &&
                        dlDate.getDate() === now.getDate();

        let urgencyBadge = '';
        let itemBorderHighlight = '';

        if (act.status === 'Selesai') {
            urgencyBadge = '<span class="badge badge-done"><i class="fas fa-check"></i> Selesai</span>';
        } else if (diffMs < 0) {
            urgencyBadge = '<span class="badge" style="background:#fee2e2; color:#dc2626; border: 1px solid #fca5a5;"><i class="fas fa-times-circle"></i> Terlambat</span>';
            itemBorderHighlight = 'border-left: 4px solid #ef4444;';
        } else if (isToday) {
            urgencyBadge = '<span class="deadline-today-indicator"><i class="far fa-clock"></i> Hari Ini</span>';
            itemBorderHighlight = 'border-left: 4px solid #f59e0b;';
        } else if (diffMs <= 24 * 3600 * 1000) {
            urgencyBadge = '<span class="deadline-approaching-indicator"><i class="fas fa-fire"></i> &lt; 24 Jam</span>';
            itemBorderHighlight = 'border-left: 4px solid #ef4444;';
        } else {
            urgencyBadge = '<span class="badge badge-secondary"><i class="far fa-calendar"></i> Mendatang</span>';
        }

        let prioBadge = '<span class="badge badge-medium">Sedang</span>';
        if (act.prioritas === 'Tinggi') prioBadge = '<span class="badge badge-high">Tinggi</span>';
        if (act.prioritas === 'Rendah') prioBadge = '<span class="badge badge-low">Rendah</span>';

        const item = document.createElement('div');
        item.style.cssText = `display: flex; flex-direction: column; gap: 8px; padding: 12px 14px; background: #faf8f5; border-radius: 12px; border: 1px solid var(--border-color); ${itemBorderHighlight} transition: transform 0.15s ease;`;
        item.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                <div>
                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px; flex-wrap: wrap;">
                        <span class="cat-pill" style="background:${cat.color}15; color:${cat.color}; border:1px solid ${cat.color}40; font-size: 0.72rem; padding: 2px 7px;">${escapeHtml(cat.name)}</span>
                        ${urgencyBadge}
                        ${prioBadge}
                    </div>
                    <strong style="font-size: 0.92rem; color: var(--text-dark); display: block; line-height: 1.35;">${escapeHtml(act.judul)}</strong>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 6px; border-top: 1px dashed var(--border-color); font-size: 0.78rem; color: var(--text-muted);">
                <span><i class="far fa-clock"></i> ${dlFormatted}</span>
                <div style="display: flex; gap: 6px;">
                    <button type="button" onclick="openDashDetailModal(${act.id})" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.76rem;" title="Lihat detail">
                        <i class="fas fa-eye"></i> Detail
                    </button>
                    <a href="/aktivitas?highlight=${act.id}" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.76rem;" title="Buka di halaman aktivitas">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>
        `;
        container.appendChild(item);
    });
}

// ==========================================================
// 2. FITUR KALENDER INTERAKTIF DI DASHBOARD
// ==========================================================
function changeDashMonth(delta) {
    dashCalendarDate = new Date(dashCalendarDate.getFullYear(), dashCalendarDate.getMonth() + delta, 1);
    renderDashCalendar();
}

function goDashToday() {
    dashCalendarDate = new Date();
    dashSelectedDateStr = formatDateKey(dashCalendarDate);
    renderDashCalendar();
    renderDashAgendaForDate(dashSelectedDateStr);
}

function selectDashCalendarDate(dateKey) {
    dashSelectedDateStr = dateKey;
    renderDashCalendar();
    renderDashAgendaForDate(dateKey);
}

function renderDashCalendar() {
    const year = dashCalendarDate.getFullYear();
    const month = dashCalendarDate.getMonth();
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    document.getElementById('dash-cal-month-title').innerHTML = `${monthNames[month]} ${year}`;

    const firstDay = new Date(year, month, 1, 12, 0, 0);
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const dayOfWeek = firstDay.getDay();
    const emptyOffset = (dayOfWeek + 6) % 7;

    const todayKey = formatDateKey(new Date());
    const grid = document.getElementById('dash-cal-days-grid');
    grid.innerHTML = '';

    for (let i = 0; i < emptyOffset; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'dash-cal-cell empty';
        grid.appendChild(emptyCell);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const isToday = (dateKey === todayKey);
        const isSelected = (dateKey === dashSelectedDateStr);

        const dayActs = dashActivities.filter(a => a.deadline && formatDateKey(new Date(a.deadline)) === dateKey);
        const hasPendingUrgent = dayActs.some(a => a.status !== 'Selesai' && a.prioritas === 'Tinggi');

        const cell = document.createElement('div');
        cell.className = `dash-cal-cell ${isToday ? 'today' : ''} ${isSelected ? 'selected' : ''}`;
        cell.title = `${day} ${monthNames[month]} ${year} (${dayActs.length} aktivitas)`;
        cell.onclick = () => selectDashCalendarDate(dateKey);

        let dotsHtml = '';
        if (dayActs.length > 0) {
            const dotCount = Math.min(dayActs.length, 3);
            let dots = '';
            for (let d = 0; d < dotCount; d++) {
                const isUrg = (d === 0 && hasPendingUrgent);
                dots += `<span class="dash-cal-dot ${isUrg ? 'urgent' : ''}"></span>`;
            }
            dotsHtml = `<div class="dash-cal-dots">${dots}</div>`;
        }

        cell.innerHTML = `
            <span class="dash-cal-num">${day}</span>
            ${dotsHtml}
        `;
        grid.appendChild(cell);
    }

    const totalCells = emptyOffset + daysInMonth;
    const trailingCount = (7 - (totalCells % 7)) % 7;
    for (let i = 0; i < trailingCount; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'dash-cal-cell empty';
        grid.appendChild(emptyCell);
    }
}

function renderDashAgendaForDate(dateKey) {
    const container = document.getElementById('dash-selected-agenda-list');
    const countBadge = document.getElementById('dash-selected-count-badge');
    const titleDate = document.getElementById('dash-selected-date-str');

    const parts = dateKey.split('-');
    const selDateObj = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    titleDate.textContent = `${days[selDateObj.getDay()]}, ${selDateObj.getDate()} ${months[selDateObj.getMonth()]} ${selDateObj.getFullYear()}`;

    const dayActs = dashActivities.filter(a => a.deadline && formatDateKey(new Date(a.deadline)) === dateKey);
    countBadge.textContent = `${dayActs.length} Tugas`;

    container.innerHTML = '';

    if (dayActs.length === 0) {
        container.innerHTML = `
            <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 24px 10px; color: var(--text-muted); font-size: 0.85rem;">
                <i class="far fa-calendar-check" style="font-size: 1.8rem; color: #cbd5e1; margin-bottom: 8px;"></i>
                Tidak ada aktivitas yang dijadwalkan pada tanggal ini.
                <a href="/aktivitas?action=create" style="margin-top: 10px; font-size: 0.78rem; font-weight: 700; color: var(--primary-accent-hover); text-decoration: none;">
                    <i class="fas fa-plus"></i> Tambah Jadwal Baru
                </a>
            </div>
        `;
        return;
    }

    dayActs.forEach(act => {
        const cat = dashCategories.find(c => Number(c.id) === Number(act.kategoriId)) || { name: 'Umum', color: '#64748b' };
        const dl = new Date(act.deadline);
        const jamStr = !isNaN(dl.getTime()) ? dl.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '';
        const isDone = (act.status === 'Selesai');

        const item = document.createElement('div');
        item.style.cssText = `background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 10px 12px; display: flex; justify-content: space-between; align-items: center; gap: 8px; border-left: 3px solid ${isDone ? '#10b981' : cat.color};`;
        item.innerHTML = `
            <div style="min-width: 0; flex: 1;">
                <div style="display: flex; align-items: center; gap: 6px; font-size: 0.73rem; color: var(--text-muted); margin-bottom: 2px;">
                    <span style="font-weight: 700; color: #182531;"><i class="far fa-clock"></i> ${jamStr || 'Sepanjang Hari'}</span>
                    <span>&bull;</span>
                    <span style="color: ${cat.color}; font-weight: 700;">${escapeHtml(cat.name)}</span>
                </div>
                <strong style="font-size: 0.86rem; color: var(--text-dark); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    ${escapeHtml(act.judul)}
                </strong>
            </div>
            <button type="button" onclick="openDashDetailModal(${act.id})" class="btn btn-secondary btn-icon btn-sm" style="flex-shrink: 0;" title="Lihat detail">
                <i class="fas fa-chevron-right"></i>
            </button>
        `;
        container.appendChild(item);
    });
}

// ==========================================================
// 3. FITUR PENCARIAN & FILTER DI DASHBOARD
// ==========================================================
function populateDashCategoryDropdown() {
    const select = document.getElementById('dashSearchCat');
    if (!select) return;
    select.innerHTML = '<option value="">Semua Kategori</option>';
    dashCategories.forEach(c => {
        select.innerHTML += `<option value="${c.id}">${escapeHtml(c.name)}</option>`;
    });
}

function resetDashSearch() {
    document.getElementById('dashSearchKeyword').value = '';
    document.getElementById('dashSearchCat').value = '';
    document.getElementById('dashSearchStatus').value = '';
    document.getElementById('dashSearchPrio').value = '';
    document.getElementById('dashSearchDeadline').value = '';
    executeDashSearch();
}

function executeDashSearch() {
    const keyword = (document.getElementById('dashSearchKeyword').value || '').trim().toLowerCase();
    const catId = document.getElementById('dashSearchCat').value;
    const status = document.getElementById('dashSearchStatus').value;
    const priority = document.getElementById('dashSearchPrio').value;
    const deadlineFilter = document.getElementById('dashSearchDeadline').value;

    const now = new Date();
    const nowTime = now.getTime();
    const limit7d = nowTime + 7 * 24 * 60 * 60 * 1000;

    const filtered = dashActivities.filter(act => {
        const matchKey = !keyword ||
            (act.judul && act.judul.toLowerCase().includes(keyword)) ||
            (act.deskripsi && act.deskripsi.toLowerCase().includes(keyword));
        const matchCat = !catId || Number(act.kategoriId) === Number(catId);
        const matchStat = !status || act.status === status;
        const matchPrio = !priority || act.prioritas === priority;

        let matchDeadline = true;
        if (deadlineFilter && act.deadline) {
            const dlDate = new Date(act.deadline);
            const dlTime = dlDate.getTime();
            if (deadlineFilter === 'today') {
                matchDeadline = (dlDate.getFullYear() === now.getFullYear() &&
                                 dlDate.getMonth() === now.getMonth() &&
                                 dlDate.getDate() === now.getDate());
            } else if (deadlineFilter === 'week') {
                matchDeadline = (dlTime >= nowTime && dlTime <= limit7d);
            } else if (deadlineFilter === 'overdue') {
                matchDeadline = (dlTime < nowTime && act.status !== 'Selesai');
            } else if (deadlineFilter === 'upcoming') {
                matchDeadline = (dlTime >= nowTime);
            }
        }

        return matchKey && matchCat && matchStat && matchPrio && matchDeadline;
    });

    filtered.sort((a, b) => new Date(a.deadline || 0) - new Date(b.deadline || 0));

    const tbody = document.getElementById('dashSearchTableBody');
    const countLabel = document.getElementById('dashSearchResultCount');
    tbody.innerHTML = '';

    countLabel.textContent = `Menampilkan ${filtered.length} dari ${dashActivities.length} aktivitas`;

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:28px 16px; color:var(--text-muted); font-size:0.86rem;">Tidak ada aktivitas yang sesuai dengan kriteria pencarian Anda.</td></tr>`;
        return;
    }

    filtered.forEach(act => {
        const cat = dashCategories.find(c => Number(c.id) === Number(act.kategoriId)) || { name: 'Umum', color: '#64748b' };
        const deadlineDate = new Date(act.deadline);
        const dateFormatted = !isNaN(deadlineDate.getTime())
            ? deadlineDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
            : '-';

        let prioBadge = '<span class="badge badge-medium">Sedang</span>';
        if (act.prioritas === 'Tinggi') prioBadge = '<span class="badge badge-high">Tinggi</span>';
        if (act.prioritas === 'Rendah') prioBadge = '<span class="badge badge-low">Rendah</span>';

        let statusBadge = '<span class="badge badge-todo">Belum Dimulai</span>';
        if (act.status === 'Sedang Dikerjakan') statusBadge = '<span class="badge badge-wip">Sedang Dikerjakan</span>';
        if (act.status === 'Selesai') statusBadge = '<span class="badge badge-done">Selesai</span>';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <strong style="font-size:0.88rem; color:var(--text-dark); display:block;">${escapeHtml(act.judul)}</strong>
                ${act.deskripsi ? `<div style="font-size:0.75rem; color:var(--text-muted); max-width:240px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${escapeHtml(act.deskripsi)}</div>` : ''}
            </td>
            <td><span class="cat-pill" style="background:${cat.color}15; color:${cat.color}; border:1px solid ${cat.color}40; font-size: 0.74rem;">${escapeHtml(cat.name)}</span></td>
            <td style="font-size:0.80rem; font-weight:600; white-space:nowrap;">${dateFormatted}</td>
            <td>${prioBadge}</td>
            <td>${statusBadge}</td>
            <td style="text-align: right; white-space:nowrap;">
                <button type="button" onclick="openDashDetailModal(${act.id})" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size:0.75rem;" title="Lihat detail">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <a href="/aktivitas?highlight=${act.id}" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size:0.75rem;" title="Kelola di Semua Aktivitas">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// ==========================================================
// 4. JADWAL PADAT & GOALS WIDGET
// ==========================================================
function checkDenseSchedule(activities, now) {
    const nowTime = now.getTime();
    const limit24hTime = nowTime + 24 * 60 * 60 * 1000;

    const acts24h = activities.filter(a => {
        if (!a.deadline) return false;
        const dlTime = new Date(a.deadline).getTime();
        return dlTime >= nowTime && dlTime <= limit24hTime;
    });

    const denseBanner = document.getElementById('dense-schedule-banner');
    const todayHeading = document.getElementById('today-heading-status');
    const todaySub = document.getElementById('today-sub-status');

    if (acts24h.length >= 3) {
        denseBanner.style.display = 'flex';
        document.getElementById('dense-banner-title').textContent = `Peringatan Jadwal Padat (${acts24h.length} Deadline)`;
        document.getElementById('dense-banner-desc').textContent = `Terdapat ${acts24h.length} aktivitas yang harus diselesaikan dalam waktu kurang dari 24 jam!`;

        todayHeading.textContent = 'Jadwal padat';
        todayHeading.style.color = '#ef4444';
        todaySub.innerHTML = `
            <div style="margin-bottom: 10px; color: var(--text-dark); font-weight: 700; font-size: 0.88rem;">
                Ada <span style="color: #ef4444;">${acts24h.length} deadline</span> dalam 24 jam ke depan!
            </div>
            ${acts24h.map(a => {
                const dl = new Date(a.deadline);
                const jamStr = dl.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                const isDone = (a.status === 'Selesai');
                if (isDone) {
                    return `<div style="font-size: 0.78rem; padding: 7px 10px; background: #f0fdf4; border-radius: 8px; margin-bottom: 6px; color: #15803d; border-left: 3px solid #16a34a; display: flex; justify-content: space-between; align-items: center;">
                        <div><strong>${escapeHtml(a.judul)}</strong> (${jamStr})</div>
                        <span style="font-size: 0.70rem; font-weight: 700; background: #dcfce7; color: #166534; padding: 2px 5px; border-radius: 4px;"><i class="fas fa-check"></i> Selesai</span>
                    </div>`;
                }
                return `<div style="font-size: 0.78rem; padding: 7px 10px; background: #fef2f2; border-radius: 8px; margin-bottom: 6px; color: #991b1b; border-left: 3px solid #ef4444;">
                    <strong>${escapeHtml(a.judul)}</strong> (${jamStr})
                </div>`;
            }).join('')}
        `;
    } else {
        denseBanner.style.display = 'none';
        todayHeading.textContent = 'Tidak ada jadwal padat';
        todayHeading.style.color = 'var(--text-dark)';
        todaySub.textContent = 'Nikmati harimu atau mulai kerjakan tugas prioritas!';
    }
}

function renderDashboardGoals() {
    const container = document.getElementById('dashboard-goals-container');
    if (!container) return;

    if (dashGoals.length === 0) {
        container.innerHTML = `
            <div style="font-size: 0.85rem; color: var(--text-muted); padding: 8px 0;">
                Belum ada target yang dibuat. <a href="/goals" style="color: var(--primary-accent-hover); font-weight: 700; text-decoration: none;">Buat Target Baru</a>
            </div>
        `;
        return;
    }

    const activeGoals = dashGoals.slice(0, 2);
    container.innerHTML = activeGoals.map(g => {
        const prog = Number(g.progres) || 0;
        return `
            <div style="margin-bottom: 10px; background: #faf8f5; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border-color);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <span style="font-size: 0.86rem; font-weight: 700; color: var(--text-dark); max-width: 72%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${escapeHtml(g.judul)}
                    </span>
                    <span style="font-size: 0.78rem; font-weight: 800; color: ${prog === 100 ? '#10b981' : 'var(--text-dark)'};">
                        ${prog}%
                    </span>
                </div>
                <div style="height: 6px; background: #e2e8f0; border-radius: 6px; overflow: hidden;">
                    <div style="width: ${prog}%; height: 100%; background: ${prog === 100 ? '#10b981' : 'linear-gradient(90deg, #34d399, #059669)'}; transition: width 0.3s ease;"></div>
                </div>
            </div>
        `;
    }).join('');
}

// ==========================================================
// 5. DETAIL MODAL DI DASHBOARD
// ==========================================================
function openDashDetailModal(actId) {
    const act = dashActivities.find(a => Number(a.id) === Number(actId));
    if (!act) return;

    const cat = dashCategories.find(c => Number(c.id) === Number(act.kategoriId)) || { name: 'Umum', color: '#64748b' };
    const now = new Date();
    const dl = new Date(act.deadline);

    document.getElementById('dashModalHeading').textContent = act.judul;
    document.getElementById('dashModalCategory').innerHTML = `<span class="cat-pill" style="background:${cat.color}15; color:${cat.color}; border:1px solid ${cat.color}40;">${escapeHtml(cat.name)}</span>`;
    document.getElementById('dashModalDeadline').textContent = !isNaN(dl.getTime())
        ? dl.toLocaleDateString('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        })
        : '-';

    const diffMs = dl - now;
    let urgText = '';
    if (act.status === 'Selesai') {
        urgText = '<span style="color:#10b981; font-weight:700;"><i class="fas fa-check-circle"></i> Sudah Diselesaikan</span>';
    } else if (diffMs < 0) {
        urgText = '<span style="color:#ef4444; font-weight:700;"><i class="fas fa-exclamation-circle"></i> Melewati Tenggat Waktu</span>';
    } else if (diffMs <= 24 * 3600 * 1000) {
        const jamSisa = Math.max(1, Math.round(diffMs / (3600 * 1000)));
        urgText = `<span style="color:#f59e0b; font-weight:700;"><i class="fas fa-clock"></i> Tersisa ~${jamSisa} jam lagi</span>`;
    } else {
        const hariSisa = Math.round(diffMs / (24 * 3600 * 1000));
        urgText = `<span style="color:#10b981; font-weight:700;"><i class="far fa-calendar-alt"></i> Tersisa ${hariSisa} hari lagi</span>`;
    }
    document.getElementById('dashModalUrgency').innerHTML = urgText;

    let prioBadge = '<span class="badge badge-medium">Sedang</span>';
    if (act.prioritas === 'Tinggi') prioBadge = '<span class="badge badge-high">Tinggi</span>';
    if (act.prioritas === 'Rendah') prioBadge = '<span class="badge badge-low">Rendah</span>';

    let statusBadge = '<span class="badge badge-todo">Belum Dimulai</span>';
    if (act.status === 'Sedang Dikerjakan') statusBadge = '<span class="badge badge-wip">Sedang Dikerjakan</span>';
    if (act.status === 'Selesai') statusBadge = '<span class="badge badge-done">Selesai</span>';

    document.getElementById('dashModalBadges').innerHTML = `${statusBadge} ${prioBadge}`;
    document.getElementById('dashModalStatusPrio').innerHTML = `${escapeHtml(act.status)} &bull; Prioritas ${escapeHtml(act.prioritas)}`;
    document.getElementById('dashModalDesc').textContent = act.deskripsi || 'Tidak ada catatan atau instruksi tambahan.';
    document.getElementById('dashModalEditLink').href = `/aktivitas?highlight=${act.id}`;

    document.getElementById('dashDetailModal').style.display = 'flex';
}

function closeDashDetailModal() {
    document.getElementById('dashDetailModal').style.display = 'none';
}