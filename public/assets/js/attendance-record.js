document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("attendanceChart");

    if (!ctx || typeof Chart === "undefined") {
        return;
    }

    const present = Number(ctx.dataset.present || 0);
    const absent = Number(ctx.dataset.absent || 0);
    const none = Number(ctx.dataset.none || 0);

    new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["Present", "Absent", "Unmarked"],
            datasets: [
                {
                    data: [present, absent, none],
                    backgroundColor: ["#16a34a", "#dc2626", "#cbd5e1"],
                    borderColor: ["#16a34a", "#dc2626", "#cbd5e1"],
                    borderWidth: 1,
                    hoverOffset: 5,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "72%",
            plugins: {
                legend: {
                    display: false,
                },
            },
        },
    });
});
