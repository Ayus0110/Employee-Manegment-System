document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("leaveUserSearch");
    const rows = document.querySelectorAll("#leaveUserTable tbody tr");

    if (!input) {
        return;
    }

    input.addEventListener("input", function () {
        const term = this.value.toLowerCase().trim();

        rows.forEach(function (row) {
            row.style.display = row.innerText.toLowerCase().includes(term) ? "" : "none";
        });
    });
});
