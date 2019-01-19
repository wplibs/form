/* globals jQuery, window */

const Manager = (($, Backbone, _) => {
  return Backbone.View.extend({
    /**
     * Initialize the manager
     */
    initialize() {
      this.controls = {}
    },

    /**
     * Add a control into the collect
     *
     * @param control
     */
    addControl(control) {
      this.controls[control.get('id')] = control
    },

    /**
     * Gets all control values.
     */
    getValues() {
      return _.mapObject(this.controls, (c) => c.getValue())
    },
  })
})(jQuery, window.Backbone, window._)

export default Manager
