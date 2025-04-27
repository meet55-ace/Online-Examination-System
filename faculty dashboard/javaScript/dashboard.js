document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.querySelector(".sidebar");
    const toggleBtn = document.querySelector(".sidebar-toggler");

    toggleBtn.addEventListener("click", function() {
        sidebar.classList.toggle("closed");
    });
});
