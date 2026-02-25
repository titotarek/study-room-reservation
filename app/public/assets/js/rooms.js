async function viewSlots(roomId) {
	const container = document.getElementById(`slots-container-${roomId}`);
	const grid = document.getElementById(`slot-grid-${roomId}`);

	if (!container || !grid) return;

	if (!container.classList.contains("hidden")) {
		container.classList.add("hidden");
		return;
	}

	try {
		// ✅ FIXED: Use LOCAL date (no UTC shift)
		const now = new Date();
		const today =
			now.getFullYear() +
			"-" +
			String(now.getMonth() + 1).padStart(2, "0") +
			"-" +
			String(now.getDate()).padStart(2, "0");

		console.log("Room ID:", roomId);
		console.log("Date sent:", today);

		const res = await fetch(
			`/api/available-slots?room_id=${roomId}&date=${today}`,
		);

		if (!res.ok) {
			throw new Error("Network error");
		}

		const data = await res.json();

		container.classList.remove("hidden");
		grid.innerHTML = "";

		if (!data.success || !data.slots || data.slots.length === 0) {
			grid.innerHTML =
				'<p class="text-sm text-slate-400 italic">No slots available.</p>';
			return;
		}

		data.slots.forEach((slot) => {
			const wrapper = document.createElement("div");
			wrapper.className =
				"flex justify-between items-center bg-slate-50 border p-4 rounded-2xl";

			wrapper.innerHTML = `
                <div>
                    <p class="text-xs font-bold">
                        ${slot.start_time.substring(0, 5)} - ${slot.end_time.substring(0, 5)}
                    </p>
                    <p class="text-[10px] text-slate-400 uppercase">
                        ${slot.day_of_week}
                    </p>
                </div>
                <form action="/reservations/store" method="POST">
                    <input type="hidden" name="room_id" value="${roomId}">
                    <input type="hidden" name="time_slot_id" value="${slot.id}">
                    <input type="hidden" name="reservation_date" value="${today}">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-700 transition">
                        BOOK
                    </button>
                </form>
            `;

			grid.appendChild(wrapper);
		});
	} catch (error) {
		container.classList.remove("hidden");
		grid.innerHTML =
			'<p class="text-sm text-red-500 italic">Error loading slots.</p>';

		console.error("Slots error:", error);
	}
}
