"use strict";

(function ($) {
  $(document).ready(function () {
    var _window$_masterstudy_;
    if ((_window$_masterstudy_ = window._masterstudy_utils) !== null && _window$_masterstudy_ !== void 0 && (_window$_masterstudy_ = _window$_masterstudy_.slots) !== null && _window$_masterstudy_ !== void 0 && _window$_masterstudy_.render) {
      var drawerScrollTop = 0;
      var drawerScrollLocked = false;
      var getDrawerAdminBarOffset = function getDrawerAdminBarOffset() {
        var isDesktopView = window.innerWidth >= 768;
        return $('#wpadminbar').length && isDesktopView ? 32 : 0;
      };
      var lockDrawerBodyScroll = function lockDrawerBodyScroll() {
        if (drawerScrollLocked) {
          return;
        }
        drawerScrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
        $('body').css({
          position: 'fixed',
          top: getDrawerAdminBarOffset() - drawerScrollTop + "px",
          left: '0',
          right: '0',
          width: '100%'
        });
        drawerScrollLocked = true;
      };
      var unlockDrawerBodyScroll = function unlockDrawerBodyScroll() {
        if (!drawerScrollLocked) {
          return;
        }
        $('body').css({
          position: '',
          top: '',
          left: '',
          right: '',
          width: ''
        });
        window.scrollTo(0, drawerScrollTop);
        drawerScrollLocked = false;
      };
      var updateDrawerBodyState = function updateDrawerBodyState() {
        var hasOpenDrawer = $('[data-masterstudy-drawer-slot-id].masterstudy-drawer-component_open').length > 0;
        if (hasOpenDrawer) {
          lockDrawerBodyScroll();
        } else {
          unlockDrawerBodyScroll();
        }
      };
      $('[data-masterstudy-drawer-slot-id]').each(function () {
        var slotId = $(this).attr('data-masterstudy-drawer-slot-id');
        var drawerEl = $(this);
        $(this).css('display', '');
        window._masterstudy_utils.slots.render([slotId]);
        $(this).on('click', function (e) {
          if (e.target === e.currentTarget) {
            $(this).toggleClass('masterstudy-drawer-component_open', false);
            updateDrawerBodyState();
          }
        });
        if (window.MutationObserver) {
          var observer = new MutationObserver(updateDrawerBodyState);
          observer.observe(drawerEl[0], {
            attributes: true,
            attributeFilter: ['class']
          });
        }
      });
      updateDrawerBodyState();
    }
  });
})(jQuery);