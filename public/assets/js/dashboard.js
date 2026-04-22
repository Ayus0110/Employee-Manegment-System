


   



function openAttendance() {
    
    window.location.href = ["Admin", "HR", "Manager"].includes(currentUser.role)
        ? "/attendance-admin"
        : "/attendance-user";
}

function openLeave() {
    
    window.location.href = ["Admin", "HR", "Manager"].includes(currentUser.role)
        ? "/leave-admin"
        : "/leave-user";
}

function openSalary() {
    
    window.location.href = ["Admin", "HR"].includes(currentUser.role)
        ? "/salary-admin"
        : "/salary-user";
}

function openEmployeeDetails() {
    window.location.href = "/employee-details";
}

function openManageUser() {
    window.location.href = "/manage-user";
}

loadDashboard();
