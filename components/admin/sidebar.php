<aside class="sidebar">
    <nav class="sidebar-menu">
        <a href="../dashboard/index.php" class="menu-item" id="sidebar-dashboard">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <a href="../products/index.php" class="menu-item" id="sidebar-products">
            <i class="bi bi-box-seam"></i> Products
        </a>
        <a href="../categories/index.php" class="menu-item" id="sidebar-categories">
            <i class="bi bi-grid"></i> Categories
        </a>
        <!-- Diperbaiki: href ke index.php dan id menjadi sidebar-users -->
        <a href="../users/index.php" class="menu-item" id="sidebar-users">
            <i class="bi bi-person"></i> Users
        </a>
        <a href="../profile/edit.php" class="menu-item" id="sidebar-profile">
            <i class="bi bi-person-vcard"></i> Profile
        </a>
    </nav>
</aside>

<script>
    let pathname = window.location.pathname.split("/")[3];
    let sidebarMenu = document.getElementById(`sidebar-${pathname}`);
    if (sidebarMenu) {
        sidebarMenu.classList.add("active");
    }
</script>
