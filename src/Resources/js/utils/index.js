import setTransitionEndSupport from './transitionend'

const Utils = (($) => {
  setTransitionEndSupport()

  return {
    TRANSITION_END: 'srTransitionEnd',

    /**
     * //
     *
     * @param element
     * @return {*}
     */
    getTargetFromElement(element) {
      let selector = element.getAttribute('data-target')

      if (!selector || selector === '#') {
        selector = element.getAttribute('href') || ''
      }

      try {
        return document.querySelector(selector) ? selector : null
      } catch (err) {
        return null
      }
    },

    /**
     * //
     *
     * @param element
     */
    triggerTransitionEnd(element) {
      $(element).trigger('transitionend')
    },

    /**
     * //
     *
     * @param element
     * @return {number}
     */
    getTransitionDurationFromElement(element) {
      if (!element) {
        return 0
      }

      // Get transition-duration of the element
      let transitionDuration = $(element).css('transition-duration')
      const floatTransitionDuration = parseFloat(transitionDuration)

      // Return 0 if element or transition duration is not found
      if (!floatTransitionDuration) {
        return 0
      }

      // If multiple durations are defined, take the first
      transitionDuration = transitionDuration.split(',')[0]

      return parseFloat(transitionDuration) * 1000
    },

    /**
     * Return whether the supplied Event object is for a keydown event but not the Enter key.
     *
     * @param {jQuery.Event} e
     * @returns {boolean}
     */
    isKeydownButNotEnterEvent(e) {
      return ('keydown' === e.type && 13 !== e.which)
    },

    /**
     * Returns a field arguments by given ID or hash.
     *
     * @param id
     * @param box
     * @return {*}
     */
    getFieldData(id, box) {
      if (!window.cmb2_l10) {
        return
      }

      const fields = window.cmb2_l10.fields || {}

      // Convert jQuery object to the Element
      if (id instanceof jQuery) {
        id = id[0]
      }

      if (_.isElement(id) && id.hasAttribute('data-hash')) {
        id = id.getAttribute('data-hash')
      }

      // Find by hash.
      if (fields.hasOwnProperty(id)) {
        return fields[id]
      }

      // Find by ID.
      return _.find(fields, (f) => f.id === id && f.box === box)
    }
  }
})(jQuery)

export default Utils
