"use strict";

(function ($) {
  'use strict';

  var masterstudyMegaMenuMobileNavCounter = 0;
  $(document).ready(function () {
    // Initialize all mega menu instances
    $('.masterstudy-mega-menu-wrapper').each(function () {
      var $wrapper = $(this);

      // Hamburger navigation for small screens (<= 767.98px)
      initMobileNavToggle($wrapper);

      // Initialize each desktop menu separately
      $wrapper.find('.masterstudy-mega-menu--desktop').each(function () {
        initDesktopMegaMenu($(this));
      });

      // Initialize each mobile menu separately
      $wrapper.find('.masterstudy-mega-menu--mobile').each(function () {
        initMobileMegaMenu($(this));
      });
    });
  });

  /**
   * Initialize hamburger toggle for small screens (<= 767.98px).
   *
   * This does not duplicate menu items. It only toggles visibility of existing
   * top-level mobile triggers via a wrapper class.
   */
  function initMobileNavToggle($wrapper) {
    if (!$wrapper.length) {
      return;
    }
    var $toggle = $wrapper.find('.masterstudy-mega-menu__mobile-nav-toggle').first();
    var navId = masterstudyMegaMenuMobileNavCounter++;
    var ns = '.masterstudy-mega-menu-nav-' + navId;
    if (!$toggle.length) {
      return;
    }
    var isHamburgerViewport = function isHamburgerViewport() {
      return window.matchMedia && window.matchMedia('(max-width: 767.98px)').matches;
    };
    function resetMobilePanels() {
      $wrapper.find('.masterstudy-mega-menu__mobile-panel.is-open').removeClass('is-open');
      $wrapper.find('.masterstudy-mega-menu__mobile-overlay.is-visible').removeClass('is-visible');
      $wrapper.find('.masterstudy-mega-menu__mobile-panel').off('keydown.focustrap');
      $wrapper.find('.masterstudy-mega-menu--mobile.is-open').removeClass('is-open');
      $wrapper.find('.masterstudy-mega-menu__mobile-trigger[aria-expanded="true"]').attr('aria-expanded', 'false');
      $wrapper.find('.masterstudy-mega-menu__mobile-content').stop(true, true).removeAttr('style');
    }
    function anyMobilePanelOpen() {
      return $wrapper.find('.masterstudy-mega-menu__mobile-panel.is-open').length > 0;
    }
    function openNav() {
      if (!isHamburgerViewport()) {
        return;
      }

      // Close any open mobile panels within this wrapper.
      resetMobilePanels();
      $wrapper.addClass('is-mobile-nav-open');
      $toggle.attr('aria-expanded', 'true');
      $('body').css('overflow', 'hidden');
    }
    function closeNav() {
      resetMobilePanels();
      $wrapper.removeClass('is-mobile-nav-open');
      $toggle.attr('aria-expanded', 'false');

      // Restore body scroll only if no mobile panel is open.
      if (!anyMobilePanelOpen()) {
        $('body').css('overflow', '');
      }
    }
    function toggleNav() {
      if ($wrapper.hasClass('is-mobile-nav-open')) {
        closeNav();
      } else {
        openNav();
      }
    }

    // Expose close handler for inner mobile panels.
    $wrapper.data('masterstudyMegaMenuCloseNav', closeNav);
    $toggle.on('click' + ns, function (e) {
      e.preventDefault();
      e.stopPropagation();
      toggleNav();
    });

    // Close nav when clicking a top-level leaf item.
    $wrapper.on('click' + ns, '.masterstudy-mega-menu__mobile-trigger--no-children', function () {
      closeNav();
    });

    // Close on ESC key
    $(document).on('keydown' + ns, function (e) {
      if (e.key === 'Escape' && $wrapper.hasClass('is-mobile-nav-open')) {
        closeNav();
        $toggle.focus();
      }
    });

    // Auto-close when leaving hamburger viewport (e.g. rotate / resize).
    $(window).on('resize' + ns, function () {
      if (!isHamburgerViewport() && $wrapper.hasClass('is-mobile-nav-open')) {
        closeNav();
      }
    });

    // Cleanup on destroy
    $wrapper.on('remove', function () {
      $toggle.off(ns);
      $wrapper.off(ns);
      $(document).off(ns);
      $(window).off(ns);
    });
  }

  /**
   * Initialize desktop mega menu interactions
   */
  function initDesktopMegaMenu($menu) {
    if (!$menu.length) {
      return;
    }
    var $trigger = $menu.find('.masterstudy-mega-menu__trigger');
    var $panel = $menu.find('.masterstudy-mega-menu__panel');
    var menuId = $menu.data('menu-id'); // WordPress menu item ID
    var isFullWidth = $menu.hasClass('masterstudy-mega-menu--full-width');
    var hasPanel = $panel.length > 0;
    var triggerHref = typeof $trigger.attr('href') === 'string' ? $trigger.attr('href').trim() : '';
    var triggerIsLink = $trigger.is('a[href]') && '' !== triggerHref && '#' !== triggerHref;
    var openSide = ['auto', 'right', 'left'].includes($menu.data('open-side')) ? $menu.data('open-side') : 'auto';
    var viewportPadding = 10;
    var closeTimer;
    if (!menuId) {
      console.warn('Mega menu missing menu-id');
      return;
    }
    if (!hasPanel) {
      return;
    }

    // Position panel on window resize
    function positionPanel() {
      if (isFullWidth) {
        // Full-width: position at viewport left edge
        var viewportLeft = -$menu.offset().left;
        $panel.css({
          'left': viewportLeft + 'px',
          'width': '100vw'
        });
      } else {
        // Normal mode: position relative to trigger, adjust if off-screen
        var panelWidth = $panel.outerWidth();
        var triggerWidth = $trigger.outerWidth();
        var triggerOffset = $trigger.offset().left;
        var viewportWidth = $(window).width();
        var menuOffset = $menu.offset().left;
        var nextLeft = triggerOffset;

        // Reset to default
        $panel.css({
          'left': '0',
          'right': 'auto',
          'width': ''
        });
        if ('left' === openSide) {
          nextLeft = triggerOffset + triggerWidth - panelWidth;
        }
        if ('auto' === openSide && nextLeft + panelWidth > viewportWidth - 20) {
          nextLeft = triggerOffset + triggerWidth - panelWidth;
        }
        nextLeft = Math.max(20, Math.min(nextLeft, viewportWidth - panelWidth - 20));
        $panel.css('left', nextLeft - menuOffset + 'px');
      }
    }

    // Position on window resize
    $(window).on('resize.megamenu-' + menuId, function () {
      positionPanel();
      positionVisibleCascadeSubmenus($menu, viewportPadding);
    });

    // Initial positioning (set position before first open to avoid visible shift)
    setTimeout(function () {
      positionPanel();
    }, 100);

    // Open on hover
    $trigger.on('mouseenter.megamenu', function () {
      clearTimeout(closeTimer);
      openPanel();
    });
    $trigger.on('focusin.megamenu', function () {
      clearTimeout(closeTimer);
      openPanel();
    });
    $menu.on('mouseenter.megamenu', function () {
      clearTimeout(closeTimer);
    });
    $menu.on('mouseenter.megamenu-submenu focusin.megamenu-submenu', '.masterstudy-mega-menu__item--has-submenu', function () {
      positionCascadeSubmenu($(this), viewportPadding);
    });

    // Open/close on click (accessibility and mobile touch)
    $trigger.on('click.megamenu', function (e) {
      var toggleByIconOnly = triggerIsLink && !$(e.target).closest('.masterstudy-mega-menu__trigger-icon').length;
      if (toggleByIconOnly) {
        return;
      }
      e.preventDefault();
      e.stopPropagation();
      if ($panel.hasClass('is-open')) {
        closePanel();
      } else {
        openPanel();
      }
    });
    if (!triggerIsLink) {
      $trigger.on('keydown.megamenu', function (e) {
        if ('Enter' !== e.key && ' ' !== e.key) {
          return;
        }
        e.preventDefault();
        e.stopPropagation();
        if ($panel.hasClass('is-open')) {
          closePanel();
        } else {
          openPanel();
        }
      });
    }

    // Close on mouseleave with delay
    $menu.on('mouseleave.megamenu', function () {
      closeTimer = setTimeout(function () {
        closePanel();
      }, 220);
    });

    // Close on outside click
    $(document).on('click.megamenu-' + menuId, function (e) {
      if (!$menu.is(e.target) && $menu.has(e.target).length === 0) {
        closePanel();
      }
    });

    // Close on ESC key
    $(document).on('keydown.megamenu-' + menuId, function (e) {
      if (e.key === 'Escape' && $panel.hasClass('is-open')) {
        closePanel();
        $trigger.focus();
      }
    });
    function openPanel() {
      // Close all other open panels first
      $('.masterstudy-mega-menu--desktop .masterstudy-mega-menu__panel.is-open').not($panel).removeClass('is-open');
      $('.masterstudy-mega-menu--desktop .masterstudy-mega-menu__trigger[aria-expanded="true"]').not($trigger).attr('aria-expanded', 'false').find('.masterstudy-mega-menu__trigger-icon').css('transform', 'rotate(0deg)');
      positionPanel(); // Calculate position before opening
      $panel.addClass('is-open');
      $trigger.attr('aria-expanded', 'true');
      $trigger.find('.masterstudy-mega-menu__trigger-icon').css('transform', 'rotate(180deg)');
      positionVisibleCascadeSubmenus($menu, viewportPadding);
    }
    function closePanel() {
      $panel.removeClass('is-open');
      $trigger.attr('aria-expanded', 'false');
      $trigger.find('.masterstudy-mega-menu__trigger-icon').css('transform', 'rotate(0deg)');
    }

    // Cleanup on destroy
    $menu.on('remove', function () {
      $(document).off('click.megamenu-' + menuId);
      $(document).off('keydown.megamenu-' + menuId);
      $(window).off('resize.megamenu-' + menuId);
    });
  }
  function positionVisibleCascadeSubmenus($menu, viewportPadding) {
    $menu.find('.masterstudy-mega-menu__item--has-submenu').each(function () {
      positionCascadeSubmenu($(this), viewportPadding);
    });
  }
  function measureSubmenu($submenu) {
    if (!$submenu.length) {
      return null;
    }
    var previous = {
      display: $submenu[0].style.display,
      visibility: $submenu[0].style.visibility,
      pointerEvents: $submenu[0].style.pointerEvents
    };
    $submenu.css({
      display: 'block',
      visibility: 'hidden',
      pointerEvents: 'none'
    });
    var rect = $submenu[0].getBoundingClientRect();
    $submenu.css(previous);
    return rect;
  }
  function positionCascadeSubmenu($item, viewportPadding) {
    var $submenu = $item.children('.masterstudy-mega-menu__submenu');
    if (!$submenu.length) {
      return;
    }
    $item.removeClass('masterstudy-mega-menu__item--submenu-stack');
    $submenu.css({
      top: '',
      left: '',
      right: '',
      marginLeft: '',
      marginTop: '',
      transform: ''
    });
    var itemRect = $item[0].getBoundingClientRect();
    var submenuRect = measureSubmenu($submenu);
    if (!submenuRect) {
      return;
    }
    var defaultTop = itemRect.height * 0.4;
    var defaultLeft = itemRect.right + 19;
    var willOverflowRight = defaultLeft + submenuRect.width > window.innerWidth - viewportPadding;
    var nextTop = defaultTop;
    if (willOverflowRight) {
      $item.addClass('masterstudy-mega-menu__item--submenu-stack');
      nextTop = itemRect.height + 2;
    }
    var viewportTop = itemRect.top + nextTop;
    if (viewportTop + submenuRect.height > window.innerHeight - viewportPadding) {
      nextTop -= viewportTop + submenuRect.height - (window.innerHeight - viewportPadding);
      viewportTop = itemRect.top + nextTop;
    }
    if (viewportTop < viewportPadding) {
      nextTop += viewportPadding - viewportTop;
    }
    $submenu.css('top', nextTop + 'px');
  }

  /**
   * Initialize mobile mega menu interactions
   */
  function initMobileMegaMenu($menu) {
    if (!$menu.length) {
      return;
    }
    var $wrapper = $menu.closest('.masterstudy-mega-menu-wrapper');
    var $trigger = $menu.find('.masterstudy-mega-menu__mobile-trigger');
    var $panel = $menu.find('.masterstudy-mega-menu__mobile-panel');
    var $content = $panel.find('.masterstudy-mega-menu__mobile-content').first();
    var $overlay = $menu.find('.masterstudy-mega-menu__mobile-overlay');
    var $close = $menu.find('.masterstudy-mega-menu__mobile-close');
    var $navToggle = $wrapper.find('.masterstudy-mega-menu__mobile-nav-toggle').first();
    var menuId = 'mobile-' + $menu.data('menu-id'); // WordPress menu item ID
    var hasChildren = $panel.length > 0;
    if (!$menu.data('menu-id')) {
      console.warn('Mega menu missing menu-id');
      return;
    }
    if (hasChildren) {
      $trigger.attr('aria-expanded', 'false');
    }
    function isInlineMobileNav() {
      return $wrapper.length && $wrapper.hasClass('is-mobile-nav-open') && window.matchMedia && window.matchMedia('(max-width: 767.98px)').matches;
    }
    function openInlinePanel() {
      $menu.addClass('is-open');
      $trigger.attr('aria-expanded', 'true');
      $content.stop(true, true).slideDown(300);
    }
    function closeInlinePanel() {
      $menu.removeClass('is-open');
      $trigger.attr('aria-expanded', 'false');
      $content.stop(true, true).slideUp(300);
    }

    // Open panel
    $trigger.on('click.megamenu-mobile', function (e) {
      if (!hasChildren) {
        return;
      }
      e.preventDefault();
      if (isInlineMobileNav()) {
        if ($menu.hasClass('is-open')) {
          closeInlinePanel();
        } else {
          openInlinePanel();
        }
        return;
      }

      // If hamburger nav is open, close it first.
      if ($wrapper.length) {
        var closeNav = $wrapper.data('masterstudyMegaMenuCloseNav');
        if (typeof closeNav === 'function') {
          closeNav();
        }
      }
      openPanel();
    });

    // Close panel
    $close.on('click.megamenu-mobile', function (e) {
      e.preventDefault();
      closePanel();
    });
    $overlay.on('click.megamenu-mobile', function () {
      closePanel();
    });

    // Close on ESC key
    $(document).on('keydown.megamenu-mobile-' + menuId, function (e) {
      if (e.key !== 'Escape') {
        return;
      }
      if (isInlineMobileNav() && $menu.hasClass('is-open')) {
        closeInlinePanel();
        $trigger.focus();
      } else if ($panel.hasClass('is-open')) {
        closePanel();
      }
    });

    // Accordion toggle (for both full toggle and icon-only toggle)
    $menu.find('.masterstudy-mega-menu__mobile-section-toggle').each(function () {
      var $toggle = $(this);
      var $section = $toggle.closest('.masterstudy-mega-menu__mobile-section');
      var $list = $section.children('.masterstudy-mega-menu__mobile-list').first();
      $toggle.on('click.megamenu-accordion', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if ($section.hasClass('is-open')) {
          // Close accordion
          $section.removeClass('is-open');
          $toggle.attr('aria-expanded', 'false');
          $list.slideUp(300);
        } else {
          // Open accordion
          $section.addClass('is-open');
          $toggle.attr('aria-expanded', 'true');
          $list.slideDown(300);
        }
      });
    });
    function openPanel() {
      $menu.removeClass('is-open');
      $panel.find('.masterstudy-mega-menu__mobile-content').removeAttr('style');
      $panel.addClass('is-open');
      $overlay.addClass('is-visible');
      $trigger.attr('aria-expanded', 'true');

      // Prevent body scroll when panel is open
      $('body').css('overflow', 'hidden');

      // Trap focus inside panel
      trapFocus($panel);
    }
    function closePanel() {
      $panel.removeClass('is-open');
      $overlay.removeClass('is-visible');
      $trigger.attr('aria-expanded', 'false');

      // Restore body scroll only if the hamburger nav is not open
      if (!$wrapper.hasClass('is-mobile-nav-open')) {
        $('body').css('overflow', '');
      }

      // Remove focus trap
      $panel.off('keydown.focustrap');

      // Return focus to a visible control
      if (window.matchMedia && window.matchMedia('(max-width: 767.98px)').matches && $navToggle.length && $navToggle.is(':visible')) {
        $navToggle.focus();
      } else {
        $trigger.focus();
      }
    }

    /**
     * Trap focus inside container for accessibility
     */
    function trapFocus($container) {
      var focusableElements = $container.find('a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])').filter(':visible');
      if (!focusableElements.length) {
        return;
      }
      var firstFocusable = focusableElements.first();
      var lastFocusable = focusableElements.last();

      // Focus first element
      setTimeout(function () {
        firstFocusable.focus();
      }, 100);

      // Trap focus with Tab key
      $container.off('keydown.focustrap').on('keydown.focustrap', function (e) {
        if (e.key === 'Tab') {
          if (e.shiftKey) {
            // Shift + Tab
            if (document.activeElement === firstFocusable[0]) {
              e.preventDefault();
              lastFocusable.focus();
            }
          } else {
            // Tab
            if (document.activeElement === lastFocusable[0]) {
              e.preventDefault();
              firstFocusable.focus();
            }
          }
        }
      });
    }

    // Cleanup on destroy
    $menu.on('remove', function () {
      $(document).off('keydown.megamenu-mobile-' + menuId);
      $('body').css('overflow', '');
    });
  }
})(jQuery);