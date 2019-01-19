export default (function ($) {
  function getSpecialTransitionEndEvent() {
    return {
      bindType: 'transitionend',
      delegateType: 'transitionend',
      handle(event) {
        if ($(event.target).is(this)) {
          return event.handleObj.handler.apply(this, arguments)
        }

        return undefined
      }
    }
  }

  function transitionEndEmulator(duration) {
    let called = false;

    $(this).one('srTransitionEnd', () => {
      called = true
    })

    setTimeout(() => {
      if (!called) {
        $(this).trigger('transitionend')
      }
    }, duration)

    return this;
  }

  function setTransitionEndSupport() {
    $.fn.srEmulateTransitionEnd = transitionEndEmulator
    $.event.special['srTransitionEnd'] = getSpecialTransitionEndEvent()
  }

  return setTransitionEndSupport
})(jQuery)
