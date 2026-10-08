<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanMate - Kalender Aktivitas</title>
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
                <div class="breadcrumb-label">PLANMATE / Kalender</div>
            </div>

            <div class="card-section">
                <div class="card-header-flex">
                    <div>
                        <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-dark);" id="cal-month-title">Kalender Aktivitas</h2>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Visualisasi jadwal &amp; deadline tugas berbasis tanggal bulanan.</p>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button onclick="changeMonth(-1)" class="btn btn-secondary btn-icon btn-sm"><i class="fas fa-chevron-left"></i></button>
                        <button onclick="goToday()" class="btn btn-secondary btn-sm">Hari Ini</button>
                        <button onclick="changeMonth(1)" class="btn btn-secondary btn-icon btn-sm"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>

                <div class="calendar-grid">
                    <div class="cal-head">Senin</div>
                    <div class="cal-head">Selasa</div>
                    <div class="cal-head">Rabu</div>
                    <div class="cal-head">Kamis</div>
                    <div class="cal-head">Jumat</div>
                    <div class="cal-head" style="color: #ef4444;">Sabtu</div>
                    <div class="cal-head" style="color: #ef4444;">Minggu</div>
                </div>
                <div class="calendar-grid" id="cal-days-grid" style="margin-top: 8px;"></div>
            </div>
        </main>
    </div>

    <script>
        let currDate = new Date();

        document.addEventListener('DOMContentLoaded', () => {
            const user = Store.getCurrentUser();
            if (user) {
                const name = user.name || user.username;
                document.getElementById('sidebar-user-name').textContent = name;
                document.getElementById('sidebar-user-role').textContent = user.jurusan || 'Mahasiswa';
                document.getElementById('sidebar-avatar').textContent = name.charAt(0).toUpperCase();
            }

            renderCalendar();
        });

        function changeMonth(delta) { 
            currDate = new Date(currDate.getFullYear(), currDate.getMonth() + delta, 1); 
            renderCalendar(); 
        }
        function goToday() { 
            currDate = new Date(); 
            renderCalendar(); 
        }

        function renderCalendar() {
            const year = currDate.getFullYear();
            const month = currDate.getMonth();
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            document.getElementById('cal-month-title').innerHTML = `<i class="far fa-calendar-alt" style="color:var(--primary-accent);"></i> Kalender ${monthNames[month]} ${year}`;

            // Noon time prevents timezone offsets from pulling date to previous day
            const firstDay = new Date(year, month, 1, 12, 0, 0);
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            
            // Monday-first offset: 0 = Senin, 1 = Selasa, 2 = Rabu, 3 = Kamis, 4 = Jumat, 5 = Sabtu, 6 = Minggu
            const dayOfWeek = firstDay.getDay(); // 0 = Minggu, 1 = Senin, ...
            const emptyOffset = (dayOfWeek + 6) % 7;

            const activities = Store.getActivities();
            const categories = Store.getCategories();
            const grid = document.getElementById('cal-days-grid');
            grid.innerHTML = '';

            // Render leading empty cells
            for (let i = 0; i < emptyOffset; i++) {
                const empty = document.createElement('div');
                empty.className = 'cal-day';
                empty.style.opacity = '0.35';
                empty.style.background = '#faf8f5';
                grid.appendChild(empty);
            }

            const now = new Date();
            const todayYear = now.getFullYear();
            const todayMonth = now.getMonth();
            const todayDate = now.getDate();

            // Render actual month days
            for (let day = 1; day <= daysInMonth; day++) {
                const isToday = (year === todayYear && month === todayMonth && day === todayDate);
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const dayCell = document.createElement('div');
                dayCell.className = `cal-day ${isToday ? 'today' : ''}`;
                dayCell.innerHTML = `<div class="cal-num">${day}</div>`;

                const dayActs = activities.filter(a => a.deadline.startsWith(dateStr));
                dayActs.forEach(act => {
                    const cat = categories.find(c => Number(c.id) === Number(act.kategoriId)) || { color: '#3b82f6' };
                    const dlDate = new Date(act.deadline);
                    const jamStr = !isNaN(dlDate.getTime()) ? dlDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '';
                    const isDone = (act.status === 'Selesai');

                    const ev = document.createElement('a');
                    ev.className = `cal-event ${isDone ? 'cal-event-done' : ''}`;
                    ev.href = `aktivitas.php?highlight=${act.id}`;
                    ev.title = `${act.judul} (${jamStr}) ${isDone ? '[Selesai]' : ''}`;
                    
                    if (isDone) {
                        ev.style.cssText = 'background: #f0fdf4; color: #15803d; border-left: 3px solid #10b981;';
                        ev.innerHTML = `<span style="font-weight:700; color:#10b981; flex-shrink:0;"><i class="fas fa-check-circle"></i> ${jamStr}</span><span class="cal-event-title">${escapeHtml(act.judul)}</span>`;
                    } else {
                        ev.style.borderLeftColor = cat.color;
                        ev.innerHTML = `<span style="font-weight:700; color:${cat.color}; flex-shrink:0;"><i class="far fa-clock"></i> ${jamStr}</span><span class="cal-event-title">${escapeHtml(act.judul)}</span>`;
                    }
                    dayCell.appendChild(ev);
                });
                grid.appendChild(dayCell);
            }

            // Render trailing empty cells to complete the 7-column grid row
            const totalCells = emptyOffset + daysInMonth;
            const trailingCount = (7 - (totalCells % 7)) % 7;
            for (let i = 0; i < trailingCount; i++) {
                const empty = document.createElement('div');
                empty.className = 'cal-day';
                empty.style.opacity = '0.35';
                empty.style.background = '#faf8f5';
                grid.appendChild(empty);
            }
        }

        function escapeHtml(str) {
            return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
</body>
</html>
