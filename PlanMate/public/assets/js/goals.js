/* =====================================================
   PlanMate - Target & Goals
   Data goals diambil langsung dari server (/goals/data).
   Butuh: <meta name="csrf-token"> dan window.GOALS_CONFIG.aktivitas
   ===================================================== */

const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const ACTIVITIES = (window.GOALS_CONFIG && window.GOALS_CONFIG.aktivitas) || [];

let goals = [];
let currentGoalFilter = 'all';

const getGoal = id => goals.find(g => Number(g.id) === Number(id));
const getActivity = id => ACTIVITIES.find(a => Number(a.id) === Number(id));

function escapeHtml(str) {
    return String(str || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

/* ---------- Komunikasi dengan server ---------- */
async function goalApi(method, url, body) {
    const res = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF
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

async function loadGoals() {
    goals = await goalApi('GET', '/goals/data');
    renderGoals();
}

function goalError(e) {
    alert(e && e.message ? e.message : 'Terjadi kesalahan.');
}

/* ---------- Init ---------- */
document.addEventListener('DOMContentLoaded', async () => {
    // Isi sidebar (opsional, aman kalau Store/elemen tidak ada).
    // Hapus blok ini kalau sidebar sudah dirender Blade.
    try {
        const user = window.Store && Store.getCurrentUser ? Store.getCurrentUser() : null;
        if (user) {
            const name = user.name || user.username || 'Mahasiswa';
            const n = document.getElementById('sidebar-user-name');
            const r = document.getElementById('sidebar-user-role');
            const a = document.getElementById('sidebar-avatar');
            if (n) n.textContent = name;
            if (r) r.textContent = user.jurusan || 'Mahasiswa';
            if (a) a.textContent = name.charAt(0).toUpperCase();
        }
    } catch (e) {}

    try {
        await loadGoals();
    } catch (e) {
        goalError(e);
    }

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'create') {
        openGoalModal(null, urlParams.get('activity_id'));
    }
});

function populateActivitiesDropdown(selectedId = null) {
    const select = document.getElementById('goalAktivitasId');
    select.innerHTML = '<option value="">-- Target Mandiri (Tanpa Aktivitas) --</option>';
    ACTIVITIES.forEach(a => {
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

/* ---------- Render ---------- */
function renderGoals() {
    const allGoals = goals;
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
    }
    emptyState.style.display = 'none';
    grid.style.display = 'grid';

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

        // Deadline
        let deadlineHtml = '';
        if (goal.deadline) {
            const dl = new Date(goal.deadline);
            const diffDays = Math.ceil((dl - new Date()) / (1000 * 60 * 60 * 24));
            const dlFormatted = dl.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            let deadlineColor = 'var(--text-muted)';
            if (!isComplete && diffDays <= 2 && diffDays >= 0) deadlineColor = '#ef4444';
            else if (!isComplete && diffDays <= 7) deadlineColor = '#f59e0b';
            deadlineHtml = `<span style="font-size: 0.78rem; color: ${deadlineColor}; font-weight: 600;"><i class="far fa-calendar-alt"></i> ${dlFormatted}</span>`;
        }

        // Badge aktivitas
        let linkBadge = `<span style="font-size: 0.72rem; font-weight: 700; background: #f8fafc; color: #64748b; padding: 2px 8px; border-radius: 6px; border: 1px solid #e2e8f0;"><i class="fas fa-user"></i> Mandiri</span>`;
        if (goal.aktivitas) {
            linkBadge = `<a href="/aktivitas?highlight=${goal.aktivitas.id}" style="text-decoration:none; font-size: 0.72rem; font-weight: 700; background: #eff6ff; color: #2563eb; padding: 2px 8px; border-radius: 6px; border: 1px solid #bfdbfe; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block;" title="${escapeHtml(goal.aktivitas.judul)}"><i class="fas fa-link"></i> ${escapeHtml(goal.aktivitas.judul)}</a>`;
        }

        // Tahapan (accordion)
        let subtargetsHtml = '';
        if (Array.isArray(goal.targetKecil) && goal.targetKecil.length > 0) {
            const doneCount = goal.targetKecil.filter(s => s.done).length;
            const stTotal = goal.targetKecil.length;
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
                            <span class="accordion-count">${doneCount}/${stTotal} selesai</span>
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

/* ---------- Accordion & tahapan ---------- */
function toggleAccordion(bodyId, chevronId) {
    const body = document.getElementById(bodyId);
    const chevron = document.getElementById(chevronId);
    if (!body) return;
    const isOpen = body.classList.contains('open');
    body.classList.toggle('open', !isOpen);
    if (chevron) chevron.classList.toggle('open', !isOpen);
}

async function toggleSubtarget(goalId, subtargetId) {
    // Simpan accordion yang sedang terbuka supaya tidak menutup setelah render ulang
    const openIds = Array.from(document.querySelectorAll('.subtask-list-body.open')).map(el => el.id);
    try {
        await goalApi('PATCH', `/goals/${goalId}/tahapan/${subtargetId}/toggle`);
        await loadGoals();
        openIds.forEach(id => {
            const body = document.getElementById(id);
            const chevron = document.getElementById(id.replace('subtask-body-', 'subtask-chevron-'));
            if (body) body.classList.add('open');
            if (chevron) chevron.classList.add('open');
        });
    } catch (e) { goalError(e); }
}

/* ---------- Quick progress ---------- */
function openQuickProgress(goalId) {
    const goal = getGoal(goalId);
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
        await loadGoals();
        closeProgressModal();
    } catch (e) { goalError(e); }
}

/* ---------- Multi-step modal ---------- */
let _goalCurrentStep = 1;
const _goalTotalSteps = 3;

const _stepSubtitles = [
    'Langkah 1 dari 3 — Informasi Target',
    'Langkah 2 dari 3 — Pengaturan Target',
    'Langkah 3 dari 3 — Tahapan & Milestone',
];

function _goalGotoStep(step) {
    _goalCurrentStep = step;

    for (let i = 1; i <= _goalTotalSteps; i++) {
        const panel = document.getElementById(`goal-panel-${i}`);
        if (panel) panel.classList.toggle('active', i === step);
    }

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

    document.getElementById('goalModalSubtitle').textContent = _stepSubtitles[step - 1];
    document.getElementById('btnGoalBack').style.display = step > 1 ? 'inline-flex' : 'none';

    const btnNext = document.getElementById('btnGoalNext');
    const btnSubmit = document.getElementById('btnGoalSubmit');
    if (step < _goalTotalSteps) {
        btnNext.style.display = 'inline-flex';
        btnSubmit.style.display = 'none';
    } else {
        btnNext.style.display = 'none';
        btnSubmit.style.display = 'inline-flex';
    }

    if (step === 3) _updateSubtaskHint();
}

function goalStepNext() {
    if (_goalCurrentStep === 1) {
        const input = document.getElementById('goalJudul');
        if (!input.value.trim()) {
            input.focus();
            input.style.borderColor = '#ef4444';
            input.addEventListener('input', function h() {
                this.style.borderColor = '';
                this.removeEventListener('input', h);
            });
            return;
        }
    }
    if (_goalCurrentStep < _goalTotalSteps) _goalGotoStep(_goalCurrentStep + 1);
}

function goalStepBack() {
    if (_goalCurrentStep > 1) _goalGotoStep(_goalCurrentStep - 1);
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
        const goal = getGoal(goalId);
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

            if (Array.isArray(goal.targetKecil)) {
                goal.targetKecil.forEach(st => addSubtargetRow(st.text, st.done));
            }
        }
    } else {
        document.getElementById('goalModalTitle').innerHTML = '<i class="fas fa-bullseye" style="color: var(--primary-accent-hover);"></i> Buat Target Baru';
        if (preselectActId) {
            document.getElementById('goalAktivitasId').value = preselectActId;
            const linkedAct = getActivity(preselectActId);
            if (linkedAct) {
                document.getElementById('goalJudul').value = `Target: ${linkedAct.judul}`;
                if (linkedAct.deadline) {
                    document.getElementById('goalDeadline').value = String(linkedAct.deadline).slice(0, 10);
                }
            }
        }
        addSubtargetRow('Tahap 1: Perencanaan & Persiapan');
        addSubtargetRow('Tahap 2: Eksekusi & Penyelesaian');
    }

    document.getElementById('goalModal').style.display = 'flex';
    const modal = document.getElementById('goalModal');
    modal.style.display = 'flex';
    modal.classList.add('active');
}

function closeGoalModal() {
    const modal = document.getElementById('goalModal');
    modal.style.display = 'none';
    modal.classList.remove('active');
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

    const targetKecil = [];
    document.querySelectorAll('#subtargets-builder-list > div').forEach((row, idx) => {
        const text = row.querySelector('.st-text-input').value.trim();
        const done = row.querySelector('.st-done-check').checked;
        if (text) targetKecil.push({ id: idx + 1, text, done });
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
        await loadGoals();
        closeGoalModal();
    } catch (err) { goalError(err); }
});

/* ---------- Hapus ---------- */
function deleteGoalItem(id) {
    const goal = getGoal(id);
    if (!goal) return;

    const doDelete = async () => {
        try {
            await goalApi('DELETE', `/goals/${id}`);
            await loadGoals();
        } catch (e) { goalError(e); }
    };

    if (window.Store && Store.showConfirm) {
        Store.showConfirm({
            title: 'Hapus Target / Goal?',
            message: `Apakah Anda yakin ingin menghapus target "${escapeHtml(goal.judul)}"? Seluruh milestone tahapan yang telah dicatat akan dihapus secara permanen.`,
            icon: 'fas fa-trash-alt',
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal',
            isDanger: true,
            onConfirm: doDelete
        });
    } else if (confirm(`Hapus target "${goal.judul}"?`)) {
        doDelete();
    }
}

/* ---------- Detail ---------- */
function viewGoalDetail(id) {
    const goal = getGoal(id);
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
                    Status Aktivitas: <strong>${escapeHtml(goal.aktivitas.status || '-')}</strong> | Deadline: ${escapeHtml(goal.aktivitas.deadline || '-')}
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

/* ---------- Klik overlay untuk menutup modal ---------- */
['goalModal', 'progressModal', 'detailGoalModal'].forEach(mId => {
    const el = document.getElementById(mId);
    if (el) {
        el.addEventListener('click', (e) => {
            if (e.target === el) {
                el.style.display = 'none';
                el.classList.remove('active');
            }
        });
    }
});