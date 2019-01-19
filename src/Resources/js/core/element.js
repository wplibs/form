/* global jQuery, Backbone, window */

const InputElement = (function ($, Backbone, _) {
  /**
   * //
   *
   * @type {Backbone.Model}
   */
  const InputElement = Backbone.Model.extend({
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
    constructor(element, options) {
      this.element = $(element)
      Backbone.Model.prototype.constructor.call(this, options || {})
    },

    /**
     * Initialize two-way binding.
     */
    initialize() {
      let events = ''
      let synchronizer = InputElement.Synchronizer.html

      if (this.element.is('input, select, textarea')) {
        let type = this.element.prop('type')

        events += ' change input'
        synchronizer = InputElement.Synchronizer.value

        // For checkbox and radio input.
        if (this.element.is('input') && InputElement.Synchronizer[type]) {
          synchronizer = InputElement.Synchronizer[type]
        }

        _.extend(this, synchronizer)
      }

      const update = this.update
      const refresh = this.refresh

      this.update = (to) => {
        if (to !== refresh.call(this)) {
          update.call(this, to)
        }
      }

      this.refresh = () => {
        this.setValue(refresh.call(this))
      }

      // Update the input into the model.
      this.set({
        value: refresh.call(this)
      }, { silent: true })

      // Two-way binding
      this.element.on(events, this.refresh)
      this.on('change:value', (model, value) => this.update(value))
    },

    /**
     * The refresh synchronizer.
     */
    refresh() {},

    /**
     * The update synchronizer.
     */
    update() {},

    /**
     * Gets the element value.
     *
     * @return {any}
     */
    getValue() {
      return this.get('value')
    },

    /**
     * Sets the element value
     *
     * @param {any} value
     */
    setValue(value) {
      this.set('value', value)
    },

    /**
     * Is dirty value.
     *
     * @return {boolean}
     */
    isDirty() {
      return this.hasChanged('value')
    }
  })

  /**
   * Store the synchronizer
   *
   * @type {object}
   */
  InputElement.Synchronizer = {}

  InputElement.Synchronizer.html = {
    update(to) {
      this.element.html(to)
    },

    refresh() {
      return this.element.html()
    }
  }

  InputElement.Synchronizer.value = {
    update(to) {
      this.element.val(to)
    },

    refresh() {
      return this.element.val()
    }
  }

  InputElement.Synchronizer.checkbox = {
    update(to) {
      this.element.prop('checked', to)
    },

    refresh() {
      return this.element.prop('checked')
    }
  }

  InputElement.Synchronizer.radio = {
    update(to) {
      this.element.filter(() => this.getValue() === to).prop('checked', true)
    },

    refresh() {
      return this.element.filter(':checked').val()
    }
  }

  return InputElement
})(jQuery, Backbone, window._)

export default InputElement
