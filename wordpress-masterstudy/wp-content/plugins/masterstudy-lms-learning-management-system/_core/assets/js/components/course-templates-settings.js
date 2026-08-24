"use strict";

Vue.component('course_templates', {
  props: ['fields', 'field_label', 'field_name', 'field_id', 'field_value'],
  data: function data() {
    var _this = this;
    return {
      value: this.field_value,
      layout: this.fields.options.find(function (option) {
        return option.name === _this.field_value;
      }) || this.fields.options.find(function (option) {
        return option.name === 'default';
      }),
      imgError: false
    };
  },
  mounted: function mounted() {
    var _this2 = this;
    window.addEventListener('masterstudy-course-template-changed', function (e) {
      _this2.value = e.detail;
    });
  },
  watch: {
    value: function value(val) {
      this.$emit('wpcfto-get-value', val);
      this.layout = this.fields.options.find(function (option) {
        return option.name === val;
      }) || this.fields.options.find(function (option) {
        return option.name === 'default';
      });
      this.imgError = false;
    },
    field_value: function field_value(val) {
      this.value = val;
      this.layout = this.fields.options.find(function (option) {
        return option.name === val;
      }) || this.fields.options.find(function (option) {
        return option.name === 'default';
      });
      this.imgError = false;
    }
  },
  template: "\n\t\t<div class=\"wpcfto_generic_field\" :class=\"field_id\">\n\t\t\t<div class=\"masterstudy-course-templates-settings\">\n\t\t\t\t<div class=\"masterstudy-course-templates-settings__header\">\n\t\t\t\t\t<div v-if=\"field_label\" class=\"masterstudy-course-templates-settings__label\">\n\t\t\t\t\t\t<label v-html=\"field_label\" class=\"wpcfto-field-aside__label\"></label>\n\t\t\t\t\t</div>\n\t\t\t\t\t<div v-if=\"fields && fields.description\" class=\"masterstudy-course-templates-settings__description\">\n\t\t\t\t\t\t<div v-html=\"fields.description\" class=\"wpcfto-field-description wpcfto-field-description__before description\"></div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t\t<div class=\"masterstudy-course-templates-settings__content\">\n\t\t\t\t\t<div class=\"masterstudy-course-templates-settings__preview\">\n\t\t\t\t\t\t<img\n\t\t\t\t\t\t\tv-if=\"!imgError && layout\"\n\t\t\t\t\t\t\t:src=\"courseTemplates.img_url + layout.name + '.png'\"\n\t\t\t\t\t\t\tclass=\"masterstudy-course-templates-settings__image\"\n\t\t\t\t\t\t\t@error=\"imgError = true\"\n\t\t\t\t\t\t\t@load=\"imgError = false\"\n\t\t\t\t\t\t/>\n\t\t\t\t\t\t<img\n\t\t\t\t\t\t\tv-else\n\t\t\t\t\t\t\t:src=\"courseTemplates.img_url + 'empty-layout.png'\"\n\t\t\t\t\t\t\tclass=\"masterstudy-course-templates-settings__image\"\n\t\t\t\t\t\t/>\n\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"masterstudy-course-templates-settings__main\">\n\t\t\t\t\t\t<div v-if=\"layout.title\" class=\"masterstudy-course-templates-settings__title\" v-html=\"layout.title\"></div>\n\t\t\t\t\t\t<div class=\"masterstudy-course-templates-settings__actions\">\n\t\t\t\t\t\t\t<a\n\t\t\t\t\t\t\t\tv-if=\"!layout.elementor\"\n\t\t\t\t\t\t\t\t:href=\"courseTemplates.preview_url + layout.name\"\n\t\t\t\t\t\t\t\tclass=\"masterstudy-course-templates-settings__link\"\n\t\t\t\t\t\t\t\tv-html=\"courseTemplates.preview\"\n\t\t\t\t\t\t\t\ttarget=\"_blank\"\n\t\t\t\t\t\t\t></a>\n\t\t\t\t\t\t\t<span\n\t\t\t\t\t\t\t\tid=\"masterstudy-settings-course-change\"\n\t\t\t\t\t\t\t\tclass=\"masterstudy-course-templates-settings__change\"\n\t\t\t\t\t\t\t\tv-html=\"courseTemplates.change\"\n\t\t\t\t\t\t\t\tdata-id=\"edit_settings\"\n\t\t\t\t\t\t\t\t:data-current-style=\"layout.name\"\n\t\t\t\t\t\t\t></span>\n\t\t\t\t\t\t\t<a\n\t\t\t\t\t\t\t\tv-show=\"layout.elementor\"\n\t\t\t\t\t\t\t\t:href=\"courseTemplates.edit_url + layout.id + '&action=elementor'\"\n\t\t\t\t\t\t\t\tid=\"masterstudy-settings-course-edit\"\n\t\t\t\t\t\t\t\tclass=\"masterstudy-course-templates-settings__edit\"\n\t\t\t\t\t\t\t\tv-html=\"courseTemplates.edit\"\n\t\t\t\t\t\t\t\ttarget=\"_blank\"\n\t\t\t\t\t\t\t></a>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t\t<input type=\"text\" :name=\"field_name\" v-model=\"value\" class=\"masterstudy-course-templates-settings__input\" />\n\t\t\t</div>\n\t\t</div>\n\t"
});