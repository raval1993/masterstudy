"use strict";

(function ($) {
  $(document).ready(function () {
    var $root = $(".masterstudy-instructor-co-courses");
    if (!$root.length || typeof window.masterstudy_instructor_co_courses === "undefined") return;
    var cfg = window.masterstudy_instructor_co_courses;
    var state = {
      page: 1,
      total_pages: 1,
      per_page: parseInt(cfg.per_page, 10) || 6,
      user_id: parseInt(cfg.user_id, 10) || 0,
      loading: false,
      pending: null
    };
    var SEL = {
      list: ".masterstudy-instructor-courses__list",
      pagination: ".masterstudy-instructor-courses__pagination",
      loader: ".masterstudy-instructor-courses__loader",
      card: ".masterstudy-course-card[data-course-id]",
      modalBtn: ".masterstudy-instructor-course-actions__modal-btn",
      modal: ".masterstudy-instructor-course-actions__modal",
      modalShowClass: "masterstudy-instructor-course-actions__modal_show",
      modalLinks: ".masterstudy-instructor-course-actions__modal-link"
    };
    var CLS = {
      loaderShow: "masterstudy-instructor-courses__loader_show"
    };
    var $list = $root.find(SEL.list);
    var $pagination = $root.find(SEL.pagination);
    var $loader = $root.find(SEL.loader);
    if (!$list.length || !$pagination.length) return;
    function showLoader() {
      if ($loader.length) {
        $loader.addClass(CLS.loaderShow);
      } else {
        $root.addClass("masterstudy-instructor-courses__loading");
      }
    }
    function hideLoader() {
      if ($loader.length) {
        $loader.removeClass(CLS.loaderShow);
      } else {
        $root.removeClass("masterstudy-instructor-courses__loading");
      }
    }
    function scrollToListTop() {
      var offset = $list.offset();
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
        var _window$pages_data$it, _window$pages_data;
        window.initializePagination(parseInt(page, 10) || 1, parseInt(totalPages, 10) || 0, parseInt((_window$pages_data$it = (_window$pages_data = window.pages_data) === null || _window$pages_data === void 0 ? void 0 : _window$pages_data.item_width) !== null && _window$pages_data$it !== void 0 ? _window$pages_data$it : "30", 10) || 30);
      }
    }
    function getCardFromEl($el) {
      return $el.closest(SEL.card);
    }
    function getModalInCard($card) {
      if (!$card || !$card.length) return $();
      return $card.find(SEL.modal).first();
    }
    function isModalOpen($card) {
      var $modal = getModalInCard($card);
      return !!($modal.length && $modal.hasClass(SEL.modalShowClass));
    }
    function closeModal($card) {
      var $modal = getModalInCard($card);
      if ($modal.length) {
        $modal.removeClass(SEL.modalShowClass);
      }
    }
    function closeAllModals(exceptCard) {
      $root.find(SEL.modal + "." + SEL.modalShowClass).each(function () {
        var $m = $(this);
        var $c = $m.closest(SEL.card);
        if (exceptCard && exceptCard.length && $c.is(exceptCard)) return;
        $m.removeClass(SEL.modalShowClass);
      });
    }
    function openModal($card) {
      var $modal = getModalInCard($card);
      if (!$modal.length) return;
      closeAllModals($card);
      $modal.addClass(SEL.modalShowClass);
    }
    function toggleModal($card) {
      if (isModalOpen($card)) {
        closeModal($card);
      } else {
        openModal($card);
      }
    }
    function buildModalLink(href, iconClass, text) {
      if (!href) return "";
      var icon = iconClass ? "<i class=\"" + iconClass + "\"></i>" : "";
      return "<a class=\"masterstudy-instructor-course-actions__modal-link\" href=\"" + href + "\" target=\"_blank\" rel=\"noopener noreferrer\">" + icon + text + "</a>";
    }
    function rebuildModal($card) {
      var $modal = getModalInCard($card);
      if (!$modal.length) return;
      var $editLink = $modal.find(SEL.modalLinks).first();
      var editHref = $editLink.attr("href") || "";
      var viewHref = $card.find(".masterstudy-course-card__image-link").first().attr("href") || $card.find(".masterstudy-course-card__info-title").first().attr("href") || "";
      var editText = cfg.strings && cfg.strings.edit || ($editLink.text() || "Edit").trim();
      var viewText = cfg.strings && cfg.strings.view || "View";
      var html = "<div class=\"masterstudy-instructor-course-actions__modal-list\">";
      html += buildModalLink(editHref, "stmlms-course-modal-edit", editText);
      html += buildModalLink(viewHref, "stmlms-eye", viewText);
      html += "</div>";
      $modal.html(html);
    }
    function rebuildModals() {
      $root.find(SEL.card).each(function () {
        rebuildModal($(this));
      });
    }
    function renderResponse(data, page) {
      var _data$total_pages;
      var html = data && data.html ? data.html : "";
      var paginationHtml = data && data.pagination ? data.pagination : "";
      var totalPages = parseInt(data && ((_data$total_pages = data.total_pages) !== null && _data$total_pages !== void 0 ? _data$total_pages : data.pages), 10) || 0;
      state.total_pages = totalPages || 1;
      $list.empty();
      $pagination.empty();
      if (html && $.trim(html).length) {
        $list.html(html);
        rebuildModals();
      }
      if (paginationHtml && totalPages > 1) {
        $pagination.html(paginationHtml);
        initPagination(page, totalPages);
      }
    }
    function abortPending() {
      if (state.pending && typeof state.pending.abort === "function") {
        state.pending.abort();
      }
      state.pending = null;
    }
    function buildUrl(page) {
      var base = (typeof window.ms_lms_resturl !== "undefined" ? window.ms_lms_resturl : cfg.rest_url || "").replace(/\/$/, "");
      var url = base + "/instructor-co-courses" + "?per_page=" + encodeURIComponent(state.per_page) + "&user=" + encodeURIComponent(state.user_id) + "&page=" + encodeURIComponent(page) + "&render=html";
      if (typeof window.pll_current_language !== "undefined") {
        url += "&lang=" + encodeURIComponent(window.pll_current_language);
      }
      return url;
    }
    function loadCourses(page, force) {
      var safePage = parseInt(page, 10) || 1;
      var isForce = force === true;
      if (state.loading) return;
      if (!state.user_id) return;
      if (!isForce && safePage === state.page) return;
      state.page = safePage;
      state.loading = true;
      abortPending();
      $list.empty();
      $pagination.empty();
      showLoader();
      scrollToListTop();
      var url = buildUrl(state.page);
      var controller = new AbortController();
      state.pending = controller;
      fetch(url, {
        method: "GET",
        headers: {
          "X-WP-Nonce": cfg.nonce,
          "Content-Type": "application/json"
        },
        signal: controller.signal
      }).then(function (response) {
        if (!response.ok) throw new Error("Bad response");
        return response.json();
      }).then(function (data) {
        renderResponse(data, state.page);
        $(document).trigger("masterstudy:instructor_co_courses:updated", [data]);
      })["catch"](function (err) {
        if (err && (err.name === "AbortError" || err.code === 20)) return;
        alert(cfg.strings && cfg.strings.error || "Error");
      })["finally"](function () {
        state.loading = false;
        hideLoader();
        state.pending = null;
      });
    }
    $root.on("click", ".masterstudy-pagination__item-block", function (e) {
      e.preventDefault();
      var page = parseInt($(this).data("id"), 10) || 1;
      if (page === state.page) return;
      loadCourses(page, true);
    });
    $root.on("click", ".masterstudy-pagination__button-prev, .masterstudy-pagination__button-next", function (e) {
      e.preventDefault();
      var $btn = $(this);
      var currentPage = state.page || 1;
      var nextPage = $btn.hasClass("masterstudy-pagination__button-next") ? currentPage + 1 : currentPage - 1;
      if (nextPage < 1) nextPage = 1;
      if (state.total_pages > 1 && nextPage > state.total_pages) nextPage = state.total_pages;
      if (nextPage === state.page) return;
      loadCourses(nextPage, true);
    });
    $root.on("click", SEL.modalBtn, function (e) {
      e.preventDefault();
      e.stopPropagation();
      var $card = getCardFromEl($(this));
      if (!$card.length) return;
      toggleModal($card);
    });
    $root.on("click", SEL.modal, function (e) {
      e.stopPropagation();
    });
    $(document).on("click", function (e) {
      var $t = $(e.target);
      if ($t.closest(SEL.modal).length) return;
      if ($t.closest(SEL.modalBtn).length) return;
      closeAllModals();
    });
    $(document).on("keydown", function (e) {
      if (e.key === "Escape" || e.keyCode === 27) {
        closeAllModals();
      }
    });
    rebuildModals();
    closeAllModals();
  });
})(jQuery);