"use strict";

(function () {
  window._masterstudy_utils = window._masterstudy_utils || {};
  window._masterstudy_utils.slots = window._masterstudy_utils.slots || {};
  var slotsUtils = window._masterstudy_utils.slots;
  slotsUtils.render = function (slots) {
    slots.filter(Boolean).forEach(function (slot) {
      var template = document.getElementById(slot);
      if (!template) {
        console.warn("Template for slot ".concat(slot, " does not exists"));
        return;
      }
      var slotEl = document.querySelectorAll("[data-masterstudy-slot-id=\"".concat(slot, "\"]"));
      if (!slotEl.length) {
        console.warn("Slot element for slot ".concat(slot, " does not exists"));
        return;
      }
      slotEl.forEach(function (el) {
        el.appendChild(template.content.cloneNode(true));
      });
    });
  };
})();