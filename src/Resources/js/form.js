/* global jQuery, window */

import Form from './core/form'
import Control from './core/control'
import MediaControl from './controls/media-control'
import ImageControl from './controls/image-control'
import CroppedImageControl from './controls/cropped-image-control'
import TypographyControl from './controls/typography-control'
import DimensionsControl from './controls/dimensions-control'
import LinkControl from './controls/link-control'
import SliderControl from './controls/slider-control'

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
  api.Controls = {}
  api.Controls.Control = Control

  api.Controls.link = LinkControl
  api.Controls.slider = SliderControl
  api.Controls.dimensions = DimensionsControl
  api.Controls.typography = TypographyControl
  api.Controls.media = MediaControl
  api.Controls.image = ImageControl
  api.Controls.cropped_image = CroppedImageControl

  /**
   * Store the form instances
   *
   * @type {Array}
   */
  api.instances = []

  /**
   * Show the confirm message.
   *
   * @return {SweetAlert}
   */
  api.confirm = function (message, callback) {
    if (!window.swal) {
      return window.confirm(message || this.i18n.warning) && callback()
    }

    const confirm = window.swal({
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
      confirmButtonText: this.i18n.ok,
    })

    if (callback) {
      return confirm.then(function (result) {
        if (result.value) callback(result)
      })
    }

    return confirm
  }

  $(function () {
    // Init the selectize
    $('select.wplibs-selectize').selectize({
      allowEmptyOption: true,
      searchField: ['value', 'text'],
    })

    // Init the form controls
    $('.wplibs-box > .cmb2-metabox').each((index, element) => {
      if (element._wpLibsForm) {
        return
      }

      const instance = new Form({ el: element })
      api.instances.push(instance)

      element._wpLibsForm = instance
    })
  })
})(jQuery, window.WPLibsForm)
