document.addEventListener("DOMContentLoaded", function () {
	const addModal = document.getElementById("addRoomModal");
	const slotModal = document.getElementById("slotModal");
	const deleteRoomModal = document.getElementById("confirmDeleteRoomModal");

	const openAddBtn = document.getElementById("openAddRoomBtn");
	const closeAddBtn = document.getElementById("closeAddRoomBtn");
	const closeSlotBtn = document.getElementById("closeSlotBtn");
	let deleteRoomFormId = null;
	let lastFocusedElement = null;
	let activeModal = null;

	const getFocusableElements = (modal) =>
		Array.from(
			modal.querySelectorAll(
				'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
			),
		).filter((element) => !element.hasAttribute("hidden"));

	const trapFocus = (event) => {
		if (!activeModal || event.key !== "Tab" || activeModal.classList.contains("hidden")) {
			return;
		}

		const focusableElements = getFocusableElements(activeModal);
		if (!focusableElements.length) {
			event.preventDefault();
			return;
		}

		const firstElement = focusableElements[0];
		const lastElement = focusableElements[focusableElements.length - 1];

		if (event.shiftKey && document.activeElement === firstElement) {
			event.preventDefault();
			lastElement.focus();
			return;
		}

		if (!event.shiftKey && document.activeElement === lastElement) {
			event.preventDefault();
			firstElement.focus();
		}
	};

	const toggleModal = (modal, show = true) => {
		if (!modal) return;

		if (show) {
			lastFocusedElement = document.activeElement;
			activeModal = modal;
			modal.classList.remove("hidden");
			modal.classList.add("flex");
			modal.setAttribute("aria-hidden", "false");
			getFocusableElements(modal)[0]?.focus();
		} else {
			modal.classList.add("hidden");
			modal.classList.remove("flex");
			modal.setAttribute("aria-hidden", "true");
			if (activeModal === modal) {
				activeModal = null;
			}
			lastFocusedElement?.focus?.();
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
		if (event.target === deleteRoomModal) toggleModal(deleteRoomModal, false);
	});

	document.addEventListener("keydown", (event) => {
		trapFocus(event);

		if (event.key === "Escape") {
			toggleModal(addModal, false);
			toggleModal(slotModal, false);
			toggleModal(deleteRoomModal, false);
		}
	});

	window.openDeleteRoomModal = function (roomId, roomNumber) {
		const modalText = document.getElementById("confirmDeleteRoomText");
		deleteRoomFormId = `delete-room-form-${roomId}`;

		if (modalText) {
			modalText.textContent = `Delete room ${roomNumber}? This will also remove its time slots and related reservations.`;
		}

		toggleModal(deleteRoomModal, true);
	};

	window.closeDeleteRoomModal = function () {
		deleteRoomFormId = null;
		toggleModal(deleteRoomModal, false);
	};

	window.confirmDeleteRoom = function () {
		if (!deleteRoomFormId) return;

		const form = document.getElementById(deleteRoomFormId);
		window.closeDeleteRoomModal();

		if (form) {
			form.submit();
		}
	};

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
