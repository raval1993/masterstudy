"use strict";

function hideLoaders(selector) {
  var elements = document.querySelectorAll(selector);
  elements.forEach(function (element) {
    var loaders = element.querySelectorAll('.masterstudy-analytics-loader');
    loaders.forEach(function (loader) {
      loader.style.display = 'none';
    });
  });
}
function showLoaders(selector) {
  var elements = document.querySelectorAll(selector);
  elements.forEach(function (element) {
    var loaders = element.querySelectorAll('.masterstudy-analytics-loader');
    loaders.forEach(function (loader) {
      loader.style.display = 'flex';
    });
  });
}