"use strict";

Vue.component('demo_import', {
  data: function data() {
    return {
      isLoading: false,
      currentStep: 0,
      steps: ['questions', 'quizzes', 'lessons', 'courses'],
      isDone: false,
      importStarted: false,
      doneSteps: 'questions'
    };
  },
  mounted: function mounted() {
    var adminInput = document.getElementById('section_ecommerce-admin_fee');
    var authorInput = document.getElementById('section_ecommerce-author_fee');
    var syncFields = function syncFields() {
      if (authorInput && adminInput) {
        var authorValue = parseFloat(authorInput.value) || 0;
        var adminValue = parseFloat(adminInput.value) || 0;
        if (authorValue > 0 && (adminValue === 0 || isNaN(adminValue))) {
          adminInput.value = 100 - authorValue;
        } else if (adminValue > 0 && (authorValue === 0 || isNaN(authorValue))) {
          authorInput.value = 100 - adminValue;
        } else if (authorValue + adminValue !== 100) {
          adminInput.value = 100 - authorValue;
        }
      }
    };
    setTimeout(syncFields, 500);
    if (adminInput) {
      adminInput.addEventListener('input', this.onAdminInput);
    }
    if (authorInput) {
      authorInput.addEventListener('input', this.onAuthorInput);
    }
  },
  beforeDestroy: function beforeDestroy() {
    var adminInput = document.getElementById('section_ecommerce-admin_fee');
    var authorInput = document.getElementById('section_ecommerce-author_fee');
    if (adminInput) {
      adminInput.removeEventListener('input', this.onAdminInput);
    }
    if (authorInput) {
      authorInput.removeEventListener('input', this.onAuthorInput);
    }
  },
  methods: {
    onAdminInput: function onAdminInput(event) {
      var val = parseFloat(event.target.value);
      if (isNaN(val)) val = 0;
      if (val > 100) val = 100;
      var authorVal = 100 - val;
      event.target.value = val;
      var authorInput = document.getElementById('section_ecommerce-author_fee');
      if (authorInput) {
        authorInput.value = authorVal;
        authorInput.dispatchEvent(new Event('input', {
          bubbles: true
        }));
      }
    },
    onAuthorInput: function onAuthorInput(event) {
      var val = parseFloat(event.target.value);
      if (isNaN(val)) val = 0;
      if (val > 100) val = 100;
      var adminVal = 100 - val;
      event.target.value = val;
      var adminInput = document.getElementById('section_ecommerce-admin_fee');
      if (adminInput) {
        adminInput.value = adminVal;
        adminInput.dispatchEvent(new Event('input', {
          bubbles: true
        }));
      }
    },
    importData: function importData() {
      var _this = this;
      _this.$set(_this, 'importStarted', true);
      if (typeof _this.steps[_this.currentStep] !== 'undefined' && !_this.isLoading) {
        _this.$set(_this, 'isLoading', true);
        _this.$http.get(stm_lms_ajaxurl + '?action=stm_lms_import_sample_data&stm_lms_step=' + _this.steps[_this.currentStep]).then(function (r) {
          r = r.body;
          _this.$set(_this, 'currentStep', _this.currentStep + 1);
          _this.$set(_this, 'doneSteps', _this.doneSteps + ' ' + _this.steps[_this.currentStep]);
          _this.$set(_this, 'isLoading', false);
          _this.importData();
        });
      } else {
        _this.$set(_this, 'isDone', true);
        _this.$set(_this, 'doneSteps', _this.doneSteps + ' complete');
      }
    }
  }
});