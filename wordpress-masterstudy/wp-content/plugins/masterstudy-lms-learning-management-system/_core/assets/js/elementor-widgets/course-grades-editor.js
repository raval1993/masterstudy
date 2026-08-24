"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }
function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }
function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }
var CourseGradeHandler = /*#__PURE__*/function (_elementorModules$fro) {
  _inherits(CourseGradeHandler, _elementorModules$fro);
  var _super = _createSuper(CourseGradeHandler);
  function CourseGradeHandler() {
    _classCallCheck(this, CourseGradeHandler);
    return _super.apply(this, arguments);
  }
  _createClass(CourseGradeHandler, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        selectors: {
          gradeDetails: '.masterstudy-grade-details',
          gradeDetailsBlock: '.masterstudy-grade-details__block',
          gradeLoader: '.masterstudy-grade-details__loader',
          gradeDetailsButton: '[data-id="show-grade-details"]',
          gradeRegenerateButton: '[data-id="regenerate-course-grade"]',
          gradeCloseButton: '.masterstudy-grade-details__close',
          examsList: '.masterstudy-grade-details__exams-list',
          regenerateMessage: '.masterstudy-single-course-grades__message_regenerate_sidebar'
        }
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var selectors = this.getSettings('selectors');
      return {
        $gradeDetails: this.$element.find(selectors.gradeDetails),
        $gradeDetailsBlock: this.$element.find(selectors.gradeDetailsBlock),
        $gradeLoader: this.$element.find(selectors.gradeLoader),
        $gradeDetailsButton: this.$element.find(selectors.gradeDetailsButton),
        $gradeRegenerateButton: this.$element.find(selectors.gradeRegenerateButton),
        $gradeCloseButton: this.$element.find(selectors.gradeCloseButton),
        $examsList: this.$element.find(selectors.examsList),
        $regenerateMessage: this.$element.find(selectors.regenerateMessage)
      };
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _this = this;
      document.addEventListener('click', function (event) {
        return _this.handleClickOutside(event);
      });
      if (this.elements.$gradeDetailsButton.length) {
        this.elements.$gradeDetailsButton.on('click', function (event) {
          return _this.showGradeDetails(event);
        });
      }
      if (this.elements.$gradeRegenerateButton.length) {
        this.elements.$gradeRegenerateButton.on('click', function (event) {
          return _this.regenerateGrade(event);
        });
      }
      if (this.elements.$gradeCloseButton.length) {
        this.elements.$gradeCloseButton.on('click', function (event) {
          return _this.closeGradeDetails(event);
        });
      }
    }
  }, {
    key: "handleClickOutside",
    value: function handleClickOutside(event) {
      if (this.elements.$gradeDetailsBlock.length && !this.elements.$gradeDetailsBlock[0].contains(event.target) && this.elements.$gradeDetailsButton.length && !this.elements.$gradeDetailsButton[0].contains(event.target)) {
        document.body.classList.remove('masterstudy-grade-details-body-hidden');
        this.elements.$gradeDetails.removeClass('masterstudy-grade-details_show');
        this.elements.$gradeLoader.removeClass('masterstudy-grade-details__loader_hide');
      }
    }
  }, {
    key: "showGradeDetails",
    value: function showGradeDetails(event) {
      var _this2 = this;
      event.preventDefault();
      document.body.classList.add('masterstudy-grade-details-body-hidden');
      this.elements.$gradeDetails.addClass('masterstudy-grade-details_show');
      this.courseId = this.getElementSettings('course');
      var detailsPath = "student-grade/".concat(this.courseId);
      var api = new MasterstudyApiProvider();
      api.get(detailsPath).then(function (result) {
        if (result.error_code) {
          return;
        }
        if (Object.keys(result).length > 0) {
          _this2.elements.$gradeLoader.addClass('masterstudy-grade-details__loader_hide');
          if (_this2.elements.$examsList.length) {
            _this2.elements.$examsList.html('');
            result.exams.forEach(function (lesson) {
              var examItemHTML = _this2.createExamItem(lesson);
              _this2.elements.$examsList.append(examItemHTML);
            });
          }
        }
      });
    }
  }, {
    key: "createExamItem",
    value: function createExamItem(lesson) {
      return "\n            <div class=\"masterstudy-grade-details__exams-item masterstudy-grade-details__exams-item-".concat(lesson.type, "\">\n                <div class=\"masterstudy-grade-details__exams-item-title\">\n                    <div class=\"masterstudy-grade-details__exams-item-icon\"></div>\n                    ").concat(lesson.title, "\n                </div>\n                ").concat(lesson.grade && Object.keys(lesson.grade).length > 0 ? "\n                    <div class=\"masterstudy-grade-details__exams-item-attempt\">".concat(lesson.attempts, " ").concat(course_grade.attempts, "</div>\n                    <div class=\"masterstudy-grade-details__exams-item-grade\">\n                        <div class=\"masterstudy-grade-details__exams-item-grade-badge\" style=\"background:").concat(lesson.grade.color, "\">").concat(lesson.grade.badge, "</div>\n                        <div class=\"masterstudy-grade-details__exams-item-grade-value\">(").concat(lesson.grade.current).concat(course_grade.grade_separator).concat(lesson.grade.max_point, ")</div>\n                    </div>\n                    <div class=\"masterstudy-grade-details__exams-item-percent\">").concat(lesson.grade.range, "%</div>\n                ") : "\n                    <div class=\"masterstudy-grade-details__exams-item-start\">".concat(course_grade.not_started, "</div>\n                "), "\n            </div>\n        ");
    }
  }, {
    key: "regenerateGrade",
    value: function regenerateGrade(event) {
      var _this3 = this;
      event.preventDefault();
      var regeneratePath = "student-grade/".concat(course_grade.course_id, "/regenerate");
      var api = new MasterstudyApiProvider();
      api.get(regeneratePath).then(function (result) {
        if (result.error_code) {
          return;
        }
        if (!_this3.elements.$regenerateMessage.length) {
          var currentUrl = new URL(window.location.href);
          currentUrl.searchParams.set('tab', 'grades');
          window.location.href = currentUrl.toString();
        } else {
          window.location.reload();
        }
      });
    }
  }, {
    key: "closeGradeDetails",
    value: function closeGradeDetails(event) {
      document.body.classList.remove('masterstudy-grade-details-body-hidden');
      this.elements.$gradeDetails.removeClass('masterstudy-grade-details_show');
      this.elements.$gradeLoader.removeClass('masterstudy-grade-details__loader_hide');
    }
  }]);
  return CourseGradeHandler;
}(elementorModules.frontend.handlers.Base);
jQuery(window).on('elementor/frontend/init', function () {
  var addHandler = function addHandler($element) {
    elementorFrontend.elementsHandler.addHandler(CourseGradeHandler, {
      $element: $element
    });
  };
  elementorFrontend.hooks.addAction('frontend/element_ready/ms_lms_course_grades.default', addHandler);
});