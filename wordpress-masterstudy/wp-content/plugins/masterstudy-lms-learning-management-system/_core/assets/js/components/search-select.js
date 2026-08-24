"use strict";

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function _iterableToArrayLimit(arr, i) { var _i = null == arr ? null : "undefined" != typeof Symbol && arr[Symbol.iterator] || arr["@@iterator"]; if (null != _i) { var _s, _e, _x, _r, _arr = [], _n = !0, _d = !1; try { if (_x = (_i = _i.call(arr)).next, 0 === i) { if (Object(_i) !== _i) return; _n = !1; } else for (; !(_n = (_s = _x.call(_i)).done) && (_arr.push(_s.value), _arr.length !== i); _n = !0); } catch (err) { _d = !0, _e = err; } finally { try { if (!_n && null != _i["return"] && (_r = _i["return"](), Object(_r) !== _r)) return; } finally { if (_d) throw _e; } } return _arr; } }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
Vue.component('search-select', {
  props: ['saved_search_select', 'options', 'field_label'],
  data: function data() {
    return {
      search: '',
      selected: this.saved_search_select ? this.saved_search_select : '',
      dropdownOpen: false
    };
  },
  computed: {
    filteredOptions: function filteredOptions() {
      if (!this.search) return this.options;
      var result = {};
      for (var _i = 0, _Object$entries = Object.entries(this.options); _i < _Object$entries.length; _i++) {
        var _Object$entries$_i = _slicedToArray(_Object$entries[_i], 2),
          value = _Object$entries$_i[0],
          label = _Object$entries$_i[1];
        if (label.toLowerCase().includes(this.search.toLowerCase())) {
          result[value] = label;
        }
      }
      return result;
    },
    selectedLabel: function selectedLabel() {
      return this.options[this.selected] || "Select ".concat(this.field_label);
    }
  },
  watch: {
    saved_search_select: function saved_search_select(newVal) {
      this.selected = newVal;
    }
  },
  mounted: function mounted() {
    document.addEventListener('click', this.outsideHandler);
  },
  beforeDestroy: function beforeDestroy() {
    document.removeEventListener('click', this.outsideHandler);
  },
  methods: {
    toggleDropdown: function toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
    },
    selectOption: function selectOption(value) {
      this.selected = value;
      this.$emit('update-search-select', this.selected);
      this.dropdownOpen = false;
    },
    outsideHandler: function outsideHandler(event) {
      if (!this.$el.contains(event.target)) {
        this.dropdownOpen = false;
      }
    }
  },
  template: "\n\t\t\t<div class=\"masterstudy-search-select\">\n\t\t\t\t<div class=\"masterstudy-search-select__selected\" @click=\"toggleDropdown\">\n\t\t\t\t\t{{ selectedLabel }}\n\t\t\t\t\t<span class=\"masterstudy-search-select__arrow\">\n\t\t\t\t\t\t<i class=\"fa fa-chevron-right\"></i>\n\t\t\t\t\t</span>\n\t\t\t\t</div>\n\t\t\t\t<div v-if=\"dropdownOpen\" class=\"masterstudy-search-select__dropdown\">\n\t\t\t\t\t<div class=\"masterstudy-search-select__search\">\n\t\t\t\t\t\t<input type=\"text\" v-model=\"search\" :placeholder=\"searchSelect.placeholder\"/>\n\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"masterstudy-search-select__options\">\n\t\t\t\t\t\t<div\n\t\t\t\t\t\t\t\tv-for=\"(label, value) in filteredOptions\"\n\t\t\t\t\t\t\t\t:key=\"value\"\n\t\t\t\t\t\t\t\tclass=\"masterstudy-search-select__option\"\n\t\t\t\t\t\t\t\t:class=\"{ selected: value === selected }\"\n\t\t\t\t\t\t\t\t@click=\"selectOption(value)\"\n\t\t\t\t\t\t>\n\t\t\t\t\t\t\t{{ label }}\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t"
});