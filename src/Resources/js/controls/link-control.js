/* global jQuery, window */

import Control from '../core/control'

const LinkControl = (function ($, wpLink) {
  return Control.extend({
    events: {
      'click .js-open-link': 'openLinkDialog',
      'click .js-toggle-link-options': 'toggleOptions',
    },

    ready() {
      this.find('textarea').on('change', this.onLinkSelect.bind(this))
    },

    onLinkSelect(e) {
      const atts = wpLink.getAttrs()

      this.setValue('url', atts.href)
      this.setValue('is_external', !!atts.target)

      // Empty the value of textarea.
      e.currentTarget.value = ''
    },

    openLinkDialog(e) {
      e.preventDefault()
      const textarea = this.find('textarea')
      wpLink.open(textarea.attr('id'))
    },

    toggleOptions(e) {
      e.preventDefault()
      this.find('.wplibs-control-link__options').toggle()
    }
  })
})(jQuery, window.wpLink || {})

export default LinkControl
