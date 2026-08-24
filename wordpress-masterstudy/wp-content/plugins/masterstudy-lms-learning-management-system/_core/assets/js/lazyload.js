"use strict";

document.addEventListener('DOMContentLoaded', function () {
  var lazyImageClass = '.masterstudy-lazyload-image';
  function observeLazyImages() {
    var lazyImageContainers = document.querySelectorAll(lazyImageClass);
    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries, observer) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            var lazyImageContainer = entry.target;
            lazyImageContainer.classList.add('masterstudy-lazyload-image__loaded');
            observer.unobserve(lazyImageContainer);
          }
        });
      });
      lazyImageContainers.forEach(function (container) {
        return observer.observe(container);
      });
    } else {
      lazyImageContainers.forEach(function (container) {
        container.classList.add('masterstudy-lazyload-image__loaded');
      });
    }
  }
  observeLazyImages();
  var mutationObserver = new MutationObserver(function (mutationsList) {
    mutationsList.forEach(function (mutation) {
      if (mutation.type === 'childList') {
        mutation.addedNodes.forEach(function (node) {
          var _node$matches, _node$querySelectorAl;
          if (node.nodeType === 1 && (_node$matches = node.matches) !== null && _node$matches !== void 0 && _node$matches.call(node, lazyImageClass)) {
            node.classList.add('masterstudy-lazyload-image__loaded');
          } else if (((_node$querySelectorAl = node.querySelectorAll) === null || _node$querySelectorAl === void 0 ? void 0 : _node$querySelectorAl.call(node, lazyImageClass).length) > 0) {
            observeLazyImages();
          }
        });
      }
    });
  });
  mutationObserver.observe(document.body, {
    childList: true,
    subtree: true
  });
});