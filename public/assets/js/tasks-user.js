document.addEventListener("DOMContentLoaded", function () {
    const taskConfig = document.getElementById("tasksUserConfig");
    const input = document.getElementById("myTasksSearch");
    const rows = document.querySelectorAll("#myTasksTable tbody tr");
    const modal = document.getElementById("taskSubmitModal");
    const modalClose = document.getElementById("taskModalClose");
    const form = document.getElementById("taskSubmitForm");
    const title = document.getElementById("taskSubmitTitle");
    const note = document.getElementById("taskSubmissionNote");
    const status = document.getElementById("taskSubmitStatus");
    const taskBaseUrl = taskConfig ? taskConfig.dataset.taskBaseUrl : "/tasks";

    if (input) {
        input.addEventListener("input", function () {
            const term = this.value.toLowerCase().trim();

            rows.forEach(function (row) {
                row.style.display = row.innerText.toLowerCase().includes(term) ? "" : "none";
            });
        });
    }

    document.querySelectorAll(".task-submit-trigger").forEach(function (button) {
        button.addEventListener("click", function () {
            form.action = `${taskBaseUrl}/${this.dataset.id}/submit`;
            title.textContent = this.dataset.title;
            note.value = this.dataset.note || "";
            status.value = ["In Progress", "Submitted", "Completed"].includes(this.dataset.status)
                ? this.dataset.status
                : "Submitted";
            modal.classList.add("show");
        });
    });

    if (modalClose) {
        modalClose.addEventListener("click", function () {
            modal.classList.remove("show");
        });
    }

    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.classList.remove("show");
        }
    });
});
