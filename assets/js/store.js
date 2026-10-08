/**
 * PlanMate Store & Auth Engine
 * Storage: Browser LocalStorage
 */

const Store = {
  KEYS: {
    USERS: 'planmate_users',
    CURRENT_USER: 'planmate_current_user',
    ACTIVITIES: 'planmate_activities',
    CATEGORIES: 'planmate_categories'
  },

  init() {
    if (!localStorage.getItem(this.KEYS.USERS)) {
      const defaultUsers = [{
        id: 1,
        name: 'Mahasiswa PlanMate',
        username: 'mahasiswa',
        email: 'mahasiswa@student.ac.id',
        password: 'password123',
        jurusan: 'Teknik Informatika',
        bio: 'Semangat belajar & mengerjakan tugas perkuliahan!'
      }];
      localStorage.setItem(this.KEYS.USERS, JSON.stringify(defaultUsers));
    }

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
  },

  // Auth Methods
  getUsers() { return JSON.parse(localStorage.getItem(this.KEYS.USERS) || '[]'); },
  getCurrentUser() { return JSON.parse(localStorage.getItem(this.KEYS.CURRENT_USER) || 'null'); },
  setCurrentUser(user) { localStorage.setItem(this.KEYS.CURRENT_USER, JSON.stringify(user)); },

  registerUser(data) {
    const users = this.getUsers();
    if (users.find(u => u.email === data.email || u.username === data.username)) {
      return { success: false, message: 'Email atau Username sudah terdaftar!' };
    }
    const newUser = {
      id: Date.now(),
      name: data.name,
      username: data.username,
      email: data.email,
      password: data.password,
      jurusan: data.jurusan || 'Teknik Informatika',
      bio: data.bio || 'Mahasiswa Aktif'
    };
    users.push(newUser);
    localStorage.setItem(this.KEYS.USERS, JSON.stringify(users));
    this.setCurrentUser(newUser);
    return { success: true, user: newUser };
  },

  loginUser(identifier, password) {
    const users = this.getUsers();
    const user = users.find(u => (u.email === identifier || u.username === identifier) && u.password === password);
    if (user) {
      this.setCurrentUser(user);
      return { success: true, user };
    }
    return { success: false, message: 'Username/Email atau Password salah!' };
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
  getCategories() { return JSON.parse(localStorage.getItem(this.KEYS.CATEGORIES) || '[]'); },
  addCategory(data) {
    const list = this.getCategories();
    const newItem = { id: Date.now(), name: data.name, color: data.color || '#4f46e5', description: data.description || '' };
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

  // Activities CRUD
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
    const all = JSON.parse(localStorage.getItem(this.KEYS.ACTIVITIES) || '[]');
    if (!user) return [];
    const userActs = all.filter(a => Number(a.userId) === Number(user.id));
    return userActs.map(a => ({
      ...a,
      prioritas: this.getPriorityFromDeadline(a.deadline)
    }));
  },
  getActivity(id) {
    return this.getActivities().find(a => Number(a.id) === Number(id)) || null;
  },
  addActivity(data) {
    const user = this.getCurrentUser();
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
  }
};

Store.init();
