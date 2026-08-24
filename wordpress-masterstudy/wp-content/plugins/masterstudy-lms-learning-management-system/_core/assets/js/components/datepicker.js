"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
var defaultDateRanges = getDefaultDateRanges();
var selectedPeriod = defaultDateRanges.this_month;
function createDatepicker(selector) {
  var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var localeObject = flatpickr.l10ns[datepicker_data.locale['current_locale']];
  var defaultOptions = {
    inline: true,
    mode: 'range',
    monthSelectorType: 'static',
    locale: _objectSpread(_objectSpread({}, localeObject), {}, {
      firstDayOfWeek: datepicker_data.locale['firstDayOfWeek']
    })
  };
  var finalOptions = Object.assign({}, defaultOptions, options);
  return flatpickr(selector, finalOptions);
}
function closeDatepickerModal() {
  document.querySelector('.masterstudy-datepicker-modal').classList.remove('masterstudy-datepicker-modal_open');
  document.body.classList.remove('masterstudy-datepicker-body-hidden');
}
function updateDates(period) {
  var datepicker = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  var firstTime = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  if (!period) {
    return;
  }
  var periodStart = resetTime(period[0]);
  var periodEnd = resetTime(period[1]);
  var selectedStart = resetTime(selectedPeriod[0]);
  var selectedEnd = resetTime(selectedPeriod[1]);
  if (!firstTime && periodStart.getTime() === selectedStart.getTime() && periodEnd.getTime() === selectedEnd.getTime()) {
    return;
  }
  selectedPeriod = period;
  document.querySelectorAll('.masterstudy-datepicker-modal__single-item').forEach(function (item) {
    var periodKey = item.id.replace('masterstudy-datepicker-modal-', '');
    if (defaultDateRanges[periodKey][0].toDateString() === selectedPeriod[0].toDateString() && defaultDateRanges[periodKey][1].toDateString() === selectedPeriod[1].toDateString()) {
      item.classList.add('masterstudy-datepicker-modal__single-item_fill');
      if (document.querySelector('.masterstudy-date-field-label')) {
        document.querySelector('.masterstudy-date-field-label').textContent = item.textContent.trim();
      }
    } else {
      item.classList.remove('masterstudy-datepicker-modal__single-item_fill');
    }
  });
  if (!firstTime) {
    var event = new CustomEvent('datesUpdated', {
      detail: {
        selectedPeriod: selectedPeriod
      }
    });
    document.dispatchEvent(event);
  }
  if (datepicker) {
    datepicker.setDate(selectedPeriod, true);
  }
  if (document.querySelector('.masterstudy-date-field-value')) {
    document.querySelector('.masterstudy-date-field-value').textContent = "".concat(formatDate(selectedPeriod[0]), " - ").concat(formatDate(selectedPeriod[1]));
  }
  document.querySelectorAll('.masterstudy-datepicker-modal__single-item').forEach(function (item) {
    item.classList.remove('masterstudy-datepicker-modal__single-item_fill');
  });
  if (document.querySelector('.masterstudy-date-field-label')) {
    document.querySelector('.masterstudy-date-field-label').textContent = datepicker_data.custom_period;
  }
}
function initializeDatepicker(selector) {
  var datepickerElement = document.querySelector(selector);
  if (!datepickerElement) {
    console.error("Element not found for selector: ".concat(selector));
    return;
  }
  var datepicker = createDatepicker(selector, {
    dateFormat: 'M d, Y',
    defaultDate: selectedPeriod,
    maxDate: new Date(),
    onClose: function onClose(selectedDates, dateStr, instance) {
      updateDates(selectedDates, datepicker);
      closeDatepickerModal();
    }
  });
  if (!(selectedPeriod[0] instanceof Date)) {
    selectedPeriod = selectedPeriod.map(function (dateStr) {
      return new Date(dateStr);
    });
  }
  updateDates(selectedPeriod, datepicker, true);
  document.querySelector('.masterstudy-datepicker-modal__reset').addEventListener('click', function () {
    datepicker.setDate(defaultDateRanges.this_week, true);
    updateDates(defaultDateRanges.this_week, datepicker);
    document.querySelector('#masterstudy-datepicker-modal-this_week').classList.add('masterstudy-datepicker-modal__single-item_fill');
    Array.from(document.querySelector('#masterstudy-datepicker-modal-this_week').parentNode.children).forEach(function (sibling) {
      if (sibling !== document.querySelector('#masterstudy-datepicker-modal-this_week')) {
        sibling.classList.remove('masterstudy-datepicker-modal__single-item_fill');
      }
    });
  });
  document.querySelector('.masterstudy-datepicker-modal__close').addEventListener('click', function () {
    closeDatepickerModal();
  });
  document.querySelectorAll('.masterstudy-datepicker-modal__single-item').forEach(function (item) {
    item.addEventListener('click', function () {
      var period = this.id.replace('masterstudy-datepicker-modal-', '');
      if (defaultDateRanges[period]) {
        datepicker.setDate(defaultDateRanges[period], true);
        updateDates(defaultDateRanges[period], datepicker);
        if (document.querySelector('.masterstudy-date-field-label')) {
          document.querySelector('.masterstudy-date-field-label').textContent = this.textContent.trim();
        }
        closeDatepickerModal();
      }
    });
  });
  if (document.querySelector('.masterstudy-date-field')) {
    document.querySelector('.masterstudy-date-field').addEventListener('click', function () {
      document.querySelector('.masterstudy-datepicker-modal').classList.add('masterstudy-datepicker-modal_open');
      document.body.classList.add('masterstudy-datepicker-body-hidden');
    });
  }
  document.querySelector('.masterstudy-datepicker-modal').addEventListener('click', function (event) {
    if (event.target === this) {
      closeDatepickerModal();
    }
  });
}
function getDateFrom() {
  return formatDateForFetch(selectedPeriod[0]);
}
function getDateTo() {
  return formatDateForFetch(selectedPeriod[1]);
}
function getDefaultDateRanges() {
  var now = new Date();
  var today = [new Date(), new Date()];
  var yesterday = [new Date(now.setDate(now.getDate() - 1)), new Date(now)];
  var startOfThisWeek = new Date(now.setDate(now.getDate() - now.getDay() + 1));
  var thisWeek = [new Date(startOfThisWeek), new Date()];
  var startOfLastWeek = new Date(now.setDate(now.getDate() - now.getDay() - 6));
  var endOfLastWeek = new Date(now.setDate(startOfLastWeek.getDate() + 6));
  var lastWeek = [startOfLastWeek, endOfLastWeek];
  var startOfThisMonth = new Date(now.getFullYear(), now.getMonth(), 1);
  var thisMonth = [startOfThisMonth, new Date()];
  var startOfLastMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1);
  var endOfLastMonth = new Date(now.getFullYear(), now.getMonth(), 0);
  var lastMonth = [startOfLastMonth, endOfLastMonth];
  var startOfThisYear = new Date(now.getFullYear(), 0, 1);
  var thisYear = [startOfThisYear, new Date()];
  var startOfLastYear = new Date(now.getFullYear() - 1, 0, 1);
  var endOfLastYear = new Date(now.getFullYear() - 1, 11, 31);
  var lastYear = [startOfLastYear, endOfLastYear];
  var allTime = [new Date(0), new Date()];
  return {
    today: today,
    yesterday: yesterday,
    this_week: thisWeek,
    last_week: lastWeek,
    this_month: thisMonth,
    last_month: lastMonth,
    this_year: thisYear,
    last_year: lastYear,
    all_time: allTime
  };
}
function resetTime(date) {
  var d = typeof date === 'string' ? new Date(date) : date;
  return new Date(d.getFullYear(), d.getMonth(), d.getDate());
}
function formatDate(date) {
  var _window$datepicker_da;
  var options = {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  };
  var dateObj = new Date(date);
  if ((_window$datepicker_da = window.datepicker_data) !== null && _window$datepicker_da !== void 0 && _window$datepicker_da.short_months) {
    var _window$datepicker_da2;
    var year = dateObj.getFullYear();
    var day = dateObj.getDate();
    var month = dateObj.getMonth();
    return "".concat((_window$datepicker_da2 = window.datepicker_data) === null || _window$datepicker_da2 === void 0 ? void 0 : _window$datepicker_da2.short_months[month], " ").concat(day, ", ").concat(year);
  }
  return dateObj.toLocaleDateString('en-US', options);
}
function formatDateForFetch(date) {
  if (!date) {
    return '';
  }
  var d = new Date(date);
  var year = d.getFullYear();
  var month = String(d.getMonth() + 1).padStart(2, '0');
  var day = String(d.getDate()).padStart(2, '0');
  return "".concat(year, "-").concat(month, "-").concat(day);
}