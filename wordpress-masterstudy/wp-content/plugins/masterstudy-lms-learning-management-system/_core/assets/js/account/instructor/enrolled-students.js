"use strict";

(function ($) {
  var config = {
      selectors: {
        container: '.masterstudy-account-enrolled-students__items',
        loading: 'items-loading',
        no_found: '.masterstudy-account-enrolled-students-no-found__info',
        row: '.masterstudy-account-enrolled-students__row',
        search_input: '.masterstudy-form-search__input',
        checkboxAll: '#masterstudy-account-enrolled-students-checkbox',
        checkbox: 'input[name="student[]"]',
        per_page: '#items-per-page',
        navigation: '.masterstudy-account-enrolled-students__navigation',
        pagination: '.masterstudy-account-enrolled-students__navigation-pagination',
        perPage: '.masterstudy-account-enrolled-students__navigation-per-page',
        "export": '[data-id="export-students-to-csv"]',
        selectByCourse: '.filter-students-by-courses',
        deleteBtn: '[data-id="masterstudy-students-delete"]',
        modalDelete: '[data-id="masterstudy-delete-students"]',
        topBar: '.masterstudy-account-enrolled-students__top-bar'
      },
      templates: {
        no_found: 'masterstudy-account-enrolled-students-no-found-template',
        row: 'masterstudy-account-enrolled-students-row-template'
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
    init();
  });
  function init() {
    window._masterstudy_utils.pagination.bindPerPageHandler($(config.selectors.row, config.selectors.container), config.selectors.perPage, fetchItems);
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
    var $container = $(config.selectors.container);
    var $loader = $container.find('.masterstudy-loader');
    $("".concat(config.selectors.row, ", ").concat(config.selectors.no_found), config.selectors.container).remove();
    $container.addClass(config.selectors.loading);
    $loader.css('display', 'flex');
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
      $loader.css('display', 'none');
      $("".concat(config.selectors.row, ", ").concat(config.selectors.no_found), config.selectors.container).remove();
      totalPages = parseInt(data.pages, 10) || 1;
      updatePagination(data.pagination, currentPage);
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
      (data.students || []).forEach(function (item) {
        var html = renderItemTemplate(item);
        $container.append(html);
      });
    })["catch"](function (err) {
      console.error("Error fetching items:", err);
      $container.css("height", "auto").removeClass(config.selectors.loading);
      $loader.css('display', 'none');
    });
  }
  function renderItemTemplate(item) {
    var template = document.getElementById(config.templates.row);
    if (!template) return '';
    var clone = template.content.cloneNode(true);
    var url = new URL(item.url, window.location.origin);
    clone.querySelector('[name="student[]"]').value = item.user_id;
    if (clone.querySelector('.masterstudy-account-enrolled-students__row-link')) {
      clone.querySelector('.masterstudy-account-enrolled-students__row-link').href = url.toString();
    }
    clone.querySelector('.masterstudy-account-enrolled-students__td--name .masterstudy-account-enrolled-students__student-row-name').textContent = item.display_name;
    clone.querySelector('.masterstudy-account-enrolled-students__td--name .masterstudy-account-enrolled-students__student-row-email').textContent = item.email;
    clone.querySelector('.masterstudy-account-enrolled-students__td--joined').textContent = "".concat(item.date_formatted.date, " - ").concat(item.date_formatted.time);
    clone.querySelector('.masterstudy-account-enrolled-students__td--enrolled').textContent = item.enrolled;
    if (clone.querySelector('.masterstudy-account-enrolled-students__td--points')) {
      clone.querySelector('.masterstudy-account-enrolled-students__td--points').textContent = item.points;
    }
    return clone;
  }
  function updatePagination(paginationHtml, currentPage) {
    window._masterstudy_utils.pagination.renderPagination({
      paginationHtml: paginationHtml,
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
      if (anyChecked) {
        $deleteBtn.removeClass('masterstudy-button_disabled');
      } else {
        $deleteBtn.addClass('masterstudy-button_disabled');
      }
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
    $(config.selectors["export"]).on('click', function (e) {
      e.preventDefault();
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
    $('.masterstudy-account-enrolled-students__header .masterstudy-tcell__title').on('click', function () {
      $('.masterstudy-sort-indicator', $(this).parent()).trigger('click');
    });
  }
})(jQuery);