import Utils from '../utils'
import Control from '../core/control'

const MediaControl = (function ($) {
  return Control.extend({
    events: {
      'click .upload-button': 'openFrame',
      'click .change-button': 'openFrame',
      'click .thumbnail-image img': 'openFrame',
      'click .default-button': 'restoreDefault',
      'click .remove-button': 'removeFile',
    },

    openFrame(e) {
      if (Utils.isKeydownButNotEnterEvent(e)) {
        return
      }

      e.preventDefault()

      if (!this.frame) {
        this.initFrame()
      }

      this.frame.open()
    },

    initFrame() {
      this.frame = wp.media({
        button: {
          text: 'Select'
        },
        states: [
          new wp.media.controller.Library({
            title: 'Title',
            library: wp.media.query({ type: 'image' }),
            multiple: false,
            date: false
          })
        ]
      })

      // When a file is selected, run a callback.
      this.frame.on('select', this.select.bind(this))
    },

    select() {
      this.model.set('attachment', this.frame.state().get('selection').first().toJSON())
      this.setValue(this.model.get('attachment').id)
    },

    removeFile(e) {
      if (Utils.isKeydownButNotEnterEvent(e)) {
        return
      }

      e.preventDefault()

      this.setValue(null)
      this.model.set('attachment', undefined)
    },

    restoreDefault(e) {
      if (Utils.isKeydownButNotEnterEvent(e)) {
        return
      }

      e.preventDefault()

      // this.params.attachment = this.params.defaultAttachment
      // this.setting(this.params.defaultAttachment.url)
    }
  })
})(jQuery)

export default MediaControl
