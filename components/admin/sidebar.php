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
        <a href="../profile/edit.php" class="menu-item" id="sidebar-profile">
            <i class="bi bi-person-vcard"></i> Profile
        </a>
    </nav>
</aside>

<script>
    let pathname = window.location.pathname.split("/")[3];
    let sidebarMenu = document.getElementById(`sidebar-${pathname}`);
    sidebarMenu.classList.add("active");
</script>