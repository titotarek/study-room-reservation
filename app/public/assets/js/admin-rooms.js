document.addEventListener("DOMContentLoaded", function () {

    const addModal = document.getElementById("addRoomModal");
    const slotModal = document.getElementById("slotModal");

    const openAddBtn = document.getElementById("openAddRoomBtn");
    const closeAddBtn = document.getElementById("closeAddRoomBtn");
    const closeSlotBtn = document.getElementById("closeSlotBtn");

    const toggleModal = (modal, show = true) => {
        if (!modal) return;

        if (show) {
            modal.classList.remove("hidden");
            modal.classList.add("flex");
        } else {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }
    };

    if (openAddBtn) {
        openAddBtn.addEventListener("click", () => toggleModal(addModal, true));
    }

    if (closeAddBtn) {
        closeAddBtn.addEventListener("click", () => toggleModal(addModal, false));
    }

    if (closeSlotBtn) {
        closeSlotBtn.addEventListener("click", () => toggleModal(slotModal, false));
    }

    document.querySelectorAll(".openSlotBtn").forEach(btn => {
        btn.addEventListener("click", function () {
            toggleModal(slotModal, true);
        });
    });

    window.addEventListener("click", (event) => {
        if (event.target === addModal) toggleModal(addModal, false);
        if (event.target === slotModal) toggleModal(slotModal, false);
    });
});
