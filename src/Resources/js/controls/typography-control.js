import Control from '../core/control'

const TypographyControl = (function ($, Suru) {
  return Control.extend({
    ready() {
      console.log(this.elements)

      const weight = this.find('[data-element="weight"]').selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
      })

      this.find('[data-element="family"]').selectize({
        options: Suru.webfonts.getGoogleFonts(),
        optgroups: [],
        valueField: 'family',
        labelField: 'family',
        searchField: ['name'],
        onChange(value) {
          const data = this.options[value]

          if (weight[0] && weight[0].selectize) {
            const s = weight[0].selectize;

            s.clearOptions();
            s.load(function (callback) {
              callback(Suru.webfonts.toVariants(data.variants))
            })
          }
        }
      })
    }
  })
})(jQuery, window.SuruLibs || {})

export default TypographyControl
