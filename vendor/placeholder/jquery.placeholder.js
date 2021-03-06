// jQuery HTML5 placeholder shim; doesn't do passwords.
// $('body').placeholders();
; (function ($) {
  var p = 'placeholder', f = 'input['+p+'][type!=password], textarea['+p+']';
  function r () {
    var t = $(this);
    if (t.val() === t.attr(p))
      t.val('').removeClass(p)
  };
  function a () {
    var t = $(this), v = t.attr(p);
    if (t.val() === '' || t.val() === v)
      t.val(v).addClass(p)
  };
  function s () {
    var t = $(this), v = t.find(f).each(r);
    setTimeout(function () { v.each(a) }, 10);
  };

  $.fn.placeholders = function() {
    return this.length ?
      this.find(f).add(this.filter(f))
        .focus(r).blur(a).each(a)
        .parents('form').submit(s).end()
        .end().end()
      : this;
  }
}(jQuery));