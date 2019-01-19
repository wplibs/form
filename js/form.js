(function () {
  'use strict';

  /* globals jQuery, window */
  var Manager = function ($, Backbone, _) {
    return Backbone.View.extend({
      /**
       * Initialize the manager
       */
      initialize: function initialize() {
        this.controls = {};
      },

      /**
       * Add a control into the collect
       *
       * @param control
       */
      addControl: function addControl(control) {
        this.controls[control.get('id')] = control;
      },

      /**
       * Gets all control values.
       */
      getValues: function getValues() {
        return _.mapObject(this.controls, function (c) {
          return c.getValue();
        });
      }
    });
  }(jQuery, window.Backbone, window._);

  function createCommonjsModule(fn, module) {
  	return module = { exports: {} }, fn(module, module.exports), module.exports;
  }

  var js_cookie = createCommonjsModule(function (module, exports) {

    (function (factory) {
      var registeredInModuleLoader = false;

      {
        module.exports = factory();
        registeredInModuleLoader = true;
      }

      if (!registeredInModuleLoader) {
        var OldCookies = window.Cookies;
        var api = window.Cookies = factory();

        api.noConflict = function () {
          window.Cookies = OldCookies;
          return api;
        };
      }
    })(function () {
      function extend() {
        var i = 0;
        var result = {};

        for (; i < arguments.length; i++) {
          var attributes = arguments[i];

          for (var key in attributes) {
            result[key] = attributes[key];
          }
        }

        return result;
      }

      function init(converter) {
        function api(key, value, attributes) {
          var result;

          if (typeof document === 'undefined') {
            return;
          } // Write


          if (arguments.length > 1) {
            attributes = extend({
              path: '/'
            }, api.defaults, attributes);

            if (typeof attributes.expires === 'number') {
              var expires = new Date();
              expires.setMilliseconds(expires.getMilliseconds() + attributes.expires * 864e+5);
              attributes.expires = expires;
            } // We're using "expires" because "max-age" is not supported by IE


            attributes.expires = attributes.expires ? attributes.expires.toUTCString() : '';

            try {
              result = JSON.stringify(value);

              if (/^[\{\[]/.test(result)) {
                value = result;
              }
            } catch (e) {}

            if (!converter.write) {
              value = encodeURIComponent(String(value)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);
            } else {
              value = converter.write(value, key);
            }

            key = encodeURIComponent(String(key));
            key = key.replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent);
            key = key.replace(/[\(\)]/g, escape);
            var stringifiedAttributes = '';

            for (var attributeName in attributes) {
              if (!attributes[attributeName]) {
                continue;
              }

              stringifiedAttributes += '; ' + attributeName;

              if (attributes[attributeName] === true) {
                continue;
              }

              stringifiedAttributes += '=' + attributes[attributeName];
            }

            return document.cookie = key + '=' + value + stringifiedAttributes;
          } // Read


          if (!key) {
            result = {};
          } // To prevent the for loop in the first place assign an empty array
          // in case there are no cookies at all. Also prevents odd result when
          // calling "get()"


          var cookies = document.cookie ? document.cookie.split('; ') : [];
          var rdecode = /(%[0-9A-Z]{2})+/g;
          var i = 0;

          for (; i < cookies.length; i++) {
            var parts = cookies[i].split('=');
            var cookie = parts.slice(1).join('=');

            if (!this.json && cookie.charAt(0) === '"') {
              cookie = cookie.slice(1, -1);
            }

            try {
              var name = parts[0].replace(rdecode, decodeURIComponent);
              cookie = converter.read ? converter.read(cookie, name) : converter(cookie, name) || cookie.replace(rdecode, decodeURIComponent);

              if (this.json) {
                try {
                  cookie = JSON.parse(cookie);
                } catch (e) {}
              }

              if (key === name) {
                result = cookie;
                break;
              }

              if (!key) {
                result[name] = cookie;
              }
            } catch (e) {}
          }

          return result;
        }

        api.set = api;

        api.get = function (key) {
          return api.call(api, key);
        };

        api.getJSON = function () {
          return api.apply({
            json: true
          }, [].slice.call(arguments));
        };

        api.defaults = {};

        api.remove = function (key, attributes) {
          api(key, '', extend(attributes, {
            expires: -1
          }));
        };

        api.withConverter = init;
        return api;
      }

      return init(function () {});
    });
  });

  /*!
  * metismenujs - v1.0.3
  * MetisMenu with Vanilla-JS
  * https://github.com/onokumus/metismenujs#readme
  *
  * Made by Osman Nuri Okumus <onokumus@gmail.com> (https://github.com/onokumus)
  * Under MIT License
  */
  var __assign = Object.assign || function (target) {
    for (var source, i = 1; i < arguments.length; i++) {
      source = arguments[i];

      for (var prop in source) {
        if (Object.prototype.hasOwnProperty.call(source, prop)) {
          target[prop] = source[prop];
        }
      }
    }

    return target;
  };

  /*!
  * metismenujs - v1.0.3
  * MetisMenu with Vanilla-JS
  * https://github.com/onokumus/metismenujs#readme
  *
  * Made by Osman Nuri Okumus <onokumus@gmail.com> (https://github.com/onokumus)
  * Under MIT License
  */
  var Default = {
    parentTrigger: "li",
    subMenu: "ul",
    toggle: true,
    triggerElement: "a"
  };
  var ClassNames = {
    activeClass: "active",
    collapseClass: "collapse",
    collapseInClass: "in",
    collapsingClass: "collapsing"
  };

  /*!
  * metismenujs - v1.0.3
  * MetisMenu with Vanilla-JS
  * https://github.com/onokumus/metismenujs#readme
  *
  * Made by Osman Nuri Okumus <onokumus@gmail.com> (https://github.com/onokumus)
  * Under MIT License
  */

  var MetisMenu =
  /** @class */
  function () {
    /**
     * Creates an instance of OnoffCanvas.
     *
     * @constructor
     * @param {HTMLElement | string} element
     * @param {IMMOptions} [options]
     * @memberof MetisMenu
     */
    function MetisMenu(element, options) {
      this.element = typeof element === "string" ? document.querySelector(element) : element;
      this.cacheEl = this.element;
      this.config = __assign({}, Default, options);
      this.cacheConfig = this.config;
      this.disposed = false;
      this.ulArr = [];
      this.listenerOb = [];
      this.init();
    }

    MetisMenu.prototype.update = function () {
      this.disposed = false;
      this.element = this.cacheEl;
      this.config = this.cacheConfig;
      this.init();
    };

    MetisMenu.prototype.dispose = function () {
      for (var _i = 0, _a = this.listenerOb; _i < _a.length; _i++) {
        var lo = _a[_i];

        for (var key in lo) {
          if (lo.hasOwnProperty(key)) {
            var el = lo[key];
            el[1].removeEventListener(el[0], el[2]);
          }
        }
      }

      this.ulArr = [];
      this.listenerOb = [];
      this.config = null;
      this.element = null;
      this.disposed = true;
    };

    MetisMenu.prototype.on = function (event, fn) {
      this.element.addEventListener(event, fn, false);
      return this;
    };

    MetisMenu.prototype.off = function (event, fn) {
      this.element.removeEventListener(event, fn);
      return this;
    };

    MetisMenu.prototype.emit = function (event, eventDetail, shouldBubble) {
      if (shouldBubble === void 0) {
        shouldBubble = false;
      }

      var evt;

      if (typeof CustomEvent === "function") {
        evt = new CustomEvent(event, {
          bubbles: shouldBubble,
          detail: eventDetail
        });
      } else {
        evt = document.createEvent("CustomEvent");
        evt.initCustomEvent(event, shouldBubble, false, eventDetail);
      }

      this.element.dispatchEvent(evt);
      return this;
    };

    MetisMenu.prototype.init = function () {
      this.ulArr = [].slice.call(this.element.querySelectorAll(this.config.subMenu));

      for (var _i = 0, _a = this.ulArr; _i < _a.length; _i++) {
        var ul = _a[_i];
        var li = ul.parentNode;
        ul.classList.add(ClassNames.collapseClass);

        if (li.classList.contains(ClassNames.activeClass)) {
          this.show(ul);
        } else {
          this.hide(ul);
        }

        var a$$1 = li.querySelector(this.config.triggerElement);

        if (a$$1.getAttribute("aria-disabled") === "true") {
          return;
        }

        a$$1.setAttribute("aria-expanded", "false");
        var listenerOb = {
          aClick: ["click", a$$1, this.clickEvent.bind(this)]
        };

        for (var key in listenerOb) {
          if (listenerOb.hasOwnProperty(key)) {
            var listener = listenerOb[key];
            listener[1].addEventListener(listener[0], listener[2]);
          }
        }

        this.listenerOb.push(listenerOb);
      }
    };

    MetisMenu.prototype.clickEvent = function (ev) {
      if (!this.disposed) {
        if (ev.currentTarget.tagName === "A") {
          ev.preventDefault();
        }

        var li = ev.currentTarget.parentNode;
        var ul = li.querySelector(this.config.subMenu);
        this.toggle(ul);
      }
    };

    MetisMenu.prototype.toggle = function (ul) {
      if (ul.parentNode.classList.contains(ClassNames.activeClass)) {
        this.hide(ul);
      } else {
        this.show(ul);
      }
    };

    MetisMenu.prototype.show = function (ul) {
      var _this = this;

      if (this.isTransitioning || ul.classList.contains(ClassNames.collapseInClass)) {
        return;
      }

      var complete = function complete() {
        ul.classList.remove(ClassNames.collapsingClass);
        ul.style.height = "";
        ul.removeEventListener("transitionend", complete);

        _this.setTransitioning(false);

        _this.emit("shown.metisMenu", {
          shownElement: ul
        });
      };

      var li = ul.parentNode;
      li.classList.add(ClassNames.activeClass);
      var a$$1 = li.querySelector(this.config.triggerElement);
      a$$1.setAttribute("aria-expanded", "true");
      ul.style.height = "0px";
      ul.classList.remove(ClassNames.collapseClass);
      ul.classList.remove(ClassNames.collapseInClass);
      ul.classList.add(ClassNames.collapsingClass);
      var eleParentSiblins = [].slice.call(li.parentNode.children).filter(function (c) {
        return c !== li;
      });

      if (this.config.toggle && eleParentSiblins.length > 0) {
        for (var _i = 0, eleParentSiblins_1 = eleParentSiblins; _i < eleParentSiblins_1.length; _i++) {
          var sibli = eleParentSiblins_1[_i];
          var sibUl = sibli.querySelector(this.config.subMenu);

          if (sibUl !== null) {
            this.hide(sibUl);
          }
        }
      }

      this.setTransitioning(true);
      ul.classList.add(ClassNames.collapseClass);
      ul.classList.add(ClassNames.collapseInClass);
      ul.style.height = ul.scrollHeight + "px";
      this.emit("show.metisMenu", {
        showElement: ul
      });
      ul.addEventListener("transitionend", complete);
    };

    MetisMenu.prototype.hide = function (ul) {
      var _this = this;

      if (this.isTransitioning || !ul.classList.contains(ClassNames.collapseInClass)) {
        return;
      }

      this.emit("hide.metisMenu", {
        hideElement: ul
      });
      var li = ul.parentNode;
      li.classList.remove(ClassNames.activeClass);

      var complete = function complete() {
        ul.classList.remove(ClassNames.collapsingClass);
        ul.classList.add(ClassNames.collapseClass);
        ul.removeEventListener("transitionend", complete);

        _this.setTransitioning(false);

        _this.emit("hidden.metisMenu", {
          hiddenElement: ul
        });
      };

      ul.style.height = ul.getBoundingClientRect().height + "px";
      ul.style.height = ul.offsetHeight + "px";
      ul.classList.add(ClassNames.collapsingClass);
      ul.classList.remove(ClassNames.collapseClass);
      ul.classList.remove(ClassNames.collapseInClass);
      this.setTransitioning(true);
      ul.addEventListener("transitionend", complete);
      ul.style.height = "0px";
      var a$$1 = li.querySelector(this.config.triggerElement);
      a$$1.setAttribute("aria-expanded", "false");
    };

    MetisMenu.prototype.setTransitioning = function (isTransitioning) {
      this.isTransitioning = isTransitioning;
    };

    return MetisMenu;
  }();

  function _typeof(obj) {
    if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
      _typeof = function (obj) {
        return typeof obj;
      };
    } else {
      _typeof = function (obj) {
        return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
      };
    }

    return _typeof(obj);
  }

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    return Constructor;
  }

  function _slicedToArray(arr, i) {
    return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest();
  }

  function _arrayWithHoles(arr) {
    if (Array.isArray(arr)) return arr;
  }

  function _iterableToArrayLimit(arr, i) {
    var _arr = [];
    var _n = true;
    var _d = false;
    var _e = undefined;

    try {
      for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
        _arr.push(_s.value);

        if (i && _arr.length === i) break;
      }
    } catch (err) {
      _d = true;
      _e = err;
    } finally {
      try {
        if (!_n && _i["return"] != null) _i["return"]();
      } finally {
        if (_d) throw _e;
      }
    }

    return _arr;
  }

  function _nonIterableRest() {
    throw new TypeError("Invalid attempt to destructure non-iterable instance");
  }

  /*eslint no-fallthrough: "off"*/
  var callbackEvents = ['hide', 'show'];
  /**
   * Tablist constructor
   * @constructor
   * @param {Node} el - DOM node
   */

  var Tablist =
  /*#__PURE__*/
  function () {
    function Tablist(el) {
      _classCallCheck(this, Tablist);

      if (!el || !el.nodeName) {
        throw new Error('No DOM node provided. Abort.');
      }

      this.el = el;
      this._tablist = {};
      this._callbacks = {};
      this._handleDisplay = this._handleDisplay.bind(this);
      this._handleFocus = this._handleFocus.bind(this);
      this._handleTab = this._handleTab.bind(this);
      this._handlePanelFocus = this._handlePanelFocus.bind(this);
      this._handlePanel = this._handlePanel.bind(this);
    }
    /**
     * Retrieve first activable tab (that does not have `disabled` attribute)
     */


    _createClass(Tablist, [{
      key: "_firstActiveTab",
      value: function _firstActiveTab() {
        var activeTab;

        for (var i = 0; i < this._tablist.tabs.length; i++) {
          if (!this._tablist.tabs[i].disabled) {
            activeTab = i;
            break;
          }
        }

        return activeTab;
      }
      /**
       * Toggle display of the tabPanel (show/hide)
       * @param {DOMEvent} e - Can be a `MouseEvent` or a `KeyboardEvent` object
       */

    }, {
      key: "_handleDisplay",
      value: function _handleDisplay(e) {
        e.preventDefault();
        var tab = e.currentTarget;

        if (tab.disabled) {
          return;
        } // ensure the tab has the focus when a click occurs


        if (tab !== document.activeElement) {
          tab.focus();
        }

        this._toggleDisplay(this._tablist.tabs.indexOf(tab));
      }
      /**
       * Update the current tab index before selecting the current tab
       * @param {DOMEvent} e - A `FocusEvent` object
       */

    }, {
      key: "_handleFocus",
      value: function _handleFocus(e) {
        var tab = e.currentTarget;

        if (tab.disabled) {
          return;
        }

        this._tablist.currentTabIndex = this._tablist.tabs.indexOf(tab);

        this._select(this._tablist.tabs[this._tablist.currentTabIndex]);
      }
      /**
       * Handle keystroke on [role=tabpanel]
       * @param {DOMEvent} e - A `KeyboardEvent` object
       */

    }, {
      key: "_handlePanel",
      value: function _handlePanel(e) {
        if (this._tablist.currentTabIndex === undefined) {
          this._handlePanelFocus(e);
        }

        switch (e.keyCode) {
          // ctrl + page up
          case 33:
            if (e.ctrlKey) {
              e.preventDefault(); // focus the previous tab

              this._switchTab(this._tablist.currentTabIndex - 1);
            }

            break;
          // ctrl + page down

          case 34:
            if (e.ctrlKey) {
              e.preventDefault(); // focus the next tab

              this._switchTab(this._tablist.currentTabIndex + 1);
            }

            break;
          // focus back to tab
          // ctrl + up

          case 38:
            if (e.ctrlKey) {
              e.preventDefault(); // focus linked tab

              this._switchTab(this._tablist.currentTabIndex);
            }

            break;
        }
      }
      /**
       * Ensure that the current tab index is the one matching the tabPanel
       * @param {DOMEvent} e - A `FocusEvent` or `KeyboardEvent` object
       */

    }, {
      key: "_handlePanelFocus",
      value: function _handlePanelFocus(e) {
        if (e.target.doubleFocus) {
          e.preventDefault();
          delete e.target.doubleFocus;
          return;
        }

        var tabPanel = e.currentTarget;
        this._tablist.currentTabIndex = this._tablist.tabPanels.indexOf(tabPanel); // prevent double focus event when the inputs are focused

        if (['radio', 'checkbox'].indexOf(e.target.type) >= 0) {
          e.target.doubleFocus = true;
        }
      }
      /**
       * Handle keystroke on [role=tab]
       * @param {DOMEvent} e - A `KeyboardEvent` object
       */

    }, {
      key: "_handleTab",
      value: function _handleTab(e) {
        if (this._tablist.currentTabIndex === undefined) {
          this._handleFocus(e);
        }

        switch (e.keyCode) {
          // space
          case 32: // return

          case 13:
            // toggle the display of the linked tabpanel
            this._handleDisplay(e);

            break;
          // end

          case 35:
            e.preventDefault(); // focus the last tab

            this._switchTab(this._tablist.tabs.length - 1);

            break;
          // home

          case 36:
            e.preventDefault(); // focus the first active tab

            this._switchTab(this._firstActiveTab());

            break;
          // left

          case 37: // up

          case 38:
            e.preventDefault(); // focus the previous tab

            this._switchTab(this._tablist.currentTabIndex - 1);

            break;
          // right

          case 39: // down

          case 40:
            e.preventDefault(); // focus the next tab

            this._switchTab(this._tablist.currentTabIndex + 1);

            break;
        }
      }
      /**
       * Dummy function
       */

    }, {
      key: "_noop",
      value: function _noop() {}
      /**
       * Update tab selected attributes (`aria-selected`, `tabindex`)
       * based on the `tabToSelect` attribute
       * @param {DOMElement} tabToSelect - Tab element to select
       */

    }, {
      key: "_select",
      value: function _select(tabToSelect) {
        var _this = this;

        // loop on each tab
        this._tablist.tabs.forEach(function (tab, index) {
          var shouldSelect = tabToSelect === tab;
          tab.setAttribute('aria-selected', shouldSelect);
          tab.setAttribute('tabindex', shouldSelect ? 0 : -1); // only for tab to be selected

          if (shouldSelect) {
            _this._toggleDisplay(index);
          }
        });
      }
      /**
       * Move the focus to the tab based on the index
       * @param {number} index - Index of the element to focus
       */

    }, {
      key: "_switchTab",
      value: function _switchTab(index) {
        // handle disabled tab
        if (this._tablist.tabs[index] && this._tablist.tabs[index].disabled) {
          // cycling forward? Then go one item farther
          var newIndex = index > this._tablist.currentTabIndex ? index + 1 : index - 1;

          this._switchTab(newIndex);

          return;
        }

        this._tablist.currentTabIndex = index;

        if (this._tablist.currentTabIndex < this._firstActiveTab()) {
          this._tablist.currentTabIndex = this._tablist.tabsLength - 1;
        } else if (this._tablist.currentTabIndex >= this._tablist.tabsLength) {
          this._tablist.currentTabIndex = this._firstActiveTab();
        }

        this._tablist.tabs[this._tablist.currentTabIndex].focus();
      }
      /**
       * Toggle the `aria-hidden` attribute on the tabpanel based on the passed tab
       * @param {integer} index - index of the tab
       * @param {boolean} show - whether or not display the panel
       */

    }, {
      key: "_toggleDisplay",
      value: function _toggleDisplay(index) {
        var show = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

        if (show && index === this._tablist.openedIndex) {
          return;
        }

        var tab = this._tablist.tabs[index];
        var tabPanel = this._tablist.tabPanels[index]; // close the previous tab

        if (show && this._tablist.openedIndex !== undefined) {
          this._toggleDisplay(this._tablist.openedIndex, false);
        }

        tabPanel.setAttribute('aria-hidden', !show);

        if (show) {
          this._tablist.openedIndex = index;

          if (this._tablist.openedIndex !== undefined) {
            this._trigger('show', [tab, tabPanel]);
          }
        } else if (this._tablist.openedIndex !== undefined) {
          this._trigger('hide', [tab, tabPanel]);
        }
      }
    }, {
      key: "_trigger",
      value: function _trigger(eventName, params) {
        var _this2 = this;

        if (!this._callbacks[eventName]) {
          return;
        }

        this._callbacks[eventName].forEach(function (callback) {
          callback.apply(_this2, params);
        });
      }
      /**
       * Parse tablist element to setup the tab and tabpanel elements
       */

    }, {
      key: "mount",
      value: function mount() {
        var _this3 = this;

        var firstTabIndex; // create reference arrays

        this._tablist.tabs = [];
        this._tablist.tabPanels = []; // loop on each tab elements to find tabpanel elements and update their attributes

        Array.from(this.el.querySelectorAll('[role=tab]')).forEach(function (tab, index) {
          var controls = tab.getAttribute('aria-controls');
          var tabPanel;
          var openedTab = false; // get the tabpanel linked to the tab element

          if (controls) {
            tabPanel = document.getElementById(controls);
          } else if (tab.nextElementSibling && tab.nextElementSibling.getAttribute('aria-labelledby') === tab.id) {
            tabPanel = tab.nextElementSibling;
          }

          if (!tabPanel) {
            throw new Error("Could not find associated tabpanel for tab ".concat(tab.id, ". Use [aria-controls=\"tabpanelId\"] on the [role=\"tab\"] element to link them together"));
          } // store the tab and the tabpanel on their respective arrays on the tablist


          _this3._tablist.tabs.push(tab);

          _this3._tablist.tabPanels.push(tabPanel);

          tab.disabled = tab.hasAttribute('disabled') || tab.getAttribute('aria-disabled') === 'true'; // if there's no opened tab yet

          if (tab.getAttribute('data-open') === 'true' && !tab.disabled) {
            if (_this3._tablist.openedIndex === undefined) {
              _this3._toggleDisplay(index, true);

              openedTab = true;
            }
          } // remove setup data attributes


          tab.removeAttribute('data-open'); // get first non-disabled tab

          if (firstTabIndex === undefined && !tab.disabled) {
            firstTabIndex = index;
          } // set the attributes according the the openedTab status


          tab.setAttribute('tabindex', -1);
          tabPanel.setAttribute('aria-hidden', !openedTab); // subscribe internal events for tab and tap panel

          tab.addEventListener('click', _this3._handleDisplay);
          tab.addEventListener('focus', _this3._handleFocus);
          tab.addEventListener('keydown', _this3._handleTab);
          tabPanel.addEventListener('focus', _this3._handlePanelFocus, true);
          tabPanel.addEventListener('keydown', _this3._handlePanel);
        }); // store constants

        this._tablist.tabsLength = this._tablist.tabs.length;
        this._tablist.tabPanelsLength = this._tablist.tabPanels.length; // set the tabindex so the first opened tab or the first non-disabled tab can be focused on tab navigation

        if (this._tablist.openedIndex !== undefined) {
          this._tablist.tabs[this._tablist.openedIndex].setAttribute('tabindex', 0);

          this._tablist.tabs[this._tablist.openedIndex].setAttribute('aria-selected', 'true');
        } // if there's no opened tab and it's not an accordion open the first tab
        else {
            this._toggleDisplay(firstTabIndex, true);

            this._tablist.tabs[firstTabIndex].setAttribute('tabindex', 0);

            this._tablist.tabs[firstTabIndex].setAttribute('aria-selected', 'true');
          }
      }
    }, {
      key: "off",
      value: function off(event, callback) {
        if (!this._callbacks[event]) {
          return;
        }

        var callbackIndex = this._callbacks[event].indexOf(callback);

        if (callbackIndex < 0) {
          return;
        }

        this._callbacks[event].splice(callbackIndex, 1);
      }
    }, {
      key: "on",
      value: function on(event, callback) {
        if (callbackEvents.indexOf(event) < 0) {
          return;
        }

        if (!this._callbacks[event]) {
          this._callbacks[event] = [];
        }

        this._callbacks[event].push(callback);
      }
      /**
       * Returns the opened tab or array of opened tabs
       */

    }, {
      key: "unmount",

      /**
       * unbind tablist
       */
      value: function unmount() {
        var _this4 = this;

        this._tablist.tabs.forEach(function (tab, index) {
          var tabPanel = _this4._tablist.tabPanels[index]; // unsubscribe internal events for tab and tap panel

          tab.removeEventListener('click', _this4._handleDisplay);
          tab.removeEventListener('focus', _this4._handleFocus);
          tab.removeEventListener('keydown', _this4._handleTab);
          tab.removeAttribute('tabindex');
          tab.removeAttribute('aria-selected');
          tabPanel.removeEventListener('focus', _this4._handlePanelFocus, true);
          tabPanel.removeEventListener('keydown', _this4._handlePanel);
          tabPanel.setAttribute('aria-hidden', 'false');
        });

        this._tablist = {};
      }
    }, {
      key: "current",
      get: function get() {
        var tab = this._tablist.tabs[this._tablist.openedIndex];
        var tabPanel = this._tablist.tabPanels[this._tablist.openedIndex];
        return {
          tab: tab,
          tabPanel: tabPanel
        };
      }
    }]);

    return Tablist;
  }();

  /* globals jQuery, window */

  var Form = function ($) {
    return Manager.extend({
      /**
       * Initialize the Form
       */
      initialize: function initialize() {
        Manager.prototype.initialize.apply(this, arguments);
        this.box = String(this.el.getAttribute('id')).replace('cmb2-metabox-', '');
        this.navElement = this.el.querySelector('[role="tablist"]');

        if (this.navElement) {
          this._menu = new MetisMenu(this.navElement);
          this._tablist = new Tablist(this.navElement);

          this._setupTabs();
        }

        this.initControls();
      },
      initControls: function initControls() {
        var self = this;
        this.$el.find('.wplibs-form__row').each(function () {
          var element = this;
          var type = $(this).data('fieldtype');
          var isRepeatable = $(this).hasClass('cmb-repeat');

          if ('title' === type || type.substr(0, 2) === '__') {
            return '';
          }

          if ('group' === type) {
            self.initGroupControl(element);
            return;
          }

          if (!isRepeatable) {
            self.createNewControl(type, element, self);
          } else {
            $(this).find('.cmb-repeat-row').each(function () {
              self.createNewControl(type, this, self);
            });
          }

          $(element).on('cmb2_add_row', function (e) {
            var el = $(e.target).find('.cmb-repeat-row').last();
            self.createNewControl(type, el, self);
          });
        });
      },
      initGroupControl: function initGroupControl(element) {
        var self = this;
        $(element).find('.cmb-repeatable-grouping').each(function () {
          var controls = new Manager({
            el: this
          });
          $(this).find('.wplibs-form__grouprow').each(function () {
            var element = this;
            var type = $(this).data('fieldtype');
            var isRepeatable = $(this).hasClass('cmb-repeat');

            if (!isRepeatable) {
              self.createNewControl(type, element, controls);
            } else {
              $(this).find('.cmb-repeat-row').each(function () {
                self.createNewControl(type, this);
              });
            }
          });
        });
      },
      createNewControl: function createNewControl(type, root, manager) {
        var api = window.WPLibsForm || {};
        var _control = api.Controls.Control;

        if (api.Controls.hasOwnProperty(type)) {
          _control = api.Controls[type];
        }

        return new _control({
          el: root,
          manager: manager
        });
      },

      /**
       * Setup the tabs.
       *
       * @private
       */
      _setupTabs: function _setupTabs() {
        var _this = this;

        var menu = this._menu;
        var tabList = this._tablist;
        var $menu = $(this.navElement);
        var containerHeight = '';

        var setCurrentSection = function setCurrentSection(tab) {
          var section = tab.getAttribute('data-section');
          var currents = js_cookie.getJSON('_wplibs_current_sections') || {};
          currents[_this.box] = section;
          js_cookie.set('_wplibs_current_sections', currents, {
            path: ''
          });
        }; // Close showing ul element on section click


        $menu.on('click', '> [role="tab"] > a', function () {
          var ul = $menu.find('> [data-panel].active > ul')[0];

          if (typeof ul !== 'undefined') {
            setTimeout(function () {
              return menu.hide(ul);
            }, 0);
          }
        });
        menu.on('show.metisMenu', function (e) {
          var ul = e.detail.showElement; // Remove hover from parent li.

          if (ul.parentNode.classList.contains('hover')) {
            ul.parentNode.classList.remove('hover');
          }

          if (!ul.querySelector('.active')) ;
        });
        tabList.on('show', function (tab, panel) {
          setCurrentSection(tab);
          var ul = tab.parentNode;

          if (ul.classList.contains('sugar-options__submenu')) {
            menu.show(ul);
          }

          tab.classList.add('active');
          panel.classList.add('open');
          containerHeight = $(panel).outerHeight();
          $(panel.parentNode).animate({
            height: containerHeight
          }, 230, function () {
            $(panel.parentNode).height('');
          });
        });
        tabList.on('hide', function (tab, panel) {
          containerHeight = $(panel).outerHeight();
          $(panel.parentNode).height(containerHeight);
          tab.classList.remove('active');
          panel.classList.remove('open');
        });
        tabList.mount();
      }
    });
  }(jQuery);

  var setTransitionEndSupport = (function ($) {
    function getSpecialTransitionEndEvent() {
      return {
        bindType: 'transitionend',
        delegateType: 'transitionend',
        handle: function handle(event) {
          if ($(event.target).is(this)) {
            return event.handleObj.handler.apply(this, arguments);
          }

          return undefined;
        }
      };
    }

    function transitionEndEmulator(duration) {
      var _this = this;

      var called = false;
      $(this).one('srTransitionEnd', function () {
        called = true;
      });
      setTimeout(function () {
        if (!called) {
          $(_this).trigger('transitionend');
        }
      }, duration);
      return this;
    }

    function setTransitionEndSupport() {
      $.fn.srEmulateTransitionEnd = transitionEndEmulator;
      $.event.special['srTransitionEnd'] = getSpecialTransitionEndEvent();
    }

    return setTransitionEndSupport;
  })(jQuery);

  var Utils = function ($) {
    setTransitionEndSupport();
    return {
      TRANSITION_END: 'srTransitionEnd',

      /**
       * //
       *
       * @param element
       * @return {*}
       */
      getTargetFromElement: function getTargetFromElement(element) {
        var selector = element.getAttribute('data-target');

        if (!selector || selector === '#') {
          selector = element.getAttribute('href') || '';
        }

        try {
          return document.querySelector(selector) ? selector : null;
        } catch (err) {
          return null;
        }
      },

      /**
       * //
       *
       * @param element
       */
      triggerTransitionEnd: function triggerTransitionEnd(element) {
        $(element).trigger('transitionend');
      },

      /**
       * //
       *
       * @param element
       * @return {number}
       */
      getTransitionDurationFromElement: function getTransitionDurationFromElement(element) {
        if (!element) {
          return 0;
        } // Get transition-duration of the element


        var transitionDuration = $(element).css('transition-duration');
        var floatTransitionDuration = parseFloat(transitionDuration); // Return 0 if element or transition duration is not found

        if (!floatTransitionDuration) {
          return 0;
        } // If multiple durations are defined, take the first


        transitionDuration = transitionDuration.split(',')[0];
        return parseFloat(transitionDuration) * 1000;
      },

      /**
       * Return whether the supplied Event object is for a keydown event but not the Enter key.
       *
       * @param {jQuery.Event} e
       * @returns {boolean}
       */
      isKeydownButNotEnterEvent: function isKeydownButNotEnterEvent(e) {
        return 'keydown' === e.type && 13 !== e.which;
      },

      /**
       * Returns a field arguments by given ID or hash.
       *
       * @param id
       * @param box
       * @return {*}
       */
      getFieldData: function getFieldData(id, box) {
        if (!window.cmb2_l10) {
          return;
        }

        var fields = window.cmb2_l10.fields || {}; // Convert jQuery object to the Element

        if (id instanceof jQuery) {
          id = id[0];
        }

        if (_.isElement(id) && id.hasAttribute('data-hash')) {
          id = id.getAttribute('data-hash');
        } // Find by hash.


        if (fields.hasOwnProperty(id)) {
          return fields[id];
        } // Find by ID.


        return _.find(fields, function (f) {
          return f.id === id && f.box === box;
        });
      }
    };
  }(jQuery);

  var JsLogic = function () {
    function isString(string) {
      return Object.prototype.toString.call(string) === '[object String]';
    }

    function isTruthy(value) {
      if (Array.isArray(value) && value.length === 0) {
        return false;
      }

      return !!value;
    }

    var Variable =
    /*#__PURE__*/
    function () {
      function Variable(name, instance) {
        _classCallCheck(this, Variable);

        this.name = name;
        this.instance = instance;
      }

      _createClass(Variable, [{
        key: "getName",
        value: function getName() {
          return this.name;
        }
      }, {
        key: "getValue",
        value: function getValue() {
          if (this.instance.data.hasOwnProperty(this.name)) {
            return this.instance.data[this.name];
          }

          return undefined;
        }
      }]);

      return Variable;
    }();

    var JsLogic =
    /*#__PURE__*/
    function () {
      /**
       * Constructor
       *
       * @param data
       */
      function JsLogic(data) {
        _classCallCheck(this, JsLogic);

        this.data = data || {};
      }
      /**
       * //
       *
       * @param  {*} rules
       * @return {boolean}
       */


      _createClass(JsLogic, [{
        key: "apply",
        value: function apply(rules) {
          var logical = 'and';

          if (JsLogic.isProposition(rules)) {
            rules = [rules];
          } else if (JsLogic.isLogicSet(rules)) {
            logical = this.getOperator(rules);
            rules = rules[logical];
          }

          return this.testLogical(rules, logical);
        }
        /**
         * //
         *
         * @param  {*}      rules
         * @param  {string} logical
         * @return {boolean}
         */

      }, {
        key: "testLogical",
        value: function testLogical(rules, logical) {
          if (!Array.isArray(rules)) {
            rules = Object.values(rules);
          }

          if (rules.length === 0) {
            return true;
          }

          var current;

          switch (logical) {
            case 'and':
              for (var i = 0; i < rules.length; i++) {
                try {
                  current = this.testProposition(rules[i]);
                } catch (e) {
                  console.error && console.error(e);
                  continue;
                }

                if (!isTruthy(current)) {
                  return current;
                }
              }

              return current;

            case 'or':
              for (var _i = 0; _i < rules.length; _i++) {
                try {
                  current = this.testProposition(rules[_i]);
                } catch (e) {
                  console.error && console.error(e);
                  continue;
                }

                if (isTruthy(current)) {
                  return current;
                }
              }

              return current;
          }

          return true;
        }
        /**
         * //
         *
         * @param  {array} rule
         * @return {boolean}
         */

      }, {
        key: "testProposition",
        value: function testProposition(rule) {
          var _this$normalizePropos = this.normalizeProposition.apply(this, rule),
              _this$normalizePropos2 = _slicedToArray(_this$normalizePropos, 3),
              variable = _this$normalizePropos2[0],
              operator = _this$normalizePropos2[1],
              check = _this$normalizePropos2[2];

          if (check instanceof Variable) {
            check = check.getValue();
          }

          if (typeof JsLogic.operators[operator] === 'function') {
            return JsLogic.operators[operator].call(null, variable.getValue(), check);
          }

          throw new Error("Unrecognized operation ".concat(operator));
        }
        /**
         * //
         *
         * @param  {object} logic
         * @return {string}
         */

      }, {
        key: "getOperator",
        value: function getOperator(logic) {
          return Object.keys(logic)[0];
        }
        /**
         * Create a variable.
         *
         * @param  {string} name
         * @return {Variable}
         */

      }, {
        key: "newVariable",
        value: function newVariable(name) {
          return new Variable(name, this);
        }
        /**
         * //
         *
         * @param  {string}      name
         * @param  {string|null} operator
         * @param  {*}           check
         * @return {array}
         * @private
         */

      }, {
        key: "normalizeProposition",
        value: function normalizeProposition(name) {
          var operator = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
          var check = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;

          if (arguments.length === 2 && 0 !== operator.indexOf('is_')) {
            var _ref = [operator, '='];
            check = _ref[0];
            operator = _ref[1];
          } // Some operator like "=", "<=", etc. need to normalize to valid name.


          operator = this.normalizeOperator(operator); // Wrap the value as array in case "in" or "not_in".

          if (isString(check) && ['in', 'not_in'].includes(operator)) {
            check = check.split(',').map(function (item) {
              return item.trim();
            });
          } // Allow check value can be a reference value of a variable in the context.
          // This can be done with: ['some-check', '!=', '@another-check'].


          if (isString(check) && check[0] === '@') {
            check = this.newVariable(check.substr(1));
          }

          return [this.newVariable(name), operator, check];
        }
        /**
         * //
         *
         * @param  {string} operator
         * @return {string}
         * @private
         */

      }, {
        key: "normalizeOperator",
        value: function normalizeOperator(operator) {
          switch (operator) {
            case '=':
            case '==':
              return 'equal';

            case '!=':
            case '!==':
              return 'not_equal';

            case '<':
            case 'lt':
              return 'less_than';

            case '<=':
            case 'lte':
              return 'less_than_or_equal';

            case '>':
            case 'gt':
              return 'greater_than';

            case '>=':
            case 'gte':
              return 'greater_than_or_equal';

            case 'is_truthy':
              return 'is_not_empty';

            default:
              return operator;
          }
        }
      }]);

      return JsLogic;
    }();
    /**
     * //
     *
     * @param  {object} logic
     * @return {boolean}
     */


    JsLogic.isLogicSet = function (logic) {
      if (Array.isArray(logic) || logic === null) {
        return false;
      }

      return _typeof(logic) === 'object' && Object.keys(logic).length === 1;
    };
    /**
     * //
     *
     * @param  {array} rule
     * @return {boolean}
     */


    JsLogic.isProposition = function (rule) {
      return Array.isArray(rule) && rule.length >= 2 && isString(rule[0]);
    };
    /**
     * Store the operators.
     *
     * @type {*}
     */


    JsLogic.operators = {
      equal: function equal(a, b) {
        return a == b;
      },
      not_equal: function not_equal(a, b) {
        return a != b;
      },
      greater_than: function greater_than(a, b) {
        return a > b;
      },
      greater_than_or_equal: function greater_than_or_equal(a, b) {
        return a >= b;
      },
      less_than: function less_than(a, b) {
        return a < b;
      },
      less_than_or_equal: function less_than_or_equal(a, b) {
        return a <= b;
      },
      between: function between(a, b) {
        return a >= a && a <= b;
      },
      not_between: function not_between(a, b) {
        return !(a >= a && a <= b);
      },
      in: function _in(a, b) {
        return Array.isArray(b) && b.includes(a);
      },
      not_in: function not_in(a, b) {
        return Array.isArray(b) && !b.includes(a);
      },
      contains: function contains(a, b) {
        return isString(a) && a.includes(b);
      },
      not_contains: function not_contains(a, b) {
        return isString(a) && !a.includes(b);
      },
      begins_with: function begins_with(a, b) {
        return isString(a) && a.startsWith(b);
      },
      not_begins_with: function not_begins_with(a, b) {
        return isString(a) && !a.startsWith(b);
      },
      ends_with: function ends_with(a, b) {
        return isString(a) && a.endsWith(b);
      },
      not_ends_with: function not_ends_with(a, b) {
        return isString(a) && !a.endsWith(b);
      },
      is_empty: function is_empty(a) {
        return !isTruthy(a);
      },
      is_not_empty: function is_not_empty(a) {
        return isTruthy(a);
      }
    };
    return JsLogic;
  }();

  /* global jQuery, Backbone, window */
  var InputElement = function ($, Backbone, _) {
    /**
     * //
     *
     * @type {Backbone.Model}
     */
    var InputElement = Backbone.Model.extend({
      defaults: {
        id: '',
        value: ''
      },

      /**
       * Constructor
       *
       * @param element
       * @param options
       */
      constructor: function constructor(element, options) {
        this.element = $(element);
        Backbone.Model.prototype.constructor.call(this, options || {});
      },

      /**
       * Initialize two-way binding.
       */
      initialize: function initialize() {
        var _this = this;

        var events = '';
        var synchronizer = InputElement.Synchronizer.html;

        if (this.element.is('input, select, textarea')) {
          var type = this.element.prop('type');
          events += ' change input';
          synchronizer = InputElement.Synchronizer.value; // For checkbox and radio input.

          if (this.element.is('input') && InputElement.Synchronizer[type]) {
            synchronizer = InputElement.Synchronizer[type];
          }

          _.extend(this, synchronizer);
        }

        var update = this.update;
        var refresh = this.refresh;

        this.update = function (to) {
          if (to !== refresh.call(_this)) {
            update.call(_this, to);
          }
        };

        this.refresh = function () {
          _this.setValue(refresh.call(_this));
        }; // Update the input into the model.


        this.set({
          value: refresh.call(this)
        }, {
          silent: true
        }); // Two-way binding

        this.element.on(events, this.refresh);
        this.on('change:value', function (model, value) {
          return _this.update(value);
        });
      },

      /**
       * The refresh synchronizer.
       */
      refresh: function refresh() {},

      /**
       * The update synchronizer.
       */
      update: function update() {},

      /**
       * Gets the element value.
       *
       * @return {any}
       */
      getValue: function getValue() {
        return this.get('value');
      },

      /**
       * Sets the element value
       *
       * @param {any} value
       */
      setValue: function setValue(value) {
        this.set('value', value);
      },

      /**
       * Is dirty value.
       *
       * @return {boolean}
       */
      isDirty: function isDirty() {
        return this.hasChanged('value');
      }
    });
    /**
     * Store the synchronizer
     *
     * @type {object}
     */

    InputElement.Synchronizer = {};
    InputElement.Synchronizer.html = {
      update: function update(to) {
        this.element.html(to);
      },
      refresh: function refresh() {
        return this.element.html();
      }
    };
    InputElement.Synchronizer.value = {
      update: function update(to) {
        this.element.val(to);
      },
      refresh: function refresh() {
        return this.element.val();
      }
    };
    InputElement.Synchronizer.checkbox = {
      update: function update(to) {
        this.element.prop('checked', to);
      },
      refresh: function refresh() {
        return this.element.prop('checked');
      }
    };
    InputElement.Synchronizer.radio = {
      update: function update(to) {
        var _this2 = this;

        this.element.filter(function () {
          return _this2.getValue() === to;
        }).prop('checked', true);
      },
      refresh: function refresh() {
        return this.element.filter(':checked').val();
      }
    };
    return InputElement;
  }(jQuery, Backbone, window._);

  /* globals jQuery, window, wp */

  var Control = function ($, Backbone, _) {
    return Backbone.View.extend({
      /**
       * Initialize the control
       */
      initialize: function initialize(options) {
        // Add the control into the manager
        this.manager = options.manager; // Setup control settings

        this.model = new Backbone.Model(options.settings || this._getControlSettings());

        if (!this.model.has('type')) {
          this.model.set('type', this.getRowElement().data('fieldtype'));
        } // Add the control into the manager


        if (this.manager) {
          this.manager.addControl(this);
        } // Handle JS template.


        this.controlContainer = this.find('.wplibs-form__control-area')[0];

        if (this.controlContainer && document.getElementById('tmpl-' + this.getTemplateName())) {
          this.template = wp.template(this.getTemplateName());
          this.render();
        } // Store the input elements


        this.elements = {};
        this.linkElements();
        this.elementsCollect = new Backbone.Collection(Object.values(this.elements)); // Binding template render events

        if (this.template) {
          this.on('change', _.debounce(this.render, 100));
          this.listenTo(this.model, 'change', _.debounce(this.render, 100));
        } // Trigger elements changes


        if (this.manager) {
          this.listenTo(this.elementsCollect, 'change', _.debounce(this.change, 50));
          this.listenTo(this.manager, 'changes', _.debounce(this.toggleControlVisibility, 50));
        }

        this.ready();
      },

      /**
       * Triggered when the control's markup has been injected into the DOM
       */
      ready: function ready() {},

      /**
       * Link elements between settings and inputs
       */
      linkElements: function linkElements() {
        var control = this;
        var nodes = control.$el.find('[data-hash]');
        var radios = {};
        nodes.each(function (index, element) {
          var node = $(element);

          if (node.data('_controlSettingLinked')) {
            return;
          } // Prevent re-linking element.


          node.data('_controlSettingLinked', true);

          if (node.is(':radio')) {
            var name = node.prop('name');

            if (radios[name]) {
              return;
            }

            radios[name] = true;
            node = nodes.filter('[name="' + name + '"]');
          }

          if (node.data('element')) {
            index = node.data('element');
          }

          control.elements[index] = new InputElement(node, {
            id: index
          });
        });
      },

      /**
       * Trigger events when any elements changed
       */
      change: function change() {
        this.manager.trigger('changes', this);
      },

      /**
       * Render the control from its JS template, if it exists
       */
      render: function render() {
        var data = this.getTemplateData();

        if (this.template && this.controlContainer) {
          $(this.controlContainer).html(this.template(data));
        }

        return this;
      },

      /**
       * Returns the data for the template
       *
       * @return {object}
       */
      getTemplateData: function getTemplateData() {
        return this.model.toJSON();
      },

      /**
       * Toggle the control visibility
       */
      toggleControlVisibility: function toggleControlVisibility() {
        var rules = this._getDependencyRules();

        if (typeof rules === 'undefined') {
          return;
        }

        var row = this.getRowElement();
        var deps = new JsLogic(this.manager.getValues());
        var satisfied = deps.apply(rules);

        if (satisfied === this.model.get('active')) {
          return;
        }

        if (satisfied) {
          row.attr('data-satisfied', 'true');
        } else {
          row.attr('data-satisfied', 'false');
        }

        if (satisfied && $.fn.effect) {
          row.effect('highlight');
        }

        this.model.set('active', satisfied);
      },

      /**
       * Gets a control setting
       *
       * @param key
       * @return {any}
       */
      get: function get(key) {
        return this.model.get(key);
      },

      /**
       * Gets the control value
       *
       * @param element
       * @return {*}
       */
      getValue: function getValue(element) {
        if (_.isEmpty(this.elements)) {
          return null;
        }

        var _elements = Object.values(this.elements);

        if (_elements.length === 1) {
          return _elements[0].getValue();
        }

        if (element) {
          return this.elements.hasOwnProperty(element) ? this.elements[element].getValue() : undefined;
        }

        return _.mapObject(this.elements, function (e) {
          return e.getValue();
        });
      },

      /**
       * //
       *
       * @param element
       * @param value
       */
      setValue: function setValue(element, value) {
        if (typeof value === 'undefined') {
          value = element;
        }

        var _elements = Object.values(this.elements);

        if (_elements.length === 1) {
          _elements[0].setValue(value);
        }

        if (this.elements.hasOwnProperty(element)) {
          this.elements[element].setValue(value);
        }

        _.each(this.elements, function (element, key) {
          if (_.isObject(value) && value.hasOwnProperty(key)) {
            element.setValue(value[key]);
          }
        });
      },

      /**
       * Determines if the control is group field
       *
       * @return {boolean}
       */
      isGroup: function isGroup() {
        return !!this.model.get('is_group');
      },

      /**
       * Determines if the control is repeatable
       *
       * @return {boolean}
       */
      isRepeatable: function isRepeatable() {
        return this.getRowElement().hasClass('cmb-repeat');
      },

      /**
       * Find sub-element
       *
       * @param element
       * @return {JQuery}
       */
      find: function find(element) {
        return this.$el.find(element);
      },

      /**
       * Returns the row element
       *
       * @return {JQuery}
       */
      getRowElement: function getRowElement() {
        return this.$el.closest('.wplibs-form__grouprow, .wplibs-form__row');
      },

      /**
       * Returns the form element
       *
       * @return {jQuery}
       */
      getFormElement: function getFormElement() {
        return this.$el.closest('form');
      },

      /**
       * Returns the template name
       *
       * @return {string}
       */
      getTemplateName: function getTemplateName() {
        return 'wplibs-field-' + this.model.get('type') + '-content';
      },

      /**
       * //
       *
       * @return {Array|undefined}
       * @private
       */
      _getDependencyRules: function _getDependencyRules() {
        var rules = this.model.get('deps');

        if (!rules) {
          try {
            rules = JSON.parse(this.getRowElement().data('deps'));
          } catch (e) {
            return undefined;
          }
        }

        return JsLogic.isLogicSet(rules) || JsLogic.isProposition(rules) ? rules : undefined;
      },

      /**
       * Returns the control settings
       *
       * @return {Object}
       * @private
       */
      _getControlSettings: function _getControlSettings() {
        var row = this.getRowElement();
        var settings = Utils.getFieldData(row[0]) || {};

        var _settings = row.data('settings');

        if (_.isObject(_settings) && _.isEmpty(_settings)) {
          return _.extend(settings, _settings);
        }

        return settings;
      }
    });
  }(jQuery, window.Backbone, window._);

  var MediaControl = function ($) {
    return Control.extend({
      events: {
        'click .upload-button': 'openFrame',
        'click .change-button': 'openFrame',
        'click .thumbnail-image img': 'openFrame',
        'click .default-button': 'restoreDefault',
        'click .remove-button': 'removeFile'
      },
      openFrame: function openFrame(e) {
        if (Utils.isKeydownButNotEnterEvent(e)) {
          return;
        }

        e.preventDefault();

        if (!this.frame) {
          this.initFrame();
        }

        this.frame.open();
      },
      initFrame: function initFrame() {
        this.frame = wp.media({
          button: {
            text: 'Select'
          },
          states: [new wp.media.controller.Library({
            title: 'Title',
            library: wp.media.query({
              type: 'image'
            }),
            multiple: false,
            date: false
          })]
        }); // When a file is selected, run a callback.

        this.frame.on('select', this.select.bind(this));
      },
      select: function select() {
        this.model.set('attachment', this.frame.state().get('selection').first().toJSON());
        this.setValue(this.model.get('attachment').id);
      },
      removeFile: function removeFile(e) {
        if (Utils.isKeydownButNotEnterEvent(e)) {
          return;
        }

        e.preventDefault();
        this.setValue(null);
        this.model.set('attachment', undefined);
      },
      restoreDefault: function restoreDefault(e) {
        if (Utils.isKeydownButNotEnterEvent(e)) {
          return;
        }

        e.preventDefault(); // this.params.attachment = this.params.defaultAttachment
        // this.setting(this.params.defaultAttachment.url)
      }
    });
  }(jQuery);

  var ImageControl = function ($) {
    return MediaControl.extend({});
  }(jQuery);

  /* global wp.media */

  var CroppedImageControl = function ($) {
    return ImageControl.extend({
      /**
       * Open the media modal to the library state.
       */
      openFrame: function openFrame(e) {
        if (Utils.isKeydownButNotEnterEvent(e)) {
          return;
        }

        this.initFrame();
        this.frame.setState('library').open();
      },

      /**
       * Create a media modal select frame, and store it so the instance can be reused when needed.
       */
      initFrame: function initFrame() {
        var l10n = window._wpMediaViewsL10n || {};
        var labels = this.model.get('labels');
        this.frame = wp.media({
          button: {
            text: l10n.select,
            close: false
          },
          states: [new wp.media.controller.Library({
            title: labels.frame_title,
            library: wp.media.query({
              type: 'image'
            }),
            multiple: false,
            date: false,
            priority: 20,
            suggestedWidth: this.model.get('width'),
            suggestedHeight: this.model.get('height')
          }), new wp.media.controller.Cropper({
            imgSelectOptions: this.calculateImageSelectOptions,
            control: this
          })]
        });
        this.frame.on('select', this.onSelect, this);
        this.frame.on('cropped', this.onCropped, this);
        this.frame.on('skippedcrop', this.onSkippedCrop, this);
      },

      /**
       * After an image is selected in the media modal, switch to the cropper
       * state if the image isn't the right size.
       */
      onSelect: function onSelect() {
        var attachment = this.frame.state().get('selection').first().toJSON();

        if (this.model.get('width') === attachment.width && this.model.get('height') === attachment.height && !this.model.get('flex_width') && !this.model.get('flex_height')) {
          this.setImageFromAttachment(attachment);
          this.frame.close();
        } else {
          this.frame.setState('cropper');
        }
      },

      /**
       * After the image has been cropped, apply the cropped image data to the setting.
       *
       * @param {object} croppedImage Cropped attachment data.
       */
      onCropped: function onCropped(croppedImage) {
        this.setImageFromAttachment(croppedImage);
      },

      /**
       * Returns a set of options, computed from the attached image data and
       * control-specific data, to be fed to the imgAreaSelect plugin in
       * wp.media.view.Cropper.
       *
       * @param {wp.media.model.Attachment} attachment
       * @param {wp.media.controller.Cropper} controller
       * @returns {Object} Options
       */
      calculateImageSelectOptions: function calculateImageSelectOptions(attachment, controller) {
        var control = controller.get('control'),
            flexWidth = !!parseInt(control.model.get('flex_width'), 10),
            flexHeight = !!parseInt(control.model.get('flex_height'), 10),
            realWidth = attachment.get('width'),
            realHeight = attachment.get('height'),
            xInit = parseInt(control.model.get('width'), 10),
            yInit = parseInt(control.model.get('height'), 10),
            ratio = xInit / yInit,
            xImg = xInit,
            yImg = yInit,
            x1,
            y1,
            imgSelectOptions;
        controller.set('canSkipCrop', !control.mustBeCropped(flexWidth, flexHeight, xInit, yInit, realWidth, realHeight));

        if (realWidth / realHeight > ratio) {
          yInit = realHeight;
          xInit = yInit * ratio;
        } else {
          xInit = realWidth;
          yInit = xInit / ratio;
        }

        x1 = (realWidth - xInit) / 2;
        y1 = (realHeight - yInit) / 2;
        imgSelectOptions = {
          handles: true,
          keys: true,
          instance: true,
          persistent: true,
          imageWidth: realWidth,
          imageHeight: realHeight,
          minWidth: xImg > xInit ? xInit : xImg,
          minHeight: yImg > yInit ? yInit : yImg,
          x1: x1,
          y1: y1,
          x2: xInit + x1,
          y2: yInit + y1
        };

        if (flexHeight === false && flexWidth === false) {
          imgSelectOptions.aspectRatio = xInit + ':' + yInit;
        }

        if (true === flexHeight) {
          delete imgSelectOptions.minHeight;
          imgSelectOptions.maxWidth = realWidth;
        }

        if (true === flexWidth) {
          delete imgSelectOptions.minWidth;
          imgSelectOptions.maxHeight = realHeight;
        }

        return imgSelectOptions;
      },

      /**
       * Return whether the image must be cropped, based on required dimensions.
       *
       * @param {bool} flexW
       * @param {bool} flexH
       * @param {int}  dstW
       * @param {int}  dstH
       * @param {int}  imgW
       * @param {int}  imgH
       * @return {bool}
       */
      mustBeCropped: function mustBeCropped(flexW, flexH, dstW, dstH, imgW, imgH) {
        if (true === flexW && true === flexH) {
          return false;
        }

        if (true === flexW && dstH === imgH) {
          return false;
        }

        if (true === flexH && dstW === imgW) {
          return false;
        }

        if (dstW === imgW && dstH === imgH) {
          return false;
        }

        return imgW > dstW;
      },

      /**
       * If cropping was skipped, apply the image data directly to the setting.
       */
      onSkippedCrop: function onSkippedCrop() {
        var attachment = this.frame.state().get('selection').first().toJSON();
        this.setImageFromAttachment(attachment);
      },

      /**
       * Updates the setting and re-renders the control UI.
       *
       * @param {object} attachment
       */
      setImageFromAttachment: function setImageFromAttachment(attachment) {
        this.model.set('attachment', attachment);
        this.setValue(attachment.id);
      }
    });
  }(jQuery);

  var TypographyControl = function ($, Suru) {
    return Control.extend({
      ready: function ready() {
        console.log(this.elements);
        var weight = this.find('[data-element="weight"]').selectize({
          valueField: 'id',
          labelField: 'name',
          searchField: ['name']
        });
        this.find('[data-element="family"]').selectize({
          options: Suru.webfonts.getGoogleFonts(),
          optgroups: [],
          valueField: 'family',
          labelField: 'family',
          searchField: ['name'],
          onChange: function onChange(value) {
            var data = this.options[value];

            if (weight[0] && weight[0].selectize) {
              var s = weight[0].selectize;
              s.clearOptions();
              s.load(function (callback) {
                callback(Suru.webfonts.toVariants(data.variants));
              });
            }
          }
        });
      }
    });
  }(jQuery, window.SuruLibs || {});

  /* global jQuery, _ */

  var DimensionsControl = function ($) {
    var Dimensions = ['top', 'right', 'bottom', 'left'];
    return Control.extend({
      events: {
        'click [data-toggle="link"]': 'onLinkDimensionsClicked',
        'change [type="text"],[type="number"]': 'onInputChange'
      },
      ready: function ready() {
        this.linkElement = this.find('[data-toggle="link"]');

        if (!this.isLinkedDimensions()) {
          this.linkElement.addClass('unlinked');
        }

        this.fillEmptyDimensions();
      },
      fillEmptyDimensions: function fillEmptyDimensions() {
        var _this = this;

        if (this.isLinkedDimensions()) {
          return;
        }

        _.each(this.geDimensionElements(), function (element) {
          if (!element.getValue()) {
            element.setValue(_this.model.get('default') || 0);
          }
        });
      },

      /**
       * //
       *
       * @param e
       */
      onInputChange: function onInputChange(e) {
        var el = $(e.target);
        var inputSetting = el.data('element');

        if ('unit' === inputSetting) {
          this.setDimensionsValue('');
          return;
        }

        if (!Dimensions.includes(inputSetting)) {
          return;
        }

        if (this.isLinkedDimensions()) {
          this.setDimensionsValue(el.val());
        }
      },

      /**
       * //
       *
       * @param e
       */
      onLinkDimensionsClicked: function onLinkDimensionsClicked(e) {
        e.preventDefault();
        e.stopPropagation();
        var elements = this.geDimensionElements();
        this.linkElement.toggleClass('unlinked');
        this.elements.linked.setValue(this.linkElement.hasClass('unlinked') ? 0 : 1);

        if (this.isLinkedDimensions()) {
          var value = Object.values(elements)[0].getValue();
          this.setDimensionsValue(value);
        }
      },

      /**
       * Sets dimensions value.
       *
       * @param value
       */
      setDimensionsValue: function setDimensionsValue(value) {
        _.each(this.geDimensionElements(), function (element) {
          element.setValue(value);
        });
      },

      /**
       * Returns the dimensions elements.
       */
      geDimensionElements: function geDimensionElements() {
        var _this2 = this;

        var elements = {};

        _.each(Dimensions, function (name) {
          if (_this2.elements.hasOwnProperty(name)) {
            elements[name] = _this2.elements[name];
          }
        });

        return elements;
      },

      /**
       * Is inputs is linked
       *
       * @return {boolean}
       */
      isLinkedDimensions: function isLinkedDimensions() {
        return !!parseInt(this.getValue('linked'), 10);
      },
      filterDimensions: function filterDimensions() {
        var filter = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'all';

        if ('all' === filter || '*' == filter) {
          return Dimensions;
        }

        if (!Array.isArray(filter)) {
          if ('horizontal' === filter) {
            return ['right', 'left'];
          } else if ('vertical' === filter) {
            return ['top', 'bottom'];
          }
        }

        return filter;
      }
    });
  }(jQuery);

  /* global jQuery, window */

  var LinkControl = function ($, wpLink) {
    return Control.extend({
      events: {
        'click .js-open-link': 'openLinkDialog',
        'click .js-toggle-link-options': 'toggleOptions'
      },
      ready: function ready() {
        this.find('textarea').on('change', this.onLinkSelect.bind(this));
      },
      onLinkSelect: function onLinkSelect(e) {
        var atts = wpLink.getAttrs();
        this.setValue('url', atts.href);
        this.setValue('is_external', !!atts.target); // Empty the value of textarea.

        e.currentTarget.value = '';
      },
      openLinkDialog: function openLinkDialog(e) {
        e.preventDefault();
        var textarea = this.find('textarea');
        wpLink.open(textarea.attr('id'));
      },
      toggleOptions: function toggleOptions(e) {
        e.preventDefault();
        this.find('.wplibs-control-link__options').toggle();
      }
    });
  }(jQuery, window.wpLink || {});

  /* global wpLink */

  var SliderControl = function ($) {
    return Control.extend({
      ready: function ready() {
        this.$el.find('.cmb2-ui-slider-input').each(function () {
          var $input = $(this);

          if ($input.closest('.empty-row').length) {
            return;
          }

          var $text = $input.parent().find('.cmb2-ui-slider-preview');
          var $range = $input.parent().find('.cmb2-ui-slider'); // Setup jQuery UI Slider.

          var rangeSlider = $range.slider({
            range: 'min',
            min: $input.data('min'),
            max: $input.data('max'),
            step: $input.data('step'),
            value: $input.data('value'),
            animate: true,
            slide: function slide(e, ui) {
              syncInputValue(ui.value);
            }
          }); // Enable pips ui float.

          if ($input.data('float') && _.isObject($input.data('float'))) {
            rangeSlider.slider('float', $input.data('float'));
          } // Enable pips ui.


          if ($input.data('pips') && _.isObject($input.data('pips'))) {
            rangeSlider.slider('pips', $input.data('pips'));
          }

          var syncInputValue = function syncInputValue(value) {
            $text.text(value);
            $input.val(value).trigger('change');
          };

          var syncInputRange = function syncInputRange() {
            var inputValue = parseInt($(this).val());
            rangeSlider.slider('value', inputValue); // Fallback invalid value.

            if (rangeSlider.slider('value') !== inputValue) {
              $(this).val(rangeSlider.slider('value'));
            }
          }; // Initiate the display.


          syncInputValue(rangeSlider.slider('value'));
          $input.on('change blur', syncInputRange);
        });
      }
    });
  }(jQuery);

  /* global jQuery, window */
  /**
   * Store the form instances.
   */

  window.WPLibsForm = {};

  (function ($, api) {
    /**
     * Control field types
     *
     * @type {{}}
     */
    api.Controls = {};
    api.Controls.Control = Control;
    api.Controls.link = LinkControl;
    api.Controls.slider = SliderControl;
    api.Controls.dimensions = DimensionsControl;
    api.Controls.typography = TypographyControl;
    api.Controls.media = MediaControl;
    api.Controls.image = ImageControl;
    api.Controls.cropped_image = CroppedImageControl;
    /**
     * Store the form instances
     *
     * @type {Array}
     */

    api.instances = [];
    /**
     * Show the confirm message.
     *
     * @return {SweetAlert}
     */

    api.confirm = function (message, callback) {
      if (!window.swal) {
        return window.confirm(message || this.i18n.warning) && callback();
      }

      var confirm = window.swal({
        text: message || this.i18n.warning,
        customClass: 'suru-confirm-dialog',
        position: 'center',
        animation: false,
        backdrop: 'rgba(0,0,0,.8)',
        reverseButtons: true,
        buttonsStyling: false,
        showCancelButton: true,
        cancelButtonClass: '',
        confirmButtonClass: '',
        cancelButtonText: this.i18n.cancel,
        confirmButtonText: this.i18n.ok
      });

      if (callback) {
        return confirm.then(function (result) {
          if (result.value) callback(result);
        });
      }

      return confirm;
    };

    $(function () {
      // Init the selectize
      $('select.wplibs-selectize').selectize({
        allowEmptyOption: true,
        searchField: ['value', 'text']
      }); // Init the form controls

      $('.wplibs-box > .cmb2-metabox').each(function (index, element) {
        if (element._wpLibsForm) {
          return;
        }

        var instance = new Form({
          el: element
        });
        api.instances.push(instance);
        element._wpLibsForm = instance;
      });
    });
  })(jQuery, window.WPLibsForm);

}());

//# sourceMappingURL=form.js.map
