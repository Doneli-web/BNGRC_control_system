document.addEventListener("DOMContentLoaded", function () {
    const addBtn = document.getElementById("addCategoryBtn");
    const modal = document.getElementById("categoryModal");
    const closeBtn = document.getElementById("closeCategoryModal");
    const cancelBtn = document.getElementById("cancelCategoryBtn");
    const category = document.querySelector("input[type='text']");
    const form = document.querySelector("form");

    addBtn.addEventListener("click", function () {
        modal.style.display = "flex";
    });

    closeBtn.addEventListener("click", function () {
        modal.style.display = "none";
    });

    cancelBtn.addEventListener("click", function () {
        modal.style.display = "none";
    });

    modal.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });

    form.addEventListener('submit',(e)=>{
        e.preventDefault();
        const fd = new FormData(form);
        const categoryName = category.value.trim();
        fetch("/admin-category-create", {
            method: "POST",
            body: fd
        });
        window.location.href = "/admin-categories";
        e.stopPropagation();
    });

});
