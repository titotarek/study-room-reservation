document.addEventListener("DOMContentLoaded", () => {
	let pendingCancelId = null;
	let lastFocusedElement = null;
	const getFocusableElements = (modal) =>
		Array.from(
			modal.querySelectorAll(
				'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
			),
		).filter((element) => !element.hasAttribute("hidden"));

	const trapFocus = (event, modal) => {
		if (!modal || event.key !== "Tab" || modal.classList.contains("hidden")) {
			return;
		}

		const focusableElements = getFocusableElements(modal);
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

	window.openCancelModal = function (id) {
		if (!id) return;

		pendingCancelId = id;
		lastFocusedElement = document.activeElement;

		const modal = document.getElementById("confirmCancelModal");
		if (!modal) return;
		modal.classList.remove("hidden");
		modal.classList.add("flex");
		modal.setAttribute("aria-hidden", "false");
		getFocusableElements(modal)[0]?.focus();
	};

	window.closeCancelModal = function () {
		const modal = document.getElementById("confirmCancelModal");
		if (!modal) return;
		modal.classList.add("hidden");
		modal.classList.remove("flex");
		modal.setAttribute("aria-hidden", "true");
		pendingCancelId = null;
		lastFocusedElement?.focus?.();
	};

	window.confirmCancelReservation = function () {
		if (!pendingCancelId) return;

		const field = document.getElementById("cancelReservationId");
		const form = document.getElementById("cancelReservationForm");

		if (!field || !form) return;

		field.value = pendingCancelId;
		window.closeCancelModal();
		form.submit();
	};

	document.addEventListener("keydown", (event) => {
		const modal = document.getElementById("confirmCancelModal");

		if (modal && !modal.classList.contains("hidden")) {
			trapFocus(event, modal);
		}

		if (event.key === "Escape") {
			window.closeCancelModal();
		}
	});
});
