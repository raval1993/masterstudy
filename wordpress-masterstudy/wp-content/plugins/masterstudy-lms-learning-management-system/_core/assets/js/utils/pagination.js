"use strict";

(function ($) {
  window._masterstudy_utils = window._masterstudy_utils || {};
  window._masterstudy_utils.pagination = window._masterstudy_utils.pagination || {};
  var pagination = window._masterstudy_utils.pagination;
  pagination.renderPagination = function (_ref) {
    var paginationHtml = _ref.paginationHtml,
      paginationContainer = _ref.paginationContainer,
      totalPages = _ref.totalPages,
      currentPage = _ref.currentPage,
      onPageChange = _ref.onPageChange,
      getPerPageSelector = _ref.getPerPageSelector;
    var $nav = $(paginationContainer);
    $nav.toggle(totalPages > 1).html(paginationHtml);
    attachPaginationClickHandlers($nav, totalPages, onPageChange, getPerPageSelector);
    updatePaginationView($nav, totalPages, currentPage);
  };
  pagination.bindPerPageHandler = function (containerSelector, perPage, fetchFn) {
    $(".masterstudy-select__option, .masterstudy-select__clear", perPage).off("click").on("click", function () {
      $(containerSelector).remove();
      fetchFn($(this).data("value"));
    });
  };
  function updatePaginationView($scope, totalPages, currentPage) {
    var pagesWrapper = $scope.find('.masterstudy-pagination__wrapper');
    var wrapperWidth = pagesWrapper.data('width');
    pagesWrapper.css('width', wrapperWidth || '');
    $scope.find(".masterstudy-pagination__item").removeClass('masterstudy-pagination__item_current').hide();
    var start = Math.max(1, currentPage - 1);
    var end = Math.min(totalPages, currentPage + 1);
    if (currentPage === 1 || start === 1) end = Math.min(totalPages, start + 2);
    if (currentPage === totalPages || end === totalPages) start = Math.max(1, end - 2);
    for (var i = start; i <= end; i++) {
      $scope.find(".masterstudy-pagination__item:has([data-id=\"".concat(i, "\"])")).show();
    }
    $scope.find(".masterstudy-pagination__item-block[data-id=\"".concat(currentPage, "\"]")).parent().addClass('masterstudy-pagination__item_current');
    $scope.find(".masterstudy-pagination__button-next").toggle(currentPage < totalPages);
    $scope.find(".masterstudy-pagination__button-prev").toggle(currentPage > 1);
  }
  function attachPaginationClickHandlers($scope, totalPages, onPageChange, getPerPageSelector) {
    $scope.find(".masterstudy-pagination__item-block").off("click").on("click", function () {
      if ($(this).parent().hasClass('masterstudy-pagination__item_current')) {
        return;
      }
      var page = $(this).data("id");
      onPageChange($(getPerPageSelector()).val(), page);
    });
    $scope.find(".masterstudy-pagination__button-prev").off("click").on("click", function () {
      var current = $scope.find(".masterstudy-pagination__item_current .masterstudy-pagination__item-block").data("id");
      if (current > 1) onPageChange($(getPerPageSelector()).val(), current - 1);
    });
    $scope.find(".masterstudy-pagination__button-next").off("click").on("click", function () {
      var current = $scope.find(".masterstudy-pagination__item_current .masterstudy-pagination__item-block").data("id");
      var total = $scope.find(".masterstudy-pagination__item-block").length;
      if (current < total) onPageChange($(getPerPageSelector()).val(), current + 1);
    });
  }
})(jQuery);