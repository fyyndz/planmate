@auth
@php
    $pmUser = auth()->user()->only(['id', 'name', 'username', 'email', 'jurusan', 'bio']);
    $pmGoals = \App\Models\Goal::forFrontend(auth()->id());
@endphp
<script>
    // Jembatan sementara: sinkronkan user yang login di server ke localStorage,
    // supaya halaman lama (yang masih memakai store.js) tetap jalan.
    try {
        localStorage.setItem('planmate_current_user', JSON.stringify({{ \Illuminate\Support\Js::from($pmUser) }}));
        // Goals dibaca dari MySQL, lalu disalin ke localStorage agar halaman lama (mis. dashboard) ikut menampilkannya
        localStorage.setItem('planmate_goals', JSON.stringify({{ \Illuminate\Support\Js::from($pmGoals) }}));
    } catch (e) {}
</script>
@endauth
<form id="logout-form" method="POST" action="/logout" style="display: none;">@csrf</form>

<aside class="app-sidebar">
    <a href="/dashboard" class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i class="fas fa-check"></i>
        </div>
        planmate<span>.</span>
    </a>

    <div class="sidebar-section-label">RUANG KERJA</div>

    <nav class="sidebar-nav">
        <a href="/dashboard" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Dashboard
        </a>

        <a href="/aktivitas" class="sidebar-link {{ request()->is('aktivitas') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i> Semua Aktivitas
        </a>

        <a href="/goals" class="sidebar-link {{ request()->is('goals') ? 'active' : '' }}">
            <i class="fas fa-bullseye"></i> Target &amp; Goals
        </a>

        <a href="/kategori" class="sidebar-link {{ request()->is('kategori') ? 'active' : '' }}">
            <i class="fas fa-tags"></i> Kategori &amp; Status
        </a>

        <a href="/profil" class="sidebar-link {{ request()->is('profil') ? 'active' : '' }}">
            <i class="fas fa-user-circle"></i> Profil Saya
        </a>
    </nav>

    <a href="/aktivitas?action=create" class="sidebar-btn-add">
        <i class="fas fa-plus"></i> Tambah aktivitas
    </a>

    <div class="sidebar-user-footer">
        <div class="sidebar-user-info">
            <div class="sidebar-avatar" id="sidebar-avatar">RA</div>
            <div>
                <div class="sidebar-user-name" id="sidebar-user-name">Mahasiswa</div>
                <div class="sidebar-user-role" id="sidebar-user-role">Mahasiswa</div>
            </div>
        </div>
        <button onclick="Store.logout()" class="sidebar-logout-btn" title="Keluar (Logout)">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </div>
</aside>