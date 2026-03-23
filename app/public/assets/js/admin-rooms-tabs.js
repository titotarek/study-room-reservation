console.log("ADMIN JS LOADED");

let activeModal = null;
const getFocusableElements = (modal) =>
	Array.from(
		modal.querySelectorAll(
			'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
		),
	).filter((element) => !element.hasAttribute("hidden"));

function showModal(modal) {
	if (!modal) return;
	activeModal = modal;
	modal.classList.remove("hidden");
	modal.classList.add("flex");
	modal.setAttribute("aria-hidden", "false");
	getFocusableElements(modal)[0]?.focus();
}

function hideModal(modal) {
	if (!modal) return;
	modal.classList.add("hidden");
	modal.classList.remove("flex");
	modal.setAttribute("aria-hidden", "true");

	if (activeModal === modal) {
		activeModal = null;
	}
}

function trapFocus(event) {
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
}

window.openAddRoomModal = function () {
	const modal = document.getElementById("addRoomModal");
	if (!modal) return;
	showModal(modal);
};

window.closeAddRoomModal = function () {
	const modal = document.getElementById("addRoomModal");
	hideModal(modal);
};

window.openSlotModal = function (roomId) {
	const modal = document.getElementById("slotModal");
	const input = document.getElementById("slotRoomId");
	if (!modal || !input) return;

	input.value = roomId;
	showModal(modal);

	loadExistingSlots(roomId);
};

window.closeSlotModal = function () {
	const modal = document.getElementById("slotModal");
	hideModal(modal);
};

// =============================
// LOAD SLOTS
// =============================

function loadExistingSlots(roomId) {
	const container = document.getElementById("existingSlots");
	if (!container) return;

	container.innerHTML =
		'<p class="text-sm text-slate-400">Loading slots...</p>';

	fetch(`/admin/slots/by-room?room_id=${roomId}`)
		.then((response) => response.json())
		.then((data) => {
			container.innerHTML = "";

			if (!data || data.length === 0) {
				container.innerHTML =
					'<p class="text-sm text-slate-400">No slots yet.</p>';
				return;
			}

			data.forEach((slot) => {
				const start = slot.start_time.substring(0, 5);
				const end = slot.end_time.substring(0, 5);

				const row = document.createElement("div");
				row.className =
					"flex justify-between items-center bg-slate-50 p-3 rounded-lg border mb-2";

				row.innerHTML = `
					<div>
						<p class="font-semibold">${slot.day_of_week}</p>
						<p class="text-sm text-slate-600">${start} - ${end}</p>
					</div>
					<div class="flex gap-4">
						<button 
							onclick="editSlot(${slot.id}, '${slot.day_of_week}', '${slot.start_time}', '${slot.end_time}')"
							class="text-blue-600 text-sm font-bold hover:underline">
							Edit
						</button>
						<button 
							onclick="deleteSlot(${slot.id}, ${roomId}, '${slot.day_of_week}', '${slot.start_time}', '${slot.end_time}')"
							class="text-red-600 text-sm font-bold hover:underline">
							Delete
						</button>
					</div>
				`;

				container.appendChild(row);
			});
		})
		.catch(console.error);
}

// =============================
// EDIT SLOT (GLOBAL)
// =============================

window.editSlot = function (id, day, start, end) {
	const form = document.getElementById("slotForm");
	const submitBtn = document.getElementById("slotSubmitBtn");

	const daySelect = document.querySelector('select[name="day_of_week"]');
	const startInput = document.querySelector('input[name="start_time"]');
	const endInput = document.querySelector('input[name="end_time"]');
	const slotIdInput = document.getElementById("slotId");

	// Fill inputs
	if (daySelect) daySelect.value = day;
	if (startInput) startInput.value = start.substring(0, 5);
	if (endInput) endInput.value = end.substring(0, 5);
	if (slotIdInput) slotIdInput.value = id;

	// Switch form to UPDATE mode
	if (form) form.action = "/admin/slots/update";

	// Change button text
	if (submitBtn) submitBtn.innerText = "Update Slot";
};

// //////////////////

function resetSlotForm() {
	const form = document.getElementById("slotForm");
	const submitBtn = document.getElementById("slotSubmitBtn");
	const slotIdInput = document.getElementById("slotId");

	if (form) form.action = "/admin/slots/store";
	if (submitBtn) submitBtn.innerText = "Add Slot";
	if (slotIdInput) slotIdInput.value = "";
}
// =============================
// DELETE SLOT (GLOBAL)
// =============================

let pendingDeleteSlot = null;
let lastFocusedDeleteSlotTrigger = null;

window.deleteSlot = function (id, roomId, dayOfWeek, startTime, endTime) {
	const start = startTime.substring(0, 5);
	const end = endTime.substring(0, 5);

	pendingDeleteSlot = { id, roomId };
	lastFocusedDeleteSlotTrigger = document.activeElement;

	const textEl = document.getElementById("confirmDeleteSlotText");
	if (textEl) {
		textEl.textContent = `Delete time slot ${dayOfWeek} ${start} - ${end}? This will remove the slot and cancel any future reservations for that slot.`;
	}

	const modalEl = document.getElementById("confirmDeleteSlotModal");
	showModal(modalEl);
};

window.closeDeleteSlotModal = function () {
	const modalEl = document.getElementById("confirmDeleteSlotModal");
	hideModal(modalEl);
	pendingDeleteSlot = null;
	lastFocusedDeleteSlotTrigger?.focus?.();
};

window.confirmDeleteSlot = function () {
	if (!pendingDeleteSlot || !pendingDeleteSlot.id) {
		return;
	}

	const { id, roomId } = pendingDeleteSlot;
	window.closeDeleteSlotModal();
	const csrfToken =
		document.getElementById("slotDeleteCsrfToken")?.value || "";

	fetch("/admin/slots/delete", {
		method: "POST",
		headers: {
			"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
		},
		body: new URLSearchParams({
			id: String(id),
			csrf_token: csrfToken,
		}).toString(),
	})
		.then(async (response) => {
			if (!response.ok) {
				throw new Error("Failed to delete slot");
			}

			const result = await response.json();

			if (!result.success) {
				throw new Error(result.message || "Delete slot failed");
			}

			loadExistingSlots(roomId);

			if (result.reservations_cancelled && result.reservations_cancelled > 0) {
				alert(
					`${result.reservations_cancelled} future reservation(s) were cancelled.`,
				);
			}
		})
		.catch((err) => {
			console.error(err);
			alert("Something went wrong while deleting the time slot.");
		});
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

startInput.addEventListener("input", validateTime);
endInput.addEventListener("input", validateTime);

slotForm.addEventListener("submit", function (e) {
	if (endInput.value <= startInput.value) {
		e.preventDefault();
	}
});

document.addEventListener("keydown", (event) => {
	trapFocus(event);

	if (event.key === "Escape") {
		window.closeDeleteSlotModal();
	}
});
