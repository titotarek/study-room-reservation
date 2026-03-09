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

	document.querySelectorAll(".openSlotBtn").forEach((btn) => {
		btn.addEventListener("click", function () {
			toggleModal(slotModal, true);
		});
	});

	window.addEventListener("click", (event) => {
		if (event.target === addModal) toggleModal(addModal, false);
		if (event.target === slotModal) toggleModal(slotModal, false);
	});

	/* -----------------------------
	   SLOT TIME VALIDATION
	----------------------------- */

	const slotForm = document.getElementById("slotForm");
	const startInput = document.querySelector('input[name="start_time"]');
	const endInput = document.querySelector('input[name="end_time"]');
	const submitBtn = document.getElementById("slotSubmitBtn");
	const error = document.getElementById("slotError");

	function validateTime() {
		const start = startInput.value;
		const end = endInput.value;

		if (!start || !end) return;

		if (end <= start) {
			error.classList.remove("hidden");

			submitBtn.disabled = true;
			submitBtn.classList.add("opacity-50", "cursor-not-allowed");
		} else {
			error.classList.add("hidden");

			submitBtn.disabled = false;
			submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
		}
	}

	if (startInput && endInput) {
		startInput.addEventListener("input", validateTime);
		endInput.addEventListener("input", validateTime);
	}

	if (slotForm) {
		slotForm.addEventListener("submit", function (e) {
			if (endInput.value <= startInput.value) {
				e.preventDefault();
			}
		});
	}
});
