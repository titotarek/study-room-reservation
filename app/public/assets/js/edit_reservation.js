const room = document.getElementById("roomSelect");
const dateInput = document.getElementById("dateInput");
const slot = document.getElementById("timeSlotSelect");
const people = document.getElementById("numPeople");
const warning = document.getElementById("capacityWarning");
const btn = document.getElementById("submitBtn");
const messageContainer = document.getElementById("messageContainer");

const resId = document.getElementById("reservationId")?.value;
const currentSlot = document.getElementById("currentSlotId")?.value;
let preferredSlotId = currentSlot || "";

const weekdayMap = {
    sunday: 0,
    monday: 1,
    tuesday: 2,
    wednesday: 3,
    thursday: 4,
    friday: 5,
    saturday: 6,
};

function formatDate(date) {
    return date.toISOString().split("T")[0];
}

function addDays(dateValue, days) {
    const date = new Date(`${dateValue}T00:00:00`);
    date.setDate(date.getDate() + days);
    return formatDate(date);
}

function getMatchingDateForWeekday(baseDateValue, weekdayName) {
    if (!baseDateValue || !weekdayName) return null;

    const weekday = weekdayMap[weekdayName.toLowerCase()];
    if (weekday === undefined) return null;

    const baseDate = new Date(`${baseDateValue}T00:00:00`);
    if (Number.isNaN(baseDate.getTime())) return null;

    const offset = (weekday - baseDate.getDay() + 7) % 7;
    baseDate.setDate(baseDate.getDate() + offset);
    return formatDate(baseDate);
}

function showMessage(message) {
    if (!messageContainer) return;

    messageContainer.className =
        "mb-4 p-4 rounded-2xl bg-amber-50 text-amber-800 border border-amber-200";
    messageContainer.textContent = message;
    messageContainer.classList.remove("hidden");
}

function hideMessage() {
    if (messageContainer) {
        messageContainer.classList.add("hidden");
    }
}

function checkDateValidity() {
    if (!dateInput || !btn || !slot) return false;

    const selected = new Date(`${dateInput.value}T00:00:00`);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (Number.isNaN(selected.getTime())) {
        btn.disabled = true;
        slot.disabled = true;
        return false;
    }

    const isPast = selected < today;
    btn.disabled = isPast;
    slot.disabled = isPast;

    if (isPast) {
        showMessage("You cannot choose a past date.");
        return false;
    }

    return true;
}

function checkCapacity() {
    if (!room || !people || !btn || !slot) return;

    const cap = parseInt(room.selectedOptions[0]?.dataset.capacity || 0, 10);
    const val = parseInt(people.value || 0, 10);
    const invalid = val > cap || val <= 0;

    if (warning) {
        warning.classList.toggle("hidden", !invalid);
    }

    btn.disabled = invalid || slot.disabled;
}

async function fetchJson(url) {
    const response = await fetch(url);
    if (!response.ok) {
        throw new Error("Request failed");
    }
    return response.json();
}

async function loadRoomSlots(selectedSlotId = preferredSlotId) {
    if (!room?.value || !slot) return;

    slot.innerHTML = '<option value="">Loading...</option>';
    slot.disabled = true;

    try {
        const data = await fetchJson(`/api/room-slots?room_id=${room.value}`);
        slot.innerHTML = "";

        if (data.success && Array.isArray(data.slots) && data.slots.length > 0) {
            data.slots.forEach((s) => {
                const label = `${s.day_of_week} • ${s.start_time.substring(0, 5)} - ${s.end_time.substring(0, 5)}`;
                const option = new Option(label, s.id);
                option.dataset.day = s.day_of_week || "";
                option.dataset.start = s.start_time || "";
                option.dataset.end = s.end_time || "";

                if (String(s.id) == String(selectedSlotId)) {
                    option.selected = true;
                }

                slot.add(option);
            });

            if (!slot.value && slot.options.length > 0) {
                slot.selectedIndex = 0;
            }

            preferredSlotId = slot.value || "";
            slot.disabled = false;
            hideMessage();
        } else {
            slot.innerHTML = '<option value="">No slots found</option>';
            preferredSlotId = "";
            showMessage("No weekly time slots are configured for this room.");
        }
    } catch (error) {
        console.error("Room slots fetch error:", error);
        slot.innerHTML = '<option value="">Error loading slots</option>';
        preferredSlotId = "";
        showMessage("Unable to load room slots right now.");
    }

    checkCapacity();
}

async function findNextAvailableDateForSlot(slotId, weekdayName, startDateValue) {
    const firstCandidate = getMatchingDateForWeekday(startDateValue, weekdayName);
    if (!slotId || !firstCandidate) return null;

    let candidate = firstCandidate;

    for (let week = 0; week < 12; week += 1) {
        const data = await fetchJson(
            `/api/available-slots?room_id=${room.value}&date=${candidate}&reservation_id=${resId ?? ""}`,
        );

        const isAvailable =
            data.success &&
            Array.isArray(data.slots) &&
            data.slots.some((availableSlot) => String(availableSlot.id) === String(slotId));

        if (isAvailable) {
            return candidate;
        }

        candidate = addDays(candidate, 7);
    }

    return null;
}

async function syncDateToSelectedSlot() {
    if (!slot || !dateInput) return;

    const selectedOption = slot.selectedOptions[0];
    if (!selectedOption) return;

    preferredSlotId = selectedOption.value;

    try {
        const nextAvailableDate = await findNextAvailableDateForSlot(
            selectedOption.value,
            selectedOption.dataset.day,
            dateInput.value,
        );

        if (!nextAvailableDate) {
            showMessage("No available date was found for that time slot in the next 12 weeks.");
            btn.disabled = true;
            return;
        }

        dateInput.value = nextAvailableDate;
        hideMessage();
        checkDateValidity();
        checkCapacity();
    } catch (error) {
        console.error("Date sync error:", error);
        showMessage("Unable to match that slot to an available date right now.");
    }
}

room?.addEventListener("change", async () => {
    preferredSlotId = "";
    await loadRoomSlots();
    await syncDateToSelectedSlot();
});

dateInput?.addEventListener("change", async () => {
    if (!checkDateValidity()) {
        return;
    }

    hideMessage();
    await syncDateToSelectedSlot();
});

slot?.addEventListener("change", async () => {
    await syncDateToSelectedSlot();
});

people?.addEventListener("input", checkCapacity);

checkDateValidity();
checkCapacity();
loadRoomSlots().then(() => syncDateToSelectedSlot());
