"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
var MasterstudyPagination = /*#__PURE__*/function () {
  function MasterstudyPagination(options) {
    _classCallCheck(this, MasterstudyPagination);
    this.options = options;
    this.container = document.querySelector(".masterstudy-pagination");
    this.dataListContainer = document.querySelector(this.options.dataListContainer);
    this.wrapper = this.container.querySelector(".masterstudy-pagination__wrapper");
    this.nextButton = this.container.querySelector(".masterstudy-pagination__button-next");
    this.prevButton = this.container.querySelector(".masterstudy-pagination__button-prev");
    this.pagesNumbers = this.container.querySelector(".masterstudy-pagination__list");
    this.perPageLimit = this.setPerPageLimit(this.options.perPageLimit || 10);
    this.isQueryable = this.options.isQueryable || false;
    this.currentPage = this.options.currentPage || 1;
    this.itemWidth = this.options.itemWidth || 50;
    this.visibleNumber = this.options.visibleNumber || 3;
    this.autoRefresh = this.options.autoRefresh || false;
    this.isManual = false;
    this.centeredPos = 0;
    this.maxPosition = 0;
    this.pageCount = 0;
    this.pageIndex = 0;
    this.totalPages = 0;
    this.hookActions = {};
    this.loadedPages = [];
    this.perPages = {};
    this.dataItemElements = null;
    this.isReset = false;
    this.dataItemDisplayCss = this.options.dataItemDisplayCss || 'block';
    this.dataItemExcludeClass = this.removeClassNameDot(this.options.dataItemExcludeClass);
    this.dataItemHideClass = this.removeClassNameDot(this.options.dataItemHideClass);
    this.dataItemElementsClass = this.addClassNameDot(this.options.dataItemElementsClass);
    this.nextButton.addEventListener("click", this.scrollNext.bind(this));
    this.prevButton.addEventListener("click", this.scrollPrev.bind(this));
  }
  _createClass(MasterstudyPagination, [{
    key: "paginate",
    value: function paginate(totalPages, perPageLimit, isReset) {
      this.isReset = isReset || false;
      if (perPageLimit !== this.getPerPageLimit() || this.isReset) {
        this.loadedPages = [];
        this.currentPage = 1;
        this.pagesNumbers.style.left = '0px';
      }
      this.setPerPageLimit(perPageLimit);
      if (this.dataListContainer) {
        this.dataItemElements = this.dataListContainer.querySelectorAll(this.dataItemElementsClass);
        if (this.dataItemElements.length) {
          this.pageCount = this.dataItemElements.length;
          this.totalPages = Math.ceil(this.pageCount / this.perPageLimit);
        }
      }
      if (totalPages && /^\d+$/.test(totalPages)) {
        this.isManual = true;
        this.totalPages = totalPages;
      }
      this.setCurrentPage(this.getPageNumber());
      this.addPagesToList();
      this.init();
      this.isReset = false;
      return this.currentPage;
    }
  }, {
    key: "setCurrentPage",
    value: function setCurrentPage(pageNumber) {
      var _this = this;
      this.currentPage = +(pageNumber < 1 ? 1 : pageNumber > this.totalPages ? 1 : pageNumber);
      this.pagesNumbers.querySelectorAll('.masterstudy-pagination__item-block').forEach(function (el) {
        if (el.dataset.id == _this.currentPage) {
          el.parentElement.classList.add("masterstudy-pagination__item_current");
        } else {
          el.parentElement.classList.remove("masterstudy-pagination__item_current");
        }
      });
      this.navButtonsState();
      var isPageLoadedBefore = this.loadedPages.indexOf(this.currentPage) !== -1;
      if (!isPageLoadedBefore) {
        this.loadedPages.push(this.currentPage);
      }
      this.perPageLimit = this.getPerPageLimit();
      this.showCurrentPageData();
      this.doAction('onPageChange', this.currentPage, isPageLoadedBefore, this.perPageLimit, this.loadedPages);
    }
  }, {
    key: "init",
    value: function init() {
      var _this2 = this;
      var pageBlocks = this.container.querySelectorAll('.masterstudy-pagination__item-block');
      pageBlocks.forEach(function (block) {
        block.addEventListener('click', _this2.pageButton.bind(_this2));
      });
    }
  }, {
    key: "showCurrentPageData",
    value: function showCurrentPageData() {
      var _this3 = this;
      var prevRange = (this.currentPage - 1) * this.perPageLimit;
      var currRange = this.currentPage * this.perPageLimit;
      if (this.dataListContainer) {
        this.dataItemElements = this.dataListContainer.querySelectorAll(this.dataItemElementsClass);
        if (this.dataItemExcludeClass) {
          this.dataItemElements = Array.from(this.dataItemElements).filter(function (element) {
            return !element.classList.contains(this.dataItemExcludeClass);
          }, this);
        }
        if (this.dataItemElements && this.dataItemElements.length) {
          this.dataItemElements.forEach(function (item, index) {
            if (_this3.dataItemHideClass) {
              if (index >= prevRange && index < currRange) {
                item.classList.remove(_this3.dataItemHideClass);
              } else {
                item.classList.add(_this3.dataItemHideClass);
              }
            } else {
              if (index >= prevRange && index < currRange) {
                item.style.display = _this3.dataItemDisplayCss;
              } else {
                item.style.display = 'none';
              }
            }
          });
        }
      }
    }
  }, {
    key: "scrollNext",
    value: function scrollNext(e) {
      e.preventDefault();
      this.paginationNav('next');
    }
  }, {
    key: "scrollPrev",
    value: function scrollPrev(e) {
      e.preventDefault();
      this.paginationNav('prev');
    }
  }, {
    key: "paginationNav",
    value: function paginationNav(direction) {
      if ('prev' === direction && this.currentPage > 1) {
        this.currentPage--;
      }
      if ('next' === direction && this.currentPage < this.totalPages) {
        this.currentPage++;
      }
      this.updatePageQueryParam(this.currentPage);
      this.navButtonsState();
      this.centerActivePaginationButton();
      this.setCurrentPage(this.currentPage);
    }
  }, {
    key: "centerActivePaginationButton",
    value: function centerActivePaginationButton() {
      if (this.pagesNumbers && this.visibleNumber % 2 !== 0) {
        var pageStep = this.getPageStepWidth();
        this.maxPosition = this.pagesNumbers.clientWidth - pageStep * this.visibleNumber;
        var containerCenter = Math.ceil(this.visibleNumber / 2);
        if (this.currentPage > containerCenter) {
          this.centeredPos = (this.currentPage - containerCenter) * pageStep;
        } else {
          this.centeredPos = 0;
        }
        if (this.centeredPos <= this.maxPosition) {
          this.pagesNumbers.style.left = -this.centeredPos + "px";
        }
      }
    }
  }, {
    key: "getPageOptions",
    value: function getPageOptions() {
      return {
        perPage: this.perPageLimit,
        page: this.currentPage,
        totalPages: this.totalPages
      };
    }
  }, {
    key: "addPagesToList",
    value: function addPagesToList() {
      var fragment = document.createDocumentFragment();
      var itemNumber = this.totalPages < this.visibleNumber ? this.totalPages : this.visibleNumber;
      this.pageIndex = 0;
      this.pagesNumbers.innerHTML = '';
      for (var page = this.pageIndex + 1; page <= this.totalPages; page++) {
        var listItem = document.createElement("li");
        listItem.classList.add("masterstudy-pagination__item");
        if (page === this.currentPage) {
          listItem.classList.add("masterstudy-pagination__item_current");
        }
        var span = document.createElement("span");
        span.classList.add("masterstudy-pagination__item-block");
        span.setAttribute("data-id", page);
        span.textContent = page;
        listItem.appendChild(span);
        fragment.appendChild(listItem);
        this.pageIndex = page;
      }
      this.pagesNumbers.appendChild(fragment);
      this.wrapper.style.width = itemNumber * this.getPageStepWidth() + "px";
      this.container.classList.remove('masterstudy-pagination_hidden');
      this.centerActivePaginationButton();
    }
  }, {
    key: "getPageStepWidth",
    value: function getPageStepWidth() {
      var firstPage = this.pagesNumbers.querySelector('.masterstudy-pagination__item');
      var pageWidth = firstPage ? Math.round(firstPage.getBoundingClientRect().width) : 0;
      return pageWidth > 0 ? pageWidth : this.itemWidth;
    }
  }, {
    key: "pageButton",
    value: function pageButton(e) {
      this.setCurrentPage(e.target.dataset.id);
      this.updatePageQueryParam(this.currentPage);
      this.centerActivePaginationButton();
    }
  }, {
    key: "navButtonsState",
    value: function navButtonsState() {
      this.prevButton.classList.toggle("masterstudy-pagination__button_disabled", this.currentPage === 1);
      this.nextButton.classList.toggle("masterstudy-pagination__button_disabled", this.currentPage === this.totalPages);
    }
  }, {
    key: "updatePagination",
    value: function updatePagination() {
      var _this4 = this;
      if (this.dataListContainer) {
        this.dataItemElements = this.dataListContainer.querySelectorAll(this.dataItemElementsClass);
        if (this.dataItemExcludeClass) {
          this.dataItemElements = Array.from(this.dataItemElements).filter(function (element) {
            return !element.classList.contains(this.dataItemExcludeClass);
          }, this);
        }
        if (this.dataItemElements && this.dataItemElements.length) {
          this.dataItemElements.forEach(function (item, index) {
            if (_this4.dataItemHideClass) {
              if (index >= prevRange && index < currRange) {
                item.classList.remove(_this4.dataItemHideClass);
              } else {
                item.classList.add(_this4.dataItemHideClass);
              }
            } else {
              if (index >= prevRange && index < currRange) {
                item.style.display = _this4.dataItemDisplayCss;
              } else {
                item.style.display = 'none';
              }
            }
          });
        }
      }
    }
  }, {
    key: "updatePageQueryParam",
    value: function updatePageQueryParam(pageNumber) {
      if (this.isQueryable) {
        var currentUrl = window.location.href;
        var urlParams = new URLSearchParams(window.location.search);
        var queryName = "page";
        if (urlParams.has(queryName)) {
          urlParams.set(queryName, pageNumber);
        } else {
          urlParams.append(queryName, pageNumber);
        }
        var queryUrl = currentUrl.split("?")[0] + "?" + urlParams.toString();
        window.history.replaceState({}, document.title, queryUrl);
        if (this.autoRefresh) {
          window.location.href = queryUrl;
        }
      }
    }
  }, {
    key: "getPageNumber",
    value: function getPageNumber() {
      if (this.isQueryable) {
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('page')) {
          if (this.totalPages < urlParams.get('page') || 0 === +this.totalPages) {
            urlParams["delete"]('page');
            var currentUrl = window.location.href;
            var queryUrl = currentUrl.split("?")[0] + "?" + urlParams.toString();
            window.history.replaceState({}, document.title, queryUrl);
            return 1;
          }
          return urlParams.get('page');
        }
      }
      return +this.currentPage;
    }
  }, {
    key: "onPageChange",
    value: function onPageChange(callback) {
      this.addAction('onPageChange', callback);
      this.showCurrentPageData();
      return this;
    }
  }, {
    key: "addAction",
    value: function addAction(action, callback) {
      if (!this.hookActions[action]) {
        this.hookActions[action] = [];
      }
      this.hookActions[action].push(callback);
    }
  }, {
    key: "doAction",
    value: function doAction(action) {
      for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        args[_key - 1] = arguments[_key];
      }
      if (this.hookActions[action]) {
        this.hookActions[action].forEach(function (callback) {
          callback.apply(void 0, args);
        });
      }
    }
  }, {
    key: "setPerPageLimit",
    value: function setPerPageLimit(perPageLimit) {
      if (typeof perPageLimit === 'number' && 0 <= perPageLimit) {
        this.container.setAttribute('data-perpage', perPageLimit);
      }
      return perPageLimit;
    }
  }, {
    key: "getPerPageLimit",
    value: function getPerPageLimit() {
      var perPage = this.container.getAttribute('data-perpage');
      if (perPage) {
        return +perPage;
      } else {
        return this.options.perPageLimit || 10;
      }
    }
  }, {
    key: "removeClassNameDot",
    value: function removeClassNameDot(className) {
      if (this.isClassNameDotSet(className)) {
        return className.slice(1);
      }
      return className;
    }
  }, {
    key: "addClassNameDot",
    value: function addClassNameDot(className) {
      if (this.isClassNameDotSet(className)) {
        return className;
      }
      return ".".concat(className);
    }
  }, {
    key: "isClassNameDotSet",
    value: function isClassNameDotSet(className) {
      if (className === null || className === undefined) {
        return false;
      }
      return className.charAt(0) === '.';
    }
  }]);
  return MasterstudyPagination;
}();