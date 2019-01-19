const JsLogic = (function () {
  function isString(string) {
    return Object.prototype.toString.call(string) === '[object String]'
  }

  function isTruthy(value) {
    if (Array.isArray(value) && value.length === 0) {
      return false
    }

    return !!value
  }

  class Variable {
    constructor(name, instance) {
      this.name = name
      this.instance = instance
    }

    getName() {
      return this.name
    }

    getValue() {
      if (this.instance.data.hasOwnProperty(this.name)) {
        return this.instance.data[this.name]
      }

      return undefined
    }
  }

  class JsLogic {
    /**
     * Constructor
     *
     * @param data
     */
    constructor(data) {
      this.data = data || {}
    }

    /**
     * //
     *
     * @param  {*} rules
     * @return {boolean}
     */
    apply(rules) {
      let logical = 'and'

      if (JsLogic.isProposition(rules)) {
        rules = [rules]
      } else if (JsLogic.isLogicSet(rules)) {
        logical = this.getOperator(rules)
        rules = rules[logical]
      }

      return this.testLogical(rules, logical)
    }

    /**
     * //
     *
     * @param  {*}      rules
     * @param  {string} logical
     * @return {boolean}
     */
    testLogical(rules, logical) {
      if (!Array.isArray(rules)) {
        rules = Object.values(rules)
      }

      if (rules.length === 0) {
        return true
      }

      let current

      switch (logical) {
        case 'and':
          for (let i = 0; i < rules.length; i++) {
            try {
              current = this.testProposition(rules[i])
            } catch (e) {
              console.error && console.error(e)
              continue
            }

            if (!isTruthy(current)) {
              return current
            }
          }
          return current

        case 'or':
          for (let i = 0; i < rules.length; i++) {
            try {
              current = this.testProposition(rules[i])
            } catch (e) {
              console.error && console.error(e)
              continue
            }

            if (isTruthy(current)) {
              return current
            }
          }
          return current
      }

      return true
    }

    /**
     * //
     *
     * @param  {array} rule
     * @return {boolean}
     */
    testProposition(rule) {
      let [variable, operator, check] = this.normalizeProposition.apply(this, rule)

      if (check instanceof Variable) {
        check = check.getValue()
      }

      if (typeof JsLogic.operators[operator] === 'function') {
        return JsLogic.operators[operator].call(null, variable.getValue(), check)
      }

      throw new Error(`Unrecognized operation ${operator}`)
    }

    /**
     * //
     *
     * @param  {object} logic
     * @return {string}
     */
    getOperator(logic) {
      return Object.keys(logic)[0]
    }

    /**
     * Create a variable.
     *
     * @param  {string} name
     * @return {Variable}
     */
    newVariable(name) {
      return new Variable(name, this)
    }

    /**
     * //
     *
     * @param  {string}      name
     * @param  {string|null} operator
     * @param  {*}           check
     * @return {array}
     * @private
     */
    normalizeProposition(name, operator = null, check = null) {
      if (arguments.length === 2 && 0 !== operator.indexOf('is_')) {
        [check, operator] = [operator, '=']
      }

      // Some operator like "=", "<=", etc. need to normalize to valid name.
      operator = this.normalizeOperator(operator)

      // Wrap the value as array in case "in" or "not_in".
      if (isString(check) && ['in', 'not_in'].includes(operator)) {
        check = check.split(',').map(item => item.trim())
      }

      // Allow check value can be a reference value of a variable in the context.
      // This can be done with: ['some-check', '!=', '@another-check'].
      if (isString(check) && check[0] === '@') {
        check = this.newVariable(check.substr(1))
      }

      return [this.newVariable(name), operator, check]
    }

    /**
     * //
     *
     * @param  {string} operator
     * @return {string}
     * @private
     */
    normalizeOperator(operator) {
      switch (operator) {
        case '=':
        case '==':
          return 'equal'
        case '!=':
        case '!==':
          return 'not_equal'
        case '<':
        case 'lt':
          return 'less_than'
        case '<=':
        case 'lte':
          return 'less_than_or_equal'
        case '>':
        case 'gt':
          return 'greater_than'
        case '>=':
        case 'gte':
          return 'greater_than_or_equal'
        case 'is_truthy':
          return 'is_not_empty'
        default:
          return operator
      }
    }
  }

  /**
   * //
   *
   * @param  {object} logic
   * @return {boolean}
   */
  JsLogic.isLogicSet = function (logic) {
    if (Array.isArray(logic) || logic === null) {
      return false
    }

    return typeof logic === 'object' && Object.keys(logic).length === 1
  }

  /**
   * //
   *
   * @param  {array} rule
   * @return {boolean}
   */
  JsLogic.isProposition = function (rule) {
    return Array.isArray(rule) && rule.length >= 2 && isString(rule[0])
  }

  /**
   * Store the operators.
   *
   * @type {*}
   */
  JsLogic.operators = {
    equal: (a, b) => a == b,
    not_equal: (a, b) => a != b,
    greater_than: (a, b) => a > b,
    greater_than_or_equal: (a, b) => a >= b,
    less_than: (a, b) => a < b,
    less_than_or_equal: (a, b,) => a <= b,
    between: (a, b) => (a >= a && a <= b),
    not_between: (a, b) => (!(a >= a && a <= b)),
    in: (a, b) => Array.isArray(b) && b.includes(a),
    not_in: (a, b) => Array.isArray(b) && !b.includes(a),
    contains: (a, b) => isString(a) && a.includes(b),
    not_contains: (a, b) => isString(a) && !a.includes(b),
    begins_with: (a, b) => isString(a) && a.startsWith(b),
    not_begins_with: (a, b) => isString(a) && !a.startsWith(b),
    ends_with: (a, b) => isString(a) && a.endsWith(b),
    not_ends_with: (a, b) => isString(a) && !a.endsWith(b),
    is_empty: (a) => !isTruthy(a),
    is_not_empty: (a) => isTruthy(a),
  }

  return JsLogic
})();

export default JsLogic
