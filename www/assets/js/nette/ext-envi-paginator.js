$.nette.ext('envi-paginator', {
  complete: function (jqXHR, status, settings) {
    var $closestPaginatorWrapper = $(settings.nette.el).closest('.paginator-wrapper')
    if ($closestPaginatorWrapper.length) {
      var anchor = $closestPaginatorWrapper.data('anchor')
      var $anchor = $(anchor)
      var offsetY = $anchor.data('offset-y') || 0

      if (anchor) {
        $('html, body').animate({
          scrollTop: $anchor.offset().top + offsetY
        })
      }
    }
  }
})
