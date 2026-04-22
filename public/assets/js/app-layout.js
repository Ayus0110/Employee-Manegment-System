document.addEventListener("DOMContentLoaded", function () {
    const notificationToggle = document.getElementById("notificationToggle");
    const notificationMenu = document.getElementById("notificationMenu");
    const profileToggle = document.getElementById("profileToggle");
    const profileMenu = document.getElementById("profileMenu");
    const sidebar = document.getElementById("sidebar");
    const sidebarToggle = document.getElementById("mobileMenuToggle");
    const sidebarBackdrop = document.getElementById("sidebarBackdrop");

    const closeSidebar = function () {
        if (window.innerWidth <= 768 && sidebar && sidebarBackdrop) {
            sidebar.classList.remove("show");
            sidebarBackdrop.classList.remove("show");
            document.body.classList.remove("sidebar-open");
        }
    };

    if (sidebarToggle && sidebar && sidebarBackdrop) {
        sidebarToggle.addEventListener("click", function (event) {
            event.stopPropagation();
            const willOpen = !sidebar.classList.contains("show");
            sidebar.classList.toggle("show", willOpen);
            sidebarBackdrop.classList.toggle("show", willOpen);
            document.body.classList.toggle("sidebar-open", willOpen);
        });

        sidebarBackdrop.addEventListener("click", closeSidebar);
    }

    if (notificationToggle && notificationMenu) {
        notificationToggle.addEventListener("click", function (event) {
            event.stopPropagation();
            notificationMenu.classList.toggle("show");
            if (profileMenu) {
                profileMenu.classList.remove("show");
            }
        });
    }

    if (profileToggle && profileMenu) {
        profileToggle.addEventListener("click", function (event) {
            event.stopPropagation();
            profileMenu.classList.toggle("show");
            if (notificationMenu) {
                notificationMenu.classList.remove("show");
            }
        });
    }

    document.addEventListener("click", function (event) {
        if (
            notificationMenu &&
            notificationToggle &&
            !notificationMenu.contains(event.target) &&
            !notificationToggle.contains(event.target)
        ) {
            notificationMenu.classList.remove("show");
        }

        if (
            profileMenu &&
            profileToggle &&
            !profileMenu.contains(event.target) &&
            !profileToggle.contains(event.target)
        ) {
            profileMenu.classList.remove("show");
        }
    });

    window.addEventListener("resize", function () {
        if (window.innerWidth > 768) {
            if (sidebar) {
                sidebar.classList.remove("show");
            }
            if (sidebarBackdrop) {
                sidebarBackdrop.classList.remove("show");
            }
            document.body.classList.remove("sidebar-open");
        }
    });

    document.querySelectorAll(".sidebar a, .sidebar form button").forEach(function (item) {
        item.addEventListener("click", closeSidebar);
    });
});
