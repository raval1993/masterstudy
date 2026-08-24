(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.attachPaginationClickHandlers = attachPaginationClickHandlers;
exports.bindPerPageHandler = bindPerPageHandler;
exports.renderPagination = renderPagination;
exports.updatePaginationView = updatePaginationView;
window.$ = jQuery;
function updatePaginationView(totalPages, currentPage, container) {
  var $container = $(container);
  var $paginationItems = $container.find(".masterstudy-pagination__item");
  var $paginationBlocks = $container.find(".masterstudy-pagination__item-block");
  var $buttonNext = $container.find(".masterstudy-pagination__button-next");
  var $buttonPrev = $container.find(".masterstudy-pagination__button-prev");
  $paginationItems.removeClass('masterstudy-pagination__item_current').hide();
  var start = Math.max(1, currentPage - 1);
  var end = Math.min(totalPages, currentPage + 1);
  if (currentPage === 1 || start === 1) end = Math.min(totalPages, start + 2);
  if (currentPage === totalPages || end === totalPages) start = Math.max(1, end - 2);
  for (var i = start; i <= end; i++) {
    $paginationItems.filter(":has([data-id=\"".concat(i, "\"])")).show();
  }
  $paginationBlocks.filter("[data-id=\"".concat(currentPage, "\"]")).parent().addClass('masterstudy-pagination__item_current');
  $buttonNext.toggle(currentPage < totalPages);
  $buttonPrev.toggle(currentPage > 1);
}
function attachPaginationClickHandlers(totalPages, onPageChange, getPerPageSelector, container) {
  var $container = $(container);
  var $paginationBlocks = $container.find(".masterstudy-pagination__item-block");
  var $buttonPrev = $container.find(".masterstudy-pagination__button-prev");
  var $buttonNext = $container.find(".masterstudy-pagination__button-next");
  $paginationBlocks.off("click").on("click", function () {
    if ($(this).parent().hasClass('masterstudy-pagination__item_current')) {
      return;
    }
    var page = $(this).data("id");
    onPageChange($(getPerPageSelector()).val(), page);
  });
  $buttonPrev.off("click").on("click", function () {
    var current = $container.find(".masterstudy-pagination__item_current .masterstudy-pagination__item-block").data("id");
    if (current > 1) onPageChange($(getPerPageSelector()).val(), current - 1);
  });
  $buttonNext.off("click").on("click", function () {
    var current = $container.find(".masterstudy-pagination__item_current .masterstudy-pagination__item-block").data("id");
    var total = $paginationBlocks.length;
    if (current < total) onPageChange($(getPerPageSelector()).val(), current + 1);
  });
}
function bindPerPageHandler(containerSelector, perPage, fetchFn) {
  $(".masterstudy-select__option, .masterstudy-select__clear", perPage).off("click").on("click", function () {
    $(containerSelector).remove();
    fetchFn($(this).data("value"));
  });
}
function renderPagination(_ref) {
  var ajaxurl = _ref.ajaxurl,
    nonce = _ref.nonce,
    totalPages = _ref.totalPages,
    currentPage = _ref.currentPage,
    paginationContainer = _ref.paginationContainer,
    onPageChange = _ref.onPageChange,
    getPerPageSelector = _ref.getPerPageSelector;
  $.post(ajaxurl, {
    action: "get_pagination",
    total_pages: totalPages,
    current_page: currentPage,
    _ajax_nonce: nonce
  }, function (response) {
    if (response.success) {
      var $nav = $(paginationContainer);
      $nav.toggle(totalPages > 1).html(response.data.pagination);
      attachPaginationClickHandlers(totalPages, onPageChange, getPerPageSelector, paginationContainer);
      updatePaginationView(totalPages, currentPage, paginationContainer);
    }
  });
}

},{}],2:[function(require,module,exports){
"use strict";

var _utils = require("../enrolled-quizzes/modules/utils.js");
(function ($) {
  var config = {
      selectors: {
        container: '.masterstudy-table-list-items',
        loading: 'items-loading',
        no_found: '.masterstudy-table-list-no-found__info',
        row: '.masterstudy-table-list__row',
        search_input: '.masterstudy-form-search__input',
        checkboxAll: '#masterstudy-table-list-checkbox',
        checkbox: 'input[name="student[]"]',
        per_page: '#items-per-page',
        navigation: '.masterstudy-table-list-navigation',
        pagination: '.masterstudy-table-list-navigation__pagination',
        perPage: '.masterstudy-table-list-navigation__per-page',
        "export": '[data-id="export-students-to-csv"]',
        selectByCourse: '.filter-students-by-courses',
        deleteBtn: '[data-id="masterstudy-students-delete"]',
        modalDelete: '[data-id="masterstudy-delete-students"]',
        topBar: '.masterstudy-table-list__top-bar'
      },
      templates: {
        no_found: 'masterstudy-table-list-no-found-template',
        row: 'masterstudy-table-list-row-template'
      },
      endpoints: {
        students: '/students/',
        deleting: '/students/delete/',
        courses: '/courses',
        exportStudents: '/export/students/'
      },
      apiBase: ms_lms_resturl,
      nonce: ms_lms_nonce
    },
    totalPages = 1,
    courseId = '';
  $(document).ready(function () {
    if ($('.masterstudy-students-list').length) {
      init();
    }
  });
  function init() {
    (0, _utils.bindPerPageHandler)($(config.selectors.row, config.selectors.container), config.selectors.perPage, fetchItems);
    fetchItems();
    initSearch();
    checkAll();
    deleteStudents();
    searchByCourse();
    exportStudents();
    dateFilter();
    itemsSort();
  }
  function fetchItems() {
    var perPage = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : undefined;
    var currentPage = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1;
    var orderby = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
    var order = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : '';
    var url = config.apiBase + config.endpoints.students;
    var query = [];
    var $input = $(config.selectors.search_input);
    var searchQuery = $input.length ? $input.val().trim() : '';
    var dateFrom = getDateFrom();
    var dateTo = getDateTo();
    query.push("show_all_enrolled=1");
    if (searchQuery) query.push("s=".concat(encodeURIComponent(searchQuery)));
    if (perPage) query.push("per_page=".concat(perPage));
    if (currentPage) query.push("page=".concat(currentPage));
    if (courseId) query.push("course_id=".concat(courseId));
    if (dateFrom) query.push("date_from=".concat(dateFrom));
    if (dateTo) query.push("date_to=".concat(dateTo));
    if (orderby) query.push("orderby=".concat(orderby));
    if (order) query.push("order=".concat(order));
    if (query.length) url += "?".concat(query.join("&"));
    (0, _utils.updatePaginationView)(totalPages, currentPage);
    var $container = $(config.selectors.container);
    $("".concat(config.selectors.row, ", ").concat(config.selectors.no_found), config.selectors.container).remove();
    $container.addClass(config.selectors.loading);
    $(config.selectors.navigation).hide();
    fetch(url, {
      headers: {
        "X-WP-Nonce": config.nonce,
        "Content-Type": "application/json"
      }
    }).then(function (res) {
      return res.json();
    }).then(function (data) {
      $container.css("height", "auto").removeClass(config.selectors.loading);
      $("".concat(config.selectors.row, ", ").concat(config.selectors.no_found), config.selectors.container).remove();
      updatePagination(data.pages, currentPage);
      if (!data.students || data.students.length === 0) {
        var template = document.getElementById(config.templates.no_found);
        if (template) {
          var clone = template.content.cloneNode(true);
          $(config.selectors.navigation).hide();
          $container.append(clone);
        }
        return;
      }
      $(config.selectors.navigation).show();
      totalPages = data.pages;
      (data.students || []).forEach(function (item) {
        var html = renderItemTemplate(item);
        $container.append(html);
      });
    })["catch"](function (err) {
      console.error("Error fetching items:", err);
      $container.css("height", "auto").removeClass(config.selectors.loading);
    });
  }
  function renderItemTemplate(item) {
    var template = document.getElementById(config.templates.row);
    if (!template) return '';
    var clone = template.content.cloneNode(true);
    var url = new URL(item.url, window.location.origin);
    clone.querySelector('[name="student[]"]').value = item.user_id;
    if (clone.querySelector('.masterstudy-table-list__row--link')) {
      clone.querySelector('.masterstudy-table-list__row--link').href = url.toString();
    }
    clone.querySelector('.masterstudy-table-list__td--name').textContent = item.display_name;
    clone.querySelector('.masterstudy-table-list__td--email').textContent = item.email;
    clone.querySelector('.masterstudy-table-list__td--joined').textContent = item.registered;
    clone.querySelector('.masterstudy-table-list__td--enrolled').textContent = item.enrolled;
    if (clone.querySelector('.masterstudy-table-list__td--points')) {
      clone.querySelector('.masterstudy-table-list__td--points').textContent = item.points;
    }
    return clone;
  }
  function updatePagination(totalPages, currentPage) {
    (0, _utils.renderPagination)({
      ajaxurl: stm_lms_ajaxurl,
      nonce: config.nonce,
      totalPages: totalPages,
      currentPage: currentPage,
      paginationContainer: config.selectors.pagination,
      onPageChange: fetchItems,
      getPerPageSelector: function getPerPageSelector() {
        return config.selectors.per_page;
      }
    });
  }
  function initSearch() {
    var $input = $(config.selectors.search_input);
    if (!$input.length) return;
    var timer;
    var lastQuery = '';
    $input.off("input").on("input", function () {
      clearTimeout(timer);
      timer = setTimeout(function () {
        var query = $input.val().trim();
        if (query !== lastQuery) {
          lastQuery = query;
          fetchItems($(config.selectors.per_page).val(), 1);
        }
      }, 300);
    });
  }
  function checkAll() {
    var $selectAll = $(config.selectors.checkboxAll);
    var $deleteBtn = $(config.selectors.deleteBtn);
    if (!$selectAll.length) return;
    function updateDeleteBtn() {
      var anyChecked = $(config.selectors.checkbox).filter(':checked').length > 0;
      $deleteBtn.prop('disabled', !anyChecked);
    }
    $selectAll.on('change', function () {
      var isChecked = this.checked;
      $(config.selectors.checkbox).prop('checked', isChecked).trigger('change');
    });
    $(document).on('change', config.selectors.checkbox, function () {
      var $all = $(config.selectors.checkbox);
      var checkedCnt = $all.filter(':checked').length;
      $selectAll.prop('checked', checkedCnt === $all.length);
      updateDeleteBtn();
    });
    updateDeleteBtn();
  }
  function deleteStudents() {
    var url = config.apiBase + config.endpoints.deleting;
    var _config$selectors = config.selectors,
      checkboxAll = _config$selectors.checkboxAll,
      deleteBtn = _config$selectors.deleteBtn,
      modalDelete = _config$selectors.modalDelete,
      container = _config$selectors.container,
      row = _config$selectors.row,
      no_found = _config$selectors.no_found,
      loading = _config$selectors.loading,
      checkbox = _config$selectors.checkbox,
      per_page = _config$selectors.per_page;
    var students = [];
    $(deleteBtn).on('click', function (e) {
      e.preventDefault();
      students = $('input[name="student[]"]:checked').map(function () {
        return this.value;
      }).get();
      if (students.length) {
        $(modalDelete).addClass('masterstudy-alert_open');
      }
    });
    $(modalDelete).on('click', "[data-id='cancel'], .masterstudy-alert__header-close", function (e) {
      e.preventDefault();
      $(modalDelete).removeClass('masterstudy-alert_open');
    });
    $(modalDelete).on('click', "[data-id='submit']", function (e) {
      e.preventDefault();
      if (!students.length) return;
      $(container).find("".concat(row, ", ").concat(no_found)).remove();
      $(container).addClass(loading);
      $(modalDelete).removeClass('masterstudy-alert_open');
      $(checkbox).prop('checked', false);
      $(config.selectors.navigation).hide();
      $(checkboxAll).prop('checked', false);
      fetch(url, {
        method: 'DELETE',
        headers: {
          'X-WP-Nonce': config.nonce,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          students: students
        })
      }).then(function (res) {
        students = [];
        return res.json().then(function (data) {
          var $msg = $("<div class=\"stm-lms-message error\">".concat(data.message, "</div>"));
          if (['error', 'demo_forbidden_access'].includes(data.status) || 'demo_forbidden_access' === data.error_code) {
            $(config.selectors.topBar).after($msg);
            setTimeout(function () {
              $msg.remove();
            }, 5000);
          }
          return fetchItems($(per_page).val(), 1);
        });
      })["catch"](console.error);
    });
  }
  function searchByCourse() {
    var $input = $(config.selectors.selectByCourse);
    var $parent = $input.parent();
    var apiBase = config.apiBase,
      endpoints = config.endpoints,
      nonce = config.nonce;
    var URL = apiBase + endpoints.courses;
    var PER_PAGE = 20;
    var staticCourses = [];
    var staticTotalPages = 0;
    function fetchCourses() {
      var term = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
      var page = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1;
      return $.ajax({
        url: URL,
        method: 'GET',
        dataType: 'json',
        headers: {
          'X-WP-Nonce': nonce
        },
        data: {
          s: term,
          per_page: PER_PAGE,
          current_user: 1,
          page: page
        }
      });
    }
    function truncateWithEllipsis(text) {
      return text.length > 23 ? text.slice(0, 23) + '…' : text;
    }
    fetchCourses().done(function (response) {
      staticCourses = response.courses || [];
      staticTotalPages = parseInt(response.pages, 10) || 0;
    }).always(initSelect2);
    function initSelect2() {
      $parent.removeClass('filter-students-by-courses-default');
      $input.select2({
        dropdownParent: $parent,
        placeholder: $input.data('placeholder'),
        allowClear: true,
        minimumInputLength: 0,
        templateSelection: function templateSelection(data) {
          return truncateWithEllipsis(data.text);
        },
        escapeMarkup: function escapeMarkup(markup) {
          return markup;
        },
        ajax: {
          transport: function transport(params, success, failure) {
            var term = params.data.term || '';
            var page = parseInt(params.data.page, 10) || 1;
            if (!term && page === 1) {
              return success({
                results: staticCourses.map(function (item) {
                  return {
                    id: item.ID,
                    text: item.post_title
                  };
                }),
                pagination: {
                  more: staticTotalPages > 1
                }
              });
            }
            fetchCourses(term, page).done(function (response) {
              return success({
                results: (response.courses || []).map(function (item) {
                  return {
                    id: item.ID,
                    text: item.post_title
                  };
                }),
                pagination: {
                  more: page < (parseInt(response.pages, 10) || 0)
                }
              });
            }).fail(failure);
          },
          delay: 250,
          processResults: function processResults(data) {
            return data;
          },
          cache: true
        }
      });
      $input.on('select2:select select2:clear', function (e) {
        if (e.type === 'select2:select') {
          courseId = e.params.data.id;
        } else if (e.type === 'select2:clear') {
          courseId = null;
        }
        fetchItems($(config.selectors.per_page).val(), 1);
      });
    }
  }
  function exportStudents() {
    $(config.selectors["export"]).on('click', function () {
      var url = config.apiBase + config.endpoints.exportStudents;
      var query = [];
      var $selectByCourse = $(config.selectors.selectByCourse);
      var courseId = $selectByCourse.length ? $selectByCourse.val().trim() : '';
      var $inputSearch = $(config.selectors.search_input);
      var searchQuery = $inputSearch.length ? $inputSearch.val().trim() : '';
      var dateFrom = getDateFrom();
      var dateTo = getDateTo();
      query.push("show_all_enrolled=1");
      query.push("s=".concat(encodeURIComponent(searchQuery)));
      query.push("course_id=".concat(courseId));
      query.push("date_from=".concat(dateFrom));
      query.push("date_to=".concat(dateTo));
      if (query.length) url += "?".concat(query.join("&"));
      fetch(url, {
        headers: {
          "X-WP-Nonce": config.nonce,
          "Content-Type": "application/json"
        }
      }).then(function (res) {
        return res.json();
      }).then(function (data) {
        downloadCSV(data);
      })["catch"](function (err) {
        console.error("Error export items:", err);
      });
    });
    function downloadCSV(data) {
      var csv = convertArrayOfObjectsToCSV({
        data: data
      });
      if (!csv) return;
      var filename = "enrolled_students.csv";
      var csvUtf = 'data:text/csv;charset=utf-8,';
      var href = encodeURI(csvUtf + "\uFEFF" + csv);
      var link = document.createElement('a');
      link.setAttribute('href', href);
      link.setAttribute('download', filename);
      link.click();
    }
    function convertArrayOfObjectsToCSV(_ref) {
      var data = _ref.data,
        _ref$columnDelimiter = _ref.columnDelimiter,
        columnDelimiter = _ref$columnDelimiter === void 0 ? ',' : _ref$columnDelimiter,
        _ref$lineDelimiter = _ref.lineDelimiter,
        lineDelimiter = _ref$lineDelimiter === void 0 ? '\r\n' : _ref$lineDelimiter;
      if (!Array.isArray(data) || data.length === 0) return null;
      var keys = Object.keys(data[0]);
      var result = '';
      result += keys.join(columnDelimiter) + lineDelimiter;
      data.forEach(function (item) {
        keys.forEach(function (key, idx) {
          if (idx > 0) result += columnDelimiter;
          var cell = item[key];
          if (Array.isArray(cell)) {
            result += "\"".concat(cell.map(function (item) {
              return decodeStr(item);
            }).join(','), "\"");
          } else {
            cell = cell == null ? '' : String(cell);
            if (cell.includes(columnDelimiter) || cell.includes('"') || cell.includes('\n')) {
              cell = "\"".concat(cell.replace(/"/g, '""'), "\"");
            }
            result += cell;
          }
        });
        result += lineDelimiter;
      });
      return result;
    }
    function decodeStr(str) {
      return str.replace(/&#(\d+);/g, function (_, code) {
        return String.fromCharCode(code);
      });
    }
  }
  function dateFilter() {
    initializeDatepicker('#masterstudy-datepicker-students');
    document.addEventListener('datesUpdated', function () {
      fetchItems();
    });
  }
  function itemsSort() {
    document.addEventListener('msSortIndicatorEvent', function (event) {
      var order = event.detail.sortOrder,
        orderby = event.detail.indicator.parents('.masterstudy-tcell__header').data('sort');
      order = 'none' === order ? 'asc' : order;
      fetchItems($(config.selectors.per_page).val(), 1, orderby, order);
    });
    $('.masterstudy-tcell__title').on('click', function () {
      $('.masterstudy-sort-indicator', $(this).parent()).trigger('click');
    });
  }
})(jQuery);

},{"../enrolled-quizzes/modules/utils.js":1}]},{},[2])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvZXM2L2Vucm9sbGVkLXF1aXp6ZXMvbW9kdWxlcy91dGlscy5qcyIsImFzc2V0cy9lczYvc3R1ZGVudHMvaW5kZXguanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7Ozs7Ozs7Ozs7QUNBQSxNQUFNLENBQUMsQ0FBQyxHQUFHLE1BQU07QUFFVixTQUFTLG9CQUFvQixDQUFDLFVBQVUsRUFBRSxXQUFXLEVBQUUsU0FBUyxFQUFFO0VBQ3JFLElBQU0sVUFBVSxHQUFHLENBQUMsQ0FBQyxTQUFTLENBQUM7RUFDL0IsSUFBTSxnQkFBZ0IsR0FBRyxVQUFVLENBQUMsSUFBSSxDQUFDLCtCQUErQixDQUFDO0VBQ3pFLElBQU0saUJBQWlCLEdBQUcsVUFBVSxDQUFDLElBQUksQ0FBQyxxQ0FBcUMsQ0FBQztFQUNoRixJQUFNLFdBQVcsR0FBRyxVQUFVLENBQUMsSUFBSSxDQUFDLHNDQUFzQyxDQUFDO0VBQzNFLElBQU0sV0FBVyxHQUFHLFVBQVUsQ0FBQyxJQUFJLENBQUMsc0NBQXNDLENBQUM7RUFFM0UsZ0JBQWdCLENBQUMsV0FBVyxDQUFDLHNDQUFzQyxDQUFDLENBQUMsSUFBSSxDQUFDLENBQUM7RUFDM0UsSUFBSSxLQUFLLEdBQUcsSUFBSSxDQUFDLEdBQUcsQ0FBQyxDQUFDLEVBQUUsV0FBVyxHQUFHLENBQUMsQ0FBQztFQUN4QyxJQUFJLEdBQUcsR0FBRyxJQUFJLENBQUMsR0FBRyxDQUFDLFVBQVUsRUFBRSxXQUFXLEdBQUcsQ0FBQyxDQUFDO0VBRS9DLElBQUksV0FBVyxLQUFLLENBQUMsSUFBSSxLQUFLLEtBQUssQ0FBQyxFQUFFLEdBQUcsR0FBRyxJQUFJLENBQUMsR0FBRyxDQUFDLFVBQVUsRUFBRSxLQUFLLEdBQUcsQ0FBQyxDQUFDO0VBQzNFLElBQUksV0FBVyxLQUFLLFVBQVUsSUFBSSxHQUFHLEtBQUssVUFBVSxFQUFFLEtBQUssR0FBRyxJQUFJLENB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S0FBTSxDQUFDO0lBQ3pFLENBQUMsQ0FBQztJQUVGLENBQUMsQ0FBRSwyQkFBNEIsQ0FBQyxDQUFDLEVBQUUsQ0FBRSxPQUFPLEVBQUUsWUFBVztNQUNyRCxDQUFDLENBQUUsNkJBQTZCLEVBQUUsQ0FBQyxDQUFFLElBQUssQ0FBQyxDQUFDLE1BQU0sQ0FBQyxDQUFFLENBQUMsQ0FBQyxPQUFPLENBQUUsT0FBUSxDQUFDO0lBQzdFLENBQUMsQ0FBQztFQUNOO0FBQ0osQ0FBQyxFQUFFLE1BQU0sQ0FBQyIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uKCl7ZnVuY3Rpb24gcihlLG4sdCl7ZnVuY3Rpb24gbyhpLGYpe2lmKCFuW2ldKXtpZighZVtpXSl7dmFyIGM9XCJmdW5jdGlvblwiPT10eXBlb2YgcmVxdWlyZSYmcmVxdWlyZTtpZighZiYmYylyZXR1cm4gYyhpLCEwKTtpZih1KXJldHVybiB1KGksITApO3ZhciBhPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIraStcIidcIik7dGhyb3cgYS5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGF9dmFyIHA9bltpXT17ZXhwb3J0czp7fX07ZVtpXVswXS5jYWxsKHAuZXhwb3J0cyxmdW5jdGlvbihyKXt2YXIgbj1lW2ldWzFdW3JdO3JldHVybiBvKG58fHIpfSxwLHAuZXhwb3J0cyxyLGUsbix0KX1yZXR1cm4gbltpXS5leHBvcnRzfWZvcih2YXIgdT1cImZ1bmN0aW9uXCI9PXR5cGVvZiByZXF1aXJlJiZyZXF1aXJlLGk9MDtpPHQubGVuZ3RoO2krKylvKHRbaV0pO3JldHVybiBvfXJldHVybiByfSkoKSIsIndpbmRvdy4kID0galF1ZXJ5O1xuXG5leHBvcnQgZnVuY3Rpb24gdXBkYXRlUGFnaW5hdGlvblZpZXcodG90YWxQYWdlcywgY3VycmVudFBhZ2UsIGNvbnRhaW5lcikge1xuICAgIGNvbnN0ICRjb250YWluZXIgPSAkKGNvbnRhaW5lcik7XG4gICAgY29uc3QgJHBhZ2luYXRpb25JdGVtcyA9ICRjb250YWluZXIuZmluZChcIi5tYXN0ZXJzdHVkeS1wYWdpbmF0aW9uX19pdGVtXCIpO1xuICAgIGNvbnN0ICRwYWdpbmF0aW9uQmxvY2tzID0gJGNvbnRhaW5lci5maW5kKFwiLm1hc3RlcnN0dWR5LXBhZ2luYXRpb25fX2l0ZW0tYmxvY2tcIik7XG4gICAgY29uc3QgJGJ1dHRvbk5leHQgPSAkY29udGFpbmVyLmZpbmQoXCIubWFzdGVyc3R1ZHktcGFnaW5hdGlvbl9fYnV0dG9uLW5leHRcIik7XG4gICAgY29uc3QgJGJ1dHRvblByZXYgPSAkY29udGFpbmVyLmZpbmQoXCIubWFzdGVyc3R1ZHktcGFnaW5hdGlvbl9fYnV0dG9uLXByZXZcIik7XG5cbiAgICAkcGFnaW5hdGlvbkl0ZW1zLnJlbW92ZUNsYXNzKCdtYXN0ZXJzdHVkeS1wYWdpbmF0aW9uX19pdGVtX2N1cnJlbnQnKS5oaWRlKCk7XG4gICAgbGV0IHN0YXJ0ID0gTWF0aC5tYXgoMSwgY3VycmVudFBhZ2UgLSAxKTtcbiAgICBsZXQgZW5kID0gTWF0aC5taW4odG90YWxQYWdlcywgY3VycmVudFBhZ2UgKyAxKTtcblxuICAgIGlmIChjdXJyZW50UGFnZSA9PT0gMSB8fCBzdGFydCA9PT0gMSkgZW5kID0gTWF0aC5taW4odG90YWxQYWdlcywgc3RhcnQgKyAyKTtcbiAgICBpZiAoY3VycmVudFBhZ2UgPT09IHRvdGFsUGFnZXMgfHwgZW5kID09PSB0b3RhbFBhZ2VzKSBzdGFydCA9IE1hdGgubWF4KDEsIGVuZCAtIDIpO1xuXG4gICAgZm9yIChsZXQgaSA9IHN0YXJ0OyBpIDw9IGVuZDsgaSsrKSB7XG4gICAgICAgICRwYWdpbmF0aW9uSXRlbXMuZmlsdGVyKGA6aGFzKFtkYXRhLWlkPVwiJHtpfVwiXSlgKS5zaG93KCk7XG4gICAgfVxuXG4gICAgJHBhZ2luYXRpb25CbG9ja3MuZmlsdGVyKGBbZGF0YS1pZD1cIiR7Y3VycmVudFBhZ2V9XCJdYCkucGFyZW50KCkuYWRkQ2xhc3MoICdtYXN0ZXJzdHVkeS1wYWdpbmF0aW9uX19pdGVtX2N1cnJlbnQnICk7XG4gICAgJGJ1dHRvbk5leHQudG9nZ2xlKGN1cnJlbnRQYWdlIDwgdG90YWxQYWdlcyk7XG4gICAgJGJ1dHRvblByZXYudG9nZ2xlKGN1cnJlbnRQYWdlID4gMSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBhdHRhY2hQYWdpbmF0aW9uQ2xpY2tIYW5kbGVycyh0b3RhbFBhZ2VzLCBvblBhZ2VDaGFuZ2UsIGdldFBlclBhZ2VTZWxlY3RvciwgY29udGFpbmVyKSB7XG4gICAgY29uc3QgJGNvbnRhaW5lciA9ICQoY29udGFpbmVyKTtcbiAgICBjb25zdCAkcGFnaW5hdGlvbkJsb2NrcyA9ICRjb250YWluZXIuZmluZChcIi5tYXN0ZXJzdHVkeS1wYWdpbmF0aW9uX19pdGVtLWJsb2NrXCIpO1xuICAgIGNvbnN0ICRidXR0b25QcmV2ID0gJGNvbnRhaW5lci5maW5kKFwiLm1hc3RlcnN0dWR5LXBhZ2luYXRpb25fX2J1dHRvbi1wcmV2XCIpO1xuICAgIGNvbnN0ICRidXR0b25OZXh0ID0gJGNvbnRhaW5lci5maW5kKFwiLm1hc3RlcnN0dWR5LXBhZ2luYXRpb25fX2J1dHRvbi1uZXh0XCIpO1xuXG4gICAgJHBhZ2luYXRpb25CbG9ja3Mub2ZmKFwiY2xpY2tcIikub24oXCJjbGlja1wiLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgIGlmICggJCh0aGlzKS5wYXJlbnQoKS5oYXNDbGFzcyggJ21hc3RlcnN0dWR5LXBhZ2luYXRpb25fX2l0ZW1fY3VycmVudCcgKSApIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IHBhZ2UgPSAkKHRoaXMpLmRhdGEoXCJpZFwiKTtcbiAgICAgICAgb25QYWdlQ2hhbmdlKCQoZ2V0UGVyUGFnZVNlbGVjdG9yKCkpLnZhbCgpLCBwYWdlKTtcbiAgICB9KTtcblxuICAgICRidXR0b25QcmV2Lm9mZihcImNsaWNrXCIpLm9uKFwiY2xpY2tcIiwgZnVuY3Rpb24gKCkge1xuICAgICAgICBjb25zdCBjdXJyZW50ID0gJGNvbnRhaW5lci5maW5kKFwiLm1hc3RlcnN0dWR5LXBhZ2luYXRpb25fX2l0ZW1fY3VycmVudCAubWFzdGVyc3R1ZHktcGFnaW5hdGlvbl9faXRlbS1ibG9ja1wiKS5kYXRhKFwiaWRcIik7XG4gICAgICAgIGlmIChjdXJyZW50ID4gMSkgb25QYWdlQ2hhbmdlKCQoZ2V0UGVyUGFnZVNlbGVjdG9yKCkpLnZhbCgpLCBjdXJyZW50IC0gMSk7XG4gICAgfSk7XG5cbiAgICAkYnV0dG9uTmV4dC5vZmYoXCJjbGlja1wiKS5vbihcImNsaWNrXCIsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgY29uc3QgY3VycmVudCA9ICRjb250YWluZXIuZmluZChcIi5tYXN0ZXJzdHVkeS1wYWdpbmF0aW9uX19pdGVtX2N1cnJlbnQgLm1hc3RlcnN0dWR5LXBhZ2luYXRpb25fX2l0ZW0tYmxvY2tcIikuZGF0YShcImlkXCIpO1xuICAgICAgICBjb25zdCB0b3RhbCA9ICRwYWdpbmF0aW9uQmxvY2tzLmxlbmd0aDtcbiAgICAgICAgaWYgKGN1cnJlbnQgPCB0b3RhbCkgb25QYWdlQ2hhbmdlKCQoZ2V0UGVyUGFnZVNlbGVjdG9yKCkpLnZhbCgpLCBjdXJyZW50ICsgMSk7XG4gICAgfSk7XG59XG5cbmV4cG9ydCBmdW5jdGlvbiBiaW5kUGVyUGFnZUhhbmRsZXIoY29udGFpbmVyU2VsZWN0b3IsIHBlclBhZ2UsIGZldGNoRm4pIHtcbiAgICAkKFwiLm1hc3RlcnN0dWR5LXNlbGVjdF9fb3B0aW9uLCAubWFzdGVyc3R1ZHktc2VsZWN0X19jbGVhclwiLCBwZXJQYWdlKS5vZmYoXCJjbGlja1wiKS5vbihcImNsaWNrXCIsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgJChjb250YWluZXJTZWxlY3RvcikucmVtb3ZlKCk7XG4gICAgICAgIGZldGNoRm4oJCh0aGlzKS5kYXRhKFwidmFsdWVcIikpO1xuICAgIH0pO1xufVxuXG5leHBvcnQgZnVuY3Rpb24gcmVuZGVyUGFnaW5hdGlvbih7XG4gICAgYWpheHVybCxcbiAgICBub25jZSxcbiAgICB0b3RhbFBhZ2VzLFxuICAgIGN1cnJlbnRQYWdlLFxuICAgIHBhZ2luYXRpb25Db250YWluZXIsXG4gICAgb25QYWdlQ2hhbmdlLFxuICAgIGdldFBlclBhZ2VTZWxlY3Rvcixcbn0pIHtcbiAgICAkLnBvc3QoYWpheHVybCwge1xuICAgICAgICBhY3Rpb246IFwiZ2V0X3BhZ2luYXRpb25cIixcbiAgICAgICAgdG90YWxfcGFnZXM6IHRvdGFsUGFnZXMsXG4gICAgICAgIGN1cnJlbnRfcGFnZTogY3VycmVudFBhZ2UsXG4gICAgICAgIF9hamF4X25vbmNlOiBub25jZSxcbiAgICB9LCBmdW5jdGlvbiAocmVzcG9uc2UpIHtcbiAgICAgICAgaWYgKHJlc3BvbnNlLnN1Y2Nlc3MpIHtcbiAgICAgICAgICAgIGNvbnN0ICRuYXYgPSAkKHBhZ2luYXRpb25Db250YWluZXIpO1xuICAgICAgICAgICAgJG5hdi50b2dnbGUodG90YWxQYWdlcyA+IDEpLmh0bWwocmVzcG9uc2UuZGF0YS5wYWdpbmF0aW9uKTtcbiAgICAgICAgICAgIGF0dGFjaFBhZ2luYXRpb25DbGlja0hhbmRsZXJzKHRvdGFsUGFnZXMsIG9uUGFnZUNoYW5nZSwgZ2V0UGVyUGFnZVNlbGVjdG9yLCBwYWdpbmF0aW9uQ29udGFpbmVyKTtcbiAgICAgICAgICAgIHVwZGF0ZVBhZ2luYXRpb25WaWV3KHRvdGFsUGFnZXMsIGN1cnJlbnRQYWdlLCBwYWdpbmF0aW9uQ29udGFpbmVyKTtcbiAgICAgICAgfVxuICAgIH0pO1xufVxuIiwiaW1wb3J0IHtiaW5kUGVyUGFnZUhhbmRsZXIsIHJlbmRlclBhZ2luYXRpb24sIHVwZGF0ZVBhZ2luYXRpb25WaWV3fSBmcm9tICcuLi9lbnJvbGxlZC1xdWl6emVzL21vZHVsZXMvdXRpbHMuanMnO1xuXG4oZnVuY3Rpb24oJCkge1xuICAgIGxldCBjb25maWcgPSB7XG4gICAgICAgIHNlbGVjdG9yczoge1xuICAgICAgICAgICAgY29udGFpbmVyOiAnLm1hc3RlcnN0dWR5LXRhYmxlLWxpc3QtaXRlbXMnLFxuICAgICAgICAgICAgbG9hZGluZzogJ2l0ZW1zLWxvYWRpbmcnLFxuICAgICAgICAgICAgbm9fZm91bmQ6ICcubWFzdGVyc3R1ZHktdGFibGUtbGlzdC1uby1mb3VuZF9faW5mbycsXG4gICAgICAgICAgICByb3c6ICcubWFzdGVyc3R1ZHktdGFibGUtbGlzdF9fcm93JyxcbiAgICAgICAgICAgIHNlYXJjaF9pbnB1dDogJy5tYXN0ZXJzdHVkeS1mb3JtLXNlYXJjaF9faW5wdXQnLFxuICAgICAgICAgICAgY2hlY2tib3hBbGw6ICcjbWFzdGVyc3R1ZHktdGFibGUtbGlzdC1jaGVja2JveCcsXG4gICAgICAgICAgICBjaGVja2JveDogJ2lucHV0W25hbWU9XCJzdHVkZW50W11cIl0nLFxuICAgICAgICAgICAgcGVyX3BhZ2U6ICcjaXRlbXMtcGVyLXBhZ2UnLFxuICAgICAgICAgICAgbmF2aWdhdGlvbjogJy5tYXN0ZXJzdHVkeS10YWJsZS1saXN0LW5hdmlnYXRpb24nLFxuICAgICAgICAgICAgcGFnaW5hdGlvbjogJy5tYXN0ZXJzdHVkeS10YWJsZS1saXN0LW5hdmlnYXRpb25fX3BhZ2luYXRpb24nLFxuICAgICAgICAgICAgcGVyUGFnZTogJy5tYXN0ZXJzdHVkeS10YWJsZS1saXN0LW5hdmlnYXRpb25fX3Blci1wYWdlJyxcbiAgICAgICAgICAgIGV4cG9ydDogJ1tkYXRhLWlkPVwiZXhwb3J0LXN0dWRlbnRzLXRvLWNzdlwiXScsXG4gICAgICAgICAgICBzZWxlY3RCeUNvdXJzZTogJy5maWx0ZXItc3R1ZGVudHMtYnktY291cnNlcycsXG4gICAgICAgICAgICBkZWxldGVCdG46ICdbZGF0YS1pZD1cIm1hc3RlcnN0dWR5LXN0dWRlbnRzLWRlbGV0ZVwiXScsXG4gICAgICAgICAgICBtb2RhbERlbGV0ZTogJ1tkYXRhLWlkPVwibWFzdGVyc3R1ZHktZGVsZXRlLXN0dWRlbnRzXCJdJyxcbiAgICAgICAgICAgIHRvcEJhcjogJy5tYXN0ZXJzdHVkeS10YWJsZS1saXN0X190b3AtYmFyJ1xuICAgICAgICB9LFxuICAgICAgICB0ZW1wbGF0ZXM6IHtcbiAgICAgICAgICBub19mb3VuZDogJ21hc3RlcnN0dWR5LXRhYmxlLWxpc3Qtbm8tZm91bmQtdGVtcGxhdGUnLFxuICAgICAgICAgIHJvdzogJ21hc3RlcnN0dWR5LXRhYmxlLWxpc3Qtcm93LXRlbXBsYXRlJyxcbiAgICAgICAgfSxcbiAgICAgICAgZW5kcG9pbnRzOiB7XG4gICAgICAgICAgICBzdHVkZW50czogJy9zdHVkZW50cy8nLFxuICAgICAgICAgICAgZGVsZXRpbmc6ICcvc3R1ZGVudHMvZGVsZXRlLycsXG4gICAgICAgICAgICBjb3Vyc2VzOiAnL2NvdXJzZXMnLFxuICAgICAgICAgICAgZXhwb3J0U3R1ZGVudHM6ICcvZXhwb3J0L3N0dWRlbnRzLydcbiAgICAgICAgfSxcbiAgICAgICAgYXBpQmFzZTogbXNfbG1zX3Jlc3R1cmwsXG4gICAgICAgIG5vbmNlOiBtc19sbXNfbm9uY2UsXG4gICAgfSxcbiAgICB0b3RhbFBhZ2VzID0gMSxcbiAgICBjb3Vyc2VJZCA9ICcnO1xuXG4gICAgJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKSB7XG4gICAgICAgIGlmICggJCggJy5tYXN0ZXJzdHVkeS1zdHVkZW50cy1saXN0JyApLmxlbmd0aCApIHtcbiAgICAgICAgICAgIGluaXQoKTtcbiAgICAgICAgfVxuICAgIH0pO1xuXG4gICAgZnVuY3Rpb24gaW5pdCgpIHtcbiAgICAgICAgYmluZFBlclBhZ2VIYW5kbGVyKCQoIGNvbmZpZy5zZWxlY3RvcnMucm93LCBjb25maWcuc2VsZWN0b3JzLmNvbnRhaW5lciApLCBjb25maWcuc2VsZWN0b3JzLnBlclBhZ2UsIGZldGNoSXRlbXMpO1xuICAgICAgICBmZXRjaEl0ZW1zKCk7XG4gICAgICAgIGluaXRTZWFyY2goKTtcbiAgICAgICAgY2hlY2tBbGwoKTtcbiAgICAgICAgZGVsZXRlU3R1ZGVudHMoKTtcbiAgICAgICAgc2VhcmNoQnlDb3Vyc2UoKTtcbiAgICAgICAgZXhwb3J0U3R1ZGVudHMoKTtcbiAgICAgICAgZGF0ZUZpbHRlcigpO1xuICAgICAgICBpdGVtc1NvcnQoKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBmZXRjaEl0ZW1zKCBwZXJQYWdlID0gdW5kZWZpbmVkLCBjdXJyZW50UGFnZSA9IDEsIG9yZGVyYnkgPSAnJywgb3JkZXIgPSAnJyApIHtcbiAgICAgICAgbGV0IHVybCA9IGNvbmZpZy5hcGlCYXNlICsgY29uZmlnLmVuZHBvaW50cy5zdHVkZW50cztcbiAgICAgICAgY29uc3QgcXVlcnkgPSBbXTtcbiAgICAgICAgY29uc3QgJGlucHV0ID0gJCggY29uZmlnLnNlbGVjdG9ycy5zZWFyY2hfaW5wdXQgKTtcbiAgICAgICAgY29uc3Qgc2VhcmNoUXVlcnkgPSAkaW5wdXQubGVuZ3RoID8gJGlucHV0LnZhbCgpLnRyaW0oKSA6ICcnO1xuICAgICAgICBjb25zdCBkYXRlRnJvbSA9IGdldERhdGVGcm9tKCk7XG4gICAgICAgIGNvbnN0IGRhdGVUbyA9IGdldERhdGVUbygpO1xuXG4gICAgICAgIHF1ZXJ5LnB1c2goYHNob3dfYWxsX2Vucm9sbGVkPTFgKTtcblxuICAgICAgICBpZiAoc2VhcmNoUXVlcnkpIHF1ZXJ5LnB1c2goYHM9JHtlbmNvZGVVUklDb21wb25lbnQoc2VhcmNoUXVlcnkpfWApO1xuICAgICAgICBpZiAocGVyUGFnZSkgcXVlcnkucHVzaChgcGVyX3BhZ2U9JHtwZXJQYWdlfWApO1xuICAgICAgICBpZiAoY3VycmVudFBhZ2UpIHF1ZXJ5LnB1c2goYHBhZ2U9JHtjdXJyZW50UGFnZX1gKTtcbiAgICAgICAgaWYgKGNvdXJzZUlkKSBxdWVyeS5wdXNoKGBjb3Vyc2VfaWQ9JHtjb3Vyc2VJZH1gKTtcbiAgICAgICAgaWYgKGRhdGVGcm9tKSBxdWVyeS5wdXNoKGBkYXRlX2Zyb209JHtkYXRlRnJvbX1gKTtcbiAgICAgICAgaWYgKGRhdGVUbykgcXVlcnkucHVzaChgZGF0ZV90bz0ke2RhdGVUb31gKTtcbiAgICAgICAgaWYgKG9yZGVyYnkpIHF1ZXJ5LnB1c2goYG9yZGVyYnk9JHtvcmRlcmJ5fWApO1xuICAgICAgICBpZiAob3JkZXIpIHF1ZXJ5LnB1c2goYG9yZGVyPSR7b3JkZXJ9YCk7XG4gICAgICAgIGlmIChxdWVyeS5sZW5ndGgpIHVybCArPSBgPyR7cXVlcnkuam9pbihcIiZcIil9YDtcblxuICAgICAgICB1cGRhdGVQYWdpbmF0aW9uVmlldyggdG90YWxQYWdlcywgY3VycmVudFBhZ2UgKTtcblxuICAgICAgICBjb25zdCAkY29udGFpbmVyID0gJCggY29uZmlnLnNlbGVjdG9ycy5jb250YWluZXIgKTtcblxuICAgICAgICAkKCBgJHtjb25maWcuc2VsZWN0b3JzLnJvd30sICR7Y29uZmlnLnNlbGVjdG9ycy5ub19mb3VuZH1gLCBjb25maWcuc2VsZWN0b3JzLmNvbnRhaW5lciApLnJlbW92ZSgpO1xuXG4gICAgICAgICRjb250YWluZXIuYWRkQ2xhc3MoIGNvbmZpZy5zZWxlY3RvcnMubG9hZGluZyApO1xuICAgICAgICAkKCBjb25maWcuc2VsZWN0b3JzLm5hdmlnYXRpb24gKS5oaWRlKCk7XG5cbiAgICAgICAgZmV0Y2godXJsLCB7XG4gICAgICAgICAgICBoZWFkZXJzOiB7XG4gICAgICAgICAgICAgICAgXCJYLVdQLU5vbmNlXCI6IGNvbmZpZy5ub25jZSxcbiAgICAgICAgICAgICAgICBcIkNvbnRlbnQtVHlwZVwiOiBcImFwcGxpY2F0aW9uL2pzb25cIixcbiAgICAgICAgICAgIH0sXG4gICAgICAgIH0pXG4gICAgICAgIC50aGVuKHJlcyA9PiByZXMuanNvbigpKVxuICAgICAgICAudGhlbihkYXRhID0+IHtcbiAgICAgICAgICAgICRjb250YWluZXIuY3NzKFwiaGVpZ2h0XCIsIFwiYXV0b1wiKS5yZW1vdmVDbGFzcyggY29uZmlnLnNlbGVjdG9ycy5sb2FkaW5nICk7XG4gICAgICAgICAgICAkKCBgJHtjb25maWcuc2VsZWN0b3JzLnJvd30sICR7Y29uZmlnLnNlbGVjdG9ycy5ub19mb3VuZH1gLCBjb25maWcuc2VsZWN0b3JzLmNvbnRhaW5lciApLnJlbW92ZSgpO1xuXG4gICAgICAgICAgICB1cGRhdGVQYWdpbmF0aW9uKGRhdGEucGFnZXMsIGN1cnJlbnRQYWdlKTtcblxuICAgICAgICAgICAgaWYgKCFkYXRhLnN0dWRlbnRzIHx8IGRhdGEuc3R1ZGVudHMubGVuZ3RoID09PSAwKSB7XG4gICAgICAgICAgICAgICAgY29uc3QgdGVtcGxhdGUgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCggY29uZmlnLnRlbXBsYXRlcy5ub19mb3VuZCApO1xuICAgICAgICAgICAgICAgIGlmICggdGVtcGxhdGUgKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGNsb25lID0gdGVtcGxhdGUuY29udGVudC5jbG9uZU5vZGUodHJ1ZSk7XG4gICAgICAgICAgICAgICAgICAgICQoIGNvbmZpZy5zZWxlY3RvcnMubmF2aWdhdGlvbiApLmhpZGUoKTtcbiAgICAgICAgICAgICAgICAgICAgJGNvbnRhaW5lci5hcHBlbmQoY2xvbmUpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICQoIGNvbmZpZy5zZWxlY3RvcnMubmF2aWdhdGlvbiApLnNob3coKTtcblxuICAgICAgICAgICAgdG90YWxQYWdlcyA9IGRhdGEucGFnZXM7XG4gICAgICAgICAgICAoZGF0YS5zdHVkZW50cyB8fCBbXSkuZm9yRWFjaChpdGVtID0+IHtcbiAgICAgICAgICAgICAgICBjb25zdCBodG1sID0gcmVuZGVySXRlbVRlbXBsYXRlKGl0ZW0pO1xuICAgICAgICAgICAgICAgICRjb250YWluZXIuYXBwZW5kKGh0bWwpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pXG4gICAgICAgIC5jYXRjaChlcnIgPT4ge1xuICAgICAgICAgICAgY29uc29sZS5lcnJvcihcIkVycm9yIGZldGNoaW5nIGl0ZW1zOlwiLCBlcnIpO1xuICAgICAgICAgICAgJGNvbnRhaW5lci5jc3MoXCJoZWlnaHRcIiwgXCJhdXRvXCIpLnJlbW92ZUNsYXNzKCBjb25maWcuc2VsZWN0b3JzLmxvYWRpbmcgKTtcbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gcmVuZGVySXRlbVRlbXBsYXRlKGl0ZW0pIHtcbiAgICAgICAgY29uc3QgdGVtcGxhdGUgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChjb25maWcudGVtcGxhdGVzLnJvdyk7XG4gICAgICAgIGlmICghdGVtcGxhdGUpIHJldHVybiAnJztcbiAgICAgICAgY29uc3QgY2xvbmUgPSB0ZW1wbGF0ZS5jb250ZW50LmNsb25lTm9kZSh0cnVlKTtcblxuICAgICAgICBjb25zdCB1cmwgPSBuZXcgVVJMKCBpdGVtLnVybCwgd2luZG93LmxvY2F0aW9uLm9yaWdpbiApO1xuXG4gICAgICAgIGNsb25lLnF1ZXJ5U2VsZWN0b3IoJ1tuYW1lPVwic3R1ZGVudFtdXCJdJykudmFsdWUgPSBpdGVtLnVzZXJfaWQ7XG4gICAgICAgIGlmICggY2xvbmUucXVlcnlTZWxlY3RvcignLm1hc3RlcnN0dWR5LXRhYmxlLWxpc3RfX3Jvdy0tbGluaycpICkge1xuICAgICAgICAgICAgY2xvbmUucXVlcnlTZWxlY3RvcignLm1hc3RlcnN0dWR5LXRhYmxlLWxpc3RfX3Jvdy0tbGluaycpLmhyZWYgPSB1cmwudG9TdHJpbmcoKTtcbiAgICAgICAgfVxuICAgICAgICBjbG9uZS5xdWVyeVNlbGVjdG9yKCcubWFzdGVyc3R1ZHktdGFibGUtbGlzdF9fdGQtLW5hbWUnKS50ZXh0Q29udGVudCA9IGl0ZW0uZGlzcGxheV9uYW1lO1xuICAgICAgICBjbG9uZS5xdWVyeVNlbGVjdG9yKCcubWFzdGVyc3R1ZHktdGFibGUtbGlzdF9fdGQtLWVtYWlsJykudGV4dENvbnRlbnQgPSBpdGVtLmVtYWlsO1xuICAgICAgICBjbG9uZS5xdWVyeVNlbGVjdG9yKCcubWFzdGVyc3R1ZHktdGFibGUtbGlzdF9fdGQtLWpvaW5lZCcpLnRleHRDb250ZW50ID0gaXRlbS5yZWdpc3RlcmVkO1xuICAgICAgICBjbG9uZS5xdWVyeVNlbGVjdG9yKCcubWFzdGVyc3R1ZHktdGFibGUtbGlzdF9fdGQtLWVucm9sbGVkJykudGV4dENvbnRlbnQgPSBpdGVtLmVucm9sbGVkO1xuICAgICAgICBpZiAoIGNsb25lLnF1ZXJ5U2VsZWN0b3IoJy5tYXN0ZXJzdHVkeS10YWJsZS1saXN0X190ZC0tcG9pbnRzJykgKSB7XG4gICAgICAgICAgICBjbG9uZS5xdWVyeVNlbGVjdG9yKCcubWFzdGVyc3R1ZHktdGFibGUtbGlzdF9fdGQtLXBvaW50cycpLnRleHRDb250ZW50ID0gaXRlbS5wb2ludHM7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gY2xvbmU7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gdXBkYXRlUGFnaW5hdGlvbih0b3RhbFBhZ2VzLCBjdXJyZW50UGFnZSkge1xuICAgICAgICByZW5kZXJQYWdpbmF0aW9uKHtcbiAgICAgICAgICAgIGFqYXh1cmw6IHN0bV9sbXNfYWpheHVybCxcbiAgICAgICAgICAgIG5vbmNlOiBjb25maWcubm9uY2UsXG4gICAgICAgICAgICB0b3RhbFBhZ2VzLFxuICAgICAgICAgICAgY3VycmVudFBhZ2UsXG4gICAgICAgICAgICBwYWdpbmF0aW9uQ29udGFpbmVyOiBjb25maWcuc2VsZWN0b3JzLnBhZ2luYXRpb24sXG4gICAgICAgICAgICBvblBhZ2VDaGFuZ2U6IGZldGNoSXRlbXMsXG4gICAgICAgICAgICBnZXRQZXJQYWdlU2VsZWN0b3I6ICgpID0+IGNvbmZpZy5zZWxlY3RvcnMucGVyX3BhZ2UsXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIGluaXRTZWFyY2goKSB7XG4gICAgICAgIGNvbnN0ICRpbnB1dCA9ICQoIGNvbmZpZy5zZWxlY3RvcnMuc2VhcmNoX2lucHV0ICk7XG4gICAgICAgIGlmICggISAkaW5wdXQubGVuZ3RoICkgcmV0dXJuO1xuXG4gICAgICAgIGxldCB0aW1lcjtcbiAgICAgICAgbGV0IGxhc3RRdWVyeSA9ICcnO1xuXG4gICAgICAgICRpbnB1dC5vZmYoXCJpbnB1dFwiKS5vbihcImlucHV0XCIsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIGNsZWFyVGltZW91dCh0aW1lcik7XG4gICAgICAgICAgICB0aW1lciA9IHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICAgICAgICAgIGNvbnN0IHF1ZXJ5ID0gJGlucHV0LnZhbCgpLnRyaW0oKTtcbiAgICAgICAgICAgICAgICBpZiAocXVlcnkgIT09IGxhc3RRdWVyeSkge1xuICAgICAgICAgICAgICAgICAgICBsYXN0UXVlcnkgPSBxdWVyeTtcbiAgICAgICAgICAgICAgICAgICAgZmV0Y2hJdGVtcygkKCBjb25maWcuc2VsZWN0b3JzLnBlcl9wYWdlICkudmFsKCksIDEpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0sIDMwMCk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIGNoZWNrQWxsKCkge1xuICAgICAgICBjb25zdCAkc2VsZWN0QWxsID0gJChjb25maWcuc2VsZWN0b3JzLmNoZWNrYm94QWxsKTtcbiAgICAgICAgY29uc3QgJGRlbGV0ZUJ0biA9ICQoY29uZmlnLnNlbGVjdG9ycy5kZWxldGVCdG4pO1xuXG4gICAgICAgIGlmICggISAkc2VsZWN0QWxsLmxlbmd0aCApIHJldHVybjtcblxuICAgICAgICBmdW5jdGlvbiB1cGRhdGVEZWxldGVCdG4oKSB7XG4gICAgICAgICAgICBjb25zdCBhbnlDaGVja2VkID0gJChjb25maWcuc2VsZWN0b3JzLmNoZWNrYm94KS5maWx0ZXIoJzpjaGVja2VkJykubGVuZ3RoID4gMDtcbiAgICAgICAgICAgICRkZWxldGVCdG4ucHJvcCgnZGlzYWJsZWQnLCAhYW55Q2hlY2tlZCk7XG4gICAgICAgIH1cblxuICAgICAgICAkc2VsZWN0QWxsLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGNvbnN0IGlzQ2hlY2tlZCA9IHRoaXMuY2hlY2tlZDtcbiAgICAgICAgICAgICQoY29uZmlnLnNlbGVjdG9ycy5jaGVja2JveCkucHJvcCgnY2hlY2tlZCcsIGlzQ2hlY2tlZCkudHJpZ2dlcignY2hhbmdlJyk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgICQoZG9jdW1lbnQpLm9uKCdjaGFuZ2UnLCBjb25maWcuc2VsZWN0b3JzLmNoZWNrYm94LCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGNvbnN0ICRhbGwgPSAkKGNvbmZpZy5zZWxlY3RvcnMuY2hlY2tib3gpO1xuICAgICAgICAgICAgY29uc3QgY2hlY2tlZENudCA9ICRhbGwuZmlsdGVyKCc6Y2hlY2tlZCcpLmxlbmd0aDtcblxuICAgICAgICAgICAgJHNlbGVjdEFsbC5wcm9wKCdjaGVja2VkJywgY2hlY2tlZENudCA9PT0gJGFsbC5sZW5ndGgpO1xuXG4gICAgICAgICAgICB1cGRhdGVEZWxldGVCdG4oKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdXBkYXRlRGVsZXRlQnRuKCk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gZGVsZXRlU3R1ZGVudHMoKSB7XG4gICAgICAgIGNvbnN0IHVybCA9IGNvbmZpZy5hcGlCYXNlICsgY29uZmlnLmVuZHBvaW50cy5kZWxldGluZztcbiAgICAgICAgY29uc3Qge2NoZWNrYm94QWxsLCBkZWxldGVCdG4sIG1vZGFsRGVsZXRlLCBjb250YWluZXIsIHJvdywgbm9fZm91bmQsIGxvYWRpbmcsIGNoZWNrYm94LCBwZXJfcGFnZX0gPSBjb25maWcuc2VsZWN0b3JzO1xuXG4gICAgICAgIGxldCBzdHVkZW50cyA9IFtdO1xuXG4gICAgICAgICQoZGVsZXRlQnRuKS5vbignY2xpY2snLCBlID0+IHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIHN0dWRlbnRzID0gJCgnaW5wdXRbbmFtZT1cInN0dWRlbnRbXVwiXTpjaGVja2VkJylcbiAgICAgICAgICAgICAgICAubWFwKGZ1bmN0aW9uKCkgeyByZXR1cm4gdGhpcy52YWx1ZTsgfSlcbiAgICAgICAgICAgICAgICAuZ2V0KCk7XG5cbiAgICAgICAgICAgIGlmIChzdHVkZW50cy5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAkKG1vZGFsRGVsZXRlKS5hZGRDbGFzcygnbWFzdGVyc3R1ZHktYWxlcnRfb3BlbicpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcblxuICAgICAgICAkKG1vZGFsRGVsZXRlKS5vbignY2xpY2snLCBcIltkYXRhLWlkPSdjYW5jZWwnXSwgLm1hc3RlcnN0dWR5LWFsZXJ0X19oZWFkZXItY2xvc2VcIiwgZSA9PiB7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAkKG1vZGFsRGVsZXRlKS5yZW1vdmVDbGFzcygnbWFzdGVyc3R1ZHktYWxlcnRfb3BlbicpO1xuICAgICAgICB9KTtcblxuICAgICAgICAkKG1vZGFsRGVsZXRlKS5vbignY2xpY2snLCBcIltkYXRhLWlkPSdzdWJtaXQnXVwiLCBlID0+IHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGlmICghc3R1ZGVudHMubGVuZ3RoKSByZXR1cm47XG5cbiAgICAgICAgICAgICQoY29udGFpbmVyKS5maW5kKGAke3Jvd30sICR7bm9fZm91bmR9YCkucmVtb3ZlKCk7XG4gICAgICAgICAgICAkKGNvbnRhaW5lcikuYWRkQ2xhc3MobG9hZGluZyk7XG4gICAgICAgICAgICAkKG1vZGFsRGVsZXRlKS5yZW1vdmVDbGFzcygnbWFzdGVyc3R1ZHktYWxlcnRfb3BlbicpO1xuICAgICAgICAgICAgJChjaGVja2JveCkucHJvcCgnY2hlY2tlZCcsIGZhbHNlKTtcbiAgICAgICAgICAgICQoIGNvbmZpZy5zZWxlY3RvcnMubmF2aWdhdGlvbiApLmhpZGUoKTtcbiAgICAgICAgICAgICQoY2hlY2tib3hBbGwpLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG5cbiAgICAgICAgICAgIGZldGNoKHVybCwge1xuICAgICAgICAgICAgICAgIG1ldGhvZDogJ0RFTEVURScsXG4gICAgICAgICAgICAgICAgaGVhZGVyczoge1xuICAgICAgICAgICAgICAgICAgICAnWC1XUC1Ob25jZSc6IGNvbmZpZy5ub25jZSxcbiAgICAgICAgICAgICAgICAgICAgJ0NvbnRlbnQtVHlwZSc6ICdhcHBsaWNhdGlvbi9qc29uJyxcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgIGJvZHk6IEpTT04uc3RyaW5naWZ5KHsgc3R1ZGVudHMgfSlcbiAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAudGhlbihyZXMgPT4ge1xuICAgICAgICAgICAgICAgIHN0dWRlbnRzID0gW107XG5cbiAgICAgICAgICAgICAgICByZXR1cm4gcmVzLmpzb24oKS50aGVuKGRhdGEgPT4ge1xuICAgICAgICAgICAgICAgICAgICBjb25zdCAkbXNnID0gJChgPGRpdiBjbGFzcz1cInN0bS1sbXMtbWVzc2FnZSBlcnJvclwiPiR7ZGF0YS5tZXNzYWdlfTwvZGl2PmApO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmICggWydlcnJvcicsICdkZW1vX2ZvcmJpZGRlbl9hY2Nlc3MnXS5pbmNsdWRlcyggZGF0YS5zdGF0dXMgKSB8fCAnZGVtb19mb3JiaWRkZW5fYWNjZXNzJyA9PT0gZGF0YS5lcnJvcl9jb2RlICkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJCggY29uZmlnLnNlbGVjdG9ycy50b3BCYXIgKS5hZnRlciggJG1zZyApO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHsgJG1zZy5yZW1vdmUoKTsgfSwgNTAwMCk7XG4gICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gZmV0Y2hJdGVtcyggJChwZXJfcGFnZSkudmFsKCksIDEgKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAuY2F0Y2goY29uc29sZS5lcnJvcik7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIHNlYXJjaEJ5Q291cnNlKCkge1xuICAgICAgICBjb25zdCAkaW5wdXQgID0gJCggY29uZmlnLnNlbGVjdG9ycy5zZWxlY3RCeUNvdXJzZSApO1xuICAgICAgICBjb25zdCAkcGFyZW50ID0gJGlucHV0LnBhcmVudCgpO1xuICAgICAgICBjb25zdCB7IGFwaUJhc2UsIGVuZHBvaW50cywgbm9uY2UgfSA9IGNvbmZpZztcbiAgICAgICAgY29uc3QgVVJMICAgICAgID0gYXBpQmFzZSArIGVuZHBvaW50cy5jb3Vyc2VzO1xuICAgICAgICBjb25zdCBQRVJfUEFHRSAgPSAyMDtcblxuICAgICAgICBsZXQgc3RhdGljQ291cnNlcyAgICA9IFtdO1xuICAgICAgICBsZXQgc3RhdGljVG90YWxQYWdlcyA9IDA7XG5cbiAgICAgICAgZnVuY3Rpb24gZmV0Y2hDb3Vyc2VzKHRlcm0gPSAnJywgcGFnZSA9IDEpIHtcbiAgICAgICAgICAgIHJldHVybiAkLmFqYXgoe1xuICAgICAgICAgICAgICAgIHVybDogICAgICBVUkwsXG4gICAgICAgICAgICAgICAgbWV0aG9kOiAgICdHRVQnLFxuICAgICAgICAgICAgICAgIGRhdGFUeXBlOiAnanNvbicsXG4gICAgICAgICAgICAgICAgaGVhZGVyczogIHsgJ1gtV1AtTm9uY2UnOiBub25jZSB9LFxuICAgICAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgICAgICAgczogICAgICAgIHRlcm0sXG4gICAgICAgICAgICAgICAgICAgIHBlcl9wYWdlOiBQRVJfUEFHRSxcbiAgICAgICAgICAgICAgICAgICAgY3VycmVudF91c2VyOiAxLFxuICAgICAgICAgICAgICAgICAgICBwYWdlOiAgICAgcGFnZVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG5cbiAgICAgICAgZnVuY3Rpb24gdHJ1bmNhdGVXaXRoRWxsaXBzaXMoIHRleHQgKSB7XG4gICAgICAgICAgICByZXR1cm4gdGV4dC5sZW5ndGggPiAyMyA/IHRleHQuc2xpY2UoMCwgMjMpICsgJ+KApicgOiB0ZXh0O1xuICAgICAgICB9XG5cbiAgICAgICAgZmV0Y2hDb3Vyc2VzKClcbiAgICAgICAgICAgIC5kb25lKHJlc3BvbnNlID0+IHtcbiAgICAgICAgICAgICAgICBzdGF0aWNDb3Vyc2VzICAgID0gcmVzcG9uc2UuY291cnNlcyB8fCBbXTtcbiAgICAgICAgICAgICAgICBzdGF0aWNUb3RhbFBhZ2VzID0gcGFyc2VJbnQocmVzcG9uc2UucGFnZXMsIDEwKSB8fCAwO1xuICAgICAgICAgICAgfSlcbiAgICAgICAgICAgIC5hbHdheXMoaW5pdFNlbGVjdDIpO1xuXG4gICAgICAgIGZ1bmN0aW9uIGluaXRTZWxlY3QyKCkge1xuICAgICAgICAgICAgJHBhcmVudC5yZW1vdmVDbGFzcyggJ2ZpbHRlci1zdHVkZW50cy1ieS1jb3Vyc2VzLWRlZmF1bHQnICk7XG5cbiAgICAgICAgICAgICRpbnB1dC5zZWxlY3QyKHtcbiAgICAgICAgICAgICAgICBkcm9wZG93blBhcmVudDogICAgICRwYXJlbnQsXG4gICAgICAgICAgICAgICAgcGxhY2Vob2xkZXI6ICAgICAgICAkaW5wdXQuZGF0YSgncGxhY2Vob2xkZXInKSxcbiAgICAgICAgICAgICAgICBhbGxvd0NsZWFyOiAgICAgICAgIHRydWUsXG4gICAgICAgICAgICAgICAgbWluaW11bUlucHV0TGVuZ3RoOiAwLFxuICAgICAgICAgICAgICAgIHRlbXBsYXRlU2VsZWN0aW9uOiBmdW5jdGlvbihkYXRhKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiB0cnVuY2F0ZVdpdGhFbGxpcHNpcyggZGF0YS50ZXh0ICk7XG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICBlc2NhcGVNYXJrdXA6IGZ1bmN0aW9uKG1hcmt1cCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gbWFya3VwO1xuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgYWpheDoge1xuICAgICAgICAgICAgICAgICAgICB0cmFuc3BvcnQ6IGZ1bmN0aW9uKHBhcmFtcywgc3VjY2VzcywgZmFpbHVyZSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgdGVybSA9IHBhcmFtcy5kYXRhLnRlcm0gfHwgJyc7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zdCBwYWdlID0gcGFyc2VJbnQocGFyYW1zLmRhdGEucGFnZSwgMTApIHx8IDE7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICghdGVybSAmJiBwYWdlID09PSAxKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHN1Y2Nlc3Moe1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXN1bHRzOiBzdGF0aWNDb3Vyc2VzLm1hcChpdGVtID0+ICh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZDogICBpdGVtLklELFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGV4dDogaXRlbS5wb3N0X3RpdGxlXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pKSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcGFnaW5hdGlvbjoge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbW9yZTogc3RhdGljVG90YWxQYWdlcyA+IDFcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgICAgICAgICBmZXRjaENvdXJzZXModGVybSwgcGFnZSlcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZG9uZShyZXNwb25zZSA9PiBzdWNjZXNzKHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0czogKHJlc3BvbnNlLmNvdXJzZXMgfHwgW10pLm1hcChpdGVtID0+ICh7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZDogICBpdGVtLklELFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdGV4dDogaXRlbS5wb3N0X3RpdGxlXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pKSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgcGFnaW5hdGlvbjoge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbW9yZTogcGFnZSA8IChwYXJzZUludChyZXNwb25zZS5wYWdlcywgMTApIHx8IDApXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9KSlcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAuZmFpbChmYWlsdXJlKTtcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgZGVsYXk6IDI1MCxcbiAgICAgICAgICAgICAgICAgICAgcHJvY2Vzc1Jlc3VsdHM6IGRhdGEgPT4gZGF0YSxcbiAgICAgICAgICAgICAgICAgICAgY2FjaGU6IHRydWVcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICRpbnB1dC5vbignc2VsZWN0MjpzZWxlY3Qgc2VsZWN0MjpjbGVhcicsIGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgICAgICAgICBpZiAoZS50eXBlID09PSAnc2VsZWN0MjpzZWxlY3QnKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvdXJzZUlkID0gZS5wYXJhbXMuZGF0YS5pZDtcbiAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKGUudHlwZSA9PT0gJ3NlbGVjdDI6Y2xlYXInKSB7XG4gICAgICAgICAgICAgICAgICAgIGNvdXJzZUlkID0gbnVsbDtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBmZXRjaEl0ZW1zKCQoIGNvbmZpZy5zZWxlY3RvcnMucGVyX3BhZ2UgKS52YWwoKSwgMSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIGV4cG9ydFN0dWRlbnRzKCkge1xuICAgICAgICAkKCBjb25maWcuc2VsZWN0b3JzLmV4cG9ydCApLm9uKCAnY2xpY2snLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICBsZXQgdXJsID0gY29uZmlnLmFwaUJhc2UgKyBjb25maWcuZW5kcG9pbnRzLmV4cG9ydFN0dWRlbnRzO1xuICAgICAgICAgICAgY29uc3QgcXVlcnkgPSBbXTtcbiAgICAgICAgICAgIGNvbnN0ICRzZWxlY3RCeUNvdXJzZSA9ICQoIGNvbmZpZy5zZWxlY3RvcnMuc2VsZWN0QnlDb3Vyc2UgKTtcbiAgICAgICAgICAgIGNvbnN0IGNvdXJzZUlkID0gJHNlbGVjdEJ5Q291cnNlLmxlbmd0aCA/ICRzZWxlY3RCeUNvdXJzZS52YWwoKS50cmltKCkgOiAnJztcbiAgICAgICAgICAgIGNvbnN0ICRpbnB1dFNlYXJjaCA9ICQoIGNvbmZpZy5zZWxlY3RvcnMuc2VhcmNoX2lucHV0ICk7XG4gICAgICAgICAgICBjb25zdCBzZWFyY2hRdWVyeSA9ICRpbnB1dFNlYXJjaC5sZW5ndGggPyAkaW5wdXRTZWFyY2gudmFsKCkudHJpbSgpIDogJyc7XG4gICAgICAgICAgICBjb25zdCBkYXRlRnJvbSA9IGdldERhdGVGcm9tKCk7XG4gICAgICAgICAgICBjb25zdCBkYXRlVG8gPSBnZXREYXRlVG8oKTtcblxuICAgICAgICAgICAgcXVlcnkucHVzaChgc2hvd19hbGxfZW5yb2xsZWQ9MWApO1xuICAgICAgICAgICAgcXVlcnkucHVzaChgcz0ke2VuY29kZVVSSUNvbXBvbmVudChzZWFyY2hRdWVyeSl9YCk7XG4gICAgICAgICAgICBxdWVyeS5wdXNoKGBjb3Vyc2VfaWQ9JHtjb3Vyc2VJZH1gKTtcbiAgICAgICAgICAgIHF1ZXJ5LnB1c2goYGRhdGVfZnJvbT0ke2RhdGVGcm9tfWApO1xuICAgICAgICAgICAgcXVlcnkucHVzaChgZGF0ZV90bz0ke2RhdGVUb31gKTtcbiAgICAgICAgICAgIGlmIChxdWVyeS5sZW5ndGgpIHVybCArPSBgPyR7cXVlcnkuam9pbihcIiZcIil9YDtcblxuICAgICAgICAgICAgZmV0Y2godXJsLCB7XG4gICAgICAgICAgICAgICAgaGVhZGVyczoge1xuICAgICAgICAgICAgICAgICAgICBcIlgtV1AtTm9uY2VcIjogY29uZmlnLm5vbmNlLFxuICAgICAgICAgICAgICAgICAgICBcIkNvbnRlbnQtVHlwZVwiOiBcImFwcGxpY2F0aW9uL2pzb25cIixcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgfSlcbiAgICAgICAgICAgIC50aGVuKHJlcyA9PiByZXMuanNvbigpKVxuICAgICAgICAgICAgLnRoZW4oZGF0YSA9PiB7XG4gICAgICAgICAgICAgICAgZG93bmxvYWRDU1YoIGRhdGEgKTtcbiAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAuY2F0Y2goZXJyID0+IHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKFwiRXJyb3IgZXhwb3J0IGl0ZW1zOlwiLCBlcnIpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIGZ1bmN0aW9uIGRvd25sb2FkQ1NWKGRhdGEpIHtcbiAgICAgICAgICAgIGxldCBjc3YgPSBjb252ZXJ0QXJyYXlPZk9iamVjdHNUb0NTVih7IGRhdGEgfSk7XG4gICAgICAgICAgICBpZiAoIWNzdikgcmV0dXJuO1xuXG4gICAgICAgICAgICBjb25zdCBmaWxlbmFtZSA9IGBlbnJvbGxlZF9zdHVkZW50cy5jc3ZgO1xuICAgICAgICAgICAgY29uc3QgY3N2VXRmID0gJ2RhdGE6dGV4dC9jc3Y7Y2hhcnNldD11dGYtOCwnO1xuICAgICAgICAgICAgY29uc3QgaHJlZiA9IGVuY29kZVVSSShjc3ZVdGYgKyAnXFx1RkVGRicgKyBjc3YpO1xuXG4gICAgICAgICAgICBjb25zdCBsaW5rID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnYScpO1xuICAgICAgICAgICAgbGluay5zZXRBdHRyaWJ1dGUoJ2hyZWYnLCBocmVmKTtcbiAgICAgICAgICAgIGxpbmsuc2V0QXR0cmlidXRlKCdkb3dubG9hZCcsIGZpbGVuYW1lKTtcbiAgICAgICAgICAgIGxpbmsuY2xpY2soKTtcbiAgICAgICAgfVxuXG4gICAgICAgIGZ1bmN0aW9uIGNvbnZlcnRBcnJheU9mT2JqZWN0c1RvQ1NWKHsgZGF0YSwgY29sdW1uRGVsaW1pdGVyID0gJywnLCBsaW5lRGVsaW1pdGVyID0gJ1xcclxcbicgfSkge1xuICAgICAgICAgICAgaWYgKCFBcnJheS5pc0FycmF5KGRhdGEpIHx8IGRhdGEubGVuZ3RoID09PSAwKSByZXR1cm4gbnVsbDtcblxuICAgICAgICAgICAgY29uc3Qga2V5cyA9IE9iamVjdC5rZXlzKGRhdGFbMF0pO1xuICAgICAgICAgICAgbGV0IHJlc3VsdCA9ICcnO1xuXG4gICAgICAgICAgICByZXN1bHQgKz0ga2V5cy5qb2luKGNvbHVtbkRlbGltaXRlcikgKyBsaW5lRGVsaW1pdGVyO1xuXG4gICAgICAgICAgICBkYXRhLmZvckVhY2goaXRlbSA9PiB7XG4gICAgICAgICAgICAgICAga2V5cy5mb3JFYWNoKChrZXksIGlkeCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBpZiAoaWR4ID4gMCkgcmVzdWx0ICs9IGNvbHVtbkRlbGltaXRlcjtcblxuICAgICAgICAgICAgICAgICAgICBsZXQgY2VsbCA9IGl0ZW1ba2V5XTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoQXJyYXkuaXNBcnJheShjZWxsKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgcmVzdWx0ICs9IGBcIiR7Y2VsbC5tYXAoIGl0ZW0gPT4gZGVjb2RlU3RyKCBpdGVtICkgKS5qb2luKCcsJyl9XCJgO1xuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgY2VsbCA9IGNlbGwgPT0gbnVsbCA/ICcnIDogU3RyaW5nKGNlbGwpO1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNlbGwuaW5jbHVkZXMoY29sdW1uRGVsaW1pdGVyKSB8fCBjZWxsLmluY2x1ZGVzKCdcIicpIHx8IGNlbGwuaW5jbHVkZXMoJ1xcbicpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY2VsbCA9IGBcIiR7Y2VsbC5yZXBsYWNlKC9cIi9nLCAnXCJcIicpfVwiYDtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIHJlc3VsdCArPSBjZWxsO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgcmVzdWx0ICs9IGxpbmVEZWxpbWl0ZXI7XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgcmV0dXJuIHJlc3VsdDtcbiAgICAgICAgfVxuXG4gICAgICAgIGZ1bmN0aW9uIGRlY29kZVN0ciggc3RyICkge1xuICAgICAgICAgICAgcmV0dXJuIHN0ci5yZXBsYWNlKC8mIyhcXGQrKTsvZywgKF8sIGNvZGUpID0+IFN0cmluZy5mcm9tQ2hhckNvZGUoY29kZSkpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gZGF0ZUZpbHRlcigpIHtcbiAgICAgICAgaW5pdGlhbGl6ZURhdGVwaWNrZXIoJyNtYXN0ZXJzdHVkeS1kYXRlcGlja2VyLXN0dWRlbnRzJyk7XG5cbiAgICAgICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignZGF0ZXNVcGRhdGVkJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICBmZXRjaEl0ZW1zKCk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIGl0ZW1zU29ydCgpIHtcbiAgICAgICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignbXNTb3J0SW5kaWNhdG9yRXZlbnQnLCBmdW5jdGlvbiggZXZlbnQgKSB7XG4gICAgICAgICAgICBsZXQgb3JkZXIgICA9IGV2ZW50LmRldGFpbC5zb3J0T3JkZXIsXG4gICAgICAgICAgICAgICAgb3JkZXJieSA9IGV2ZW50LmRldGFpbC5pbmRpY2F0b3IucGFyZW50cygnLm1hc3RlcnN0dWR5LXRjZWxsX19oZWFkZXInKS5kYXRhKCdzb3J0Jyk7XG5cbiAgICAgICAgICAgIG9yZGVyID0gJ25vbmUnID09PSBvcmRlciA/ICdhc2MnIDogb3JkZXI7XG4gICAgICAgICAgICBmZXRjaEl0ZW1zKCAkKCBjb25maWcuc2VsZWN0b3JzLnBlcl9wYWdlICkudmFsKCksIDEsIG9yZGVyYnksIG9yZGVyICk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgICQoICcubWFzdGVyc3R1ZHktdGNlbGxfX3RpdGxlJyApLm9uKCAnY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICQoICcubWFzdGVyc3R1ZHktc29ydC1pbmRpY2F0b3InLCAkKCB0aGlzICkucGFyZW50KCkgKS50cmlnZ2VyKCAnY2xpY2snICk7XG4gICAgICAgIH0pO1xuICAgIH1cbn0pKGpRdWVyeSk7XG4iXX0=
