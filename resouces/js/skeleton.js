//=require vendor/deps.js

const Emitter = require('component-emitter');

window.Skeleton = window.Skeleton || {};

(function($, S) {
  'use strict';

  S.emitter = new Emitter;

  /**
   * Extendable Skeleton.
   *
   * @param  {Object} args
   * @return {void}
   */
  S.extend = function(args) { _.extend(this, args); };

  /**
   * Core methods.
   */
  S.extend({
    services: {},
    initialized: false,

    bind: function(key, cb) {
      this.services[key] = cb;
    },

    /**
     * Listen on the given `event` with `fn`.
     *
     * @param  {String} event
     * @param  {Function} fn
     * @return {Emitter}
     */
    on: function(event, fn) {
      return this.emitter.on(event, fn);
    },

    /**
     * Init Skelton.
     *
     * @return {void}
     */
    init: function() {
      this.emitter.emit('initialize', this);

      // console.log(this.services);

      this.initialized = true;
      this.emitter.emit('initialized', this);
    },
  });

  /**
   * Let's start!
   */
  $(function() {
    window.Skeleton.init();
  });

})(jQuery, window.Skeleton);
