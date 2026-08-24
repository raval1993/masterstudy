"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
(function ($) {
  $(document).ready(function () {
    var $root = $(".masterstudy-instructor-courses");
    if (!$root.length || typeof window.masterstudy_instructor_courses === "undefined") return;
    var cfg = window.masterstudy_instructor_courses;
    var state = {
      status: cfg.status || "all",
      page: 1,
      total_pages: 1,
      per_page: parseInt(cfg.per_page, 10) || 12,
      user_id: parseInt(cfg.user_id, 10) || 0,
      loading: false,
      pending: null
    };
    var SEL = {
      list: ".masterstudy-instructor-courses__list",
      pagination: ".masterstudy-instructor-courses__pagination",
      loader: ".masterstudy-instructor-courses__loader",
      empty: ".masterstudy-instructor-courses__empty",
      tabs: ".masterstudy-instructor-courses__tabs .masterstudy-tabs__item[data-id]",
      card: ".masterstudy-course-card[data-course-id]",
      modalBtn: ".masterstudy-instructor-course-actions__modal-btn",
      modal: ".masterstudy-instructor-course-actions__modal",
      modalShowClass: "masterstudy-instructor-course-actions__modal_show",
      // card (actions block status)
      actionsStatusText: ".masterstudy-instructor-course-actions__status",
      // card (top badge)
      cardFeaturedBadge: ".masterstudy-course-card__featured",
      // modal actions
      modalStatus: ".masterstudy-instructor-course-actions__modal-status",
      modalFeatured: ".masterstudy-instructor-course-actions__modal-featured",
      modalLinks: ".masterstudy-instructor-course-actions__modal-link"
    };
    var CLS = {
      loaderShow: "masterstudy-instructor-courses__loader_show",
      emptyShow: "masterstudy-instructor-courses__empty_show",
      itemLoading: "masterstudy-instructor-course-actions__modal-item_loading"
    };
    var $list = $root.find(SEL.list);
    var $pagination = $root.find(SEL.pagination);
    var $loader = $root.find(SEL.loader);
    var $empty = $root.find(SEL.empty);
    var $tabs = $(SEL.tabs);
    if (!$list.length || !$pagination.length || !$empty.length) return;
    function getStatusLabelMap() {
      var map = cfg.strings && cfg.strings.status_labels || null;
      return map || {
        publish: "Published",
        draft: "In draft",
        pending: "Pending",
        rejected: "Rejected",
        "private": "Private"
      };
    }
    function getStatusLabel(status) {
      var map = getStatusLabelMap();
      return map[status] || status;
    }
    function getModalStatusText(currentWpStatus) {
      // publish -> "Move to drafts", else -> "Publish"
      if (currentWpStatus === "publish") {
        return cfg.strings && cfg.strings.to_draft || "Move to drafts";
      }
      return cfg.strings && cfg.strings.publish || "Publish";
    }
    function getFeaturedText(isOn) {
      // isOn=true -> "Remove from Featured", else -> "Make Featured"
      if (isOn) {
        return cfg.strings && cfg.strings.featured || "Remove from Featured";
      }
      return cfg.strings && cfg.strings.not_featured || "Make Featured";
    }
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
    function showNoResult() {
      $empty.addClass(CLS.emptyShow);
    }
    function hideNoResult() {
      $empty.removeClass(CLS.emptyShow);
    }
    function scrollToListTop() {
      var offset = $list.offset();
      if (!offset || typeof offset.top !== "number") return;
      $("html, body").stop(true).animate({
        scrollTop: offset.top - 90
      }, 250);
    }
    function startCountdowns() {
      if (typeof window.stmLmsStartTimers === "function") {
        window.stmLmsStartTimers();
        return;
      }
      if ($.fn.countdown) {
        $root.find(".masterstudy-countdown").each(function () {
          $(this).countdown({
            timestamp: $(this).data("timer")
          });
        });
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
    function buildUrl(page, status) {
      var base = (typeof window.ms_lms_resturl !== "undefined" ? window.ms_lms_resturl : cfg.rest_url || "").replace(/\/$/, "");
      var url = base + "/instructor-courses" + "?per_page=" + encodeURIComponent(state.per_page) + "&user=" + encodeURIComponent(state.user_id) + "&page=" + encodeURIComponent(page) + "&status=" + encodeURIComponent(status) + "&render=html";
      if (typeof window.pll_current_language !== "undefined") {
        url += "&lang=" + encodeURIComponent(window.pll_current_language);
      }
      return url;
    }
    function initPagination(page, totalPages) {
      var $paginationContainer = $pagination.find(".masterstudy-pagination").first();
      if (typeof window.pages_data !== "undefined" && window.pages_data) {
        window.pages_data.current_page = parseInt(page, 10) || 1;
        window.pages_data.total_pages = parseInt(totalPages, 10) || 0;
        window.pages_data.max_visible_pages = parseInt(window.pages_data.max_visible_pages, 10) || 5;
        window.pages_data.item_width = parseInt(window.pages_data.item_width, 10) || 40;
        window.pages_data.is_queryable = !!window.pages_data.is_queryable;
      }
      if (typeof window.initializePagination === "function" && $paginationContainer.length) {
        var _window$pages_data$it, _window$pages_data;
        window.initializePagination(parseInt(page, 10) || 1, parseInt(totalPages, 10) || 0, parseInt((_window$pages_data$it = (_window$pages_data = window.pages_data) === null || _window$pages_data === void 0 ? void 0 : _window$pages_data.item_width) !== null && _window$pages_data$it !== void 0 ? _window$pages_data$it : "40", 10) || 40, $paginationContainer);
      }
    }
    function renderResponse(data, page) {
      var _data$total_pages;
      var html = data && data.html ? data.html : "";
      var paginationHtml = data && data.pagination ? data.pagination : "";
      var totalPages = parseInt(data && ((_data$total_pages = data.total_pages) !== null && _data$total_pages !== void 0 ? _data$total_pages : data.pages), 10) || 0;
      state.total_pages = totalPages || 1;
      closeAllModals();
      $list.empty();
      $pagination.empty();
      if (html && $.trim(html).length) {
        $list.html(html);
        hideNoResult();
      } else {
        showNoResult();
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
    function loadCourses(page, status, force) {
      var safePage = parseInt(page, 10) || 1;
      var safeStatus = status || "all";
      var isForce = force === true;
      if (state.loading) return;
      if (!state.user_id) return;
      if (!isForce && safePage === state.page && safeStatus === state.status) {
        return;
      }
      state.page = safePage;
      state.status = safeStatus;
      state.loading = true;
      abortPending();
      $list.empty();
      $pagination.empty();
      hideNoResult();
      showLoader();
      scrollToListTop();
      var url = buildUrl(state.page, state.status);
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
        startCountdowns();
        $(document).trigger("masterstudy:instructor_courses:updated", [data]);
      })["catch"](function (err) {
        if (err && (err.name === "AbortError" || err.code === 20)) return;
        alert(cfg.strings && cfg.strings.error || "Error");
      })["finally"](function () {
        state.loading = false;
        hideLoader();
        state.pending = null;
      });
    }
    function refreshCurrent() {
      loadCourses(state.page, state.status, true);
    }
    function setActiveTab(status) {
      $tabs.removeClass("masterstudy-tabs__item_active");
      $tabs.filter('[data-id="' + status + '"]').addClass("masterstudy-tabs__item_active");
    }
    function stripStatusSuffixClasses($el, prefix) {
      if (!$el || !$el.length) return;
      var cls = ($el.attr("class") || "").split(/\s+/);
      var kept = [];
      for (var i = 0; i < cls.length; i++) {
        if (cls[i].indexOf(prefix) !== 0) kept.push(cls[i]);
      }
      $el.attr("class", kept.join(" "));
    }
    function updateCardAndModalStatus($card, newWpStatus) {
      if (!$card || !$card.length) return;
      var label = getStatusLabel(newWpStatus);
      var $actionsStatus = $card.find(SEL.actionsStatusText).first();
      if ($actionsStatus.length) {
        stripStatusSuffixClasses($actionsStatus, "masterstudy-instructor-course-actions__status_");
        $actionsStatus.addClass("masterstudy-instructor-course-actions__status_" + newWpStatus);
        $actionsStatus.text(label);
      }
      var $modalStatus = $card.find(SEL.modalStatus).first();
      if ($modalStatus.length) {
        $modalStatus.attr("data-status", newWpStatus);
        $modalStatus.data("status", newWpStatus);
        $modalStatus.text(getModalStatusText(newWpStatus));
      }
    }
    function updateCardAndModalFeatured($card, isOn) {
      if (!$card || !$card.length) return;

      // Featured badge in card
      var $featuredBadge = $card.find(SEL.cardFeaturedBadge).first();
      if (isOn) {
        if (!$featuredBadge.length) {
          var $wrapper = $card.find(".masterstudy-course-card__wrapper").first();
          if ($wrapper.length) {
            $wrapper.prepend("<div class=\"masterstudy-course-card__featured\"><span>".concat(cfg.strings.featured_status, "</span></div>"));
          }
        }
      } else {
        if ($featuredBadge.length) $featuredBadge.remove();
      }

      // Modal featured item
      var $modalFeatured = $card.find(SEL.modalFeatured).first();
      if ($modalFeatured.length) {
        $modalFeatured.attr("data-featured", isOn ? "featured" : "not featured");
        $modalFeatured.data("featured", isOn ? "featured" : "not featured");
        $modalFeatured.text(getFeaturedText(isOn));
        if (isOn) {
          $modalFeatured.addClass("masterstudy-instructor-course-actions__modal-featured_on");
        } else {
          $modalFeatured.removeClass("masterstudy-instructor-course-actions__modal-featured_on");
        }
      }
    }
    function statusBelongsToCurrentFilter(newWpStatus) {
      if (state.status === "all") return true;
      if (state.status === "draft") return newWpStatus === "draft";
      if (state.status === "published") return newWpStatus === "publish";
      if (state.status === "coming_soon_status") return null;
      return true;
    }
    $tabs.on("click", function (e) {
      e.preventDefault();
      var status = $(this).data("id") || "all";
      if (status === state.status) return;
      setActiveTab(status);
      loadCourses(1, status, true);
    });
    $root.on("click", ".masterstudy-pagination__item-block", function (e) {
      e.preventDefault();
      var page = parseInt($(this).data("id"), 10) || 1;
      if (page === state.page) return;
      loadCourses(page, state.status, true);
    });
    $root.on("click", ".masterstudy-pagination__button-prev, .masterstudy-pagination__button-next", function (e) {
      e.preventDefault();
      var $btn = $(e.currentTarget);
      var currentPage = state.page || 1;
      var nextPage = $btn.hasClass("masterstudy-pagination__button-next") ? currentPage + 1 : currentPage - 1;
      if (nextPage < 1) nextPage = 1;
      if (state.total_pages > 1 && nextPage > state.total_pages) nextPage = state.total_pages;
      loadCourses(nextPage, state.status, true);
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
    $root.on("click", SEL.modalLinks, function () {
      var $card = getCardFromEl($(this));
      if ($card.length) closeModal($card);
    });
    function ensureAjaxGlobals() {
      return typeof window.stm_lms_ajaxurl !== "undefined" && typeof window.stm_lms_nonces !== "undefined";
    }
    function lockActionEl($el, locked) {
      if (!$el || !$el.length) return;
      $el.data("loading", locked === true);
      if (locked) $el.addClass(CLS.itemLoading);else $el.removeClass(CLS.itemLoading);
    }

    // Toggle status: click on modal status row
    $root.on("click", SEL.modalStatus, function (e) {
      e.preventDefault();
      e.stopPropagation();
      var $btn = $(this);
      if ($btn.data("loading") === true) return;
      var $card = getCardFromEl($btn);
      var courseId = parseInt($card.data("course-id"), 10);
      if (!courseId || !ensureAjaxGlobals()) return;
      var currentWpStatus = ($btn.data("status") || $btn.attr("data-status") || "").toString();
      var requestedStatus = currentWpStatus === "publish" ? "draft" : "publish";
      lockActionEl($btn, true);
      $.get(window.stm_lms_ajaxurl, {
        action: "stm_lms_change_course_status",
        post_id: courseId,
        status: requestedStatus,
        nonce: window.stm_lms_nonces["stm_lms_change_course_status"]
      }).done(function (resp) {
        var returnedStatus = (typeof resp === "string" ? resp : "") || "";
        returnedStatus = returnedStatus.toString();
        updateCardAndModalStatus($card, returnedStatus);
        closeModal($card);
        var belongs = statusBelongsToCurrentFilter(returnedStatus);
        if (belongs === null || belongs === false) {
          refreshCurrent();
        }
      }).fail(function () {
        alert(cfg.strings && cfg.strings.error || "Error");
      }).always(function () {
        lockActionEl($btn, false);
      });
    });
    $root.on("click", SEL.modalFeatured, function (e) {
      e.preventDefault();
      e.stopPropagation();
      var $btn = $(this);
      if ($btn.data("loading") === true) return;
      var $card = getCardFromEl($btn);
      var courseId = parseInt($card.data("course-id"), 10);
      if (!courseId || !ensureAjaxGlobals()) return;
      lockActionEl($btn, true);
      $.get(window.stm_lms_ajaxurl, {
        action: "stm_lms_change_featured",
        post_id: courseId,
        nonce: window.stm_lms_nonces["stm_lms_change_featured"]
      }).done(function (resp) {
        var featuredVal = resp && _typeof(resp) === "object" ? resp.featured : "";
        var isOn = featuredVal === "on";
        updateCardAndModalFeatured($card, isOn);
        closeModal($card);
        if ((resp === null || resp === void 0 ? void 0 : resp.available_quota) === 0 && !isOn) {
          alert(cfg.strings && cfg.strings.featured_limit || "You have reached the limit of featured courses.");
        }
      }).fail(function () {
        alert(cfg.strings && cfg.strings.error || "Error");
      }).always(function () {
        lockActionEl($btn, false);
      });
    });
    setActiveTab(state.status);
    closeAllModals();
  });
})(jQuery);