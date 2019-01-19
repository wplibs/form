/* globals jQuery, window, wp */

import Utils from '../utils'
import JsLogic from '../utils/jslogic'
import InputElement from './element'

const Control = (function ($, Backbone, _) {
  return Backbone.View.extend({
    /**
     * Initialize the control
     */
    initialize(options) {
      // Add the control into the manager
      this.manager = options.manager

      // Setup control settings
      this.model = new Backbone.Model(
        options.settings || this._getControlSettings()
      )

      if (!this.model.has('type')) {
        this.model.set('type', this.getRowElement().data('fieldtype'))
      }

      // Add the control into the manager
      if (this.manager) {
        this.manager.addControl(this)
      }

      // Handle JS template.
      this.controlContainer = this.find('.wplibs-form__control-area')[0]

      if (this.controlContainer && document.getElementById('tmpl-' + this.getTemplateName())) {
        this.template = wp.template(this.getTemplateName())
        this.render()
      }

      // Store the input elements
      this.elements = {}
      this.linkElements()
      this.elementsCollect = new Backbone.Collection(Object.values(this.elements))

      // Binding template render events
      if (this.template) {
        this.on('change', _.debounce(this.render, 100))
        this.listenTo(this.model, 'change', _.debounce(this.render, 100))
      }

      // Trigger elements changes
      if (this.manager) {
        this.listenTo(this.elementsCollect, 'change', _.debounce(this.change, 50))
        this.listenTo(this.manager, 'changes', _.debounce(this.toggleControlVisibility, 50))
      }

      this.ready()
    },

    /**
     * Triggered when the control's markup has been injected into the DOM
     */
    ready() {},

    /**
     * Link elements between settings and inputs
     */
    linkElements() {
      const control = this

      const nodes = control.$el.find('[data-hash]')
      let radios = {}

      nodes.each((index, element) => {
        let node = $(element)

        if (node.data('_controlSettingLinked')) {
          return
        }

        // Prevent re-linking element.
        node.data('_controlSettingLinked', true)

        if (node.is(':radio')) {
          let name = node.prop('name')

          if (radios[name]) {
            return
          }

          radios[name] = true
          node = nodes.filter('[name="' + name + '"]')
        }

        if (node.data('element')) {
          index = node.data('element')
        }

        control.elements[index] = new InputElement(node, { id: index })
      })
    },

    /**
     * Trigger events when any elements changed
     */
    change() {
      this.manager.trigger('changes', this)
    },

    /**
     * Render the control from its JS template, if it exists
     */
    render() {
      const data = this.getTemplateData()

      if (this.template && this.controlContainer) {
        $(this.controlContainer).html(this.template(data))
      }

      return this
    },

    /**
     * Returns the data for the template
     *
     * @return {object}
     */
    getTemplateData() {
      return this.model.toJSON()
    },

    /**
     * Toggle the control visibility
     */
    toggleControlVisibility() {
      const rules = this._getDependencyRules()

      if (typeof rules === 'undefined') {
        return
      }

      const row = this.getRowElement()
      const deps = new JsLogic(this.manager.getValues())

      const satisfied = deps.apply(rules)
      if (satisfied === this.model.get('active')) {
        return
      }

      if (satisfied) {
        row.attr('data-satisfied', 'true')
      } else {
        row.attr('data-satisfied', 'false')
      }

      if (satisfied && $.fn.effect) {
        row.effect('highlight')
      }

      this.model.set('active', satisfied)
    },

    /**
     * Gets a control setting
     *
     * @param key
     * @return {any}
     */
    get(key) {
      return this.model.get(key)
    },

    /**
     * Gets the control value
     *
     * @param element
     * @return {*}
     */
    getValue(element) {
      if (_.isEmpty(this.elements)) {
        return null
      }

      const _elements = Object.values(this.elements)
      if (_elements.length === 1) {
        return _elements[0].getValue()
      }

      if (element) {
        return this.elements.hasOwnProperty(element) ? this.elements[element].getValue() : undefined
      }

      return _.mapObject(this.elements, (e) => e.getValue())
    },

    /**
     * //
     *
     * @param element
     * @param value
     */
    setValue(element, value) {
      if (typeof value === 'undefined') {
        value = element
      }

      const _elements = Object.values(this.elements)

      if (_elements.length === 1) {
        _elements[0].setValue(value)
      }

      if (this.elements.hasOwnProperty(element)) {
        this.elements[element].setValue(value)
      }

      _.each(this.elements, (element, key) => {
        if (_.isObject(value) && value.hasOwnProperty(key)) {
          element.setValue(value[key])
        }
      })
    },

    /**
     * Determines if the control is group field
     *
     * @return {boolean}
     */
    isGroup() {
      return !!this.model.get('is_group')
    },

    /**
     * Determines if the control is repeatable
     *
     * @return {boolean}
     */
    isRepeatable() {
      return this.getRowElement().hasClass('cmb-repeat')
    },

    /**
     * Find sub-element
     *
     * @param element
     * @return {JQuery}
     */
    find(element) {
      return this.$el.find(element)
    },

    /**
     * Returns the row element
     *
     * @return {JQuery}
     */
    getRowElement() {
      return this.$el.closest('.wplibs-form__grouprow, .wplibs-form__row')
    },

    /**
     * Returns the form element
     *
     * @return {jQuery}
     */
    getFormElement() {
      return this.$el.closest('form')
    },

    /**
     * Returns the template name
     *
     * @return {string}
     */
    getTemplateName() {
      return 'wplibs-field-' + this.model.get('type') + '-content'
    },

    /**
     * //
     *
     * @return {Array|undefined}
     * @private
     */
    _getDependencyRules() {
      let rules = this.model.get('deps')

      if (!rules) {
        try {
          rules = JSON.parse(this.getRowElement().data('deps'))
        } catch (e) {
          return undefined
        }
      }

      return (JsLogic.isLogicSet(rules) || JsLogic.isProposition(rules)) ? rules : undefined
    },

    /**
     * Returns the control settings
     *
     * @return {Object}
     * @private
     */
    _getControlSettings() {
      const row = this.getRowElement()

      let settings = Utils.getFieldData(row[0]) || {}
      const _settings = row.data('settings')

      if (_.isObject(_settings) && _.isEmpty(_settings)) {
        return _.extend(settings, _settings)
      }

      return settings
    }
  })
})(jQuery, window.Backbone, window._)

export default Control
