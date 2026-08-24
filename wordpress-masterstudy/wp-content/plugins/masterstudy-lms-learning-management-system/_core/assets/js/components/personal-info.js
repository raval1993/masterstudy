"use strict";

(function ($) {
  $(document).ready(function () {
    var is_admin = !!(window.masterstudy_personal_info_data && masterstudy_personal_info_data.is_admin);
    var $country = is_admin ? $("select[name=masterstudy_personal_country]") : $("select[name=country]");
    var $stateSelect = $(".masterstudy-personal-info__state-select");
    var $stateInput = $(".masterstudy-personal-info__state-input");
    var select2Width = is_admin ? "25em" : "100%";
    if ($.fn.select2) {
      $country.select2({
        width: select2Width,
        placeholder: $country.data('placeholder')
      });
    }
    function initStateSelect2() {
      if (!$.fn.select2) return;
      if ($stateSelect.hasClass("select2-hidden-accessible")) return;
      $stateSelect.select2({
        width: select2Width,
        placeholder: $stateSelect.data('placeholder')
      });
    }
    function enableSelect() {
      initStateSelect2();
      $stateSelect.prop('disabled', false).show();
      $stateInput.prop('disabled', true).hide().val('');
    }
    function enableInput() {
      if ($stateSelect.hasClass("select2-hidden-accessible")) {
        $stateSelect.select2('destroy');
      }
      $stateSelect.prop('disabled', true).hide().val('');
      $stateInput.prop('disabled', false).show();
    }
    function toggleStateField() {
      if (($country.val() || '').toUpperCase() === 'US') {
        enableSelect();
      } else {
        enableInput();
      }
    }
    toggleStateField();
    $country.on('change', toggleStateField);
    $(document).on('select2:open', function (e) {
      var target = e.target;
      if (target === $stateSelect[0] || target === $country[0]) {
        $('.select2-container--open .select2-search__field').attr('placeholder', window.personal_info_data && personal_info_data.search_placeholder || 'Search...');
      }
    });
    $('.masterstudy-personal-info__input').on('input', function () {
      $(this).removeClass('masterstudy-personal-info-error');
    });
    $stateSelect.on('change', function () {
      $(this).removeClass('masterstudy-personal-info-error');
    });
  });
})(jQuery);