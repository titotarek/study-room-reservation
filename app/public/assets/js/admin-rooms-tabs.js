console.log("ADMIN JS LOADED");

window.openAddRoomModal = function () {
	const modal = document.getElementById("addRoomModal");
	if (!modal) return;
	modal.classList.remove("hidden");
	modal.classList.add("flex");
};

window.closeAddRoomModal = function () {
	const modal = document.getElementById("addRoomModal");
	if (!modal) return;
	modal.classList.add("hidden");
	modal.classList.remove("flex");
};

window.openSlotModal = function (roomId) {
	const modal = document.getElementById("slotModal");
	const input = document.getElementById("slotRoomId");
	if (!modal || !input) return;

	input.value = roomId;
	modal.classList.remove("hidden");
	modal.classList.add("flex");

	loadExistingSlots(roomId);
};

window.closeSlotModal = function () {
	const modal = document.getElementById("slotModal");
	if (!modal) return;
	modal.classList.add("hidden");
	modal.classList.remove("flex");
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
							onclick="deleteSlot(${slot.id}, ${roomId})"
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

window.deleteSlot = function (id, roomId) {
	if (!confirm("Delete this slot?")) return;

	fetch(`/admin/slots/delete?id=${id}`)
		.then(() => {
			loadExistingSlots(roomId);
		})
		.catch(console.error);
};
