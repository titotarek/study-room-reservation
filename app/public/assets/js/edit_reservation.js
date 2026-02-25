const room = document.getElementById("roomSelect");
const dateInput = document.getElementById("dateInput");
const slot = document.getElementById("timeSlotSelect");
const people = document.getElementById("numPeople");
const warning = document.getElementById("capacityWarning");
const btn = document.getElementById("submitBtn");

const resId = document.getElementById("reservationId")?.value;
const currentSlot = document.getElementById("currentSlotId")?.value;

console.log("✅ edit_reservation.js loaded");

/* -----------------------------------
   DATE VALIDATION (NEW)
----------------------------------- */
function checkDateValidity() {
	if (!dateInput || !btn) return;

	const selected = new Date(dateInput.value);
	const today = new Date();

	// Reset time to midnight
	today.setHours(0, 0, 0, 0);
	selected.setHours(0, 0, 0, 0);

	const isPast = selected < today;

	if (isPast) {
		console.log("⛔ Past date selected");
		btn.disabled = true;
		slot.disabled = true;
	} else {
		console.log("✅ Valid date");
		slot.disabled = false;
	}
}

/* -----------------------------------
   CAPACITY CHECK
----------------------------------- */
function checkCapacity() {
	if (!room || !people) return;

	const cap = parseInt(room.selectedOptions[0]?.dataset.capacity || 0);
	const val = parseInt(people.value || 0);

	const invalid = val > cap || val <= 0;

	if (warning) {
		warning.classList.toggle("hidden", !invalid);
	}

	if (btn) {
		btn.disabled = invalid || slot.disabled;
	}
}

/* -----------------------------------
   LOAD AVAILABLE SLOTS
----------------------------------- */
async function loadSlots() {
	if (!room?.value || !dateInput?.value) return;

	slot.innerHTML = '<option value="">Loading...</option>';
	slot.disabled = true;

	try {
		const url = `/api/available-slots?room_id=${room.value}&date=${dateInput.value}&reservation_id=${resId ?? ""}`;
		const response = await fetch(url);
		const data = await response.json();

		slot.innerHTML = "";

		if (data.success && data.slots.length > 0) {
			data.slots.forEach((s) => {
				const label =
					s.start_time.substring(0, 5) + " - " + s.end_time.substring(0, 5);

				slot.add(new Option(label, s.id));
			});

			slot.disabled = false;
		} else {
			slot.innerHTML = '<option value="">No slots available</option>';
			slot.disabled = true;
		}
	} catch (e) {
		console.log("🔥 Fetch error:", e);
	}

	checkCapacity();
}

/* -----------------------------------
   EVENTS
----------------------------------- */

room?.addEventListener("change", () => {
	checkCapacity();
	loadSlots();
});

dateInput?.addEventListener("change", () => {
	checkDateValidity();
	loadSlots();
});

people?.addEventListener("input", checkCapacity);

/* -----------------------------------
   INITIAL LOAD
----------------------------------- */

checkDateValidity();
checkCapacity();
loadSlots();
