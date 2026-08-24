"use strict";

(function ($) {
  $(document).ready(function () {
    var $menu = $('.masterstudy-account-menu');
    var $switch = $menu.find('input[type="checkbox"][name="instructor_menu"]');
    var $menuButton = $('[data-id="menu"].masterstudy-account-mobile-menu__link');
    var $backButton = $('.masterstudy-account-sidebar__back');
    var USER_ID = window.masterstudy_account_data && masterstudy_account_data.user_id ? String(masterstudy_account_data.user_id) : 'guest';
    var COOKIE_NAME_BASE = 'masterstudy-account-menu-mode';
    var COOKIE_NAME = "".concat(COOKIE_NAME_BASE, ":").concat(USER_ID);
    var COOKIE_DAYS = 30;

    // items that must always stay visible and not participate in mode logic
    var ALWAYS_VISIBLE_SELECTOR = '.masterstudy-account-menu__list-item_settings, .masterstudy-account-menu__list-item_logout, .masterstudy-account-menu__list-item_messages';
    var appliedMode = null;

    // --- cookie helpers ---
    function setCookie(name, value, days) {
      var d = new Date();
      d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
      var expires = 'expires=' + d.toUTCString();
      document.cookie = "".concat(encodeURIComponent(name), "=").concat(encodeURIComponent(value), "; ").concat(expires, "; path=/; SameSite=Lax");
    }
    function getCookie(name) {
      var key = encodeURIComponent(name) + '=';
      var parts = document.cookie.split(';');
      for (var i = 0; i < parts.length; i++) {
        var c = parts[i].trim();
        if (c.indexOf(key) === 0) return decodeURIComponent(c.substring(key.length));
      }
      return null;
    }
    function normalizeCookieBool(val) {
      if (val === null || typeof val === 'undefined') return null;
      var v = String(val).toLowerCase().trim();
      if (v === '1' || v === 'true' || v === 'on' || v === 'yes') return true;
      if (v === '0' || v === 'false' || v === 'off' || v === 'no') return false;
      return null;
    }

    // --- active item ---
    function getActiveItem() {
      return $menu.find('.masterstudy-account-menu__list-item_active').not('.masterstudy-account-menu__list-item_settings').first();
    }
    function tagItemsDeterministically() {
      $menu.find('.masterstudy-account-menu__list-item').not(ALWAYS_VISIBLE_SELECTOR).each(function () {
        var $item = $(this);
        var place = String($item.data('menu-place') || '').toLowerCase();
        if (place === 'main') $item.attr('data-menu-mode', 'on');else if (place === 'learning') $item.attr('data-menu-mode', 'off');else {
          $item.attr('data-menu-mode', '');
        }
      });
    }

    // Deterministic rendering (NO SWAP)
    function renderMode(isOn) {
      // Items with data-menu-mode='on' show when isOn=true, etc.
      $menu.find('.masterstudy-account-menu__list-item').not(ALWAYS_VISIBLE_SELECTOR).each(function () {
        var $item = $(this);
        var mode = $item.attr('data-menu-mode'); // 'on' | 'off' | null
        if (!mode) return;
        var shouldShow = mode === (isOn ? 'on' : 'off');
        $item.toggleClass('masterstudy-account-menu__list-item_hidden', !shouldShow);
      });

      // Always-visible items: ensure not hidden
      $menu.find(ALWAYS_VISIBLE_SELECTOR).removeClass('masterstudy-account-menu__list-item_hidden');

      // Hide empty sections
      $menu.find('.masterstudy-account-menu__list-section').each(function () {
        var $section = $(this);
        var hasVisible = $section.find('.masterstudy-account-menu__list-item').not('.masterstudy-account-menu__list-item_hidden').length > 0;
        $section.toggle(hasVisible);
      });
    }
    function applyMode(isOn, opts) {
      var options = opts || {};
      if (appliedMode === isOn) return;
      appliedMode = isOn;
      $switch.prop('checked', isOn);
      if (options.saveCookie !== false) {
        setCookie(COOKIE_NAME, isOn ? '1' : '0', COOKIE_DAYS);
      }
      renderMode(isOn);
      if (!hasVisibleMenuItems()) {
        var flipped = !isOn;
        appliedMode = flipped;
        $switch.prop('checked', flipped);
        setCookie(COOKIE_NAME, flipped ? '1' : '0', COOKIE_DAYS);
        renderMode(flipped);
      }
    }
    function hasVisibleMenuItems() {
      return $menu.find('.masterstudy-account-menu__list-item').not(ALWAYS_VISIBLE_SELECTOR).filter(function () {
        return !$(this).hasClass('masterstudy-account-menu__list-item_hidden');
      }).length > 0;
    }

    // Active page priority:
    // if active item belongs to mode "on/off", that mode must win (direct link case)
    function syncModeToActive() {
      var $active = getActiveItem();
      if (!$active.length) return;
      var mode = $active.attr('data-menu-mode'); // 'on'|'off' or null
      if (!mode) return;
      var requiredOn = mode === 'on';
      if (appliedMode !== requiredOn) {
        applyMode(requiredOn); // updates cookie + renders deterministically
      }
    }

    // --- INIT ---
    tagItemsDeterministically();
    var cookieVal = normalizeCookieBool(getCookie(COOKIE_NAME));
    var initialMode = cookieVal !== null ? cookieVal : false;

    // Apply cookie preference first...
    applyMode(initialMode);

    // ...but then active page has priority (direct link)
    syncModeToActive();

    // If some other script sets active class later
    setTimeout(syncModeToActive, 0);
    function syncSwitchUI() {
      $switch.prop('checked', !!appliedMode);
    }
    window.addEventListener('popstate', function () {
      syncModeToActive();
      syncSwitchUI();
    });
    window.addEventListener('pageshow', function () {
      var cookieVal = normalizeCookieBool(getCookie(COOKIE_NAME));
      if (cookieVal !== null) {
        appliedMode = null;
        applyMode(cookieVal);
      }
      syncModeToActive();
      syncSwitchUI();
    });

    // --- EVENTS ---
    $switch.on('change', function () {
      applyMode($(this).is(':checked'));
    });
    $menuButton.on('click', function (event) {
      event.preventDefault();
      $('.masterstudy-account-sidebar').addClass('masterstudy-account-sidebar_open');
      $('body').addClass('masterstudy-account-body-overflow');
    });
    $backButton.on('click', function (event) {
      event.preventDefault();
      $('.masterstudy-account-sidebar').removeClass('masterstudy-account-sidebar_open');
      $('body').removeClass('masterstudy-account-body-overflow');
    });
    var $logoutLink = $menu.find('.masterstudy-account-menu__list-item_logout');
    $logoutLink.off('click.masterstudyLogoutConfirm').on('click.masterstudyLogoutConfirm', function (e) {
      var ok = window.confirm(masterstudy_account_data.log_out_confirm_message || 'Are you sure you want to log out?');
      if (!ok) {
        e.preventDefault();
        e.stopPropagation();
      }
    });
  });
})(jQuery);