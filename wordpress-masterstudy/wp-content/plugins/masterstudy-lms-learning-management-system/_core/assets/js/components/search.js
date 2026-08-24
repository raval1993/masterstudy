"use strict";

(function ($) {
  $(document).ready(function () {
    $.each($('.masterstudy-search'), function (i, search) {
      var searchInput = $(search).find('.masterstudy-search__input');
      var searchIcon = $(search).find('.masterstudy-search__icon');
      var searchClear = $(search).find('.masterstudy-search__clear-icon');
      var isQueryable = $(search).data('queryable');
      var searchName = searchInput.attr('name');
      var currentUrl = window.location.href;
      var urlParams = new URLSearchParams(window.location.search);
      if (searchName == undefined) {
        return;
      }
      // In load search existense check
      if (urlParams.get(searchName) != undefined && urlParams.get(searchName) !== '') {
        searchInput.val(urlParams.get(searchName));
        setCustomSearchEvent(searchName, searchInput.val(), 'onload');
        $(search).addClass('masterstudy-search_inuse');
      } else {
        var strUrlParams = urlParams.toString();
        urlParams["delete"](searchName);
        if (strUrlParams) {
          var queryUrl = currentUrl.split('?')[0] + '?' + strUrlParams;
          window.history.replaceState({}, document.title, queryUrl);
        }
        $(search).removeClass('masterstudy-search_inuse');
      }

      // Search click
      searchIcon.on('click', function () {
        urlParams = new URLSearchParams(window.location.search);
        urlParams["delete"]('page');
        if (urlParams.has(searchName)) {
          urlParams.set(searchName, searchInput.val());
        } else {
          urlParams.append(searchName, searchInput.val());
        }
        setCustomSearchEvent(searchName, searchInput.val(), 'submit');
        if (isQueryable) {
          var _queryUrl = currentUrl.split('?')[0] + '?' + urlParams.toString();
          window.history.replaceState({}, document.title, _queryUrl);
          window.location.href = _queryUrl;
        }
      });

      // Clearance of search box
      searchClear.on('click', function () {
        searchInput.val('');
        $(search).removeClass('masterstudy-search_inuse');
        urlParams = new URLSearchParams(window.location.search);
        var searched = urlParams.has(searchName);
        urlParams["delete"](searchName);
        setCustomSearchEvent(searchName, '', 'cleared');
        if (searched && isQueryable) {
          var _queryUrl2 = currentUrl.split('?')[0] + '?' + urlParams.toString();
          window.history.replaceState({}, document.title, _queryUrl2);
          window.location.href = _queryUrl2;
        }
      });

      // Clearance of search box
      searchInput.on('change keyup paste', function () {
        if (searchInput.val().length > 0) {
          $(search).addClass('masterstudy-search_inuse');
        } else {
          $(search).removeClass('masterstudy-search_inuse');
        }
      });
      function setCustomSearchEvent(name, value, event) {
        document.dispatchEvent(new CustomEvent('msfieldEvent', {
          detail: {
            value: value || '',
            name: name || '',
            event: event || 'change'
          }
        }));
      }
    });
  });
})(jQuery);