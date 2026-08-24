"use strict";

(function ($) {
  var statsData = null;
  var isDomReady = false;
  var coursesState = {
    page: 1,
    total_pages: 0,
    // 0 = unknown
    loading: false,
    pending: null
  };
  function clampCoursesPage(p) {
    var page = parseInt(p, 10) || 1;
    if (page < 1) page = 1;
    if (coursesState.total_pages > 0 && page > coursesState.total_pages) {
      page = coursesState.total_pages;
    }
    return page;
  }
  function abortCoursesPending() {
    if (coursesState.pending && coursesState.pending.readyState !== 4) {
      coursesState.pending.abort();
    }
    coursesState.pending = null;
  }

  // UI-only init for pagination and kill component click handlers (avoid conflicts with AJAX)
  function initPaginationUi($paginationScope, currentPage, totalPages) {
    var tp = parseInt(totalPages, 10) || 0;
    var cp = parseInt(currentPage, 10) || 1;
    if (!tp || tp < 1) return;
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

    // CRITICAL: disable pagination component click handlers in our scope
    var $ctx = $paginationScope && $paginationScope.length ? $paginationScope : $(document);
    $ctx.find(".masterstudy-pagination__button-prev, .masterstudy-pagination__button-next").off("click");
    $ctx.find(".masterstudy-pagination__item-block").off("click");
  }
  if (student_data.show_stats) {
    fetchStatsData();
  }
  $(document).ready(function () {
    isDomReady = true;
    document.title = student_data.user_login;
    var $list = $(".masterstudy-student-public__list");
    var $pagination = $(".masterstudy-student-public__list-pagination");
    var $loader = $(".masterstudy-student-public__loader");

    // Pagination item click (AJAX)
    $pagination.on("click", ".masterstudy-pagination__item-block", function (e) {
      e.preventDefault();
      var pageId = clampCoursesPage($(this).data("id"));
      if (pageId === coursesState.page) return;
      fetchCoursesData(pageId, true);
    });
    var publicFieldsContainer = $(".masterstudy-form-builder-public-fields");
    if (publicFieldsContainer.length && publicFieldsContainer.html().trim() !== "") {
      $(".masterstudy-student-public__details").css("display", "flex");
    }
    $(".masterstudy-student-public__details").click(function () {
      var fields = $(".masterstudy-form-builder-public-fields");
      fields.css("display", fields.css("display") === "none" ? "flex" : "none");
      $(this).toggleClass("masterstudy-student-public__details_hide");
    });

    // Prev/Next click (AJAX; state-based)
    $pagination.on("click", ".masterstudy-pagination__button-prev, .masterstudy-pagination__button-next", function (e) {
      e.preventDefault();

      // ✅ ignore disabled buttons (component marks disabled by class)
      if ($(this).hasClass("masterstudy-pagination__button_disabled")) return;
      var isNext = $(this).hasClass("masterstudy-pagination__button-next");
      var nextRaw = isNext ? coursesState.page + 1 : coursesState.page - 1;
      if (nextRaw < 1) return;
      if (coursesState.total_pages > 0 && nextRaw > coursesState.total_pages) return;
      var nextPage = clampCoursesPage(nextRaw);
      if (nextPage === coursesState.page) return;
      fetchCoursesData(nextPage, true);
    });
    function fetchCoursesData(pageId, force) {
      var isForce = force === true;
      var safePage = clampCoursesPage(pageId);
      if (coursesState.loading) return;
      if (!isForce && safePage === coursesState.page) return;
      coursesState.page = safePage;
      coursesState.loading = true;
      abortCoursesPending();
      var endpoint = "".concat(ms_lms_resturl, "/student-courses?page=").concat(safePage) + "&user=".concat(student_data.user) + "&pp=".concat(student_data.courses_per_page) + "&status=completed" + (typeof pll_current_language !== "undefined" ? "&lang=".concat(pll_current_language) : "");
      $list.empty();
      $pagination.empty();
      $loader.addClass("masterstudy-student-public__loader_show");
      coursesState.pending = $.ajax({
        url: endpoint,
        method: "GET",
        headers: {
          "X-WP-Nonce": stm_lms_vars.wp_rest_nonce
        },
        dataType: "json"
      }).done(function (data) {
        var _data$total_pages;
        var items = data && Array.isArray(data.courses) ? data.courses : [];

        // ✅ support both total_pages and pages
        var payloadTotalPages = parseInt(data && ((_data$total_pages = data.total_pages) !== null && _data$total_pages !== void 0 ? _data$total_pages : data.pages), 10) || 0;
        var payloadCurrentPage = parseInt(data && data.current_page, 10) || safePage || 1;
        if (payloadTotalPages > 0) coursesState.total_pages = payloadTotalPages;
        coursesState.page = payloadCurrentPage;
        if (items.length) {
          items.forEach(function (itemHtml) {
            $list.append(itemHtml);
          });
          if (data.pagination && $.trim(String(data.pagination)).length) {
            $pagination.append(data.pagination);
            // UI-only init + kill component click handlers (avoid conflicts)
            initPaginationUi($pagination, coursesState.page, coursesState.total_pages);
          }
        }
        $loader.removeClass("masterstudy-student-public__loader_show");
      }).fail(function (jqXHR, textStatus, errorThrown) {
        if (textStatus === "abort") return;
        console.error("There was a problem with the AJAX operation:", textStatus, errorThrown);
        $loader.removeClass("masterstudy-student-public__loader_show");
      }).always(function () {
        coursesState.loading = false;
        coursesState.pending = null;
      });
    }
    updateStatsContainers();
  });
  function fetchStatsData() {
    var endpoint = "".concat(ms_lms_resturl, "/student/stats/").concat(student_data.user) + (typeof pll_current_language !== "undefined" ? "?lang=".concat(pll_current_language) : "");
    $.ajax({
      url: endpoint,
      method: "GET",
      headers: {
        "X-WP-Nonce": ms_lms_nonce
      },
      dataType: "json",
      success: function success(data) {
        statsData = data;
        updateStatsContainers();
      },
      error: function error(jqXHR, textStatus, errorThrown) {
        console.error("There was a problem with the AJAX operation:", textStatus, errorThrown);
      }
    });
  }
  function updateStatsContainers() {
    if (statsData && isDomReady) {
      updateStatsBlock(".masterstudy-statistics-block_completed_courses", statsData.courses_statuses, "courses");
      updateStatsBlock(".masterstudy-statistics-block_groups", statsData.courses_types.enterprise_count);
      updateStatsBlock(".masterstudy-statistics-block_certificates", statsData.certificates);
      updateStatsBlock(".masterstudy-statistics-block_quizzes", statsData.total_quizzes);
      updateStatsBlock(".masterstudy-statistics-block_points", statsData.total_points);
      updateStatsBlock(".masterstudy-statistics-block_assignments", statsData.total_assignments);
    }
  }
  function updateStatsBlock(selector, value) {
    var type = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : "";
    var statsBlock = document.querySelector(selector);
    if (!statsBlock) return;
    var valueElement = statsBlock.querySelector(".masterstudy-statistics-block__value");
    if (!valueElement) return;
    valueElement.innerText = type === "courses" ? value.completed + " / " + value.summary : value;
    var loader = statsBlock.querySelector(".masterstudy-stats-loader");
    if (loader) loader.style.display = "none";
  }
})(jQuery);