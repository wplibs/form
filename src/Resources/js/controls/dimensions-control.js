/* global jQuery, _ */

import Control from '../core/control'

const DimensionsControl = (function ($) {
  const Dimensions = ['top', 'right', 'bottom', 'left']

  return Control.extend({
    events: {
      'click [data-toggle="link"]': 'onLinkDimensionsClicked',
      'change [type="text"],[type="number"]': 'onInputChange',
    },

    ready() {
      this.linkElement = this.find('[data-toggle="link"]')

      if (!this.isLinkedDimensions()) {
        this.linkElement.addClass('unlinked')
      }

      this.fillEmptyDimensions();
    },

    fillEmptyDimensions() {
      if (this.isLinkedDimensions()) {
        return
      }

      _.each(this.geDimensionElements(), (element) => {
        if (!element.getValue()) {
          element.setValue(this.model.get('default') || 0)
        }
      })
    },

    /**
     * //
     *
     * @param e
     */
    onInputChange(e) {
      const el = $(e.target)
      const inputSetting = el.data('element')

      if ('unit' === inputSetting) {
        this.setDimensionsValue('')
        return
      }

      if (!Dimensions.includes(inputSetting)) {
        return
      }

      if (this.isLinkedDimensions()) {
        this.setDimensionsValue(el.val())
      }
    },

    /**
     * //
     *
     * @param e
     */
    onLinkDimensionsClicked(e) {
      e.preventDefault()
      e.stopPropagation()

      const elements = this.geDimensionElements()

      this.linkElement.toggleClass('unlinked')
      this.elements.linked.setValue(this.linkElement.hasClass('unlinked') ? 0 : 1)

      if (this.isLinkedDimensions()) {
        let value = Object.values(elements)[0].getValue()
        this.setDimensionsValue(value)
      }
    },

    /**
     * Sets dimensions value.
     *
     * @param value
     */
    setDimensionsValue(value) {
      _.each(this.geDimensionElements(), (element) => {
        element.setValue(value)
      })
    },

    /**
     * Returns the dimensions elements.
     */
    geDimensionElements() {
      const elements = {}

      _.each(Dimensions, (name) => {
        if (this.elements.hasOwnProperty(name)) {
          elements[name] = this.elements[name]
        }
      })

      return elements
    },

    /**
     * Is inputs is linked
     *
     * @return {boolean}
     */
    isLinkedDimensions: function () {
      return !!parseInt(this.getValue('linked'), 10)
    },

    filterDimensions(filter = 'all') {
      if ('all' === filter || '*' == filter) {
        return Dimensions
      }

      if (!Array.isArray(filter)) {
        if ('horizontal' === filter) {
          return ['right', 'left']
        } else if ('vertical' === filter) {
          return ['top', 'bottom']
        }
      }

      return filter
    },
  })
})(jQuery)

export default DimensionsControl
