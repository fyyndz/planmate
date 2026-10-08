<?php
$current_file = basename($_SERVER['PHP_SELF']);
?>
<aside class="app-sidebar">
    <!-- Brand Logo -->
    <a href="dashboard.php" class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i class="fas fa-check"></i>
        </div>
        planmate<span>.</span>
    </a>

    <div class="sidebar-section-label">RUANG KERJA</div>

    <!-- Navigation Links for Each Feature File -->
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="sidebar-link <?php echo ($current_file === 'dashboard.php' || $current_file === 'index.php') ? 'active' : ''; ?>">
            <i class="fas fa-th-large"></i> Ringkasan
        </a>

        <a href="aktivitas.php" class="sidebar-link <?php echo $current_file === 'aktivitas.php' ? 'active' : ''; ?>">
            <i class="fas fa-clipboard-list"></i> Semua Aktivitas
        </a>

        <a href="deadline.php" class="sidebar-link <?php echo $current_file === 'deadline.php' ? 'active' : ''; ?>">
            <i class="fas fa-clock"></i> Deadline &amp; Prioritas
        </a>

        <a href="kategori.php" class="sidebar-link <?php echo $current_file === 'kategori.php' ? 'active' : ''; ?>">
            <i class="fas fa-tags"></i> Kategori &amp; Status
        </a>

        <a href="kalender.php" class="sidebar-link <?php echo $current_file === 'kalender.php' ? 'active' : ''; ?>">
            <i class="far fa-calendar-alt"></i> Kalender
        </a>

        <a href="pencarian.php" class="sidebar-link <?php echo $current_file === 'pencarian.php' ? 'active' : ''; ?>">
            <i class="fas fa-search"></i> Pencarian &amp; Filter
        </a>

        <a href="profil.php" class="sidebar-link <?php echo $current_file === 'profil.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-circle"></i> Profil Saya
        </a>
    </nav>

    <!-- Sidebar Add Activity Green Button -->
    <a href="aktivitas.php?action=create" class="sidebar-btn-add">
        <i class="fas fa-plus"></i> Tambah aktivitas
    </a>

    <!-- Sidebar User Footer -->
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
