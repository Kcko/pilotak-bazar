$.nette.on(function () {
  var that = this
  var $el = {}

  //$('.modal .paginator-wrapper').css('display', 'block')

  if ($('#myCss').length === 0) {
    var elem = document.createElement('link')
    elem.id = 'myCss'
    elem.rel = ' stylesheet'
    elem.href = '/admin/css/custom.css?v=' + new Date().getTime()
    document.head.appendChild(elem)
  }
})

var APP_VERSION = 'v-2022-11-15'

$.nette.on(function () {
  var that = this
  var $el = {}

  $('#js-version', this).append('<small class="ml-3">' + APP_VERSION + '</small>')

  /* rank fn */

  var createMoveButtons = function ($input, rank, totalCnt) {
    var $up = $('<btn>', {
      class: 'btn btn-sm btn-info mr-1',
      html: '&uarr;'
    })

    var $down = $('<btn>', {
      class: 'btn btn-sm btn-info ml-1',
      html: '&darr;'
    })

    var $upFake = $('<btn>', {
      class: 'btn btn-sm mr-1',
      html: ''
    })

    var $downFake = $('<btn>', {
      class: 'btn btn-sm ml-1',
      html: '',
      disabled: 'disabled'
    })

    $up.on('click', function () {
      var tr = $(this).closest('tr')
      tr.insertBefore(tr.prev())
      var $group = $input.closest('.form-replicator')
      var $rankGroup = $group.find('input[name*="rank"]')
      recountRanks($rankGroup)
    })

    $down.on('click', function () {
      var tr = $(this).closest('tr')
      tr.insertAfter(tr.next())

      var $group = $input.closest('.form-replicator')
      var $rankGroup = $group.find('input[name*="rank"]')
      recountRanks($rankGroup)
    })

    $input.css('max-width', '40px')
    var $siblings = $input.siblings('.btn')
    if ($siblings.length) {
      $siblings.remove()
    }

    if (rank > 0) $input.before($up)
    else $input.before($upFake)

    if (rank != totalCnt - 1) $input.after($down)
    else $input.after($downFake)
  }

  var recountRanks = function ($group) {
    var totalCnt = $group.length

    $group.each(function (index, element) {
      var $element = $(element)
      $element.val(index + 1)
      createMoveButtons($element, index, totalCnt)
    })
  }

  $el['.form-replicator'] = $('.form-replicator', this)
  if ($el['.form-replicator'].length) {
    var $set = $el['.form-replicator']
    var $ranksGroup
    $set.each(function () {
      $rankGroup = $(this).find('input[name*="rank"]')
      recountRanks($rankGroup)
    })
  }
})

// vyhledavani nad checkboxlistem ve filtru, rychla dodelavka ;-)
$.nette.on(function () {
  var that = this
  var $el = {}

  var RemoveAccents = function (str) {
    var accents = 'ÀÁÂÃÄÅàáâãäåÒÓÔÕÕÖØòóôõöøÈÉÊËèéêëðÇçÐÌÍÎÏìíîïÙÚÛÜùúûüÑñŠšŸÿýŽžřŘřČčŮů'
    var accentsOut = 'AAAAAAaaaaaaOOOOOOOooooooEEEEeeeeeCcDIIIIiiiiUUUUuuuuNnSsYyyZzrRrCcUu'
    str = str.split('')
    var strLen = str.length
    var i, x
    for (i = 0; i < strLen; i++) {
      if ((x = accents.indexOf(str[i])) != -1) {
        str[i] = accentsOut[x]
      }
    }
    return str.join('')
  }

  var $checkboxFastFilter = $('input.checkboxFastFilter', that)
  if ($checkboxFastFilter.length) {
    var $checkboxList = $checkboxFastFilter.next('.filterElement').find('li')
    var $checkboxListInfo = $checkboxFastFilter.prev()
    var $filteredCnt = $checkboxListInfo.find('span').eq(0)
    var $totalCnt = $checkboxListInfo.find('span').eq(1)

    $filteredCnt.text($checkboxList.length)
    $totalCnt.text($checkboxList.length)

    $checkboxFastFilter.on('keyup', function () {
      clearTimeout($.data(this, 'timer'))
      var $input = $(this)

      var wait = setTimeout(function () {
        var value = RemoveAccents($.trim($input.val()).toLowerCase())
        var founded = 0

        $checkboxList.each(function () {
          var $row = $(this)
          var $rowValue = RemoveAccents($row.text().toLowerCase())

          if ($rowValue.indexOf(value) > -1) {
            $row.removeClass('d-none')
            founded++
          } else {
            $row.addClass('d-none')
          }
        })

        $filteredCnt.text(founded)
      }, 250)

      $(this).data('timer', wait)
    })
  }
})
