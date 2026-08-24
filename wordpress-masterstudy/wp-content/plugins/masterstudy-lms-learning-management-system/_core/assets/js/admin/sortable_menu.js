"use strict";

(function ($) {
  $(document).ready(function () {
    var profile_menu_submenu = '[data-submenu="section_4_profile-menu-reordering"]';
    var role_query_param = 'profile_menu_role';
    var sorting_field_selectors = {
      instructor: '[data-field="wpcfto_addon_option_sorting_the_menu"]',
      student: '[data-field="wpcfto_addon_option_sorting_the_menu_student"]',
      main: '[data-field="wpcfto_addon_option_sorting_float_menu_main"]',
      learning: '[data-field="wpcfto_addon_option_sorting_float_menu_learning"]'
    };
    function is_profile_menu_context() {
      var current_url = new URL(window.location);
      var current_tab = window.location.hash.replace('#', '');
      return $(profile_menu_submenu).hasClass('active') || 'section_4' === current_tab && 'profile-menu-reordering' === current_url.searchParams.get('submenu');
    }
    function get_role_buttons() {
      return $('.select_user_role .button_list_box a');
    }
    function get_role_by_button($button) {
      return $button.parent().is(':first-child') ? 'instructor' : 'student';
    }
    function get_button_by_role(role) {
      return 'student' === role ? get_role_buttons().eq(1) : get_role_buttons().eq(0);
    }
    function get_role_from_url() {
      var current_url = new URL(window.location);
      var role = current_url.searchParams.get(role_query_param);
      return 'student' === role ? role : 'instructor';
    }
    function set_role_in_url(role) {
      var current_url = new URL(window.location);
      current_url.searchParams.set(role_query_param, role);
      window.history.replaceState(null, '', current_url.toString());
    }
    function update_sorting_fields(role) {
      var float_menu_enabled = $('#section_4-float_menu').is(':checked');
      var sorting_fields = {
        instructor: $(sorting_field_selectors.instructor),
        student: $(sorting_field_selectors.student),
        main: $(sorting_field_selectors.main),
        learning: $(sorting_field_selectors.learning)
      };
      $.each(sorting_fields, function (key, $field) {
        $field.addClass('hidden');
      });
      if (!float_menu_enabled) {
        if ('student' === role) {
          sorting_fields.student.removeClass('hidden');
        } else {
          sorting_fields.instructor.removeClass('hidden');
        }
        return;
      }
      if ('student' === role) {
        sorting_fields.student.toggleClass('hidden', float_menu_enabled);
        sorting_fields.learning.toggleClass('hidden', !float_menu_enabled);
      } else {
        sorting_fields.main.removeClass('hidden');
        sorting_fields.learning.removeClass('hidden');
      }
    }
    function sync_profile_menu_state(role) {
      var $selected_button = get_button_by_role(role);
      var $role_buttons = get_role_buttons();
      if (!is_profile_menu_context() || !$role_buttons.length) {
        return;
      }
      if (!$selected_button.length) {
        $selected_button = $role_buttons.filter('.active').first();
      }
      if (!$selected_button.length) {
        $selected_button = get_button_by_role(get_role_from_url());
      }
      if (!$selected_button.length) {
        $selected_button = $role_buttons.first();
      }
      role = get_role_by_button($selected_button);
      $selected_button.addClass('active');
      $selected_button.parent().siblings().find('a').removeClass('active');
      set_role_in_url(role);
      update_sorting_fields(role);
      $("[data-id='dashboard']").removeClass('list-group-item').addClass('list-group-item-disabled');
      add_notice();
    }
    function schedule_profile_menu_sync(attempt) {
      var sync_attempt = attempt || 0;
      if (is_profile_menu_context() && get_role_buttons().length) {
        sync_profile_menu_state();
        return;
      }
      if (sync_attempt >= 20) {
        return;
      }
      setTimeout(function () {
        schedule_profile_menu_sync(sync_attempt + 1);
      }, 150);
    }
    $(document).on('change', '.list-group', function () {
      setTimeout(add_notice, 500);
    });
    function add_notice() {
      var menu_elements = {
        '[data-id="dashboard"]': 'stmlms-lock-2',
        '[data-id="assignments"]': 'stmlms-exclamation-triangle',
        '[data-id="enrolled_courses"]': 'stmlms-exclamation-triangle',
        '[data-id="bundles"]': 'stmlms-exclamation-triangle',
        '[data-id="my_orders"]': 'stmlms-exclamation-triangle'
      };
      $.each(menu_elements, function (element, value) {
        $(element).each(function () {
          if ($(this).find('i').length <= 1) {
            $(this).append("<i class=\"".concat(value, "\"></i>"));
          }
        });
      });
    }
    $(document).on('click', '.button_list_box a', function (event) {
      event.preventDefault();
      sync_profile_menu_state(get_role_by_button($(this)));
    });
    $(document).on('click', profile_menu_submenu, function () {
      setTimeout(function () {
        sync_profile_menu_state();
      }, 0);
    });
    $(document).on('change', '#section_4-float_menu', function () {
      setTimeout(function () {
        sync_profile_menu_state();
      }, 0);
    });
    schedule_profile_menu_sync();
  });
})(jQuery);