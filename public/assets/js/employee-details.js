document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchEmployee');
    const tableBody = document.getElementById('employeeTableBody');
    const editButtons = document.querySelectorAll('.edit-btn');

    const userSelect = document.getElementById('employeeUser');
    const employeeCode = document.getElementById('employeeCode');
    const departmentSelect = document.getElementById('employeeDepartment');
    const designationInput = document.getElementById('employeeDesignation');
    const dobInput = document.getElementById('employeeDob');
    const addressInput = document.getElementById('employeeAddress');
    const salaryInput = document.getElementById('employeeSalary');
    const scheduleType = document.getElementById('scheduleType');
    const shiftStart = document.getElementById('shiftStart');
    const shiftEnd = document.getElementById('shiftEnd');
    const customShiftFields = document.querySelectorAll('.custom-shift-field');

    const defaults = {
        Morning: ['06:00', '14:00'],
        General: ['09:00', '17:00'],
        Evening: ['14:00', '22:00'],
        Night: ['22:00', '06:00'],
    };

    const updateShiftInputs = () => {
        if (!scheduleType || !shiftStart || !shiftEnd) {
            return;
        }

        const selectedType = scheduleType.value;
        const isCustom = selectedType === 'Custom';

        customShiftFields.forEach((field) => {
            field.classList.toggle('is-disabled', !isCustom);
        });

        shiftStart.readOnly = !isCustom;
        shiftEnd.readOnly = !isCustom;

        if (!isCustom && defaults[selectedType]) {
            shiftStart.value = defaults[selectedType][0];
            shiftEnd.value = defaults[selectedType][1];
        }
    };

    if (searchInput && tableBody) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim().toLowerCase();
            const rows = tableBody.querySelectorAll('tr');

            rows.forEach((row) => {
                if (row.querySelector('.empty-state')) {
                    return;
                }

                row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
            });
        });
    }

    editButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (userSelect) {
                userSelect.value = button.dataset.userId || '';
            }

            employeeCode.value = button.dataset.employeeId || '';
            departmentSelect.value = button.dataset.departmentId || '';
            designationInput.value = button.dataset.designation || '';
            dobInput.value = button.dataset.dob || '';
            addressInput.value = button.dataset.address || '';
            salaryInput.value = button.dataset.basicSalary || '';
            if (scheduleType) {
                scheduleType.value = button.dataset.scheduleType || 'General';
            }
            if (shiftStart) {
                shiftStart.value = button.dataset.shiftStart || '';
            }
            if (shiftEnd) {
                shiftEnd.value = button.dataset.shiftEnd || '';
            }
            updateShiftInputs();

            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });

    if (scheduleType) {
        scheduleType.addEventListener('change', updateShiftInputs);
        updateShiftInputs();
    }
});
