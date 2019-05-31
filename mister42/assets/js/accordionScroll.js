$('.accordion').on('shown.bs.collapse', function() {
	if ($(this).find('.show').parent().attr('id') !== 'frontCover') {
		$('html, body').animate({
			scrollTop: $(this).find('.show').offset().top - 125
		}, 500);
	}
});
