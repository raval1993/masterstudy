"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
var isDomReady = false;
var api = new MasterstudyApiProviderClass();
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
    retrieve: false,
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
  var searchFieldValue = arguments.length > 9 && arguments[9] !== undefined ? arguments[9] : '';
  var orderIndex = arguments.length > 10 && arguments[10] !== undefined ? arguments[10] : '';
  var extraParams = arguments.length > 11 && arguments[11] !== undefined ? arguments[11] : null;
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
          if (typeof extraParams === 'function') {
            var ep = extraParams(currentRoute) || {};
            Object.assign(d, ep);
          } else if (extraParams && _typeof(extraParams) === 'object') {
            Object.assign(d, extraParams);
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
function hideLoaders(selector) {
  var elements = document.querySelectorAll(selector);
  elements.forEach(function (element) {
    var loaders = element.querySelectorAll('.masterstudy-skeleton-loader');
    loaders.forEach(function (loader) {
      loader.style.display = 'none';
    });
  });
}
function showLoaders(selector) {
  var elements = document.querySelectorAll(selector);
  elements.forEach(function (element) {
    var loaders = element.querySelectorAll('.masterstudy-skeleton-loader');
    loaders.forEach(function (loader) {
      loader.style.display = 'flex';
    });
  });
}