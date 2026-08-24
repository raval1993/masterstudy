"use strict";

(function ($) {
  $(document).ready(function () {
    $("[data-id='masterstudy-single-course-stickybar-button']").click(function (event) {
      event.preventDefault();
      var buyButton = $('.masterstudy-buy-button');
      var affiliateButton = $('.masterstudy-button-affiliate');
      var target = buyButton.length ? buyButton : affiliateButton;
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top - 100
        }, 700);
      }
    });
    if (document.querySelector('.masterstudy-single-course-stickybar') !== null) {
      var options = {
        root: null,
        rootMargin: '0px',
        threshold: 1.0
      };
      var observer = new IntersectionObserver(handleIntersection, options);
      var target = document.querySelector('.masterstudy-buy-button');
      observer.observe(target);
    }
    function handleIntersection(entries, observer) {
      entries.forEach(function (entry) {
        var stickyBar = document.querySelector('.masterstudy-single-course-stickybar');
        if (!entry.isIntersecting) {
          stickyBar.classList.add('masterstudy-single-course-stickybar_show');
        } else {
          stickyBar.classList.remove('masterstudy-single-course-stickybar_show');
        }
      });
    }
  });
})(jQuery);