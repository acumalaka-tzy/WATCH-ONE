<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
$user = current_user();
?>

<header class="site-header">
    <div class="container navbar">

        <!-- Logo → acts as navbar toggle -->
        <button class="logo-btn" id="navToggle" aria-expanded="false">
            WatchOne <span class="caret">▾</span>
        </button>

        <!-- NAVIGATION -->
        <nav class="nav-menu" id="navMenu">
            <a href="/ppw/TUBES/index.php">Home</a>
            <a href="/ppw/TUBES/category.php">Kategori</a>

            <?php if ($user): ?>
                <a href="/ppw/TUBES/favorites.php">Favorit</a>
                <a href="ppw/TUBES/watch_later.php">Watch Later</a>
                


                <?php if ($user['role'] === 'admin'): ?>
                    <a href="/ppw/TUBES/admin/index.php">Admin Panel</a>
                <?php endif; ?>

                <a href="/ppw/TUBES/logout.php">Logout</a>
            <?php else: ?>
                <a href="/ppw/TUBES/login.php">Login</a>
                <a href="/ppw/TUBES/register.php">Register</a>
            <?php endif; ?>
        </nav>

    </div>
</header>

<script>
/* NAV MENU TOGGLE */
const toggleBtn = document.getElementById("navToggle");
const menu = document.getElementById("navMenu");

function closeMenu(){
    menu.classList.remove("active");
    toggleBtn.classList.remove("open");
    toggleBtn.setAttribute("aria-expanded", "false");
}

toggleBtn.addEventListener("click", () => {
    const isOpen = menu.classList.contains("active");
    if (isOpen) { closeMenu(); }
    else {
        menu.classList.add("active");
        toggleBtn.classList.add("open");
        toggleBtn.setAttribute("aria-expanded", "true");
    }
});

// Close when clicking outside
document.addEventListener("click", (e) => {
    if (!menu.contains(e.target) && !toggleBtn.contains(e.target)) closeMenu();
});

// Close on ESC
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeMenu();
});
</script>
