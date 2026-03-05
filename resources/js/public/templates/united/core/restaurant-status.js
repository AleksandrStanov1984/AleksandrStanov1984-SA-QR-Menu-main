function updateRestaurantStatus(hours) {

    const now = new Date();

    const day = now.getDay();

    const time =
        now.getHours() * 60 +
        now.getMinutes();

    const today = hours[day];

    if (!today) return;

    const open = parseTime(today.open);
    const close = parseTime(today.close);

    const status = document.getElementById("restaurantStatus");
    const label = document.getElementById("statusLabel");
    const dot = status.querySelector(".status-dot");

    if (time >= open && time < close) {

        if (close - time <= 60) {

            status.classList.add("status-closing");
            dot.classList.add("status-dot-closing");

            label.textContent = "Closing soon";

        } else {

            status.classList.add("status-open");
            dot.classList.add("status-dot-open");

            label.textContent = "Open";

        }

    } else {

        status.classList.add("status-closed");

        label.textContent = "Closed";

    }

}

function parseTime(t) {

    const [h,m] = t.split(":");

    return parseInt(h)*60 + parseInt(m);

}
