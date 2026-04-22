document.addEventListener("DOMContentLoaded", function () {
    const previewContainer = document.getElementById("salaryPreviewConfig");
    const userSelect = document.getElementById("salaryUser");
    const monthInput = document.getElementById("salaryMonth");
    const dailyRate = document.getElementById("dailyRate");
    const presentDays = document.getElementById("presentDays");
    const attendanceSalary = document.getElementById("attendanceSalary");
    const previewInfo = document.getElementById("salaryPreviewInfo");

    if (!previewContainer || !userSelect || !monthInput || !dailyRate || !presentDays || !attendanceSalary || !previewInfo) {
        return;
    }

    const previewUrl = previewContainer.dataset.previewUrl;

    const updateAttendanceSalary = function () {
        const days = parseFloat(presentDays.value || 0);
        const rate = parseFloat(dailyRate.value || 0);
        attendanceSalary.value = (days * rate).toFixed(2);
    };

    const loadPreview = async function () {
        if (!userSelect.value || !monthInput.value) {
            dailyRate.value = "";
            presentDays.value = "";
            attendanceSalary.value = "";
            previewInfo.textContent = "Select user and month to calculate salary from attendance.";
            return;
        }

        try {
            const url = `${previewUrl}?user_id=${encodeURIComponent(userSelect.value)}&month=${encodeURIComponent(monthInput.value)}`;
            const response = await fetch(url, {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error("Unable to calculate salary preview.");
            }

            const data = await response.json();
            dailyRate.value = data.daily_rate;
            presentDays.value = data.present_days;
            attendanceSalary.value = data.attendance_salary;
            previewInfo.textContent = `Salary for ${data.month_label}: ${data.present_days} present day(s) x ${data.daily_rate} daily salary = ${data.attendance_salary}`;
        } catch (error) {
            dailyRate.value = "";
            presentDays.value = "";
            attendanceSalary.value = "";
            previewInfo.textContent = "Could not load attendance-based salary preview.";
        }
    };

    userSelect.addEventListener("change", loadPreview);
    monthInput.addEventListener("change", loadPreview);
    dailyRate.addEventListener("input", function () {
        updateAttendanceSalary();

        if (userSelect.value && monthInput.value) {
            previewInfo.textContent = `Salary preview: ${presentDays.value || 0} present day(s) x ${dailyRate.value || 0} daily salary = ${attendanceSalary.value || 0}`;
        }
    });
});
