/* globals jQuery, window */

import Manager from './manager'
import Cookies from 'js-cookie'
import MetisMenu from 'metismenujs'
import Tablist from '@accede-web/tablist'

const Form = (($) => {
  return Manager.extend({
    /**
     * Initialize the Form
     */
    initialize() {
      Manager.prototype.initialize.apply(this, arguments)

      this.box = (String(this.el.getAttribute('id'))).replace('cmb2-metabox-', '')
      this.navElement = this.el.querySelector('[role="tablist"]')

      if (this.navElement) {
        this._menu = new MetisMenu(this.navElement)
        this._tablist = new Tablist(this.navElement)
        this._setupTabs()
      }

      this.initControls()
    },

    initControls() {
      const self = this

      this.$el.find('.wplibs-form__row').each(function () {
        const element = this
        const type = $(this).data('fieldtype')
        const isRepeatable = $(this).hasClass('cmb-repeat')

        if ('title' === type || type.substr(0, 2) === '__') {
          return ''
        }

        if ('group' === type) {
          self.initGroupControl(element)
          return
        }

        if (!isRepeatable) {
          self.createNewControl(type, element, self)
        } else {
          $(this).find('.cmb-repeat-row').each(function () {
            self.createNewControl(type, this, self)
          })
        }

        $(element).on('cmb2_add_row', function (e) {
          const el = $(e.target).find('.cmb-repeat-row').last()
          self.createNewControl(type, el, self)
        })
      })
    },

    initGroupControl(element) {
      const self = this

      $(element).find('.cmb-repeatable-grouping').each(function () {
        const controls = new Manager({ el: this })

        $(this).find('.wplibs-form__grouprow').each(function () {
          const element = this
          const type = $(this).data('fieldtype')
          const isRepeatable = $(this).hasClass('cmb-repeat')

          if (!isRepeatable) {
            self.createNewControl(type, element, controls)
          } else {
            $(this).find('.cmb-repeat-row').each(function () {
              self.createNewControl(type, this)
            })
          }
        })
      })
    },

    createNewControl(type, root, manager) {
      const api = window.WPLibsForm || {}

      let _control = api.Controls.Control

      if (api.Controls.hasOwnProperty(type)) {
        _control = api.Controls[type]
      }

      return new _control({
        el: root,
        manager: manager
      })
    },

    /**
     * Setup the tabs.
     *
     * @private
     */
    _setupTabs() {
      const menu = this._menu
      const tabList = this._tablist

      const $menu = $(this.navElement)
      let containerHeight = ''

      const setCurrentSection = (tab) => {
        let section = tab.getAttribute('data-section')

        let currents = Cookies.getJSON('_wplibs_current_sections') || {}
        currents[this.box] = section

        Cookies.set('_wplibs_current_sections', currents, { path: '' })
      }

      // Close showing ul element on section click
      $menu.on('click', '> [role="tab"] > a', () => {
        const ul = $menu.find('> [data-panel].active > ul')[0]

        if (typeof ul !== 'undefined') {
          setTimeout(() => menu.hide(ul), 0)
        }
      })

      menu.on('show.metisMenu', (e) => {
        const ul = e.detail.showElement

        // Remove hover from parent li.
        if (ul.parentNode.classList.contains('hover')) {
          ul.parentNode.classList.remove('hover')
        }

        if (!ul.querySelector('.active')) {
          // setTimeout(() => ul.querySelector('li').focus(), 0)
        }
      })

      tabList.on('show', (tab, panel) => {
        setCurrentSection(tab)

        const ul = tab.parentNode
        if (ul.classList.contains('sugar-options__submenu')) {
          menu.show(ul)
        }

        tab.classList.add('active')
        panel.classList.add('open')

        containerHeight = $(panel).outerHeight()
        $(panel.parentNode).animate({ height: containerHeight }, 230, function() {
          $(panel.parentNode).height('')
        })
      })

      tabList.on('hide', (tab, panel) => {
        containerHeight = $(panel).outerHeight()
        $(panel.parentNode).height(containerHeight)

        tab.classList.remove('active')
        panel.classList.remove('open')
      })

      tabList.mount()
    },
  })
})(jQuery)

export default Form
