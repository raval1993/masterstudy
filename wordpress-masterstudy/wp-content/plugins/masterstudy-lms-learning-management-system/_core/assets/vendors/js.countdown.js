const days = 24 * 60 * 60;
const hours = 60 * 60;
const minutes = 60;

function countdown(container, countdownElement, prop) {
  const options = Object.assign(
    {
      callback: function () {},
      timestamp: 0,
    },
    prop,
  );

  let left, d, h, m, s, positions;

  init(container, countdownElement, options);
  positions = countdownElement.querySelectorAll(".position");

  (function tick() {
    left = Math.floor((options.timestamp - new Date()) / 1000);

    if (left < 0) {
      left = 0;
    }

    d = Math.floor(left / days);
    const digitLength = d >= 100 ? 3 : 2;
    updateDuo(0, digitLength, d);
    left -= d * days;

    h = Math.floor(left / hours);
    updateDuo(digitLength, 2, h);
    left -= h * hours;

    m = Math.floor(left / minutes);
    updateDuo(digitLength + 2, 2, m);
    left -= m * minutes;

    s = left;
    updateDuo(digitLength + 4, 2, s);

    options.callback(d, h, m, s);
    setTimeout(tick, 1000);
  })();

  function updateDuo(start, length, value) {
    const valStr = value.toString().padStart(length, "0");
    for (let i = 0; i < length; i++) {
      switchDigit(positions[start + i], valStr[i]);
    }
  }

  return countdownElement;
}

function init(container, countdownElement, options) {
  const { dataset } = container.querySelector(
    ".lms-course-list-item-countdown",
  );

  countdownElement.classList.add("countdownHolder");
  const timeUnits = ["Days", "Hours", "Minutes", "Seconds"];
  const timeLabels = [
    dataset.days,
    dataset.hours,
    dataset.minutes,
    dataset.seconds,
  ];

  timeUnits.forEach((unit, index) => {
    const label = timeLabels[index];
    const unitElement = document.createElement("span");
    unitElement.className = "count" + unit;

    if (unit === "Days") {
      const digitLength =
        Math.floor((options.timestamp - new Date()) / (1000 * 60 * 60 * 24)) >=
        100
          ? 3
          : 2;
      unitElement.innerHTML =
        `<div class="countdown_label">${label}</div>` +
        Array.from({ length: digitLength })
          .map(
            () => `
          <span class="position">
            <span class="digit static">0</span>
          </span>
        `,
          )
          .join("");
    } else {
      unitElement.innerHTML = `<div class="countdown_label">${label}</div>
        <span class="position">
          <span class="digit static">0</span>
        </span>
        <span class="position">
          <span class="digit static">0</span>
        </span>`;
    }
    countdownElement.appendChild(unitElement);

    if (unit !== "Seconds") {
      const divElement = document.createElement("span");
      divElement.className = `countDiv countDiv${index}`;
      countdownElement.appendChild(divElement);
    }
  });
}

function switchDigit(position, number) {
  const digit = position.querySelector(".digit");
  const currentNumber = digit.textContent;

  if (currentNumber == number) {
    return;
  }

  const replacement = document.createElement("span");
  replacement.className = "digit";
  replacement.textContent = number;

  position.dataset.digit = number;
  position.appendChild(replacement);

  digit.classList.remove("static");
  replacement.style.opacity = "0";

  setTimeout(() => {
    digit.remove();
    replacement.classList.add("static");
    replacement.style.opacity = "1";
  }, 250);
}
