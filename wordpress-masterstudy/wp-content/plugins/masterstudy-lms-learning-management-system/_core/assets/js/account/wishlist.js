"use strict";

(function ($) {
  $(document).ready(function () {
    var $root = $(".masterstudy-account-wishlist");
    if (!$root.length) return;
    var $list = $root.find(".masterstudy-account-wishlist__list");
    var $pagination = $root.find(".masterstudy-account-wishlist__pagination");
    var $loader = $root.find(".masterstudy-account-wishlist__loader");
    var $noResult = $root.find(".masterstudy-account-wishlist__empty");
    var cfg = window.masterstudy_wishlist || {};
    var userId = parseInt(cfg.user_id, 10) || 0;
    var activePage = 1;
    // 0 = unknown (do not clamp upper bound until known)
    var totalPagesState = typeof cfg.pages !== "undefined" ? parseInt(cfg.pages, 10) || 0 : typeof window.pages_data !== "undefined" && window.pages_data ? parseInt(window.pages_data.total_pages, 10) || 0 : 0;
    var loading = false;
    var pendingXhr = null;
    function ensureAjaxGlobals() {
      return typeof window.stm_lms_ajaxurl !== "undefined" && typeof window.stm_lms_nonces !== "undefined" && typeof window.stm_lms_nonces["stm_lms_user_wishlist"] !== "undefined";
    }
    function showLoader() {
      if ($loader.length) {
        $loader.addClass("masterstudy-account-wishlist__loader_show");
      } else {
        $root.addClass("masterstudy-account-wishlist__loading");
      }
    }
    function hideLoader() {
      if ($loader.length) {
        $loader.removeClass("masterstudy-account-wishlist__loader_show");
      } else {
        $root.removeClass("masterstudy-account-wishlist__loading");
      }
    }
    function showNoResult() {
      if ($noResult.length) $noResult.show();
    }
    function hideNoResult() {
      if ($noResult.length) $noResult.hide();
    }
    function scrollToTop() {
      var offset = ($list.length ? $list : $root).offset();
      if (!offset || typeof offset.top !== "number") return;
      $("html, body").stop(true).animate({
        scrollTop: offset.top - 90
      }, 250);
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
    function clearBeforeLoad() {
      if ($list.length) $list.empty();
      $pagination.empty();
      hideNoResult();
    }
    function renderResponse(data, fallbackPage) {
      var _data$total_pages;
      var courses = data && Array.isArray(data.courses) ? data.courses : [];
      var paginationHtml = data && data.pagination ? data.pagination : "";
      var totalPages = parseInt(data && ((_data$total_pages = data.total_pages) !== null && _data$total_pages !== void 0 ? _data$total_pages : data.pages), 10) || 0;
      var currentPage = parseInt(data && data.current_page, 10) || parseInt(fallbackPage, 10) || 1;

      // sync state from backend payload
      if (totalPages > 0) totalPagesState = totalPages;
      activePage = currentPage;
      if ($list.length) $list.empty();
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
    function abortPending() {
      if (pendingXhr && pendingXhr.readyState !== 4) {
        pendingXhr.abort();
      }
      pendingXhr = null;
    }
    function clampPage(p) {
      var page = parseInt(p, 10) || 1;
      if (page < 1) page = 1;
      // clamp upper bound only if known
      if (totalPagesState > 0 && page > totalPagesState) page = totalPagesState;
      return page;
    }
    function fetchWishlist(page, force) {
      var isForce = force === true;
      var safePage = clampPage(page);
      if (loading) return;
      if (!ensureAjaxGlobals()) return;
      if (!isForce && safePage === activePage) return;
      loading = true;
      activePage = safePage;
      abortPending();
      clearBeforeLoad();
      showLoader();
      scrollToTop();
      pendingXhr = $.ajax({
        url: window.stm_lms_ajaxurl,
        method: "GET",
        dataType: "json",
        data: {
          action: "stm_lms_user_wishlist",
          nonce: window.stm_lms_nonces["stm_lms_user_wishlist"],
          user_id: userId || 0,
          page: safePage
        }
      }).done(function (data) {
        renderResponse(data, safePage);
        $(document).trigger("masterstudy:wishlist:updated", [data]);
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

    // Pagination item click
    $root.on("click", ".masterstudy-pagination__item-block", function (e) {
      e.preventDefault();
      var page = clampPage($(this).data("id"));
      if (page === activePage) return;
      fetchWishlist(page, true);
    });

    // Prev/Next (use activePage, not DOM)
    $root.on("click", ".masterstudy-pagination__button-prev, .masterstudy-pagination__button-next", function (e) {
      e.preventDefault();
      var isNext = $(this).hasClass("masterstudy-pagination__button-next");
      var nextPage = clampPage(isNext ? activePage + 1 : activePage - 1);
      if (nextPage === activePage) return;
      fetchWishlist(nextPage, true);
    });

    // Optional: if you want to init pagination immediately when you already know pages
    if (totalPagesState > 1) {
      initPagination(activePage, totalPagesState);
    }
  });
})(jQuery);