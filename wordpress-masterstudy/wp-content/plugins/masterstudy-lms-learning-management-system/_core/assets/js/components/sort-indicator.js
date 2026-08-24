"use strict";

(function ($) {
  $(document).ready(function () {
    $.each($('.masterstudy-sort-indicator'), function (i, indicator) {
      // Sorting data on table header click 
      $(indicator).on('click', function () {
        var indicatorUp = $(indicator).find('.masterstudy-sort-indicator__up');
        var indicatorDown = $(indicator).find('.masterstudy-sort-indicator__down');
        var sortOrders = ['none', 'asc', 'desc'];
        var currentIndex = sortOrders.indexOf($(indicator).data('sort'));
        var sortIndex = (currentIndex + 1) % sortOrders.length;
        $(indicator).data('sort', sortOrders[sortIndex]);

        // Reset indicator states
        $('.masterstudy-sort-indicator_is-hidden').removeClass('masterstudy-sort-indicator_is-hidden');
        // Asc sort indicator show
        switch (sortOrders[sortIndex]) {
          case 'asc':
            indicatorDown.addClass('masterstudy-sort-indicator_is-hidden');
            indicatorUp.removeClass('masterstudy-sort-indicator_is-hidden');
            break;
          case 'desc':
            indicatorDown.removeClass('masterstudy-sort-indicator_is-hidden');
            indicatorUp.addClass('masterstudy-sort-indicator_is-hidden');
            break;
          case 'none':
          // Fall through intentionally
          default:
            indicatorDown.removeClass('masterstudy-sort-indicator_is-hidden');
            indicatorUp.removeClass('masterstudy-sort-indicator_is-hidden');
            break;
        }
        setSortIndicatorEvent($(indicator), sortOrders[sortIndex]);
      });
    });
    function setSortIndicatorEvent(element, sortOrder) {
      document.dispatchEvent(new CustomEvent('msSortIndicatorEvent', {
        detail: {
          indicator: element,
          sortOrder: sortOrder || 'none'
        }
      }));
    }
  });
})(jQuery);