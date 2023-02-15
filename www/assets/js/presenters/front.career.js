$.nette.on('front.career', 'detail', function () {
	var context = this;

	$('.agreement-more', context).on('click', function (e) {
		e.preventDefault();
		var $this = $(this);
		$this.parent().next('div').fadeIn('fast');
		$this.hide();
	});

	$('.agreement-less', context).on('click', function (e) {
		e.preventDefault();
		var $this = $(this);
		$this.parent().fadeOut('fast', function () {
			$('.agreement-more', context).show();
		});

	});
});