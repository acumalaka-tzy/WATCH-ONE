<?php
// inc/header.php

// Cek session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$isAdmin = ($user && isset($user['role']) && $user['role'] === 'admin');
?>

<nav class="navbar">
    
    <div class="nav-normal" id="navNormal">
        <a href="./index.php" class="logo">
            <span class="red-dot"></span> WATCHONE
        </a>
        
        <ul class="nav-links">
            <li><a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Home</a></li>
            <li><a href="category.php" class="<?= $current_page == 'category.php' ? 'active' : '' ?>">Categories</a></li>
        </ul>

        <div class="nav-actions">
            <button type="button" class="btn-glass mobile-search-btn" onclick="openSearch()">
                <i class="fas fa-search"></i>
            </button>

            <form action="search.php" method="get" class="search-form-desktop">
                <input type="text" name="q" placeholder="Search..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>

            <?php if($user): ?>
                <div class="profile-dropdown" tabindex="0">
                    <img src="assets/img/profil.png" class="profile-img" alt="Profile">
                    
                    <div class="dropdown-menu">
                        <div style="padding:10px 20px; border-bottom:1px solid rgba(255,255,255,0.1); margin-bottom:5px;">
                            <div style="font-weight:bold; color:white;"><?= htmlspecialchars($user['name']) ?></div>
                            <div style="font-size:0.75rem; color:#888;"><?= htmlspecialchars($user['email']) ?></div>
                        </div>

                        <?php if($isAdmin): ?>
                            <div class="dropdown-header">Admin Zone</div>
                                <a href="index1.php"><i class="fas fa-tachometer-alt"></i> Admin Panel</a>
                                <a href="films_list.php"><i class="fas fa-film"></i> Manage Films</a>
                                <a href="users.php"><i class="fas fa-users"></i> Manage Users</a>
                                <a href="categories.php"><i class="fas fa-tags"></i> Manage Categories</a>
                            <div class="dropdown-divider"></div>
                        <?php endif; ?>

                        <a href="favorites.php"><i class="fas fa-heart"></i> Favorites</a>
                        <a href="history.php"><i class="fas fa-history"></i> History</a>
                        <a href="acc_settings.php"><i class="fas fa-cog"></i> Settings</a>
                        
                        <div class="dropdown-divider"></div>
                        
                        <a href="logout.php" style="color:#ff4d4f;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-glass" style="padding:8px 20px; border-radius:20px; font-size:0.85rem; text-decoration:none;">Login</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="nav-search-overlay" id="navSearchOverlay" style="display: none;">
        <form action="search.php" method="get" style="width: 100%; display: flex; align-items: center;">
            <input type="text" name="q" id="mobileSearchInput" placeholder="Cari film..." autocomplete="off">
            <button type="submit" class="btn-search-submit"><i class="fas fa-search"></i></button>
        </form>
        <button type="button" class="btn-close-search" onclick="closeSearch()">
            <i class="fas fa-times"></i>
        </button>
    </div>

</nav>

<script>
function openSearch() {
    // Sembunyikan navbar normal
    document.getElementById('navNormal').style.display = 'none';
    // Munculkan overlay search
    document.getElementById('navSearchOverlay').style.display = 'flex';
    // Langsung fokus ke input biar keyboard HP naik
    document.getElementById('mobileSearchInput').focus();
}

function closeSearch() {
    // Sembunyikan overlay search
    document.getElementById('navSearchOverlay').style.display = 'none';
    // Munculkan navbar normal lagi
    document.getElementById('navNormal').style.display = 'flex';
}
</script>