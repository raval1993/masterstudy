"use strict";

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
  var _window$date_helpers_;
  var options = {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  };
  var dateObj = new Date(date);
  if ((_window$date_helpers_ = window.date_helpers_data) !== null && _window$date_helpers_ !== void 0 && _window$date_helpers_.short_months) {
    var _window$date_helpers_2;
    var year = dateObj.getFullYear();
    var day = dateObj.getDate();
    var month = dateObj.getMonth();
    return "".concat((_window$date_helpers_2 = window.date_helpers_data) === null || _window$date_helpers_2 === void 0 ? void 0 : _window$date_helpers_2.short_months[month], " ").concat(day, ", ").concat(year);
  }
  return dateObj.toLocaleDateString(undefined, options);
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