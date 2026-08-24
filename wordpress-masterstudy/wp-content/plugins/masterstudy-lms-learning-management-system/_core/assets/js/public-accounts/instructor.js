"use strict";

(function ($) {
  $(document).ready(function () {
    if (typeof window.instructor_data === "undefined") return;
    document.title = instructor_data.user_login;
    var paginationState = {
      page: 1,
      total_pages: typeof window.pages_data !== "undefined" && window.pages_data ? parseInt(window.pages_data.total_pages, 10) || 0 : 0,
      tab: $(".masterstudy-tabs__item_active").data("id") || "courses",
      loading: false,
      pending: null
    };
    function clampPage(p) {
      var page = parseInt(p, 10) || 1;
      if (page < 1) page = 1;
      if (paginationState.total_pages > 0 && page > paginationState.total_pages) {
        page = paginationState.total_pages;
      }
      return page;
    }
    function abortPending() {
      if (paginationState.pending && paginationState.pending.readyState !== 4) {
        paginationState.pending.abort();
      }
      paginationState.pending = null;
    }
    function showLoader() {
      $(".masterstudy-instructor-public__loader").addClass("masterstudy-instructor-public__loader_show");
    }
    function hideLoader() {
      $(".masterstudy-instructor-public__loader").removeClass("masterstudy-instructor-public__loader_show");
    }
    function clearList() {
      $(".masterstudy-instructor-public__list").empty();
      $(".masterstudy-instructor-public__list-pagination").empty();
      $(".masterstudy-instructor-public__empty").removeClass("masterstudy-instructor-public__empty_show");
    }
    function startCountdownsForCourses(tabName) {
      if (tabName !== "courses") return;
      if (typeof window.stmLmsStartTimers === "function") {
        window.stmLmsStartTimers();
        return;
      }
      if ($.fn.countdown) {
        $(".masterstudy-countdown").each(function () {
          $(this).countdown({
            timestamp: $(this).data("timer")
          });
        });
      }
    }
    function setActiveTab(status) {
      $(".masterstudy-tabs__item").removeClass("masterstudy-tabs__item_active").filter("[data-id=\"".concat(status, "\"]")).addClass("masterstudy-tabs__item_active");
    }

    // ✅ UI-only init for pagination (then kill component click handlers to avoid conflicts)
    function initPaginationUi($paginationScope, currentPage, totalPages) {
      var tp = parseInt(totalPages, 10) || 0;
      var cp = parseInt(currentPage, 10) || 1;
      if (!tp || tp < 1) return; // nothing to init

      // keep global pages_data in sync (component expects it)
      if (typeof window.pages_data !== "undefined" && window.pages_data) {
        window.pages_data.current_page = cp;
        window.pages_data.total_pages = tp;
        window.pages_data.max_visible_pages = parseInt(window.pages_data.max_visible_pages, 10) || 5;
        window.pages_data.item_width = parseInt(window.pages_data.item_width, 10) || 50;
        window.pages_data.is_queryable = !!window.pages_data.is_queryable;
      }
      if (typeof window.initializePagination === "function") {
        window.initializePagination(cp, tp, parseInt(window.pages_data && window.pages_data.item_width, 10) || 50);
      }

      // 🔥 CRITICAL: disable pagination component click handlers inside our scope.
      // We want ONLY our AJAX handlers to control page changes.
      var $ctx = $paginationScope && $paginationScope.length ? $paginationScope : $(document);
      $ctx.find(".masterstudy-pagination__button-prev, .masterstudy-pagination__button-next").off("click");
      $ctx.find(".masterstudy-pagination__item-block").off("click");
    }

    // Initial details toggle logic
    var publicFieldsContainer = $(".masterstudy-form-builder-public-fields");
    var publicDescriptionContainer = $(".masterstudy-instructor-public__description");
    if (publicFieldsContainer.length && publicFieldsContainer.html().trim() !== "" || publicDescriptionContainer.length && publicDescriptionContainer.text().trim() !== "") {
      $(".masterstudy-instructor-public__details").css("display", "flex");
    }
    $(".masterstudy-instructor-public__details").click(function () {
      $(".masterstudy-instructor-public__details-wrapper").toggleClass("masterstudy-instructor-public__details-wrapper_show");
      $(this).toggleClass("masterstudy-instructor-public__details_hide");
    });

    // Tabs
    $(document).on("click", ".masterstudy-tabs__item", function (e) {
      e.preventDefault();
      var tabName = $(this).data("id");
      if (!tabName) return;
      setActiveTab(tabName);
      paginationState.tab = tabName;
      paginationState.page = 1;
      paginationState.total_pages = 0; // unknown until response

      if (tabName === "reviews") {
        $(".masterstudy-instructor-public__list-header").addClass("masterstudy-instructor-public__list-header_active");
        fetchDataForTab(1, tabName, $('input[name="reviews-search"]').val(), $("#reviews-rating").val());
      } else {
        $(".masterstudy-instructor-public__list-header").removeClass("masterstudy-instructor-public__list-header_active");
        fetchDataForTab(1, tabName);
      }
    });

    // Pagination: number click (AJAX)
    $(document).on("click", ".masterstudy-instructor-public__list-pagination .masterstudy-pagination__item-block", function (e) {
      e.preventDefault();
      var pageId = clampPage($(this).data("id"));
      var activeTab = $(".masterstudy-tabs__item_active").data("id") || paginationState.tab;
      if (pageId === paginationState.page && activeTab === paginationState.tab) return;
      if (activeTab === "reviews") {
        fetchDataForTab(pageId, activeTab, $('input[name="reviews-search"]').val(), $("#reviews-rating").val());
      } else {
        fetchDataForTab(pageId, activeTab);
      }
    });

    // Pagination: prev/next (AJAX; never rely on component click handlers)
    $(document).on("click", ".masterstudy-instructor-public__list-pagination .masterstudy-pagination__button-prev, .masterstudy-instructor-public__list-pagination .masterstudy-pagination__button-next", function (e) {
      e.preventDefault();
      var $btn = $(this);

      // Don't go out of bounds based on our state (most reliable)
      var isNext = $btn.hasClass("masterstudy-pagination__button-next");
      var nextRaw = isNext ? paginationState.page + 1 : paginationState.page - 1;
      if (nextRaw < 1) return;
      if (paginationState.total_pages > 0 && nextRaw > paginationState.total_pages) return;
      var newPageId = clampPage(nextRaw);
      if (newPageId === paginationState.page) return;
      var activeTab = $(".masterstudy-tabs__item_active").data("id") || paginationState.tab;
      if (activeTab === "reviews") {
        fetchDataForTab(newPageId, activeTab, $('input[name="reviews-search"]').val(), $("#reviews-rating").val());
      } else {
        fetchDataForTab(newPageId, activeTab);
      }
    });

    // Search / filters
    $(".masterstudy-search__icon").on("click", function () {
      paginationState.tab = "reviews";
      paginationState.page = 1;
      paginationState.total_pages = 0;
      fetchDataForTab(1, "reviews", $('input[name="reviews-search"]').val(), $("#reviews-rating").val());
    });
    $('input[name="reviews-search"]').on("keypress", function (event) {
      if (event.key === "Enter") {
        event.preventDefault();
        paginationState.tab = "reviews";
        paginationState.page = 1;
        paginationState.total_pages = 0;
        fetchDataForTab(1, "reviews", $(this).val(), $("#reviews-rating").val());
      }
    });
    $(".masterstudy-search__clear-icon").on("click", function () {
      paginationState.tab = "reviews";
      paginationState.page = 1;
      paginationState.total_pages = 0;
      fetchDataForTab(1, "reviews", "", $("#reviews-rating").val());
    });
    $(".masterstudy-select__option, .masterstudy-select__clear").on("click", function () {
      paginationState.tab = "reviews";
      paginationState.page = 1;
      paginationState.total_pages = 0;
      fetchDataForTab(1, "reviews", $('input[name="reviews-search"]').val(), $(this).data("value"));
    });
    function fetchDataForTab(pageId, tabName) {
      var course = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      var rating = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : "all";
      if (paginationState.loading) return;
      var safePage = clampPage(pageId);
      var endpoint = "";
      var perPage = instructor_data.courses_per_page;
      if (tabName === "reviews") {
        perPage = instructor_data.reviews_per_page;
        endpoint = "".concat(ms_lms_resturl, "/instructor-reviews?page=").concat(safePage, "&user=").concat(instructor_data.user, "&pp=").concat(perPage);
        if (course) endpoint += "&course=".concat(encodeURIComponent(course));
        if (rating && rating !== "all") endpoint += "&rating=".concat(encodeURIComponent(rating));
      } else if (tabName === "bundles") {
        perPage = instructor_data.bundles_per_page;
        endpoint = "".concat(ms_lms_resturl, "/instructor-bundles?page=").concat(safePage, "&user=").concat(instructor_data.user, "&pp=").concat(perPage);
      } else if (tabName === "co-owned") {
        perPage = instructor_data.co_owned_per_page;
        endpoint = "".concat(ms_lms_resturl, "/instructor-co-owned-courses?page=").concat(safePage, "&user=").concat(instructor_data.user, "&pp=").concat(perPage);
      } else {
        endpoint = "".concat(ms_lms_resturl, "/instructor-public-courses?page=").concat(safePage, "&user=").concat(instructor_data.user, "&pp=").concat(perPage);
      }
      if (typeof pll_current_language !== "undefined") {
        endpoint += "&lang=".concat(pll_current_language);
      }
      paginationState.loading = true;
      abortPending();
      clearList();
      showLoader();
      var $list = $(".masterstudy-instructor-public__list");
      var $pagination = $(".masterstudy-instructor-public__list-pagination");
      paginationState.pending = $.ajax({
        url: endpoint,
        method: "GET",
        headers: {
          "X-WP-Nonce": stm_lms_vars.wp_rest_nonce
        },
        dataType: "json"
      }).done(function (data) {
        var items = tabName === "reviews" ? data["reviews"] : data["courses"];
        var payloadTotalPages = parseInt(data && data["total_pages"], 10) || 0;
        var payloadCurrentPage = parseInt(data && data["current_page"], 10) || safePage || 1;
        paginationState.tab = tabName;
        paginationState.page = payloadCurrentPage;
        paginationState.total_pages = payloadTotalPages > 0 ? payloadTotalPages : 0;
        if (items && items.length > 0) {
          if (tabName === "reviews") {
            $(".masterstudy-instructor-public__list-header-total").text(data["total_posts"]);
          }
          items.forEach(function (itemHtml) {
            $list.append(itemHtml);
          });
          if (data["pagination"] && $.trim(String(data["pagination"])).length) {
            $pagination.append(data["pagination"]);
            // UI init only + kill component click handlers in this pagination scope
            initPaginationUi($pagination, paginationState.page, paginationState.total_pages);
          }
          startCountdownsForCourses(tabName);
        } else {
          $(".masterstudy-instructor-public__empty").addClass("masterstudy-instructor-public__empty_show");
        }
      }).fail(function (jqXHR, textStatus, errorThrown) {
        if (textStatus === "abort") return;
        console.error("There was a problem with the AJAX operation:", textStatus, errorThrown);
      }).always(function () {
        paginationState.loading = false;
        hideLoader();
        paginationState.pending = null;
      });
    }

    // Set initial active tab highlight
    setActiveTab(paginationState.tab);
  });
})(jQuery);