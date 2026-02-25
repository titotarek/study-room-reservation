document.addEventListener("DOMContentLoaded", () => {
	const roomSelect = document.getElementById("roomSelect");
	const dateInput = document.getElementById("dateInput");
	const slotSelect = document.getElementById("timeSlotSelect");
	const submitBtn = document.getElementById("submitBtn");
	const messageContainer = document.getElementById("messageContainer");
	const numPeopleInput = document.getElementById("numPeople");

	if (!roomSelect || !dateInput || !slotSelect || !submitBtn) return;

	const reservationId = document.getElementById("reservationId")?.value || "";
	const currentSlotId = document.getElementById("currentSlotId")?.value || "";

	const today = new Date().toISOString().split("T")[0];
	dateInput.setAttribute("min", today);

	function showMessage(message) {
		if (!messageContainer) return;

		messageContainer.className =
			"mb-4 p-4 rounded-2xl bg-red-50 text-red-700 border border-red-200";

		messageContainer.textContent = message;
		messageContainer.classList.remove("hidden");
	}

	function hideMessage() {
		if (messageContainer) {
			messageContainer.classList.add("hidden");
		}
	}

	function updateCapacityLimit() {
		const selectedOption = roomSelect.options[roomSelect.selectedIndex];
		if (!selectedOption || !numPeopleInput) return;

		const capacity =
			parseInt(selectedOption.getAttribute("data-capacity")) || 1;

		numPeopleInput.setAttribute("max", capacity);

		if (parseInt(numPeopleInput.value) > capacity) {
			numPeopleInput.value = capacity;
			showMessage(`Maximum capacity for this room is ${capacity}.`);
		}
	}

	if (numPeopleInput) {
		numPeopleInput.addEventListener("input", function () {
			const max = parseInt(this.getAttribute("max")) || 1;

			if (this.value < 1) this.value = 1;

			if (this.value > max) {
				this.value = max;
				showMessage(`Maximum capacity for this room is ${max}.`);
			}
		});
	}

	roomSelect.addEventListener("change", () => {
		updateCapacityLimit();
		fetchAvailableSlots();
	});

	dateInput.addEventListener("change", () => {
		if (dateInput.value < today) {
			dateInput.value = today;
			showMessage("You cannot select a past date.");
		}
		fetchAvailableSlots();
	});

	async function fetchAvailableSlots() {
		const roomId = roomSelect.value;
		const date = dateInput.value;

		if (!roomId || !date) return;

		slotSelect.disabled = true;
		submitBtn.disabled = true;
		slotSelect.innerHTML = "<option>Loading...</option>";

		// ✅ CORRECT ENDPOINT (NO .php)
		let url = `/api/available-slots?room_id=${roomId}&date=${date}`;

		if (reservationId) {
			url += `&reservation_id=${reservationId}`;
		}

		try {
			const response = await fetch(url);

			if (!response.ok) {
				throw new Error("Network response not OK");
			}

			const data = await response.json();

			slotSelect.innerHTML = "";

			if (data.success && Array.isArray(data.slots) && data.slots.length > 0) {
				data.slots.forEach((slot) => {
					const option = document.createElement("option");
					option.value = slot.id;
					option.textContent = slot.display_time;

					if (slot.id == currentSlotId) {
						option.selected = true;
					}

					slotSelect.appendChild(option);
				});

				slotSelect.disabled = false;
				submitBtn.disabled = false;
				hideMessage();
			} else {
				slotSelect.innerHTML = '<option value="">No slots available</option>';
				showMessage("No slots available for this date.");
			}
		} catch (error) {
			console.error("Slot fetch error:", error);
			showMessage("Server connection error.");
			slotSelect.innerHTML = '<option value="">Error loading slots</option>';
			slotSelect.disabled = true;
			submitBtn.disabled = true;
		}
	}

	updateCapacityLimit();
	fetchAvailableSlots();
});
