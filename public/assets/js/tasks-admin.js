document.addEventListener("DOMContentLoaded", function () {
    const taskConfig = document.getElementById("tasksAdminConfig");

    const bindSearch = function (inputId, tableId) {
        const input = document.getElementById(inputId);
        const rows = document.querySelectorAll(`#${tableId} tbody tr`);

        if (!input) {
            return;
        }

        input.addEventListener("input", function () {
            const term = this.value.toLowerCase().trim();

            rows.forEach(function (row) {
                row.style.display = row.innerText.toLowerCase().includes(term) ? "" : "none";
            });
        });
    };

    bindSearch("myTasksSearch", "myTasksTable");
    bindSearch("assignedTasksSearch", "assignedTasksTable");

    const modal = document.getElementById("taskSubmitModal");
    const modalClose = document.getElementById("taskModalClose");
    const form = document.getElementById("taskSubmitForm");
    const title = document.getElementById("taskSubmitTitle");
    const note = document.getElementById("taskSubmissionNote");
    const status = document.getElementById("taskSubmitStatus");
    const editModal = document.getElementById("taskEditModal");
    const editModalClose = document.getElementById("taskEditModalClose");
    const editForm = document.getElementById("taskEditForm");
    const editTitle = document.getElementById("taskEditTitle");
    const editAssignedTo = document.getElementById("editAssignedTo");
    const editDueDate = document.getElementById("editDueDate");
    const editTaskTitleInput = document.getElementById("editTaskTitleInput");
    const editTaskPriority = document.getElementById("editTaskPriority");
    const editTaskDescription = document.getElementById("editTaskDescription");
    const taskBaseUrl = taskConfig ? taskConfig.dataset.taskBaseUrl : "/tasks";

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

    document.querySelectorAll(".task-edit-trigger").forEach(function (button) {
        button.addEventListener("click", function () {
            editForm.action = `${taskBaseUrl}/${this.dataset.id}`;
            editTitle.textContent = `Edit: ${this.dataset.title}`;
            editAssignedTo.value = this.dataset.assignedTo;
            editDueDate.value = this.dataset.dueDate;
            editTaskTitleInput.value = this.dataset.title;
            editTaskPriority.value = this.dataset.priority;
            editTaskDescription.value = this.dataset.description;
            editModal.classList.add("show");
        });
    });

    if (modalClose) {
        modalClose.addEventListener("click", function () {
            modal.classList.remove("show");
        });
    }

    if (editModalClose) {
        editModalClose.addEventListener("click", function () {
            editModal.classList.remove("show");
        });
    }

    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.classList.remove("show");
        }

        if (event.target === editModal) {
            editModal.classList.remove("show");
        }
    });
});
