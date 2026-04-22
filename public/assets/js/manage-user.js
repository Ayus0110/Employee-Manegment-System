document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".editUserBtn");
    const editForm = document.getElementById("editUserForm");
    const searchInput = document.getElementById("searchUsers");
    const tableBody = document.getElementById("usersTableBody");
    const userConfig = document.getElementById("manageUserConfig");
    const updateBaseUrl = userConfig ? userConfig.dataset.updateBaseUrl : "/manage-user";

    editButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            editForm.action = `${updateBaseUrl}/${this.dataset.id}`;
            document.getElementById("edit_name").value = this.dataset.name;
            document.getElementById("edit_email").value = this.dataset.email;
            document.getElementById("edit_phone").value = this.dataset.phone;
            document.getElementById("edit_role").value = this.dataset.role;
        });
    });

    if (searchInput && tableBody) {
        searchInput.addEventListener("input", function () {
            const query = this.value.trim().toLowerCase();
            const rows = tableBody.querySelectorAll("tr");

            rows.forEach(function (row) {
                if (row.querySelector(".empty-state")) {
                    return;
                }

                row.style.display = row.innerText.toLowerCase().includes(query) ? "" : "none";
            });
        });
    }
});
