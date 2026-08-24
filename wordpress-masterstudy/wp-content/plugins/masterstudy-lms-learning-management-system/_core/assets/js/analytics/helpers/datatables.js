"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
var isDomReady = false;
document.addEventListener('DOMContentLoaded', function () {
  isDomReady = true;
  if (document.querySelectorAll('input[id*="-search"]').length) {
    document.querySelectorAll('input[id*="-search"]').forEach(function (selector) {
      selector.addEventListener('keydown', function (e) {
        var value = this;
        if (window.search_field_intent_timeout) clearTimeout(window.search_field_intent_timeout);
        if (e.keyCode === 13) {
          searchFieldIntent(value);
        } else {
          window.search_field_intent_timeout = setTimeout(function () {
            searchFieldIntent(value);
          }, 1000);
        }
      });
    });
  }
  document.addEventListener('click', function (event) {
    if (event.target.matches('div[class*="__search-dropdown-item"]')) {
      var item = event.target;
      var value = item.textContent.trim();
      var dropdown = item.parentNode;
      var input = dropdown.parentNode.querySelector('input');
      if (input) {
        input.value = value;
      }

      // Grade Search Fields
      if (input.classList.contains('grades-search')) {
        input.dataset.id = item.dataset.id;
      }
      searchFieldIntent(input);
    }
  });
  if (document.querySelectorAll('span[class*="__search-icon"]')) {
    document.querySelectorAll('span[class*="__search-icon"]').forEach(function (selector) {
      selector.addEventListener('click', function (e) {
        if (this.parentNode.querySelector('input').value !== '') {
          searchFieldIntent(this.parentNode.querySelector('input'));
        }
      });
    });
  }
});
function createDataTable(selector, columns) {
  var additionalOptions = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  var defaultOptions = {
    data: [],
    retrieve: true,
    processing: true,
    serverSide: true,
    columns: columns,
    layout: {
      topStart: null,
      topEnd: null,
      bottomStart: {
        paging: {
          numbers: 5
        }
      },
      bottomEnd: {
        pageLength: {
          menu: [10, 25, 50]
        }
      }
    },
    language: {
      lengthMenu: '_MENU_' + table_data.per_page_placeholder,
      emptyTable: table_data.not_available,
      zeroRecords: table_data.not_found
    }
  };
  var options = Object.assign({}, defaultOptions, additionalOptions);
  return new DataTable(selector, options);
}
function updateDataTable(table, selector, loaders, currentRoute, pageData, dataSrcCallback) {
  var columnDefs = arguments.length > 6 && arguments[6] !== undefined ? arguments[6] : [];
  var reloadTable = arguments.length > 7 && arguments[7] !== undefined ? arguments[7] : false;
  var hidePagination = arguments.length > 8 && arguments[8] !== undefined ? arguments[8] : false;
  var isLessonsTable = arguments.length > 9 && arguments[9] !== undefined ? arguments[9] : false;
  var lessonsData = arguments.length > 10 && arguments[10] !== undefined ? arguments[10] : [];
  var searchFieldValue = arguments.length > 11 && arguments[11] !== undefined ? arguments[11] : '';
  var orderIndex = arguments.length > 12 && arguments[12] !== undefined ? arguments[12] : '';
  if (!isDomReady) return;
  if (!table || reloadTable) {
    loaders.forEach(function (loader) {
      showLoaders(loader);
    });
    if (table) {
      table.clear().destroy();
      table = null;
      jQuery(selector).empty();
    }
    var additionalOptions = {
      order: [[0, 'desc']],
      ajax: {
        url: api.getRouteUrl(currentRoute),
        type: 'POST',
        dataType: 'json',
        beforeSend: function beforeSend(xhr) {
          xhr.setRequestHeader('X-WP-Nonce', api.getRouteNonce());
        },
        data: function data(d) {
          d.date_from = getDateFrom();
          d.date_to = getDateTo();
          d.search.value = searchFieldValue;
          if (selector === '#masterstudy-datatable-lessons') {
            d.type = document.getElementById('masterstudy-analytics-course-page-types').value;
          }
        },
        dataSrc: dataSrcCallback,
        complete: function complete() {
          loaders.forEach(function (loader) {
            hideLoaders(loader);
          });
        }
      },
      columnDefs: columnDefs
    };
    if (orderIndex) {
      additionalOptions.order = [[orderIndex, 'desc']];
    }
    if (isLessonsTable) {
      additionalOptions.ajax.data = function (d) {
        d.date_from = getDateFrom();
        d.date_to = getDateTo();
        d.search.value = searchFieldValue;
        if (document.getElementById('masterstudy-analytics-course-page-orders')) {
          d.sort = document.getElementById('masterstudy-analytics-course-page-orders').value;
        }
      };
      lessonsData.forEach(function (item) {
        var lesson_type = item.lesson_type;
        switch (lesson_type) {
          case 'zoom conference':
            lesson_type = 'zoom_conference';
            break;
          case 'assignment':
            lesson_type = 'assignments';
            break;
        }
        pageData.push({
          title: '<img src="' + table_data.img_route + '/assets/icons/lessons/' + lesson_type + '.svg' + '" class="masterstudy-datatables-lesson-icon"></img>' + item.lesson_name,
          data: item.lesson_id,
          orderable: false,
          tooltip: item.lesson_name,
          render: function render(data, type, row, meta) {
            if (data === '-') {
              return "<div class=\"masterstudy-datatables-lesson-type masterstudy-datatables-lesson-type_progress\">\n                                <div class=\"masterstudy-datatables-lesson-tooltip\">" + table_data.progress_lesson + "</div></div>";
            } else if (data === '0') {
              return "<div class=\"masterstudy-datatables-lesson-type\">\n                                <div class=\"masterstudy-datatables-lesson-tooltip\">" + table_data.not_started_lesson + "</div></div>";
            } else if (data === '1') {
              return "<div class=\"masterstudy-datatables-lesson-type masterstudy-datatables-lesson-type_complete\">\n                                <div class=\"masterstudy-datatables-lesson-tooltip\">" + table_data.completed_lesson + "</div></div>";
            } else if (data === '-1') {
              return "<div class=\"masterstudy-datatables-lesson-type masterstudy-datatables-lesson-type_failed\">\n                                <div class=\"masterstudy-datatables-lesson-tooltip\">" + table_data.failed_lesson + "</div></div>";
            }
          }
        });
      });
      pageData.push({
        title: '',
        data: 'last',
        orderable: false
      });
      additionalOptions = _objectSpread(_objectSpread({}, additionalOptions), {}, {
        columnDefs: [{
          targets: 0,
          width: '30px',
          orderable: false
        }, {
          targets: 1,
          width: '200px',
          orderable: true
        }],
        headerCallback: function headerCallback(nHead) {
          if (!jQuery(nHead).find('.masterstudy-datatables-skew').length) {
            jQuery(nHead).find('th.dt-orderable-none').not('[data-dt-column="0"]').wrapInner('<div class="masterstudy-datatables-skew"><div class="masterstudy-datatables-skew__container"></div></div>');
          }
        },
        initComplete: function initComplete() {
          loaders.forEach(function (loader) {
            hideLoaders(loader);
          });
          this.api().columns().header().to$().each(function () {
            jQuery(this).find('.masterstudy-datatables-skew').append('<div class="masterstudy-datatables-skew__tooltip"><span>' + jQuery(this).text() + '</span></div>');
          });
          jQuery('.masterstudy-datatables-skew').mouseover(function (event) {
            jQuery(this).parent().find('.masterstudy-datatables-skew__tooltip').addClass('masterstudy-datatables-skew__tooltip_active');
          }).mouseout(function () {
            jQuery(this).parent().find('.masterstudy-datatables-skew__tooltip').removeClass('masterstudy-datatables-skew__tooltip_active');
          });
        },
        columns: pageData
      });
    }

    // Initialize the DataTable
    table = createDataTable(selector, pageData, additionalOptions);
    observeTableChanges(table, hidePagination);
  }
  return table;
}
function tablePaginationVisibility(table) {
  var hide = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var tableWrapper = table.table().container();
  var paginationStart = tableWrapper.querySelector('.dt-layout-cell.dt-start');
  var paginationEnd = tableWrapper.querySelector('.dt-layout-cell.dt-end');
  if (table.data().count() === 0 || hide) {
    if (paginationStart) paginationStart.style.display = 'none';
    if (paginationEnd) paginationEnd.style.display = 'none';
  } else {
    if (paginationStart) paginationStart.style.display = '';
    if (paginationEnd) paginationEnd.style.display = '';
  }
}
function observeTableChanges(table) {
  var hide = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var tableWrapper = table.table().container();
  var observer = new MutationObserver(function () {
    tablePaginationVisibility(table, hide);
  });
  observer.observe(tableWrapper, {
    childList: true,
    subtree: true
  });
  var intersectionObserver = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        table.columns.adjust();
      }
    });
  }, {
    threshold: 0.1
  });
  intersectionObserver.observe(tableWrapper);
}
function searchFieldIntent(target) {
  // Dispatch a custom event with the search value
  var searchEvent = new CustomEvent('intentTableSearch', {
    detail: {
      searchValue: target.value.trim(),
      searchTarget: target
    }
  });
  document.dispatchEvent(searchEvent);
}