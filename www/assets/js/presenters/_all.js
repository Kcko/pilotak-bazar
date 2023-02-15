// require(['nette-ext/ext-envi-paginator'])

// preloader

$.nette.on('*', '*', function () {
  var context = this
  var $elems = {}

  // Preloader
  if ($('#web-status').length) {
    $('#web-status').fadeOut(2500)
    $('#web-preloader').delay(1000).fadeOut('slow')
  }

  // Search
  $('#mobileSearch', context).on('click', function (e) {
    $('#SearchForm').addClass('IsModal').parent().removeClass('hidden')
    $('#SearchForm').find('input[type=text]').focus()
    $('html').addClass('HasModal')
  })

  $('.MobileSearchClose', context).on('click', function (e) {
    $('#SearchForm').removeClass('IsModal').parent().addClass('hidden')
    $('html').removeClass('HasModal')
  })

  // Hamburger with Menu
  $('#mobileMenu', context).on('click', function (e) {
    $('#Menu').addClass('IsModal').parent().removeClass('hidden')
    $('html').addClass('HasModal2')
  })

  $('.MobileMenuClose', context).on('click', function (e) {
    $('#Menu').removeClass('IsModal').parent().addClass('hidden')
    $('html').removeClass('HasModal2')
  })

  // UserMobile  Menu
  $('#mobileUserMenu', context).on('click', function (e) {
    $('#UserMenu').addClass('IsModal').parent().removeClass('hidden')
    $('html').addClass('HasModal3')
  })

  $('.MobileMenuUserClose', context).on('click', function (e) {
    $('#UserMenu').removeClass('IsModal').parent().addClass('hidden')
    $('html').removeClass('HasModal3')
  })

  // external links to new window
  var all_links = document.querySelectorAll('.RichText a')
  for (var i = 0; i < all_links.length; i++) {
    var a = all_links[i]
    if (a.hostname != location.hostname && a.href.indexOf('http') != -1) {
      // a.rel = 'noopener'
      a.target = '_blank'
      a.title = 'Odkaz vede na ' + a.href
      a.className += ' ExternalLink'
    }
  }

  $('.MainNavigation__Arrow', context).on('click', function (e) {
    e.preventDefault()
    e.stopPropagation()

    var $this = $(this)
    var $children = $this.parent().next()
    var promiseToggle = $children.slideToggle()
    $children = $this.parent().next()

    var $state = $this.data('clicked')

    // otvirame
    if (typeof $state === 'undefined' || !$state) {
      $this.addClass('-Opened')
      $this.data('clicked', true)
      // zavirame
    } else {
      $this.removeClass('-Opened')
      $this.data('clicked', false)

      // vcetne deti kdyby byly opened
      promiseToggle.promise().done(function () {
        $this.closest('li').find('ul').hide()
        $this.closest('li').find('.MainNavigation__Arrow').data('clicked', false).removeClass('-Opened')
      })
    }
  })

  // Vyhledavani Mark
  $elems['#SearchMark'] = $('#SearchMark', context)
  if ($elems['#SearchMark'].length) {
    require(['mark'], function (mark) {
      var markInstance = new mark(document.querySelector('#SearchMark'))
      var options = {
        exclude: ['.no-highlight']
      }
      markInstance.unmark({
        done: function () {
          markInstance.mark(document.querySelector('#SearchQ').textContent, options)
        }
      })
    })
  }

  // snowfall :)
  if ($('.SnowFall').length) {
    // Tippy
    require(['libs/snowfall'], function (snowFall) {
      snowFall.snow(document.querySelectorAll('.SnowFall'), {
        flakeCount: 25,
        flakeIndex: 0,
        round: true,
        minSize: 3,
        maxSize: 9,
        minSpeed: 0.5,
        maxSpeed: 2
      })
    })
  }

  if ($('*[title]').length) {
    // Tippy
    require(['libs/tippy'], function (tippy) {
      tippy('*[title]', {
        arrow: true,
        performance: true
      })
    })
  }

  require(['headroom'], function (headroom) {
    var myElement = document.querySelector('.Headroom')
    var header = new headroom(myElement, {
      tolerance: 5
    })
    var mql = matchMedia('(min-width:48em)')

    function handleChange(mq) {
      if (mq.matches) {
        header.init()
      } else {
        if (header.initialized) header.destroy()
      }
    }

    handleChange(mql)
    mql.addEventListener('change', handleChange)
  })

  // Calendar
  $elems['.HasEvents'] = $('.HasEvents', context)
  $elems['.CalendarEventOverlay'] = $('.CalendarEventOverlay', context)
  $elems['.CalendarEventOverlayClose'] = $('.CalendarEventOverlayClose', context)
  $elems['.HasEvents'].on('click', function () {
    var $this = $(this)
    var date = $this.data('date')

    $elems['.CalendarEventOverlay'].removeClass('hidden')
    $elems['.CalendarEventOverlay'].find('#CalendarOverlay-' + date).removeClass('hidden')
  })

  $elems['.CalendarEventOverlayClose'].on('click', function () {
    $elems['.CalendarEventOverlay'].addClass('hidden')
    $elems['.CalendarEventOverlay'].children().addClass('hidden')
  })

  // slider HP
  $elems['#slick-slider'] = $('#slick-slider', context)
  if ($elems['#slick-slider'].length) {
    require(['nette-front-module/aw/slider'], function () {
      $elems['#slick-slider'].awSlider({
        appendDots: $('#slider-dots-append'),
        autoplay: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplaySpeed: 7000,
        speed: 900,
        arrows: true,
        fade: true,
        dots: true,
        prevArrow: $('.swiper-prev'),
        nextArrow: $('.swiper-next'),
        customPaging: function (slider, i) {
          return '<span>&nbsp;</span>'
        }
        // customPaging: function (slider, i) {
        //   var heading = $(slider.$slides[i].children[0].children[0]).data('heading')
        //   var desc = $(slider.$slides[i].children[0].children[0]).data('desc')
        //   var number = $(slider.$slides[i].children[0].children[0]).data('number')

        //   var html = '<a class="pager-item">'
        //   html += '<span class="pager-item-number-mobile">' + number + '.</span>'
        //   html += '<div class="pager-item-heading"><span class="pager-item-number">' + number + '.</span>'
        //   html += heading + '</div>'
        //   html += '<div class="pager-item-desc">' + desc + '</div>'
        //   html += '</a>'
        //   return html
        // }
      })
    })
  }

  // slider
  $elems['.slick-slider'] = $('.slick-slider', context)
  if ($elems['.slick-slider'].length) {
    require(['nette-front-module/aw/slider'], function () {
      $elems['.slick-slider'].awSlider({
        arrows: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        dots: true,
        autoplaySpeed: 2000,
        prevArrow: $('.swiper-prev'),
        nextArrow: $('.swiper-next'),
        customPaging: function (slider, i) {
          return '<span>&nbsp;</span>'
        }
      })
    })
  }

  // Recaptcha V3
  $elems['g_recaptcha_response'] = $('#g_recaptcha_response', context)

  if ($elems['g_recaptcha_response'].length) {
    grecaptcha.ready(function () {
      grecaptcha
        .execute($elems['g_recaptcha_response'].data('site-key'), {
          action: 'validate_captcha'
        })
        .then(function (token) {
          document.getElementById('g_recaptcha_response').value = token
        })
    })
  }

  // autocomplete
  $elems['#autocomplete-search'] = $('#autocomplete-search', context)
  if ($elems['#autocomplete-search'].length) {
    require(['nette-front-module/vendor/autocomplete'], function () {
      var remoteUrl = $elems['#autocomplete-search'].data('remote-url')
      var noResults = $elems['#autocomplete-search'].data('no-results')
      var searchInTables = $elems['#autocomplete-search'].data('search-in-tables')

      $elems['#autocomplete-search']
        .autocomplete(remoteUrl, {
          q: $elems['#autocomplete-search'].data('q'),
          minChars: 2,
          max: 20,
          delay: 250,
          selectFirst: false,
          autoFill: false,
          mustMatch: false,
          matchContains: true,
          scrollHeight: 300,
          extraParams: { searchInTables: searchInTables },
          width: $elems['#autocomplete-search'].outerWidth(),
          cacheLength: 0,
          formatItem: function (data, i, total) {
            if (data.title == 'NO_RECORDS') {
              return '<span class="ac-no-results">' + noResults + '</span>'
            }

            return (
              '<span class="fl">' + data.title + '</span>' + '<br> <span class="fr">' + data.breadcrumbs + '</span>'
            )
          },

          formatResult: function (data) {
            if (data.title == 'NO_RECORDS') {
              return ' '
            }

            return data.title
          }
        })
        .result(function (data, data1) {
          if (data1.title == 'NO_RECORDS') {
            return
          }

          document.location = data1.url
        })
    })
  }
})

$.nette.on(function () {
  var context = this
  var $elems = {}

  $('.eu-cookies button').click(function () {
    var date = new Date()
    date.setFullYear(date.getFullYear() + 10)
    document.cookie = 'eu-cookies=1; path=/; expires=' + date.toGMTString()
    $('.eu-cookies').hide()
  })

  $('.magnific-popup-close', context).on('click', function (e) {
    e.preventDefault()
    var magnificPopup = $.magnificPopup.instance // save instance in magnificPopup variable
    magnificPopup.close()
  })

  $('.popup-youtube', context).magnificPopup({
    disableOn: 700,
    type: 'iframe',
    mainClass: 'mfp-fade',
    removalDelay: 160,
    preloader: false,
    fixedContentPos: false
  })

  $('[data-toggle="mfp"]', context).magnificPopup({
    fixedContentPos: true,
    fixedBgPos: true,
    type: 'inline',
    midClick: true,
    callbacks: {
      beforeOpen: function () {
        $('body').addClass('mfp-zoom-out-cur')
      },
      beforeClose: function () {
        $('body').removeClass('mfp-zoom-out-cur')
      }
    }
  })

  if (window.location.hash && $(window.location.hash).length && $(window.location.hash).hasClass('mfp-popup')) {
    $.magnificPopup.open({
      fixedContentPos: true,
      fixedBgPos: true,
      items: {
        src: window.location.hash,
        type: 'inline'
      },
      callbacks: {
        beforeOpen: function () {
          $('body').addClass('mfp-zoom-out-cur')
        },
        beforeClose: function () {
          $('body').removeClass('mfp-zoom-out-cur')
        }
      }
    })
  }

  $('.mfp-gallery', context).magnificPopup({
    delegate: 'a',
    type: 'image',
    tLoading: 'Loading image #%curr%...',
    mainClass: 'mfp-img-mobile',
    gallery: {
      enabled: true,
      navigateByImgClick: true,
      preload: [0, 1]
    },
    image: {
      tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
      titleSrc: function (item) {
        return item.el.attr('title')
      }
    }
  })

  $('.mfp-image', context).magnificPopup({
    type: 'image',
    closeOnContentClick: true,
    mainClass: 'mfp-img-mobile',
    image: {
      verticalFit: true
    }
  })

  $elems['.yt-ondemand'] = $('.yt-ondemand', context)
  if ($elems['.yt-ondemand'].length) {
    require(['nette-front-module/aw/ytdemand'], function () {
      $elems['.yt-ondemand'].awYtDemand()
    })
  }
})

$.nette.on('*', '*', function () {
  var context = this
  var $elems = {}

  $('#to-top', context).on('click', function (e) {
    e.preventDefault()
    $('html, body').animate({
      scrollTop: 0
    })
  })

  $(window)
    .on('scroll', function () {
      if ($(this).scrollTop() > 300) {
        $('#to-top').fadeIn()
      } else {
        $('#to-top').fadeOut()
      }
    })
    .trigger('scroll')

  $('#iframe-map', context).addClass('scrolloff')

  $('#map-overlay', context).on('mouseup', function () {
    $('#iframe-map', context).addClass('scrolloff')
  })

  $('#map-overlay', context).on('mousedown', function () {
    $('#iframe-map', context).removeClass('scrolloff')
  })

  $('#iframe-map', context).mouseleave(function () {
    $('#iframe-map', context).addClass('scrolloff')
  })
})
