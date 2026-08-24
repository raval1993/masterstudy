"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
Vue.component('stm-taxes', {
  props: ['field'],
  data: function data() {
    var _window$masterstudyTa, _window$masterstudyTa2;
    return {
      taxes: [],
      newTax: {
        country: '',
        selected: 'default'
      },
      dropdownOpen: false,
      modalOpen: false,
      countries: ((_window$masterstudyTa = window.masterstudyTaxes) === null || _window$masterstudyTa === void 0 ? void 0 : _window$masterstudyTa.countries) || [],
      regions: ((_window$masterstudyTa2 = window.masterstudyTaxes) === null || _window$masterstudyTa2 === void 0 ? void 0 : _window$masterstudyTa2.regions) || {},
      translations: window.masterstudyTaxes || {},
      searchQuery: '',
      stateSearch: '',
      editing: false,
      editContext: {
        country: null,
        region: null
      },
      highlightRegion: null,
      applyAllPreferred: false,
      applyAllRatePreferred: '',
      form: {
        countryRate: '',
        usRates: {},
        applyAllEnabled: false,
        applyAllRate: ''
      }
    };
  },
  created: function created() {
    if (this.field && Array.isArray(this.field.value)) {
      this.taxes = this.field.value.map(function (r) {
        return {
          country: r.country,
          region: typeof r.region !== 'undefined' ? r.region : null,
          rate: Number(r.rate)
        };
      });
    }
  },
  watch: {
    'form.applyAllEnabled': function formApplyAllEnabled(v) {
      this.applyAllPreferred = !!v;
      if (v && (this.form.applyAllRate === '' || this.form.applyAllRate === null || typeof this.form.applyAllRate === 'undefined')) {
        this.form.applyAllRate = this.applyAllRatePreferred !== '' ? this.applyAllRatePreferred : this.getEqualUsRate();
      }
      if (v) {
        this.editContext.region = null;
        this.highlightRegion = null;
      }
    }
  },
  computed: {
    filteredCountries: function filteredCountries() {
      if (!this.searchQuery) return this.countries;
      var q = this.searchQuery.toLowerCase();
      return this.countries.filter(function (c) {
        return c.name.toLowerCase().includes(q) || c.code.toLowerCase().includes(q);
      });
    },
    activeCountry: function activeCountry() {
      return this.editing ? this.editContext.country : this.newTax.country;
    },
    isUSSelected: function isUSSelected() {
      return this.newTax.country === (this.translations.unitedStatesCode || 'US');
    },
    isUSActive: function isUSActive() {
      return this.activeCountry === (this.translations.unitedStatesCode || 'US');
    },
    usStates: function usStates() {
      var _this$regions;
      return ((_this$regions = this.regions) === null || _this$regions === void 0 ? void 0 : _this$regions.US) || [];
    },
    filteredUsStates: function filteredUsStates() {
      var list = this.usStates;
      var q = (this.stateSearch || '').trim().toLowerCase();
      if (!q) return list;
      return list.filter(function (st) {
        return (st.name || '').toLowerCase().includes(q) || (st.code || '').toLowerCase().includes(q);
      });
    },
    addDisabled: function addDisabled() {
      var _this = this;
      if (!this.newTax.country) return true;
      if (this.isUSSelected) return false;
      return this.taxes.some(function (t) {
        return t.country === _this.newTax.country && !t.region;
      });
    },
    displayRows: function displayRows() {
      var _this2 = this;
      var US = this.translations.unitedStatesCode || 'US';
      var countries = new Set(this.taxes.map(function (t) {
        return t.country;
      }));
      var rows = [];
      countries.forEach(function (code) {
        if (code === US) {
          var list = _this2.taxes.filter(function (t) {
            return t.country === US && t.region;
          });
          if (list.length === 0) return;
          var rates = list.map(function (x) {
            return Number(x.rate);
          }).filter(function (n) {
            return isFinite(n);
          }).map(function (n) {
            return Math.round(n * 100) / 100;
          });
          var uniq = Array.from(new Set(rates.map(function (r) {
            return r.toFixed(2);
          }))).map(Number);
          var min = Math.min.apply(Math, _toConsumableArray(uniq));
          var max = Math.max.apply(Math, _toConsumableArray(uniq));
          rows.push({
            country: US,
            isUS: true,
            min: min,
            max: max,
            uniqueCount: uniq.length
          });
        } else {
          var rec = _this2.taxes.find(function (t) {
            return t.country === code && !t.region;
          });
          if (rec) {
            rows.push({
              country: code,
              isUS: false,
              rate: Number(rec.rate)
            });
          }
        }
      });
      return rows;
    }
  },
  template: "\n    <div class=\"stm-lms-taxes\" v-if=\"field && Object.keys(field).length\">\n      <div class=\"stm-lms-taxes-header\">\n        <h4>{{ translations.taxRegions }}</h4>\n      </div>\n\n      <div class=\"stm-lms-taxes-table\">\n        <div class=\"stm-lms-taxes-header-row\">\n          <div class=\"stm-lms-taxes-cell\">{{ translations.country }}</div>\n          <div class=\"stm-lms-taxes-cell\">{{ translations.taxRate }}</div>\n          <div class=\"stm-lms-taxes-cell\"></div>\n        </div>\n\n        <div class=\"stm-lms-taxes-row\" v-for=\"row in displayRows\" :key=\"row.country\">\n          <div class=\"stm-lms-taxes-cell country-cell\">\n            <span v-html=\"showCountry(row.country)\" class=\"country-name\"></span>\n          </div>\n          <div class=\"stm-lms-taxes-cell rate-cell\">\n            <div class=\"rate-box\">\n              <template v-if=\"row.isUS\">\n                {{ formatRateRange(row.min, row.max, row.uniqueCount) }}\n              </template>\n              <template v-else>\n                {{ formatRate(row.rate) }}\n              </template>\n            </div>\n          </div>\n          <div class=\"stm-lms-taxes-cell action-cell\">\n            <div class=\"button edit\" @click=\"openEditCountry(row.country)\">{{ translations.edit }}</div>\n            <div class=\"button delete\" @click=\"removeCountry(row.country)\">\n              <i class=\"fa fa-trash\"></i>\n            </div>\n          </div>\n        </div>\n\n        <div class=\"stm-lms-taxes-empty\" v-if=\"displayRows.length === 0\">\n          <p>{{ translations.noTaxRates }}</p>\n        </div>\n      </div>\n\n      <form class=\"stm-lms-taxes-add-row\" @submit.prevent=\"openRateModal\">\n        <div class=\"stm-lms-taxes-cell\">\n          <label>{{ translations.addNewCountry }}</label>\n          <div class=\"stm-lms-taxes-select\" @click=\"toggleDropdown\" :class=\"{ open: dropdownOpen }\">\n            <div class=\"stm-lms-taxes-selected-value\">\n              <span v-html=\"showCountry(newTax.selected)\"></span>\n            </div>\n            <div class=\"stm-lms-taxes-dropdown\" v-if=\"dropdownOpen\">\n              <div class=\"stm-lms-taxes-dropdown__search\">\n                <input\n                  type=\"text\"\n                  v-model=\"searchQuery\"\n                  :placeholder=\"translations.searchCountry\"\n                  class=\"stm-lms-taxes-search\"\n                  @click.stop\n                />\n              </div>\n              <div class=\"stm-lms-taxes-dropdown__options\">\n                <div\n                  class=\"stm-lms-taxes-option\"\n                  v-for=\"country in filteredCountries\"\n                  :key=\"country.code\"\n                  @click.stop=\"selectCountry(country.code)\"\n                >\n                  <span>{{ showCountry(country.code) }}</span>\n                </div>\n              </div>\n            </div>\n          </div>\n        </div>\n\n        <div class=\"stm-lms-taxes-cell\">\n          <button type=\"submit\" class=\"button add\" :disabled=\"addDisabled\">{{ translations.add }}</button>\n        </div>\n      </form>\n\n      <div class=\"stm-taxes-modal-backdrop\" :class=\"{ 'stm-taxes-modal-backdrop_show': modalOpen }\" @click.self=\"closeModal\">\n        <div class=\"stm-taxes-modal\">\n          <div class=\"stm-taxes-modal__header\">\n            <span class=\"stm-taxes-back-button\" @click=\"closeModal\"></span>\n            <span class=\"stm-taxes-title\">\n              {{ translations.configureRatesFor }}\n              <span v-html=\"showCountry(activeCountry, false)\"></span>\n            </span>\n            <button type=\"button\" class=\"button button-primary\" @click=\"saveRates\">{{ translations.save }}</button>\n          </div>\n\n          <div class=\"stm-taxes-modal__body\">\n            <div v-if=\"isUSActive\" class=\"stm-taxes-modal__body-wrapper\">\n              <div class=\"stm-taxes-apply-all-toggle\">\n                <label class=\"masterstudy-switcher\">\n                  <input type=\"checkbox\" v-model=\"form.applyAllEnabled\" />\n                  <div class=\"masterstudy-switcher-background\"><div class=\"masterstudy-switcher-handle\"></div></div>\n                </label>\n                {{ translations.applyToAllRegions }}\n              </div>\n\n              <div class=\"stm-taxes-states-search\" v-if=\"!form.applyAllEnabled\">\n                <div class=\"stm-taxes-input with-clear\">\n                  <input type=\"text\" v-model=\"stateSearch\" :placeholder=\"translations.searchState || 'Search states\u2026'\"/>\n                  <span class=\"stm-taxes-search-clear\" v-if=\"stateSearch\" @click=\"clearStateSearch\">\xD7</span>\n                </div>\n              </div>\n\n              <div v-if=\"form.applyAllEnabled\" class=\"stm-taxes-apply-all-rate\">\n                <div class=\"stm-taxes-name\">{{ translations.allRegions }}</div>\n                <div class=\"stm-taxes-input\">\n                  <input\n                    type=\"number\"\n                    min=\"0\" max=\"100\" step=\"0.01\"\n                    v-model=\"form.applyAllRate\"\n                    :placeholder=\"translations.enterTaxRate\"\n                  />\n                  <div class=\"stm-taxes-input-up\" @click=\"taxesIncrement('applyAll')\"><i class=\"stmlms-chevron_up\"></i></div>\n                  <div class=\"stm-taxes-input-down\" @click=\"taxesDecrement('applyAll')\"><i class=\"stmlms-chevron_down\"></i></div>\n                </div>\n              </div>\n\n              <div v-else class=\"stm-taxes-list\">\n                <div\n                  class=\"stm-taxes-row\"\n                  v-for=\"st in filteredUsStates\"\n                  :key=\"st.code\"\n                >\n                  <div class=\"stm-taxes-name\">{{ st.name }}</div>\n                  <div class=\"stm-taxes-input\">\n                    <input\n                      type=\"number\"\n                      min=\"0\" max=\"100\" step=\"0.01\"\n                      v-model=\"form.usRates[st.code]\"\n                      :placeholder=\"translations.enterTaxRate\"\n                    />\n                    <div class=\"stm-taxes-input-up\" @click=\"taxesIncrement('us', st.code)\"><i class=\"stmlms-chevron_up\"></i></div>\n                    <div class=\"stm-taxes-input-down\" @click=\"taxesDecrement('us', st.code)\"><i class=\"stmlms-chevron_down\"></i></div>\n                  </div>\n                </div>\n              </div>\n            </div>\n\n            <div v-else class=\"stm-taxes-modal__body-wrapper\">\n              <div class=\"stm-taxes-list\">\n                <div class=\"stm-taxes-row\">\n                  <div class=\"stm-taxes-name\">{{ translations.taxRate }}</div>\n                  <div class=\"stm-taxes-input\">\n                    <input\n                      type=\"number\"\n                      min=\"0\" max=\"100\" step=\"0.01\"\n                      v-model=\"form.countryRate\"\n                      :placeholder=\"translations.enterTaxRate\"\n                    />\n                    <div class=\"stm-taxes-input-up\" @click=\"taxesIncrement('country')\"><i class=\"stmlms-chevron_up\"></i></div>\n                    <div class=\"stm-taxes-input-down\" @click=\"taxesDecrement('country')\"><i class=\"stmlms-chevron_down\"></i></div>\n                  </div>\n                </div>\n              </div>\n            </div>\n          </div>\n\n        </div>\n      </div>\n    </div>\n  ",
  mounted: function mounted() {
    document.addEventListener('click', this.handleClickOutside);
  },
  beforeDestroy: function beforeDestroy() {
    document.removeEventListener('click', this.handleClickOutside);
  },
  methods: {
    toggleDropdown: function toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
      if (this.dropdownOpen) this.searchQuery = '';
    },
    selectCountry: function selectCountry(code) {
      this.newTax.country = code;
      this.newTax.selected = code;
      this.dropdownOpen = false;
      this.searchQuery = '';
    },
    removeCountry: function removeCountry(code) {
      var US = this.translations.unitedStatesCode || 'US';
      if (code === US) {
        this.taxes = this.taxes.filter(function (t) {
          return !(t.country === US && t.region);
        });
      } else {
        this.taxes = this.taxes.filter(function (t) {
          return !(t.country === code && !t.region);
        });
      }
      this.taxes = _toConsumableArray(this.taxes);
      this.field.value = this.taxes.map(function (t) {
        return _objectSpread({}, t);
      });
    },
    openEditCountry: function openEditCountry(code) {
      var US = this.translations.unitedStatesCode || 'US';
      var tax = code === US ? {
        country: US,
        region: null,
        rate: null
      } : this.taxes.find(function (t) {
        return t.country === code && !t.region;
      }) || {
        country: code,
        region: null,
        rate: ''
      };
      this.openEditModal(tax);
    },
    openEditModal: function openEditModal(tax) {
      var _this3 = this;
      this.editing = true;
      this.editContext = {
        country: tax.country,
        region: tax.region || null
      };
      this.form.countryRate = '';
      this.form.usRates = {};
      this.form.applyAllEnabled = this.applyAllPreferred;
      this.form.applyAllRate = this.applyAllRatePreferred !== '' ? this.applyAllRatePreferred : '';
      this.highlightRegion = null;
      this.stateSearch = '';
      if (this.isUSActive) {
        var existingStates = this.taxes.filter(function (t) {
          return t.country === 'US' && t.region;
        });
        existingStates.forEach(function (t) {
          return _this3.$set(_this3.form.usRates, t.region, Number(t.rate));
        });
        if (this.form.applyAllEnabled && this.form.applyAllRate === '') {
          this.form.applyAllRate = this.getEqualUsRate();
        }
      } else {
        this.form.countryRate = Number(tax.rate);
      }
      this.modalOpen = true;
    },
    countryCodeToFlagEmoji: function countryCodeToFlagEmoji(code) {
      if (!code) return '';
      return code.toUpperCase().replace(/./g, function (ch) {
        return String.fromCodePoint(127397 + ch.charCodeAt());
      });
    },
    getCountryName: function getCountryName(code) {
      if (!code || code === 'default') return this.translations.selectCountry || 'Select Country';
      var c = this.countries.find(function (c) {
        return c.code === code;
      });
      return c ? c.name : code;
    },
    getUSStateName: function getUSStateName(regionCode) {
      var st = this.usStates.find(function (s) {
        return s.code === regionCode;
      });
      return st ? st.name : regionCode;
    },
    showCountry: function showCountry(code) {
      var badge = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
      if (!code || code === 'default') return this.translations.selectCountry || 'Select Country';
      var name = this.getCountryName(code);
      if (!badge) return name;
      var flag = this.countryCodeToFlagEmoji(code);
      return "".concat(flag, " ").concat(name);
    },
    showCountryFull: function showCountryFull(tax) {
      var base = this.showCountry(tax.country);
      if (tax.country === (this.translations.unitedStatesCode || 'US') && tax.region) {
        return "".concat(base, " \u2014 ").concat(this.getUSStateName(tax.region));
      }
      return base;
    },
    formatRate: function formatRate(val) {
      var n = Number(val);
      if (!isFinite(n)) return '';
      return "".concat(n, "%");
    },
    formatRateRange: function formatRateRange(min, max, uniqueCount) {
      if (!isFinite(min)) return '';
      if (uniqueCount <= 1 || !isFinite(max)) return "".concat(min, "%");
      return "".concat(min, "%\u2013").concat(max, "%");
    },
    openRateModal: function openRateModal() {
      var _this4 = this;
      if (!this.newTax.country) return;
      if (!this.isUSSelected && this.taxes.some(function (t) {
        return t.country === _this4.newTax.country && !t.region;
      })) return;
      this.editing = false;
      this.editContext = {
        country: null,
        region: null
      };
      this.highlightRegion = null;
      this.form.countryRate = '';
      this.form.usRates = {};
      this.form.applyAllEnabled = this.applyAllPreferred;
      this.form.applyAllRate = this.applyAllRatePreferred !== '' ? this.applyAllRatePreferred : '';
      this.stateSearch = '';
      if (this.isUSSelected) {
        var existing = this.taxes.filter(function (t) {
          return t.country === 'US' && t.region;
        });
        existing.forEach(function (t) {
          return _this4.$set(_this4.form.usRates, t.region, Number(t.rate));
        });
        if (this.form.applyAllEnabled && this.form.applyAllRate === '') {
          this.form.applyAllRate = this.getEqualUsRate();
        }
      } else {
        var ex = this.taxes.find(function (t) {
          return t.country === _this4.newTax.country && !t.region;
        });
        if (ex) this.form.countryRate = Number(ex.rate);
      }
      this.modalOpen = true;
    },
    closeModal: function closeModal() {
      this.modalOpen = false;
    },
    saveRates: function saveRates() {
      var _this5 = this;
      var wasEditing = this.editing;
      if (this.isUSActive) {
        if (this.form.applyAllEnabled) {
          var rate = this.normalizeRate(this.form.applyAllRate);
          if (rate === null) return;
          this.applyAllRatePreferred = String(rate);
          this.usStates.forEach(function (st) {
            var idx = _this5.taxes.findIndex(function (t) {
              return t.country === 'US' && t.region === st.code;
            });
            var rec = {
              country: 'US',
              region: st.code,
              rate: rate
            };
            if (idx >= 0) _this5.$set(_this5.taxes, idx, rec);else _this5.taxes.push(rec);
          });
        } else {
          this.usStates.forEach(function (st) {
            var raw = _this5.form.usRates[st.code];
            if (raw === '' || typeof raw === 'undefined') return;
            var rate = _this5.normalizeRate(raw);
            if (rate === null) return;
            var idx = _this5.taxes.findIndex(function (t) {
              return t.country === 'US' && t.region === st.code;
            });
            var rec = {
              country: 'US',
              region: st.code,
              rate: rate
            };
            if (idx >= 0) _this5.$set(_this5.taxes, idx, rec);else _this5.taxes.push(rec);
          });
        }
      } else {
        var _rate = this.normalizeRate(this.form.countryRate);
        if (_rate === null) return;
        var code = this.activeCountry;
        var idx = this.taxes.findIndex(function (t) {
          return t.country === code && !t.region;
        });
        var rec = {
          country: code,
          region: null,
          rate: _rate
        };
        if (idx >= 0) this.$set(this.taxes, idx, rec);else this.taxes.push(rec);
      }
      this.taxes = _toConsumableArray(this.taxes);
      this.field.value = this.taxes.map(function (t) {
        return _objectSpread({}, t);
      });
      this.closeModal();
      if (!wasEditing) {
        this.newTax = {
          country: '',
          selected: 'default'
        };
      }
      this.editing = false;
      this.editContext = {
        country: null,
        region: null
      };
      this.highlightRegion = null;
      this.stateSearch = '';
    },
    clearStateSearch: function clearStateSearch() {
      this.stateSearch = '';
      this.editContext.region = null;
      this.highlightRegion = null;
    },
    taxesIncrement: function taxesIncrement(type, code) {
      var step = 0.01;
      if (type === 'applyAll') {
        var cur = this.toNumber(this.form.applyAllRate);
        this.form.applyAllRate = this.round2(cur + step);
        return;
      }
      if (type === 'country') {
        var _cur = this.toNumber(this.form.countryRate);
        this.form.countryRate = this.round2(_cur + step);
        return;
      }
      if (type === 'us' && code) {
        var _cur2 = this.toNumber(this.form.usRates[code]);
        this.$set(this.form.usRates, code, this.round2(_cur2 + step));
      }
    },
    taxesDecrement: function taxesDecrement(type, code) {
      var step = 0.01;
      if (type === 'applyAll') {
        var cur = this.toNumber(this.form.applyAllRate);
        this.form.applyAllRate = this.round2(cur - step);
        return;
      }
      if (type === 'country') {
        var _cur3 = this.toNumber(this.form.countryRate);
        this.form.countryRate = this.round2(_cur3 - step);
        return;
      }
      if (type === 'us' && code) {
        var _cur4 = this.toNumber(this.form.usRates[code]);
        this.$set(this.form.usRates, code, this.round2(_cur4 - step));
      }
    },
    toNumber: function toNumber(v) {
      var n = parseFloat(v);
      return isFinite(n) ? n : 0;
    },
    round2: function round2(v) {
      var clamped = Math.max(0, Math.min(100, v));
      return Math.round(clamped * 100) / 100;
    },
    normalizeRate: function normalizeRate(v) {
      var n = parseFloat(v);
      if (!isFinite(n)) return null;
      if (n < 0 || n > 100) return null;
      return Math.round(n * 100) / 100;
    },
    getEqualUsRate: function getEqualUsRate() {
      var entries = this.taxes.filter(function (t) {
        return t.country === 'US' && t.region;
      });
      if (entries.length === 0) return '';
      var first = Number(entries[0].rate);
      for (var i = 1; i < entries.length; i++) {
        if (Number(entries[i].rate) !== first) return '';
      }
      return String(first);
    },
    handleClickOutside: function handleClickOutside(e) {
      var select = this.$el.querySelector('.stm-lms-taxes-select');
      if (select && !select.contains(e.target)) this.dropdownOpen = false;
    }
  }
});