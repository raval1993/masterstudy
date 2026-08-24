"use strict";

(function ($) {
  $(document).ready(function () {
    var $root = $(".masterstudy-enrolled-courses");
    var $tabs = $(".masterstudy-enrolled-courses-tabs__block[data-status]");
    if (!$root.length || !$tabs.length) return;
    var $list = $root.find(".masterstudy-enrolled-courses__list");
    var $pagination = $root.find(".masterstudy-enrolled-courses__pagination");
    var $loader = $root.find(".masterstudy-enrolled-courses__loader");
    var $noResult = $root.find(".masterstudy-enrolled-courses__empty");
    if (!$list.length || !$noResult.length) return;
    var activeStatus = "all";
    var activePage = 1;

    // 0 = unknown (do NOT clamp upper bound until we know total pages)
    var totalPagesState = typeof window.pages_data !== "undefined" && window.pages_data ? parseInt(window.pages_data.total_pages, 10) || 0 : 0;
    var loading = false;
    var pendingXhr = null;
    function clampPage(p) {
      var page = parseInt(p, 10) || 1;
      if (page < 1) page = 1;
      if (totalPagesState > 0 && page > totalPagesState) page = totalPagesState;
      return page;
    }
    function setActiveTab(status) {
      $tabs.removeClass("masterstudy-enrolled-courses-tabs__block_active");
      $tabs.filter("[data-status=\"".concat(status, "\"]")).addClass("masterstudy-enrolled-courses-tabs__block_active");
    }
    function setTabsCounts(counts) {
      if (!counts) return;
      Object.keys(counts).forEach(function (key) {
        var $value = $(".masterstudy-enrolled-courses-tabs__block-value[data-status=\"".concat(key, "\"]"));
        if ($value.length) {
          $value.text(String(parseInt(counts[key], 10) || 0));
        }
      });
    }
    function showLoader() {
      if ($loader.length) {
        $loader.addClass("masterstudy-enrolled-courses__loader_show");
      } else {
        $root.addClass("masterstudy-enrolled-courses__loading");
      }
    }
    function hideLoader() {
      if ($loader.length) {
        $loader.removeClass("masterstudy-enrolled-courses__loader_show");
      } else {
        $root.removeClass("masterstudy-enrolled-courses__loading");
      }
    }
    function showNoResult() {
      $noResult.show();
    }
    function hideNoResult() {
      $noResult.hide();
    }
    function scrollToListTop() {
      var offset = $list.offset();
      if (!offset || typeof offset.top !== "number") return;
      $("html, body").stop(true).animate({
        scrollTop: offset.top - 90
      }, 250);
    }
    function startCountdowns() {
      if ($.fn.countdown) {
        $root.find(".masterstudy-countdown").each(function () {
          var $timer = $(this);
          var timerValue = $timer.data("timer");
          if (timerValue <= 0 || new Date() > timerValue) return;
          $timer.countdown({
            timestamp: timerValue
          });
        });
      }
      if (typeof window.stmLmsStartTimers === "function") {
        window.stmLmsStartTimers();
      }
    }
    function initPagination(page, totalPages) {
      if (typeof window.pages_data !== "undefined" && window.pages_data) {
        window.pages_data.current_page = parseInt(page, 10) || 1;
        window.pages_data.total_pages = parseInt(totalPages, 10) || 0;
        window.pages_data.max_visible_pages = parseInt(window.pages_data.max_visible_pages, 10) || 5;
        window.pages_data.item_width = parseInt(window.pages_data.item_width, 10) || 30;
        window.pages_data.is_queryable = !!window.pages_data.is_queryable;
      }
      if (typeof window.initializePagination === "function") {
        window.initializePagination(parseInt(page, 10) || 1, parseInt(totalPages, 10) || 0, parseInt(window.pages_data && window.pages_data.item_width, 10) || 30);
      }
    }
    function renderResponse(data, fallbackPage) {
      var _data$total_pages;
      var courses = data && Array.isArray(data.courses) ? data.courses : [];
      var paginationHtml = data && data.pagination ? data.pagination : "";
      var totalPages = parseInt(data && ((_data$total_pages = data.total_pages) !== null && _data$total_pages !== void 0 ? _data$total_pages : data.pages), 10) || 0;
      var currentPage = parseInt(data && data.current_page, 10) || parseInt(fallbackPage, 10) || 1;

      // sync from backend payload
      if (totalPages > 0) totalPagesState = totalPages;
      activePage = currentPage;
      setTabsCounts(data && (data.tab_counts || data.tabs_counts || data.counts));
      $list.empty();
      $pagination.empty();
      if (courses.length) {
        courses.forEach(function (html) {
          $list.append(html);
        });
        hideNoResult();
      } else {
        showNoResult();
      }
      if (paginationHtml && $.trim(paginationHtml).length && totalPagesState > 1) {
        $pagination.append(paginationHtml);
        initPagination(activePage, totalPagesState);
      }
    }
    function clearBeforeLoad() {
      $list.empty();
      $pagination.empty();
      hideNoResult();
    }
    function ensureAjaxGlobals() {
      return typeof window.stm_lms_ajaxurl !== "undefined" && typeof window.stm_lms_nonces !== "undefined" && typeof window.stm_lms_nonces["stm_lms_get_user_courses"] !== "undefined";
    }
    function abortPending() {
      if (pendingXhr && pendingXhr.readyState !== 4) {
        pendingXhr.abort();
      }
      pendingXhr = null;
    }
    function fetchCourses(page, status, force) {
      var safeStatus = status || "all";
      var isForce = force === true;
      var safePage = clampPage(page);
      if (loading) return;
      if (!ensureAjaxGlobals()) return;
      if (!isForce && safePage === activePage && safeStatus === activeStatus) return;
      activeStatus = safeStatus;
      activePage = safePage;
      loading = true;
      abortPending();
      clearBeforeLoad();
      showLoader();
      scrollToListTop();
      pendingXhr = $.ajax({
        url: window.stm_lms_ajaxurl,
        method: "GET",
        dataType: "json",
        data: {
          action: "stm_lms_get_user_courses",
          nonce: window.stm_lms_nonces["stm_lms_get_user_courses"],
          status: safeStatus,
          page: safePage
        }
      }).done(function (data) {
        renderResponse(data, safePage);
        startCountdowns();
        $(document).trigger("masterstudy:enrolled_courses:updated", [data]);
      }).fail(function (jqXHR, textStatus, errorThrown) {
        if (textStatus !== "abort") {
          console.error("AJAX error:", textStatus, errorThrown);
        }
      }).always(function () {
        loading = false;
        hideLoader();
        pendingXhr = null;
      });
    }

    // Tabs
    $tabs.on("click", function () {
      var status = $(this).data("status") || "all";
      if (status === activeStatus) return;
      setActiveTab(status);

      // reset pages for new status; total pages unknown until AJAX
      activeStatus = status;
      activePage = 1;
      totalPagesState = 0;
      fetchCourses(1, status, true);
    });

    // Pagination item click
    $root.on("click", ".masterstudy-pagination__item-block", function (e) {
      e.preventDefault();
      var page = clampPage($(this).data("id"));
      if (page === activePage) return;
      fetchCourses(page, activeStatus, true);
    });

    // Prev/Next (use activePage, not DOM)
    $root.on("click", ".masterstudy-pagination__button-prev, .masterstudy-pagination__button-next", function (e) {
      e.preventDefault();
      var isNext = $(this).hasClass("masterstudy-pagination__button-next");
      var nextPage = clampPage(isNext ? activePage + 1 : activePage - 1);
      if (nextPage === activePage) return;
      fetchCourses(nextPage, activeStatus, true);
    });

    // Initial UI
    setActiveTab(activeStatus);
  });
})(jQuery);