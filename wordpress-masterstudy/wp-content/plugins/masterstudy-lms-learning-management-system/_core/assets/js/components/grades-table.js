"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
Vue.component('grades_table', {
  props: ['fields'],
  components: {
    'slider-picker': VueColor.Chrome
  },
  data: function data() {
    return {
      newRow: this.initializeNewRow(),
      colorValue: {
        r: 255,
        g: 255,
        b: 255,
        a: 1
      },
      colorInputValue: 'rgba(255, 255, 255, 1)',
      showConfirmDelete: false,
      validationErrors: {}
    };
  },
  created: function created() {
    if (typeof this.newRow.color === 'string') {
      this.colorInputValue = this.newRow.color;
      var colors = this.newRow.color.replace('rgba(', '').slice(0, -1).split(',');
      this.colorValue.r = parseInt(colors[0]);
      this.colorValue.g = parseInt(colors[1]);
      this.colorValue.b = parseInt(colors[2]);
      this.colorValue.a = parseFloat(colors[3]);
    }
  },
  template: "\n\t\t<div class=\"wpcfto_generic_field\" v-if=\"fields.options && Object.keys(fields.options).length\">\n\t\t\t<div class=\"grades_table\">\n\t\t\t\t<div class=\"grades_table__title\">{{ fields.label }}</div>\n\t\t\t\t<div class=\"grades_table__wrapper\">\n\t\t\t\t\t<div class=\"grades_table__header\">\n\t\t\t\t\t\t<div v-for=\"(column, key) in fields.options\"\n\t\t\t\t\t\t\tv-if=\"key !== 'color'\"\n\t\t\t\t\t\t\t:key=\"key\"\n\t\t\t\t\t\t\t:style=\"{ width: column.width }\"\n\t\t\t\t\t\t\t:class=\"'grades_table__column grades_table__column_' + column.type\"\n\t\t\t\t\t\t>\n\t\t\t\t\t\t\t{{ column.title }}\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"grades_table__body\">\n\t\t\t\t\t\t<div v-for=\"(row, rowIndex) in fields.value\" :key=\"rowIndex\" class=\"grades_table__row\">\n\t\t\t\t\t\t\t<div v-for=\"(column, key) in fields.options\"\n\t\t\t\t\t\t\t\tv-if=\"key !== 'color'\"\n\t\t\t\t\t\t\t\t:key=\"key\"\n\t\t\t\t\t\t\t\t:style=\"{ width: column.width }\"\n\t\t\t\t\t\t\t\t:class=\"'grades_table__item grades_table__item_' + column.type\"\n\t\t\t\t\t\t\t>\n\t\t\t\t\t\t\t\t<span v-if=\"key === 'grade'\" class=\"grades_table__item-grade\" :style=\"{ background: row.color }\">\n\t\t\t\t\t\t\t\t\t{{ row[key] }}\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t<span v-else-if=\"Array.isArray(row[key])\" class=\"grades_table__item-value\">\n\t\t\t\t\t\t\t\t\t{{ row[key].map(val => val + '%').join(' - ') }}\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t\t<span v-else class=\"grades_table__item-value\">\n\t\t\t\t\t\t\t\t\t{{ row[key] || '' }}\n\t\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<span v-if=\"rowIndex === fields.value.length - 1\" @click=\"confirmDelete\" class=\"grades_table__row-delete\">\n\t\t\t\t\t\t\t\t<i class=\"stmlms-trash-2\"></i>\n\t\t\t\t\t\t\t</span>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t\t<div class=\"grades_table__add-row\">\n\t\t\t\t\t<div v-for=\"(column, key) in fields.options\" :key=\"key\" class=\"grades_table__input-wrapper\">\n\t\t\t\t\t\t<span class=\"grades_table__input-title\">\n\t\t\t\t\t\t\t{{ column.title }}{{ column.type === 'range' ? ' min, %' : '' }}\n\t\t\t\t\t\t</span>\n\t\t\t\t\t\t<input v-if=\"column.type === 'text' || column.type === 'badge'\"\n\t\t\t\t\t\t\ttype=\"text\"\n\t\t\t\t\t\t\tv-model=\"newRow[key]\"\n\t\t\t\t\t\t\tplaceholder=\"A+\"\n\t\t\t\t\t\t\tclass=\"grades_table__input\"\n\t\t\t\t\t\t\t:class=\"{'grades_table__input_error': validationErrors[key]}\"\n\t\t\t\t\t\t\t@input=\"clearValidationError(key)\"\n\t\t\t\t\t\t/>\n\t\t\t\t\t\t<input v-else-if=\"column.type === 'number'\"\n\t\t\t\t\t\t\ttype=\"number\"\n\t\t\t\t\t\t\tmin=\"0\"\n\t\t\t\t\t\t\tv-model=\"newRow[key]\"\n\t\t\t\t\t\t\tplaceholder=\"1\"\n\t\t\t\t\t\t\tclass=\"grades_table__input\"\n\t\t\t\t\t\t\t:class=\"{'grades_table__input_error': validationErrors[key]}\"\n\t\t\t\t\t\t\t@input=\"handleNumberInput(key)\"\n                            @blur=\"onNumberInputBlur(key)\"\n\t\t\t\t\t\t/>\n\t\t\t\t\t\t<input v-else-if=\"column.type === 'range'\"\n\t\t\t\t\t\t\ttype=\"number\"\n\t\t\t\t\t\t\tmin=\"0\"\n\t\t\t\t\t\t\tv-model=\"newRow[key]\"\n\t\t\t\t\t\t\tplaceholder=\"20%\"\n\t\t\t\t\t\t\tclass=\"grades_table__input\"\n\t\t\t\t\t\t\t:class=\"{'grades_table__input_error': validationErrors[key]}\"\n\t\t\t\t\t\t\t@input=\"handleRangeInput(key)\"\n\t\t\t\t\t\t/>\n\t\t\t\t\t\t<div v-else-if=\"column.type === 'color'\" class=\"stm_colorpicker_wrapper\">\n\t\t\t\t\t\t\t<span :style=\"{'background-color': colorInputValue}\" @click=\"focusNextInput\"></span>\n\t\t\t\t\t\t\t<input\n\t\t\t\t\t\t\t\ttype=\"text\"\n\t\t\t\t\t\t\t\tv-model=\"colorInputValue\"\n\t\t\t\t\t\t\t\tplaceholder=\"rgba(255,255,255,1)\"\n\t\t\t\t\t\t\t\tclass=\"grades_table__input\"\n\t\t\t\t\t\t\t\t:class=\"{'grades_table__input_error': validationErrors[key] && colorInputValue === ''}\"\n\t\t\t\t\t\t\t\t@input=\"handleColorInput(key)\"\n\t\t\t\t\t\t\t/>\n\t\t\t\t\t\t\t<div>\n\t\t\t\t\t\t\t\t<slider-picker v-model=\"colorValue\"></slider-picker>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t\t<div v-if=\"validationErrors[key] && (key !== 'color' || colorInputValue === '')\" class=\"grades_table__error-message\">\n\t\t\t\t\t\t\t{{ validationErrors[key] }}\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t<span class=\"grades_table__add-button\" @click=\"addRow\">Add</span>\n\t\t\t\t</div>\n\t\t\t\t<div :class=\"{'grades_table__popup': true, 'grades_table__popup_show': showConfirmDelete}\">\n\t\t\t\t\t<div class=\"grades_table__popup-content\">\n\t\t\t\t\t\t<div class=\"grades_table__popup-text\">{{ gradesTable.popup_text }}</div>\n\t\t\t\t\t\t<div class=\"grades_table__popup-actions\">\n\t\t\t\t\t\t\t<span @click=\"closeDeleteConfirm\" class=\"grades_table__popup-cancel\">{{ gradesTable.popup_cancel_button }}</span>\n\t\t\t\t\t\t\t<span @click=\"deleteLastRow\" class=\"grades_table__popup-confirm\">{{ gradesTable.popup_confirm_button }}</span>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t</div>\n\t",
  methods: {
    updateColor: function updateColor(newColor) {
      this.newRow.color = newColor;
    },
    initializeNewRow: function initializeNewRow() {
      var row = {};
      for (var key in this.fields.options) {
        row[key] = '';
      }
      return row;
    },
    focusNextInput: function focusNextInput() {
      this.$nextTick(function () {
        var spanElement = event.target;
        var inputElement = spanElement.nextElementSibling;
        if (inputElement && inputElement.tagName === 'INPUT') {
          inputElement.focus();
        }
      });
    },
    addRow: function addRow() {
      var _this = this;
      var isValid = true;
      var maxRange = 100;
      this.validationErrors = {};
      for (var key in this.fields.options) {
        if (this.newRow[key] === null || this.newRow[key] === '') {
          isValid = false;
          this.$set(this.validationErrors, key, gradesTable.fields_error);
        }
      }
      var colorField = Object.keys(this.fields.options).find(function (key) {
        return _this.fields.options[key].type === 'color';
      });
      if (colorField && this.colorInputValue === '') {
        isValid = false;
        this.$set(this.validationErrors, 'color', gradesTable.fields_error);
      }
      if (this.fields.value.length > 0) {
        var previousRow = this.fields.value[this.fields.value.length - 1];
        maxRange = previousRow.range[0] - 1;
        if (this.newRow.range === undefined || this.newRow.range >= previousRow.range[0]) {
          isValid = false;
          this.$set(this.validationErrors, 'range', this.fields_range_error);
        }
      }
      if (this.newRow.range < 0) {
        isValid = false;
        this.$set(this.validationErrors, 'range', this.fields_error);
      }
      if (isValid) {
        var newRow = _objectSpread(_objectSpread({}, this.newRow), {}, {
          range: [this.newRow.range, maxRange]
        });
        this.fields.value.push(newRow);
        this.newRow = this.initializeNewRow();
        this.resetColorFields();
      }
    },
    clearValidationError: function clearValidationError(key) {
      this.$delete(this.validationErrors, key);
    },
    handleNumberInput: function handleNumberInput(key) {
      var value = this.newRow[key];
      var cleaned = value.replace(/[^\d.]/g, '').replace(/^(\d*\.?)|.*/, function (m, g) {
        return g;
      });
      var parts = cleaned.split('.');
      if (parts.length === 2) parts[1] = parts[1].slice(0, 1);
      var joined = parts.join('.');
      this.newRow[key] = joined;
      if (/^0\d/.test(joined)) {
        this.newRow[key] = String(parseInt(joined, 10));
      }
      this.clearValidationError(key);
    },
    onNumberInputBlur: function onNumberInputBlur(key) {
      var v = this.newRow[key];
      if (v === '') return;
      if (v.endsWith('.') || v.endsWith(',')) v = v.slice(0, -1);
      var num = Math.floor(parseFloat(v.replace(',', '.')) * 10) / 10;
      if (isNaN(num) || num < 0) {
        this.newRow[key] = '';
      } else {
        this.newRow[key] = num.toString();
      }
      this.clearValidationError(key);
    },
    handleColorInput: function handleColorInput(key) {
      if (this.colorInputValue === '') {
        this.resetColorFields();
        this.$set(this.validationErrors, key, this.fields_error);
      } else {
        this.clearValidationError(key);
      }
    },
    handleRangeInput: function handleRangeInput(key) {
      var value = Math.floor(this.newRow[key]);
      if (value < 0) {
        this.newRow[key] = 0;
      } else {
        this.newRow[key] = value;
      }
      this.clearValidationError(key);
    },
    resetColorFields: function resetColorFields() {
      this.colorInputValue = '';
      this.colorValue = {
        r: '',
        g: '',
        b: '',
        a: ''
      };
    },
    confirmDelete: function confirmDelete() {
      this.showConfirmDelete = true;
    },
    closeDeleteConfirm: function closeDeleteConfirm() {
      this.showConfirmDelete = false;
    },
    deleteLastRow: function deleteLastRow() {
      if (this.fields.value.length > 0) {
        this.fields.value.pop();
      }
      this.showConfirmDelete = false;
    }
  },
  watch: {
    colorValue: function colorValue(value) {
      if (typeof value.rgba !== 'undefined') {
        var rgba_color = "rgba(".concat(value.rgba.r, ",").concat(value.rgba.g, ",").concat(value.rgba.b, ",").concat(value.rgba.a, ")");
        this.colorInputValue = rgba_color;
        this.newRow.color = rgba_color;
      }
    }
  }
});