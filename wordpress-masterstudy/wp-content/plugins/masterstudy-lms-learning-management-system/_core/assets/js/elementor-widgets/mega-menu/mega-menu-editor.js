"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }
function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }
function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }
var masterstudyMegaMenuEditorMobileNavCounter = 0;
var MegaMenu = /*#__PURE__*/function (_elementorModules$fro) {
  _inherits(MegaMenu, _elementorModules$fro);
  var _super = _createSuper(MegaMenu);
  function MegaMenu() {
    _classCallCheck(this, MegaMenu);
    return _super.apply(this, arguments);
  }
  _createClass(MegaMenu, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        selectors: {
          wrapper: '.masterstudy-mega-menu-wrapper',
          desktopMenu: '.masterstudy-mega-menu--desktop',
          mobileMenu: '.masterstudy-mega-menu--mobile',
          trigger: '.masterstudy-mega-menu__trigger',
          panel: '.masterstudy-mega-menu__panel',
          mobileTrigger: '.masterstudy-mega-menu__mobile-trigger',
          mobilePanel: '.masterstudy-mega-menu__mobile-panel',
          mobileOverlay: '.masterstudy-mega-menu__mobile-overlay',
          mobileClose: '.masterstudy-mega-menu__mobile-close',
          mobileSectionToggle: '.masterstudy-mega-menu__mobile-section-toggle',
          mobileSection: '.masterstudy-mega-menu__mobile-section',
          mobileList: '.masterstudy-mega-menu__mobile-list'
        }
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var selectors = this.getSettings('selectors');
      return {
        $wrapper: this.$element.find(selectors.wrapper),
        $desktopMenus: this.$element.find(selectors.desktopMenu),
        $mobileMenus: this.$element.find(selectors.mobileMenu)
      };
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _this = this;
      this.elements.$wrapper.each(function (index, wrapperEl) {
        _this.initMobileNavToggle(jQuery(wrapperEl));
      });

      // Initialize all desktop mega menus
      this.elements.$desktopMenus.each(function (index, menuEl) {
        _this.initDesktopMenu(jQuery(menuEl));
      });

      // Initialize all mobile mega menus
      this.elements.$mobileMenus.each(function (index, menuEl) {
        _this.initMobileMenu(jQuery(menuEl));
      });
    }
  }, {
    key: "initMobileNavToggle",
    value: function initMobileNavToggle($wrapper) {
      if (!$wrapper.length) {
        return;
      }
      var $toggle = $wrapper.find('.masterstudy-mega-menu__mobile-nav-toggle').first();
      var navId = masterstudyMegaMenuEditorMobileNavCounter++;
      var ns = '.masterstudy-mega-menu-nav-' + navId;
      if (!$toggle.length) {
        return;
      }
      var isHamburgerViewport = function isHamburgerViewport() {
        return window.matchMedia && window.matchMedia('(max-width: 767.98px)').matches;
      };
      var resetMobilePanels = function resetMobilePanels() {
        $wrapper.find('.masterstudy-mega-menu__mobile-panel.is-open').removeClass('is-open');
        $wrapper.find('.masterstudy-mega-menu__mobile-overlay.is-visible').removeClass('is-visible');
        $wrapper.find('.masterstudy-mega-menu__mobile-panel').off('keydown.focustrap');
        $wrapper.find('.masterstudy-mega-menu--mobile.is-open').removeClass('is-open');
        $wrapper.find('.masterstudy-mega-menu__mobile-trigger[aria-expanded="true"]').attr('aria-expanded', 'false');
        $wrapper.find('.masterstudy-mega-menu__mobile-content').stop(true, true).removeAttr('style');
      };
      var anyMobilePanelOpen = function anyMobilePanelOpen() {
        return $wrapper.find('.masterstudy-mega-menu__mobile-panel.is-open').length > 0;
      };
      var openNav = function openNav() {
        if (!isHamburgerViewport()) {
          return;
        }
        resetMobilePanels();
        $wrapper.addClass('is-mobile-nav-open');
        $toggle.attr('aria-expanded', 'true');
        jQuery('body').css('overflow', 'hidden');
      };
      var closeNav = function closeNav() {
        resetMobilePanels();
        $wrapper.removeClass('is-mobile-nav-open');
        $toggle.attr('aria-expanded', 'false');
        if (!anyMobilePanelOpen()) {
          jQuery('body').css('overflow', '');
        }
      };
      var toggleNav = function toggleNav() {
        if ($wrapper.hasClass('is-mobile-nav-open')) {
          closeNav();
        } else {
          openNav();
        }
      };
      $wrapper.data('masterstudyMegaMenuCloseNav', closeNav);
      $toggle.off('click' + ns).on('click' + ns, function (e) {
        e.preventDefault();
        e.stopPropagation();
        toggleNav();
      });
      $wrapper.off('click' + ns, '.masterstudy-mega-menu__mobile-trigger--no-children').on('click' + ns, '.masterstudy-mega-menu__mobile-trigger--no-children', function () {
        closeNav();
      });
      jQuery(document).off('keydown' + ns).on('keydown' + ns, function (e) {
        if (e.key === 'Escape' && $wrapper.hasClass('is-mobile-nav-open')) {
          closeNav();
          $toggle.focus();
        }
      });
      jQuery(window).off('resize' + ns).on('resize' + ns, function () {
        if (!isHamburgerViewport() && $wrapper.hasClass('is-mobile-nav-open')) {
          closeNav();
        }
      });
      $wrapper.on('remove', function () {
        $toggle.off(ns);
        $wrapper.off(ns);
        jQuery(document).off(ns);
        jQuery(window).off(ns);
      });
    }
  }, {
    key: "initDesktopMenu",
    value: function initDesktopMenu($menu) {
      var _this2 = this;
      var s = this.getSettings('selectors');
      var $trigger = $menu.find(s.trigger);
      var $panel = $menu.find(s.panel);
      var menuId = $menu.data('menu-id'); // WordPress menu item ID
      var isFullWidth = $menu.hasClass('masterstudy-mega-menu--full-width');
      var openSide = ['auto', 'right', 'left'].includes($menu.data('open-side')) ? $menu.data('open-side') : 'auto';
      var viewportPadding = 10;
      var closeTimer;
      if (!menuId) {
        console.warn('Mega menu missing menu-id');
        return;
      }

      // Position panel function
      var positionPanel = function positionPanel() {
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
          var viewportWidth = jQuery(window).width();
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
      };

      // Position on window resize
      jQuery(window).on('resize.megamenu-' + menuId, function () {
        positionPanel();
        _this2.positionVisibleCascadeSubmenus($menu, viewportPadding);
      });

      // Initial positioning (set position before first open to avoid visible shift)
      setTimeout(function () {
        positionPanel();
      }, 100);

      // Open on hover
      $trigger.off('mouseenter.megamenu').on('mouseenter.megamenu', function () {
        clearTimeout(closeTimer);
        _this2.openDesktopPanel($trigger, $panel, positionPanel);
      });
      $menu.off('mouseenter.megamenu').on('mouseenter.megamenu', function () {
        clearTimeout(closeTimer);
      });
      $menu.off('mouseenter.megamenu-submenu focusin.megamenu-submenu', '.masterstudy-mega-menu__item--has-submenu').on('mouseenter.megamenu-submenu focusin.megamenu-submenu', '.masterstudy-mega-menu__item--has-submenu', function (e) {
        _this2.positionCascadeSubmenu(jQuery(e.currentTarget), viewportPadding);
      });

      // Open/close on click
      $trigger.off('click.megamenu').on('click.megamenu', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if ($panel.hasClass('is-open')) {
          _this2.closeDesktopPanel($trigger, $panel);
        } else {
          _this2.openDesktopPanel($trigger, $panel, positionPanel, $menu, viewportPadding);
        }
      });

      // Close on mouseleave with delay
      $menu.off('mouseleave.megamenu').on('mouseleave.megamenu', function () {
        closeTimer = setTimeout(function () {
          _this2.closeDesktopPanel($trigger, $panel);
        }, 100); // Reduced from 200ms to 100ms
      });

      // Close on outside click
      jQuery(document).off('click.megamenu-' + menuId).on('click.megamenu-' + menuId, function (e) {
        if (!$menu.is(e.target) && $menu.has(e.target).length === 0) {
          _this2.closeDesktopPanel($trigger, $panel);
        }
      });

      // Close on ESC
      jQuery(document).off('keydown.megamenu-' + menuId).on('keydown.megamenu-' + menuId, function (e) {
        if (e.key === 'Escape' && $panel.hasClass('is-open')) {
          _this2.closeDesktopPanel($trigger, $panel);
          $trigger.focus();
        }
      });
    }
  }, {
    key: "initMobileMenu",
    value: function initMobileMenu($menu) {
      var _this3 = this;
      var s = this.getSettings('selectors');
      var $wrapper = $menu.closest(s.wrapper);
      var $trigger = $menu.find(s.mobileTrigger);
      var $panel = $menu.find(s.mobilePanel);
      var $content = $panel.find('.masterstudy-mega-menu__mobile-content').first();
      var $overlay = $menu.find(s.mobileOverlay);
      var $close = $menu.find(s.mobileClose);
      var menuId = 'mobile-' + $menu.data('menu-id'); // WordPress menu item ID
      var hasChildren = $panel.length > 0;
      if (!$menu.data('menu-id')) {
        console.warn('Mega menu missing menu-id');
        return;
      }
      if (hasChildren) {
        $trigger.attr('aria-expanded', 'false');
      }
      var isInlineMobileNav = function isInlineMobileNav() {
        return $wrapper.length && $wrapper.hasClass('is-mobile-nav-open') && window.matchMedia && window.matchMedia('(max-width: 767.98px)').matches;
      };
      var openInlinePanel = function openInlinePanel() {
        $menu.addClass('is-open');
        $trigger.attr('aria-expanded', 'true');
        $content.stop(true, true).slideDown(300);
      };
      var closeInlinePanel = function closeInlinePanel() {
        $menu.removeClass('is-open');
        $trigger.attr('aria-expanded', 'false');
        $content.stop(true, true).slideUp(300);
      };

      // Open panel
      $trigger.off('click.megamenu-mobile').on('click.megamenu-mobile', function (e) {
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
        _this3.openMobilePanel($panel, $overlay, $trigger);
      });

      // Close panel
      $close.off('click.megamenu-mobile').on('click.megamenu-mobile', function (e) {
        e.preventDefault();
        _this3.closeMobilePanel($panel, $overlay, $trigger);
      });
      $overlay.off('click.megamenu-mobile').on('click.megamenu-mobile', function () {
        _this3.closeMobilePanel($panel, $overlay, $trigger);
      });

      // Close on ESC
      jQuery(document).off('keydown.megamenu-mobile-' + menuId).on('keydown.megamenu-mobile-' + menuId, function (e) {
        if (e.key !== 'Escape') {
          return;
        }
        if (isInlineMobileNav() && $menu.hasClass('is-open')) {
          closeInlinePanel();
          $trigger.focus();
        } else if ($panel.hasClass('is-open')) {
          _this3.closeMobilePanel($panel, $overlay, $trigger);
        }
      });

      // Accordion toggle (for both full toggle and icon-only toggle)
      $menu.find(s.mobileSectionToggle).each(function (index, toggleEl) {
        var $toggle = jQuery(toggleEl);
        var $section = $toggle.closest(s.mobileSection);
        var $list = $section.find(s.mobileList);
        $toggle.off('click.megamenu-accordion').on('click.megamenu-accordion', function (e) {
          e.preventDefault();
          e.stopPropagation();
          if ($section.hasClass('is-open')) {
            $section.removeClass('is-open');
            $list.slideUp(300);
          } else {
            $section.addClass('is-open');
            $list.slideDown(300);
          }
        });
      });
    }
  }, {
    key: "openDesktopPanel",
    value: function openDesktopPanel($trigger, $panel, positionFunc) {
      var $menu = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
      var viewportPadding = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : 10;
      // Close all other open panels first
      jQuery('.masterstudy-mega-menu--desktop .masterstudy-mega-menu__panel.is-open').not($panel).removeClass('is-open');
      jQuery('.masterstudy-mega-menu--desktop .masterstudy-mega-menu__trigger[aria-expanded="true"]').not($trigger).attr('aria-expanded', 'false').find('.masterstudy-mega-menu__trigger-icon').css('transform', 'rotate(0deg)');
      if (positionFunc) {
        positionFunc(); // Calculate position before opening
      }

      $panel.addClass('is-open');
      $trigger.attr('aria-expanded', 'true');
      $trigger.find('.masterstudy-mega-menu__trigger-icon').css('transform', 'rotate(180deg)');
      if ($menu) {
        this.positionVisibleCascadeSubmenus($menu, viewportPadding);
      }
    }
  }, {
    key: "closeDesktopPanel",
    value: function closeDesktopPanel($trigger, $panel) {
      $panel.removeClass('is-open');
      $trigger.attr('aria-expanded', 'false');
      $trigger.find('.masterstudy-mega-menu__trigger-icon').css('transform', 'rotate(0deg)');
    }
  }, {
    key: "openMobilePanel",
    value: function openMobilePanel($panel, $overlay, $trigger) {
      $panel.find('.masterstudy-mega-menu__mobile-content').removeAttr('style');
      $panel.addClass('is-open');
      $overlay.addClass('is-visible');
      $trigger.attr('aria-expanded', 'true');
      jQuery('body').css('overflow', 'hidden');
      this.trapFocus($panel);
    }
  }, {
    key: "closeMobilePanel",
    value: function closeMobilePanel($panel, $overlay, $trigger) {
      $panel.removeClass('is-open');
      $overlay.removeClass('is-visible');
      $trigger.attr('aria-expanded', 'false');
      jQuery('body').css('overflow', '');
      $panel.off('keydown.focustrap');
      $trigger.focus();
    }
  }, {
    key: "trapFocus",
    value: function trapFocus($container) {
      var focusableElements = $container.find('a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])').filter(':visible');
      if (!focusableElements.length) {
        return;
      }
      var firstFocusable = focusableElements.first();
      var lastFocusable = focusableElements.last();
      setTimeout(function () {
        firstFocusable.focus();
      }, 100);
      $container.off('keydown.focustrap').on('keydown.focustrap', function (e) {
        if (e.key === 'Tab') {
          if (e.shiftKey) {
            if (document.activeElement === firstFocusable[0]) {
              e.preventDefault();
              lastFocusable.focus();
            }
          } else {
            if (document.activeElement === lastFocusable[0]) {
              e.preventDefault();
              firstFocusable.focus();
            }
          }
        }
      });
    }
  }, {
    key: "positionVisibleCascadeSubmenus",
    value: function positionVisibleCascadeSubmenus($menu, viewportPadding) {
      var _this4 = this;
      $menu.find('.masterstudy-mega-menu__item--has-submenu').each(function (index, item) {
        _this4.positionCascadeSubmenu(jQuery(item), viewportPadding);
      });
    }
  }, {
    key: "measureSubmenu",
    value: function measureSubmenu($submenu) {
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
  }, {
    key: "positionCascadeSubmenu",
    value: function positionCascadeSubmenu($item, viewportPadding) {
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
      var submenuRect = this.measureSubmenu($submenu);
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
  }]);
  return MegaMenu;
}(elementorModules.frontend.handlers.Base);
jQuery(window).on('elementor/frontend/init', function () {
  var addHandler = function addHandler($element) {
    elementorFrontend.elementsHandler.addHandler(MegaMenu, {
      $element: $element
    });
  };
  elementorFrontend.hooks.addAction('frontend/element_ready/ms_lms_mega_menu.default', addHandler);
});