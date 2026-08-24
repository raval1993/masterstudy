"use strict";

function initializePagination(currentPage, totalPages) {
  var _containerData$maxVis, _ref, _totalPages, _ref2, _currentPage, _ref3;
  var itemWidth = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 50;
  var container = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
  var pagesContainer = container ? jQuery(container).first() : jQuery(".masterstudy-pagination").first();
  if (!pagesContainer.length) {
    return;
  }
  var pagesWrapper = pagesContainer.find(".masterstudy-pagination__wrapper");
  var pagesList = pagesContainer.find(".masterstudy-pagination__list");
  var scrollButtonNext = pagesContainer.find(".masterstudy-pagination__button-next");
  var scrollButtonPrev = pagesContainer.find(".masterstudy-pagination__button-prev");
  var localizedPagesData = typeof window.pages_data !== "undefined" && window.pages_data ? window.pages_data : {};
  var containerData = pagesContainer.data();
  function parseNumber(value, fallback) {
    var parsed = parseInt(value, 10);
    return Number.isFinite(parsed) ? parsed : fallback;
  }
  var pagesData = {
    max_visible_pages: parseNumber((_containerData$maxVis = containerData.maxVisiblePages) !== null && _containerData$maxVis !== void 0 ? _containerData$maxVis : localizedPagesData.max_visible_pages, 5),
    total_pages: parseNumber((_ref = (_totalPages = totalPages) !== null && _totalPages !== void 0 ? _totalPages : containerData.totalPages) !== null && _ref !== void 0 ? _ref : localizedPagesData.total_pages, 0),
    current_page: parseNumber((_ref2 = (_currentPage = currentPage) !== null && _currentPage !== void 0 ? _currentPage : containerData.currentPage) !== null && _ref2 !== void 0 ? _ref2 : localizedPagesData.current_page, 1),
    is_queryable: ["1", 1, true, "true"].includes(containerData.isQueryable) || typeof containerData.isQueryable === "undefined" && !!localizedPagesData.is_queryable,
    item_width: parseNumber((_ref3 = itemWidth !== null && itemWidth !== void 0 ? itemWidth : containerData.itemWidth) !== null && _ref3 !== void 0 ? _ref3 : localizedPagesData.item_width, 50)
  };
  if (pagesData.total_pages < 1) {
    return;
  }
  var wrapperWidth = parseNumber(pagesWrapper.data("width"), 0);
  var pageStep = getPageStepWidth(pagesList, pagesData.item_width);
  var containerWidth = wrapperWidth || pageStep * Math.min(pagesData.max_visible_pages, pagesData.total_pages);
  pagesWrapper.css("width", containerWidth);
  currentPage = pagesData.current_page;
  totalPages = pagesData.total_pages;
  prevNextButtonState(pagesContainer, currentPage, totalPages);
  setCurrentPage(pagesList, currentPage, 'masterstudy-pagination__item_current');
  centerActivePage(pagesWrapper, pagesList, currentPage);
  scrollButtonNext.off('click').on('click', function (e) {
    e.preventDefault();
    if (currentPage >= totalPages) return;
    currentPage += 1;
    prevNextButtonState(pagesContainer, currentPage, totalPages);
    setCurrentPage(pagesList, currentPage, 'masterstudy-pagination__item_current');
    centerActivePage(pagesWrapper, pagesList, currentPage);
    if (pagesData.is_queryable) updatePageQueryParam(currentPage);
  });
  scrollButtonPrev.off('click').on('click', function (e) {
    e.preventDefault();
    if (currentPage <= 1) return;
    currentPage -= 1;
    prevNextButtonState(pagesContainer, currentPage, totalPages);
    setCurrentPage(pagesList, currentPage, 'masterstudy-pagination__item_current');
    centerActivePage(pagesWrapper, pagesList, currentPage);
    if (pagesData.is_queryable) updatePageQueryParam(currentPage);
  });
  function clamp(n, min, max) {
    return Math.max(min, Math.min(max, n));
  }
  function centerActivePage(pagesWrapper, pagesList, pageNumber) {
    var activeBlock = pagesList.find("[data-id=\"".concat(pageNumber, "\"]"));
    if (!activeBlock.length) return;
    if (parseInt(pageNumber, 10) <= 1) {
      pagesList.stop(true).animate({
        left: "0px"
      }, 120);
      return;
    }
    var wrapperWidth = pagesWrapper[0].getBoundingClientRect().width;
    var maxScroll = Math.max(0, pagesList[0].scrollWidth - wrapperWidth);
    if (maxScroll <= 0) {
      pagesList.stop(true).animate({
        left: "0px"
      }, 120);
      return;
    }
    var blockRect = activeBlock[0].getBoundingClientRect();
    var listRect = pagesList[0].getBoundingClientRect();
    var blockCenter = blockRect.left - listRect.left + blockRect.width / 2;
    var targetLeft = blockCenter - wrapperWidth / 2;
    targetLeft = clamp(targetLeft, 0, maxScroll);
    pagesList.stop(true).animate({
      left: -targetLeft + "px"
    }, 120);
  }
  function getPageStepWidth(pagesList, fallbackWidth) {
    var firstPage = pagesList.find(".masterstudy-pagination__item").first();
    var pageWidth = Math.round(firstPage.outerWidth());
    return pageWidth > 0 ? pageWidth : fallbackWidth;
  }
  pagesContainer.find(".masterstudy-pagination__item-block").off('click').on('click', function () {
    currentPage = parseInt(jQuery(this).data("id"), 10);
    prevNextButtonState(pagesContainer, currentPage, totalPages);
    setCurrentPage(pagesList, currentPage, 'masterstudy-pagination__item_current');
    centerActivePage(pagesWrapper, pagesList, currentPage);
    if (pagesData.is_queryable) {
      updatePageQueryParam(currentPage);
    }
  });
}
function calculateInitialPosition(currentPage, centeredPage, totalPages, maxPosition) {
  if (currentPage <= centeredPage) {
    return 0;
  } else if (currentPage > totalPages - centeredPage) {
    return maxPosition;
  } else {
    return (currentPage - centeredPage) * pages_data.item_width;
  }
}
function setCurrentPage(pagesList, currentPage, className) {
  pagesList.find("[data-id=\"".concat(currentPage, "\"]")).parent().siblings().removeClass(className);
  pagesList.find("[data-id=\"".concat(currentPage, "\"]")).parent().addClass(className);
}
function updateButtonState(scrollButtonNext, scrollButtonPrev, currentPage, totalPages) {
  scrollButtonPrev.toggleClass("masterstudy-pagination__button_disabled", currentPage === 1 || totalPages === 1);
  scrollButtonNext.toggleClass("masterstudy-pagination__button_disabled", currentPage === totalPages || totalPages === 1);
}
function scrollPageList(currentPage, centeredPage, maxPosition, pagesList) {
  var currentPosition = 0;
  if (currentPage > centeredPage && currentPage < pages_data.total_pages - centeredPage + 1) {
    currentPosition = (currentPage - centeredPage) * pages_data.item_width;
  } else if (currentPage <= centeredPage) {
    currentPosition = 0;
  } else {
    currentPosition = maxPosition;
  }
  pagesList.animate({
    left: -currentPosition + "px"
  }, 50);
}
function calculateCurrentPosition(currentPage, centeredPage, maxPosition, noScroll, totalPages) {
  if (currentPage < centeredPage) {
    return 0;
  } else if (currentPage > totalPages - centeredPage + 1) {
    return noScroll ? 0 : maxPosition;
  } else {
    return (currentPage - centeredPage) * pages_data.item_width;
  }
}
function updatePageQueryParam(pageNumber) {
  var currentUrl = window.location.href;
  var urlParams = new URLSearchParams(window.location.search);
  var queryName = "page";
  if (urlParams.has(queryName)) {
    urlParams.set(queryName, pageNumber);
  } else {
    urlParams.append(queryName, pageNumber);
  }
  var queryUrl = currentUrl.split("?")[0] + "?" + urlParams.toString();
  window.history.replaceState({}, document.title, queryUrl);
  window.location.href = queryUrl;
}
function prevNextButtonState(container, currentPage, totalPages) {
  var btnClassPrev = '.masterstudy-pagination__button-prev';
  var btnClassNext = '.masterstudy-pagination__button-next';
  container.find(btnClassPrev).toggleClass("masterstudy-pagination__button_disabled", currentPage === 1 || totalPages === 1);
  container.find(btnClassNext).toggleClass("masterstudy-pagination__button_disabled", currentPage === totalPages || totalPages === 1);
}
jQuery(document).ready(function () {
  jQuery(".masterstudy-pagination").each(function () {
    initializePagination(null, null, null, this);
  });
});