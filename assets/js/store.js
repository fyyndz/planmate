/**
 * PlanMate Store & Auth Engine
 * Storage: Browser LocalStorage
 */

const Store = {
  KEYS: {
    USERS: 'planmate_users',
    CURRENT_USER: 'planmate_current_user',
    ACTIVITIES: 'planmate_activities',
    CATEGORIES: 'planmate_categories',
    GOALS: 'planmate_goals'
  },

  /**
   * Pure JavaScript Standard SHA-256 implementation
   * Ensures secure, deterministic, synchronous password hashing
   */
  hashPassword(str) {
    if (typeof str !== 'string') str = String(str || '');
    function rotr(x, n) {
      return (x >>> n) | (x << (32 - n));
    }

    const K = [
      0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5,
      0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174,
      0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da,
      0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967,
      0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85,
      0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070,
      0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3,
      0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2
    ];

    let H = [
      0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a,
      0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19
    ];

    const utf8 = unescape(encodeURIComponent(str));
    const msgLen = utf8.length;
    const words = [];
    for (let i = 0; i < msgLen; i++) {
      words[i >>> 2] |= (utf8.charCodeAt(i) & 0xff) << (24 - (i % 4) * 8);
    }
    words[msgLen >>> 2] |= 0x80 << (24 - (msgLen % 4) * 8);
    words[(((msgLen + 8) >>> 6) << 4) + 15] = msgLen * 8;

    for (let i = 0; i < words.length; i += 16) {
      const W = new Array(64);
      for (let t = 0; t < 16; t++) W[t] = words[i + t] | 0;
      for (let t = 16; t < 64; t++) {
        const s0 = rotr(W[t - 15], 7) ^ rotr(W[t - 15], 18) ^ (W[t - 15] >>> 3);
        const s1 = rotr(W[t - 2], 17) ^ rotr(W[t - 2], 19) ^ (W[t - 2] >>> 10);
        W[t] = (W[t - 16] + s0 + W[t - 7] + s1) | 0;
      }

      let a = H[0], b = H[1], c = H[2], d = H[3], e = H[4], f = H[5], g = H[6], h = H[7];

      for (let t = 0; t < 64; t++) {
        const S1 = rotr(e, 6) ^ rotr(e, 11) ^ rotr(e, 25);
        const ch = (e & f) ^ ((~e) & g);
        const temp1 = (h + S1 + ch + K[t] + W[t]) | 0;
        const S0 = rotr(a, 2) ^ rotr(a, 13) ^ rotr(a, 22);
        const maj = (a & b) ^ (a & c) ^ (b & c);
        const temp2 = (S0 + maj) | 0;

        h = g;
        g = f;
        f = e;
        e = (d + temp1) | 0;
        d = c;
        c = b;
        b = a;
        a = (temp1 + temp2) | 0;
      }

      H[0] = (H[0] + a) | 0;
      H[1] = (H[1] + b) | 0;
      H[2] = (H[2] + c) | 0;
      H[3] = (H[3] + d) | 0;
      H[4] = (H[4] + e) | 0;
      H[5] = (H[5] + f) | 0;
      H[6] = (H[6] + g) | 0;
      H[7] = (H[7] + h) | 0;
    }

    return H.map(v => ('00000000' + (v >>> 0).toString(16)).slice(-8)).join('');
  },

  init() {
    // 1. Inisialisasi Users
    if (!localStorage.getItem(this.KEYS.USERS)) {
      const defaultUsers = [{
        id: 1,
        name: 'Mahasiswa PlanMate',
        username: 'mahasiswa',
        email: 'mahasiswa@student.ac.id',
        password: this.hashPassword('password123'),
        jurusan: 'Teknik Informatika',
        bio: 'Semangat belajar & mengerjakan tugas perkuliahan!'
      }];
      localStorage.setItem(this.KEYS.USERS, JSON.stringify(defaultUsers));
    } else {
      // Auto-migrate: pastikan semua user tersimpan dengan password ter-hash
      try {
        const existingUsers = this.getUsers();
        let migrated = false;
        existingUsers.forEach(u => {
          if (u.password && u.password.length < 64) {
            u.password = this.hashPassword(u.password);
            migrated = true;
          }
        });
        if (migrated) {
          localStorage.setItem(this.KEYS.USERS, JSON.stringify(existingUsers));
        }
      } catch (e) {
        console.error('Migration error:', e);
      }
    }

    // 2. Inisialisasi Kategori
    if (!localStorage.getItem(this.KEYS.CATEGORIES)) {
      const defaultCategories = [
        { id: 1, name: 'Kuliah', color: '#3b82f6', description: 'Tugas perkuliahan, kuis, dan ujian' },
        { id: 2, name: 'Organisasi', color: '#ec4899', description: 'Kegiatan BEM, himpunan, dan kepanitiaan' },
        { id: 3, name: 'Pribadi', color: '#10b981', description: 'Kebiasaan dan catatan harian' },
        { id: 4, name: 'Pekerjaan', color: '#f59e0b', description: 'Part-time, freelance, dan magang' },
        { id: 5, name: 'Lainnya', color: '#8b5cf6', description: 'Kegiatan pendukung dan hobi' }
      ];
      localStorage.setItem(this.KEYS.CATEGORIES, JSON.stringify(defaultCategories));
    }

    // 3. Inisialisasi Aktivitas
    if (!localStorage.getItem(this.KEYS.ACTIVITIES)) {
      const now = new Date();
      const in2Days = new Date(now.getTime() + 2 * 24 * 60 * 60 * 1000).toISOString().slice(0, 16);
      const in4Days = new Date(now.getTime() + 4 * 24 * 60 * 60 * 1000).toISOString().slice(0, 16);
      const in1Day  = new Date(now.getTime() + 1 * 24 * 60 * 60 * 1000).toISOString().slice(0, 16);

      const defaultActivities = [
        {
          id: 1,
          userId: 1,
          judul: 'Laporan Praktikum Antarmuka Pengguna',
          kategoriId: 1,
          deskripsi: 'Membuat website sistem manajemen tugas berbasis PHP, CSS & LocalStorage browser.',
          deadline: in2Days,
          prioritas: 'Tinggi',
          status: 'Sedang Dikerjakan'
        },
        {
          id: 2,
          userId: 1,
          judul: 'Rapat Panitia Seminar Nasional Tech 2026',
          kategoriId: 2,
          deskripsi: 'Membahas progres divisi konsumsi dan rundown acara utama.',
          deadline: in4Days,
          prioritas: 'Sedang',
          status: 'Belum Dimulai'
        },
        {
          id: 3,
          userId: 1,
          judul: 'Revisi Proposal Skripsi Bab 1 & 2',
          kategoriId: 1,
          deskripsi: 'Memperbaiki batasan masalah dan tinjauan pustaka sesuai masukan dospem.',
          deadline: in1Day,
          prioritas: 'Tinggi',
          status: 'Sedang Dikerjakan'
        }
      ];
      localStorage.setItem(this.KEYS.ACTIVITIES, JSON.stringify(defaultActivities));
    }

    // 4. Inisialisasi Goals (Target & Pencapaian)
    if (!localStorage.getItem(this.KEYS.GOALS)) {
      const now = new Date();
      const in5Days = new Date(now.getTime() + 5 * 24 * 60 * 60 * 1000).toISOString().slice(0, 16);
      const in14Days = new Date(now.getTime() + 14 * 24 * 60 * 60 * 1000).toISOString().slice(0, 16);

      const defaultGoals = [
        {
          id: 1,
          userId: 1,
          aktivitasId: 1,
          judul: 'Selesaikan Praktikum Antarmuka Pengguna Modul 1-4',
          deskripsi: 'Menuntaskan seluruh modul praktikum, integrasi fitur, dan laporan akhir.',
          deadline: in5Days,
          progres: 75,
          targetKecil: [
            { id: 1, text: 'Review wireframe & UI layout', done: true },
            { id: 2, text: 'Perbaiki alur autentikasi & validasi data', done: true },
            { id: 3, text: 'Implementasi manajemen target & progres', done: true },
            { id: 4, text: 'Finalisasi laporan praktikum & presentasi', done: false }
          ],
          createdAt: new Date().toISOString()
        },
        {
          id: 2,
          userId: 1,
          aktivitasId: null,
          judul: 'Target Belajar Mandiri: Menguasai SQL & Query Optimization',
          deskripsi: 'Mempelajari query kompleks, relasi normalisasi, dan indexing basis data.',
          deadline: in14Days,
          progres: 50,
          targetKecil: [
            { id: 1, text: 'Belajar relasi tabel & normalisasi 1NF-3NF', done: true },
            { id: 2, text: 'Latihan query JOIN, GROUP BY, dan HAVING', done: true },
            { id: 3, text: 'Pahami query execution plan & indexing', done: false },
            { id: 4, text: 'Latihan simulasi studi kasus transaksi kasir', done: false }
          ],
          createdAt: new Date().toISOString()
        }
      ];
      localStorage.setItem(this.KEYS.GOALS, JSON.stringify(defaultGoals));
    }
  },

  // Auth Methods
  getUsers() {
    try {
      return JSON.parse(localStorage.getItem(this.KEYS.USERS) || '[]');
    } catch (e) {
      return [];
    }
  },

  getCurrentUser() {
    try {
      return JSON.parse(localStorage.getItem(this.KEYS.CURRENT_USER) || 'null');
    } catch (e) {
      return null;
    }
  },

  setCurrentUser(user) {
    if (user) {
      // Simpan salinan tanpa properti sensitif jika perlu
      const cleanUser = { ...user };
      localStorage.setItem(this.KEYS.CURRENT_USER, JSON.stringify(cleanUser));
    } else {
      localStorage.removeItem(this.KEYS.CURRENT_USER);
    }
  },

  registerUser(data) {
    // Validasi data input
    if (!data.name || !data.name.trim()) {
      return { success: false, message: 'Nama lengkap wajib diisi!' };
    }
    if (!data.username || !data.username.trim()) {
      return { success: false, message: 'Username / NIM wajib diisi!' };
    }
    if (!data.jurusan || !data.jurusan.trim()) {
      return { success: false, message: 'Jurusan wajib diisi!' };
    }
    if (!data.email || !data.email.trim()) {
      return { success: false, message: 'Email wajib diisi!' };
    }
    if (!data.password || data.password.length < 6) {
      return { success: false, message: 'Kata sandi minimal harus 6 karakter!' };
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(data.email.trim())) {
      return { success: false, message: 'Format alamat email tidak valid!' };
    }

    const users = this.getUsers();
    const cleanEmail = data.email.trim().toLowerCase();
    const cleanUsername = data.username.trim().toLowerCase();

    if (users.some(u => (u.email || '').toLowerCase() === cleanEmail)) {
      return { success: false, message: 'Email sudah terdaftar! Silakan gunakan email lain atau masuk.' };
    }
    if (users.some(u => (u.username || '').toLowerCase() === cleanUsername)) {
      return { success: false, message: 'Username / NIM sudah digunakan! Silakan gunakan yang lain.' };
    }

    const newUser = {
      id: Date.now(),
      name: data.name.trim(),
      username: data.username.trim(),
      jurusan: data.jurusan.trim(),
      email: data.email.trim(),
      password: this.hashPassword(data.password),
      bio: data.bio || 'Mahasiswa Aktif'
    };

    users.push(newUser);
    localStorage.setItem(this.KEYS.USERS, JSON.stringify(users));
    return { success: true, user: newUser };
  },

  loginUser(identifier, password) {
    if (!identifier || !identifier.trim()) {
      return { success: false, message: 'Silakan masukkan email atau username/NIM!' };
    }
    if (!password) {
      return { success: false, message: 'Silakan masukkan kata sandi Anda!' };
    }

    const cleanId = identifier.trim().toLowerCase();
    const hashedInput = this.hashPassword(password);
    const users = this.getUsers();

    // Cari user berdasarkan email ATAU username/NIM
    const user = users.find(u => {
      const uEmail = (u.email || '').toLowerCase();
      const uUser = (u.username || '').toLowerCase();
      const idMatch = (uEmail === cleanId || uUser === cleanId);
      const passMatch = (u.password === hashedInput || u.password === password);
      return idMatch && passMatch;
    });

    if (user) {
      // Auto-upgrade bila password masih plaintext
      if (user.password !== hashedInput) {
        user.password = hashedInput;
        const idx = users.findIndex(u => u.id === user.id);
        if (idx !== -1) {
          users[idx] = user;
          localStorage.setItem(this.KEYS.USERS, JSON.stringify(users));
        }
      }
      this.setCurrentUser(user);
      return { success: true, user };
    }

    return { success: false, message: 'Email/Username atau kata sandi tidak cocok!' };
  },

  updateCurrentUser(data) {
    let curr = this.getCurrentUser();
    if (!curr) return false;
    curr = { ...curr, ...data };
    this.setCurrentUser(curr);

    const users = this.getUsers();
    const idx = users.findIndex(u => u.id === curr.id);
    if (idx !== -1) {
      users[idx] = curr;
      localStorage.setItem(this.KEYS.USERS, JSON.stringify(users));
    }
    return true;
  },

  logout() {
    if (confirm('Apakah Anda yakin ingin keluar dari akun PlanMate?')) {
      localStorage.removeItem(this.KEYS.CURRENT_USER);
      window.location.href = 'login.php';
    }
  },

  requireAuth() {
    if (!this.getCurrentUser()) {
      window.location.href = 'login.php';
    }
  },

  requireGuest() {
    if (this.getCurrentUser()) {
      window.location.href = 'dashboard.php';
    }
  },

  // Categories CRUD
  getCategories() {
    try {
      return JSON.parse(localStorage.getItem(this.KEYS.CATEGORIES) || '[]');
    } catch (e) {
      return [];
    }
  },

  addCategory(data) {
    const list = this.getCategories();
    const newItem = {
      id: Date.now(),
      name: data.name,
      color: data.color || '#4f46e5',
      description: data.description || ''
    };
    list.push(newItem);
    localStorage.setItem(this.KEYS.CATEGORIES, JSON.stringify(list));
    return newItem;
  },

  updateCategory(id, data) {
    const list = this.getCategories();
    const idx = list.findIndex(c => Number(c.id) === Number(id));
    if (idx !== -1) {
      list[idx] = { ...list[idx], ...data };
      localStorage.setItem(this.KEYS.CATEGORIES, JSON.stringify(list));
    }
  },

  deleteCategory(id) {
    const list = this.getCategories().filter(c => Number(c.id) !== Number(id));
    localStorage.setItem(this.KEYS.CATEGORIES, JSON.stringify(list));
  },

  // Activities CRUD & Helper
  getPriorityFromDeadline(deadlineStr) {
    if (!deadlineStr) return 'Sedang';
    const deadlineDate = new Date(deadlineStr);
    const now = new Date();
    const diffHours = (deadlineDate - now) / (1000 * 60 * 60);

    if (diffHours <= 24) {
      return 'Tinggi';
    } else if (diffHours <= 72) {
      return 'Sedang';
    } else {
      return 'Rendah';
    }
  },

  getActivities() {
    const user = this.getCurrentUser();
    if (!user) return [];
    try {
      const all = JSON.parse(localStorage.getItem(this.KEYS.ACTIVITIES) || '[]');
      const userActs = all.filter(a => Number(a.userId) === Number(user.id));
      return userActs.map(a => ({
        ...a,
        prioritas: this.getPriorityFromDeadline(a.deadline)
      }));
    } catch (e) {
      return [];
    }
  },

  getActivity(id) {
    return this.getActivities().find(a => Number(a.id) === Number(id)) || null;
  },

  addActivity(data) {
    const user = this.getCurrentUser();
    if (!user) return null;
    const all = JSON.parse(localStorage.getItem(this.KEYS.ACTIVITIES) || '[]');
    const computedPrio = this.getPriorityFromDeadline(data.deadline);
    const newItem = {
      id: Date.now(),
      userId: user.id,
      judul: data.judul,
      kategoriId: Number(data.kategoriId),
      deskripsi: data.deskripsi || '',
      deadline: data.deadline,
      prioritas: computedPrio,
      status: data.status || 'Belum Dimulai'
    };
    all.push(newItem);
    localStorage.setItem(this.KEYS.ACTIVITIES, JSON.stringify(all));
    return newItem;
  },

  updateActivity(id, data) {
    const all = JSON.parse(localStorage.getItem(this.KEYS.ACTIVITIES) || '[]');
    const idx = all.findIndex(a => Number(a.id) === Number(id));
    if (idx !== -1) {
      const computedPrio = data.deadline ? this.getPriorityFromDeadline(data.deadline) : (all[idx].prioritas || 'Sedang');
      all[idx] = { ...all[idx], ...data, prioritas: computedPrio, id: Number(id) };
      localStorage.setItem(this.KEYS.ACTIVITIES, JSON.stringify(all));
    }
  },

  deleteActivity(id) {
    const all = JSON.parse(localStorage.getItem(this.KEYS.ACTIVITIES) || '[]').filter(a => Number(a.id) !== Number(id));
    localStorage.setItem(this.KEYS.ACTIVITIES, JSON.stringify(all));

    // Integrasi: Lepaskan relasi aktivitas pada Goals secara aman
    try {
      const goals = JSON.parse(localStorage.getItem(this.KEYS.GOALS) || '[]');
      let changed = false;
      goals.forEach(g => {
        if (Number(g.aktivitasId) === Number(id)) {
          g.aktivitasId = null;
          changed = true;
        }
      });
      if (changed) {
        localStorage.setItem(this.KEYS.GOALS, JSON.stringify(goals));
      }
    } catch (e) {
      console.error('Error unlinking goals:', e);
    }
  },

  // ==========================================
  // GOALS CRUD & PROGRESS MANAGEMENT
  // ==========================================
  getGoals() {
    const user = this.getCurrentUser();
    if (!user) return [];
    try {
      const allGoals = JSON.parse(localStorage.getItem(this.KEYS.GOALS) || '[]');
      const userGoals = allGoals.filter(g => Number(g.userId) === Number(user.id));
      const userActivities = this.getActivities();

      // Kaitkan relasi aktivitas jika ada
      return userGoals.map(g => {
        const linkedAct = g.aktivitasId ? userActivities.find(a => Number(a.id) === Number(g.aktivitasId)) : null;
        return {
          ...g,
          aktivitas: linkedAct || null
        };
      });
    } catch (e) {
      return [];
    }
  },

  getGoal(id) {
    const goals = this.getGoals();
    return goals.find(g => Number(g.id) === Number(id)) || null;
  },

  addGoal(data) {
    const user = this.getCurrentUser();
    if (!user) return null;
    if (!data.judul || !data.judul.trim()) return null;

    const allGoals = JSON.parse(localStorage.getItem(this.KEYS.GOALS) || '[]');
    const subtargets = Array.isArray(data.targetKecil) ? data.targetKecil : [];

    // Hitung progres awal
    let calculatedProgress = Number(data.progres) || 0;
    if (subtargets.length > 0) {
      const doneCount = subtargets.filter(st => st.done).length;
      calculatedProgress = Math.round((doneCount / subtargets.length) * 100);
    }
    calculatedProgress = Math.min(100, Math.max(0, calculatedProgress));

    const newGoal = {
      id: Date.now(),
      userId: user.id,
      judul: data.judul.trim(),
      deskripsi: (data.deskripsi || '').trim(),
      deadline: data.deadline || '',
      aktivitasId: data.aktivitasId ? Number(data.aktivitasId) : null,
      progres: calculatedProgress,
      targetKecil: subtargets,
      createdAt: new Date().toISOString()
    };

    allGoals.push(newGoal);
    localStorage.setItem(this.KEYS.GOALS, JSON.stringify(allGoals));
    return newGoal;
  },

  updateGoal(id, data) {
    const user = this.getCurrentUser();
    if (!user) return false;

    const allGoals = JSON.parse(localStorage.getItem(this.KEYS.GOALS) || '[]');
    const idx = allGoals.findIndex(g => Number(g.id) === Number(id) && Number(g.userId) === Number(user.id));
    if (idx === -1) return false;

    const currentGoal = allGoals[idx];
    const subtargets = Array.isArray(data.targetKecil) ? data.targetKecil : (currentGoal.targetKecil || []);

    let newProgress = data.progres !== undefined ? Number(data.progres) : currentGoal.progres;
    if (subtargets.length > 0 && data.targetKecil !== undefined) {
      const doneCount = subtargets.filter(st => st.done).length;
      newProgress = Math.round((doneCount / subtargets.length) * 100);
    }
    newProgress = Math.min(100, Math.max(0, newProgress));

    allGoals[idx] = {
      ...currentGoal,
      ...data,
      id: Number(id),
      userId: user.id,
      progres: newProgress,
      targetKecil: subtargets,
      aktivitasId: data.aktivitasId ? Number(data.aktivitasId) : (data.aktivitasId === null ? null : currentGoal.aktivitasId)
    };

    localStorage.setItem(this.KEYS.GOALS, JSON.stringify(allGoals));
    return allGoals[idx];
  },

  deleteGoal(id) {
    const user = this.getCurrentUser();
    if (!user) return false;

    const allGoals = JSON.parse(localStorage.getItem(this.KEYS.GOALS) || '[]');
    const filtered = allGoals.filter(g => !(Number(g.id) === Number(id) && Number(g.userId) === Number(user.id)));
    localStorage.setItem(this.KEYS.GOALS, JSON.stringify(filtered));
    return true;
  },

  updateGoalProgress(id, progress) {
    const user = this.getCurrentUser();
    if (!user) return false;

    const allGoals = JSON.parse(localStorage.getItem(this.KEYS.GOALS) || '[]');
    const idx = allGoals.findIndex(g => Number(g.id) === Number(id) && Number(g.userId) === Number(user.id));
    if (idx === -1) return false;

    const clampedProgress = Math.min(100, Math.max(0, Number(progress) || 0));
    allGoals[idx].progres = clampedProgress;

    // Jika memiliki tahapan kecil, sinkronisasikan tahapan secara proporsional bila di-set ke 100% atau 0%
    if (Array.isArray(allGoals[idx].targetKecil) && allGoals[idx].targetKecil.length > 0) {
      if (clampedProgress === 100) {
        allGoals[idx].targetKecil.forEach(st => st.done = true);
      } else if (clampedProgress === 0) {
        allGoals[idx].targetKecil.forEach(st => st.done = false);
      }
    }

    localStorage.setItem(this.KEYS.GOALS, JSON.stringify(allGoals));
    return true;
  },

  toggleGoalSubtarget(goalId, subtargetId) {
    const user = this.getCurrentUser();
    if (!user) return false;

    const allGoals = JSON.parse(localStorage.getItem(this.KEYS.GOALS) || '[]');
    const goalIdx = allGoals.findIndex(g => Number(g.id) === Number(goalId) && Number(g.userId) === Number(user.id));
    if (goalIdx === -1) return false;

    const goal = allGoals[goalIdx];
    if (!Array.isArray(goal.targetKecil)) goal.targetKecil = [];

    const st = goal.targetKecil.find(s => Number(s.id) === Number(subtargetId));
    if (st) {
      st.done = !st.done;
      const doneCount = goal.targetKecil.filter(s => s.done).length;
      goal.progres = Math.round((doneCount / goal.targetKecil.length) * 100);
      localStorage.setItem(this.KEYS.GOALS, JSON.stringify(allGoals));
      return goal;
    }
    return false;
  }
};

// Eksekusi inisialisasi awal
Store.init();
