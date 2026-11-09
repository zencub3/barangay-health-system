
<style>
    .capacity-calendar {
        margin: 16px 0;
        padding: 18px;
        border: 1px solid #dce9df;
        border-radius: 15px;
        background: #fff;
        color: #184e37;
    }

    .capacity-calendar-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .capacity-calendar-title {
        font-size: 16px;
        font-weight: 700;
    }

    .capacity-calendar-nav {
        background: #e8f5ec;
        color: #216e4f;
        border: 0;
        border-radius: 8px;
        padding: 7px 11px;
        cursor: pointer;
    }

    .capacity-calendar-weekdays,
    .capacity-calendar-days {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 5px;
    }

    .capacity-calendar-weekdays {
        margin-bottom: 6px;
        font-size: 11px;
        text-align: center;
        color: #789080;
    }

    .capacity-day {
        min-height: 42px;
        border-radius: 9px;
        border: 1px solid #dce9df;
        background: #f7fbf8;
        color: #184e37;
        cursor: pointer;
        font-size: 13px;
    }

    .capacity-day:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .capacity-day.full {
        background: #ffeaea;
        border-color: #e5baba;
        color: #a12b2b;
    }

    .capacity-day.selected {
        background: #216e4f;
        color: #fff;
        border-color: #216e4f;
    }

    .capacity-calendar-help {
        font-size: 12px;
        color: #60776a;
        margin-top: 12px;
        line-height: 1.6;
    }

    .capacity-calendar-slots {
        margin-top: 14px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .capacity-slot {
        padding: 9px 12px;
        border-radius: 9px;
        font-size: 12px;
        background: #e8f5ec;
        color: #216e4f;
    }

    .capacity-slot.full {
        color: #a12b2b;
        background: #ffeaea;
    }

    .capacity-calendar-message {
        margin-top: 10px;
        font-size: 12px;
        color: #a12b2b;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const dateInput = document.querySelector(
        'input[name="consultation_date"]'
    );

    const slotSelect = document.querySelector(
        'select[name="time_slot"]'
    );

    const slotRadios = Array.from(
        document.querySelectorAll(
            'input[type="radio"][name="time_slot"]'
        )
    );

    const form = dateInput ? dateInput.closest('form') : null;

    if (!dateInput || !form) return;

    const calendar = document.createElement('section');

    calendar.className = 'capacity-calendar';

    calendar.setAttribute(
        'aria-label',
        'Appointment availability calendar'
    );

    calendar.innerHTML = `
        <div class="capacity-calendar-top">
            <button
                type="button"
                class="capacity-calendar-nav"
                data-prev
                aria-label="Previous month"
            >&#8249;</button>

            <span class="capacity-calendar-title" data-title></span>

            <button
                type="button"
                class="capacity-calendar-nav"
                data-next
                aria-label="Next month"
            >&#8250;</button>
        </div>

        <div class="capacity-calendar-weekdays">
            <span>Sun</span>
            <span>Mon</span>
            <span>Tue</span>
            <span>Wed</span>
            <span>Thu</span>
            <span>Fri</span>
            <span>Sat</span>
        </div>

        <div class="capacity-calendar-days" data-days></div>

        <p class="capacity-calendar-help">
            Select a date. Each session has 5 available
            appointments. Red dates are fully booked;
            grey dates are in the past.
        </p>

        <div
            class="capacity-calendar-slots"
            data-slots
            aria-live="polite"
        ></div>

        <p
            class="capacity-calendar-message"
            data-message
            role="status"
        ></p>
    `;

    dateInput.insertAdjacentElement('afterend', calendar);

    const title = calendar.querySelector('[data-title]');
    const days = calendar.querySelector('[data-days]');
    const slots = calendar.querySelector('[data-slots]');
    const message = calendar.querySelector('[data-message]');

    const now = new Date();

    const today = [
        now.getFullYear(),
        String(now.getMonth() + 1).padStart(2, '0'),
        String(now.getDate()).padStart(2, '0')
    ].join('-');

    let year = now.getFullYear();
    let month = now.getMonth();

    let records = {};
    let loaded = false;
    let requestVersion = 0;

    function normalizeSlot(value) {
        const name = String(value || '')
            .trim()
            .toLowerCase();

        if (name.startsWith('morning')) {
            return 'Morning';
        }

        if (name.startsWith('afternoon')) {
            return 'Afternoon';
        }

        return '';
    }

    function remaining(day, session) {
        return Math.max(
            0,
            5 - Number(records[day]?.[session] ?? 0)
        );
    }

    function selectedSlot() {
        if (slotSelect) {
            return slotSelect.value;
        }

        return slotRadios.find(radio => radio.checked)?.value || '';
    }

    function syncSlotInputs() {
        const day = dateInput.value;

        // Update existing dropdown options.
        if (slotSelect) {
            for (const option of Array.from(slotSelect.options)) {
                const session = normalizeSlot(option.value);

                if (!session) continue;

                option.disabled =
                    !day ||
                    !loaded ||
                    remaining(day, session) === 0;
            }
        }

        // Also support forms that use radio buttons.
        for (const radio of slotRadios) {
            const session = normalizeSlot(radio.value);

            if (!session) continue;

            radio.disabled =
                !day ||
                !loaded ||
                remaining(day, session) === 0;
        }

        // Clear a selected time slot only when unavailable.
        const session = normalizeSlot(selectedSlot());

        if (session) {
            if (
                !day ||
                !loaded ||
                remaining(day, session) === 0
            ) {
                if (slotSelect) {
                    slotSelect.value = '';
                }

                slotRadios.forEach(radio => {
                    radio.checked = false;
                });
            }
        }

        slots.replaceChildren();

        // Display available appointments for the chosen day.
        if (day && loaded) {
            for (const session of ['Morning', 'Afternoon']) {
                const count = remaining(day, session);

                const item = document.createElement('span');

                item.className = 'capacity-slot' +
                    (count === 0 ? ' full' : '');

                item.textContent = count === 0
                    ? `${session}: Fully booked`
                    : `${session}: ${count} / 5 available`;

                slots.appendChild(item);
            }
        }
    }

    function render() {
        title.textContent = new Date(
            year,
            month,
            1
        ).toLocaleDateString('en-US', {
            month: 'long',
            year: 'numeric'
        });

        days.replaceChildren();

        const leading = new Date(
            year,
            month,
            1
        ).getDay();

        for (let i = 0; i < leading; i++) {
            days.appendChild(document.createElement('span'));
        }

        const count = new Date(
            year,
            month + 1,
            0
        ).getDate();

        for (let d = 1; d <= count; d++) {
            const day = [
                year,
                String(month + 1).padStart(2, '0'),
                String(d).padStart(2, '0')
            ].join('-');

            const full =
                remaining(day, 'Morning') === 0 &&
                remaining(day, 'Afternoon') === 0;

            const button = document.createElement('button');

            button.type = 'button';

            button.className = 'capacity-day' +
                (full ? ' full' : '') +
                (dateInput.value === day ? ' selected' : '');

            button.textContent = String(d);

            button.disabled =
                !loaded ||
                day < today ||
                full;

            button.title = day < today
                ? 'Past date'
                : full
                    ? 'Fully booked'
                    : 'Choose date';

            button.setAttribute(
                'aria-label',
                `${day}${full ? ', fully booked' : ''}`
            );

            button.addEventListener('click', function () {
                dateInput.value = day;

                dateInput.dispatchEvent(
                    new Event('change', { bubbles: true })
                );

                message.textContent = '';

                render();
            });

            days.appendChild(button);
        }

        syncSlotInputs();
    }

    async function load() {
        const currentRequest = ++requestVersion;

        loaded = false;

        message.textContent =
            'Checking appointment availability...';

        render();

        const ym = [
            year,
            String(month + 1).padStart(2, '0')
        ].join('-');

        try {
            const response = await fetch(
                `{{ route('appointments.availability') }}?month=${encodeURIComponent(ym)}`,
                {
                    headers: {
                        Accept: 'application/json'
                    },
                    credentials: 'same-origin'
                }
            );

            if (!response.ok) {
                throw new Error(
                    'Availability could not be loaded.'
                );
            }

            const payload = await response.json();

            if (currentRequest !== requestVersion) return;

            records = payload.days || {};
            loaded = true;
            message.textContent = '';

        } catch (error) {
            if (currentRequest !== requestVersion) return;

            records = {};
            loaded = false;

            message.textContent =
                'Could not check availability. Please refresh and try again.';
        }

        render();
    }

    // Previous month navigation.
    calendar.querySelector('[data-prev]')
        .addEventListener('click', function () {

            const first = new Date(
                year,
                month - 1,
                1
            );

            if (
                first < new Date(
                    now.getFullYear(),
                    now.getMonth(),
                    1
                )
            ) {
                return;
            }

            year = first.getFullYear();
            month = first.getMonth();

            load();
        });

    // Next month navigation.
    calendar.querySelector('[data-next]')
        .addEventListener('click', function () {

            const first = new Date(
                year,
                month + 1,
                1
            );

            year = first.getFullYear();
            month = first.getMonth();

            load();
        });

    // Keep calendar synchronized with existing date input.
    dateInput.addEventListener('change', function () {
        const value = dateInput.value;

        if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
            const [y, m] = value.split('-').map(Number);

            if (
                year !== y ||
                month !== m - 1
            ) {
                year = y;
                month = m - 1;

                load();
                return;
            }
        }

        render();
    });

    // Prevent submitting unavailable bookings.
    form.addEventListener('submit', function (event) {
        const day = dateInput.value;

        // FIX: Recognize full time-slot names.
        const session = normalizeSlot(selectedSlot());

        if (
            !loaded ||
            !day ||
            day < today ||
            !session ||
            remaining(day, session) === 0
        ) {
            event.preventDefault();

            message.textContent =
                'Please choose an available date and time slot.';
        }
    });

    // Load the month from an existing selected date.
    if (
        dateInput.value &&
        /^\d{4}-\d{2}-\d{2}$/.test(dateInput.value)
    ) {
        const [y, m] = dateInput.value
            .split('-')
            .map(Number);

        year = y;
        month = m - 1;
    }

    load();
});
</script>
