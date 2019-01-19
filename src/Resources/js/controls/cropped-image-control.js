/* global wp.media */

import Utils from '../utils'
import ImageControl from './image-control'

const CroppedImageControl = (function ($) {
  return ImageControl.extend({
    /**
     * Open the media modal to the library state.
     */
    openFrame(e) {
      if (Utils.isKeydownButNotEnterEvent(e)) {
        return
      }

      this.initFrame()
      this.frame.setState('library').open()
    },

    /**
     * Create a media modal select frame, and store it so the instance can be reused when needed.
     */
    initFrame() {
      const l10n = window._wpMediaViewsL10n || {}
      const labels = this.model.get('labels')

      this.frame = wp.media({
        button: {
          text: l10n.select,
          close: false
        },
        states: [
          new wp.media.controller.Library({
            title: labels.frame_title,
            library: wp.media.query({ type: 'image' }),
            multiple: false,
            date: false,
            priority: 20,
            suggestedWidth: this.model.get('width'),
            suggestedHeight: this.model.get('height')
          }),
          new wp.media.controller.Cropper({
            imgSelectOptions: this.calculateImageSelectOptions,
            control: this
          })
        ]
      })

      this.frame.on('select', this.onSelect, this)
      this.frame.on('cropped', this.onCropped, this)
      this.frame.on('skippedcrop', this.onSkippedCrop, this)
    },

    /**
     * After an image is selected in the media modal, switch to the cropper
     * state if the image isn't the right size.
     */
    onSelect() {
      const attachment = this.frame.state().get('selection').first().toJSON()

      if (this.model.get('width') === attachment.width
        && this.model.get('height') === attachment.height
        && !this.model.get('flex_width')
        && !this.model.get('flex_height')) {
        this.setImageFromAttachment(attachment)
        this.frame.close()
      } else {
        this.frame.setState('cropper')
      }
    },

    /**
     * After the image has been cropped, apply the cropped image data to the setting.
     *
     * @param {object} croppedImage Cropped attachment data.
     */
    onCropped(croppedImage) {
      this.setImageFromAttachment(croppedImage)
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
    calculateImageSelectOptions(attachment, controller) {
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
        x1, y1, imgSelectOptions

      controller.set('canSkipCrop', !control.mustBeCropped(flexWidth, flexHeight, xInit, yInit, realWidth, realHeight))

      if (realWidth / realHeight > ratio) {
        yInit = realHeight
        xInit = yInit * ratio
      } else {
        xInit = realWidth
        yInit = xInit / ratio
      }

      x1 = (realWidth - xInit) / 2
      y1 = (realHeight - yInit) / 2

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
      }

      if (flexHeight === false && flexWidth === false) {
        imgSelectOptions.aspectRatio = xInit + ':' + yInit
      }

      if (true === flexHeight) {
        delete imgSelectOptions.minHeight
        imgSelectOptions.maxWidth = realWidth
      }

      if (true === flexWidth) {
        delete imgSelectOptions.minWidth
        imgSelectOptions.maxHeight = realHeight
      }

      return imgSelectOptions
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
    mustBeCropped(flexW, flexH, dstW, dstH, imgW, imgH) {
      if (true === flexW && true === flexH) {
        return false
      }

      if (true === flexW && dstH === imgH) {
        return false
      }

      if (true === flexH && dstW === imgW) {
        return false
      }

      if (dstW === imgW && dstH === imgH) {
        return false
      }

      return imgW > dstW
    },

    /**
     * If cropping was skipped, apply the image data directly to the setting.
     */
    onSkippedCrop() {
      const attachment = this.frame.state().get('selection').first().toJSON()
      this.setImageFromAttachment(attachment)
    },

    /**
     * Updates the setting and re-renders the control UI.
     *
     * @param {object} attachment
     */
    setImageFromAttachment(attachment) {
      this.model.set('attachment', attachment)
      this.setValue(attachment.id)
    }
  })
})(jQuery)

export default CroppedImageControl
