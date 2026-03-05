document.addEventListener("DOMContentLoaded", () => {

    const modal = document.getElementById("hoursModal");
    if (!modal) return;

    const box = modal.querySelector(".modal-box");

    function openModal() {
        modal.classList.add("modal-visible");
        document.body.style.overflow = "hidden";
    }

    function closeModal() {
        modal.classList.remove("modal-visible");
        document.body.style.overflow = "";
    }

    document.addEventListener("click", (e) => {

        const btn = e.target.closest('[data-open-modal="hours"]');

        if (btn) {
            e.preventDefault();
            openModal();
        }

    });

    modal.addEventListener("click", (e) => {

        if (e.target.closest("[data-close-modal]")) {
            closeModal();
        }

        if (!box.contains(e.target)) {
            closeModal();
        }

    });

});
