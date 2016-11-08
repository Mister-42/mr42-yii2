btn = $('#btn-scrolltop');
tp = $('html,body').offset().top + 150;

$(document).on('scroll', function() {
	pos = $(window).scrollTop();
	if (pos > tp && !btn.is(':visible')) {
		btn.fadeIn();
	} else if (pos < tp && btn.is(':visible')) {
		btn.fadeOut();
	}
}).scroll();

btn.on('click', function(e){
	$('html,body').animate({scrollTop:0}, 1000);
	history.pushState({},"", $("link[rel='canonical']").attr('href'));
});
