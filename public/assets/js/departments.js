document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchDepartment');
    const tableBody = document.getElementById('departmentTableBody');
    const modal = document.getElementById('editDepartmentModal');
    const closeModalButton = document.getElementById('closeDepartmentModal');
    const cancelModalButton = document.getElementById('cancelDepartmentModal');
    const editForm = document.getElementById('editDepartmentForm');
    const editName = document.getElementById('editDepartmentName');
    const editHead = document.getElementById('editDepartmentHead');
    const editButtons = document.querySelectorAll('.edit-btn');

    if (!searchInput || !tableBody) {
        return;
    }

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach((row) => {
            if (row.querySelector('.empty-state')) {
                return;
            }

            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });

    if (!modal || !editForm || !editName || !editHead) {
        return;
    }

    const closeModal = () => {
        modal.classList.remove('show');
    };

    editButtons.forEach((button) => {
        button.addEventListener('click', () => {
            editForm.action = `/departments/${button.dataset.id}`;
            editName.value = button.dataset.name || '';
            editHead.value = button.dataset.head || '';
            modal.classList.add('show');
        });
    });

    closeModalButton?.addEventListener('click', closeModal);
    cancelModalButton?.addEventListener('click', closeModal);

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });
});
