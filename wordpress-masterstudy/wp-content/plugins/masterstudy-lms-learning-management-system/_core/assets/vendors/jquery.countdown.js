"use strict";

(function ($) {
  // Number of seconds in every time division
  const SECONDS_IN_A_DAY = 24 * 60 * 60,
    SECONDS_IN_AN_HOUR = 60 * 60,
    SECONDS_IN_A_MINUTE = 60;

  $.fn.countdown = function (prop) {
    if (prop === undefined) return
    const options = $.extend(
      {
        callback: function () {},
        timestamp: 0,
      },
      prop,
    );

    let timeLeft, daysLeft, hoursLeft, minutesLeft, secondsLeft, positions;
    const reloadStorageKey = `masterstudy_countdown_reload_${window.location.pathname}_${options.timestamp}`;
    let reloadAttempted = false;
    init(this, options);
    positions = this.find(".position");

    (function tick() {
      // Time left
      timeLeft = Math.floor((options.timestamp - new Date()) / 1000);

      if (timeLeft < 0) {
        timeLeft = 0;

        // Prevent reload recursion when stale HTML/client clock keeps countdown expired.
        const canUseStorage = typeof window.sessionStorage !== "undefined";
        const hasReloaded = canUseStorage && window.sessionStorage.getItem(reloadStorageKey) === "1";

        if (!reloadAttempted && !hasReloaded) {
          reloadAttempted = true;
          if (canUseStorage) {
            window.sessionStorage.setItem(reloadStorageKey, "1");
          }
          window.location.reload();
          return;
        }
      }

      // Number of days left
      daysLeft = Math.floor(timeLeft / SECONDS_IN_A_DAY);
      const digitLength = daysLeft >= 100 ? 3 : 2;
      updateDigits(0, daysLeft, digitLength);
      timeLeft -= daysLeft * SECONDS_IN_A_DAY;

      // Number of hours left
      hoursLeft = Math.floor(timeLeft / SECONDS_IN_AN_HOUR);
      updateDigits(digitLength, hoursLeft, 2);
      timeLeft -= hoursLeft * SECONDS_IN_AN_HOUR;

      // Number of minutes left
      minutesLeft = Math.floor(timeLeft / SECONDS_IN_A_MINUTE);
      updateDigits(digitLength + 2, minutesLeft, 2);
      timeLeft -= minutesLeft * SECONDS_IN_A_MINUTE;

      // Number of seconds left
      secondsLeft = timeLeft;
      updateDigits(digitLength + 4, secondsLeft, 2);

      // Calling an optional user supplied callback
      options.callback(daysLeft, hoursLeft, minutesLeft, secondsLeft);

      // Scheduling another call of this function in 1s
      setTimeout(tick, 1000);
    })();

    function updateDigits(start, value, length) {
      const valStr = value.toString().padStart(length, "0");
      for (let i = 0; i < length; i++) {
        switchDigit(positions.eq(start + i), valStr[i]);
      }
    }

    return this;
  };

  function init(elem, options) {
    elem.addClass("countdownHolder");

    $.each(
      {
        Days: stm_lms_jquery_countdown_vars['days'] ?? "Days",
        Hours: stm_lms_jquery_countdown_vars['hours'] ?? "Hours",
        Minutes: stm_lms_jquery_countdown_vars['minutes'] ?? "Minutes",
        Seconds: stm_lms_jquery_countdown_vars['seconds'] ?? "Seconds",
      },
      function (key, label) {
        const currentDate = new Date();
        const targetDate = new Date(options.timestamp);
        const timeDiff = targetDate.getTime() - currentDate.getTime();
        const daysLeft = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        const digitLength = key === "Days" && daysLeft >= 100 ? 3 : 2;

        const count = $(`<span class="count${key}">`).appendTo(elem);
        count.append(`<div class="countdown_label h4">${label}</div>`);
        for (let i = 0; i < digitLength; i++) {
          count.append(
            `<span class="position h1">\
              <span class="digit static h1">0</span>\
            </span>`,
          );
        }
        if (key != "Seconds") {
          elem.append(`<span class="countDiv countDiv${label}"></span>`);
        }
      },
    );
  }

  function switchDigit(position, number) {
    const digit = position.find(".digit");

    if (digit.is(":animated")) {
      return false;
    }

    if (position.data("digit") == number) {
      return false;
    }

    position.data("digit", number);
    const replacement = $("<span>", {
      class: "digit",
      css: {
        top: "-20px",
        opacity: 0,
      },
      html: number,
    });

    digit
      .before(replacement)
      .removeClass("static")
      .animate(
        {
          top: "20px",
          opacity: 0,
        },
        "fast",
        function () {
          digit.remove();
        },
      );

    replacement.delay(100).animate(
      {
        top: 0,
        opacity: 1,
      },
      "fast",
      function () {
        replacement.addClass("static");
      },
    );
  }
})(jQuery);
