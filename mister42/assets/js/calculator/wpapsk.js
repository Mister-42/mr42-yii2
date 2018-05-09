function print_psk(key) {
	$('#wpapsk .btn').removeClass('disabled');
	$('.current-progress, .field-wpapsk-psk').toggleClass('d-none');
	$('.progress-bar').width('');
	$('#wpapsk-psk').val(key);
}

function status(done) {
	$('.progress-bar').width(Math.round(done) + '%');
}

function cal_psk() {
	$.each($('#wpapsk').data('yiiActiveForm').attributes, function() {
		this.status = 3;
	});
	$('#wpapsk').yiiActiveForm("validate");

	if ($('#wpapsk').find(".has-error").length) {
		return false;
	}

	$('#wpapsk .btn').addClass('disabled');
	$('.field-wpapsk-psk').addClass('d-none');
	$('.current-progress').removeClass('d-none');
	var pskgen = new PBKDF2($('#wpapsk-pass').val(), $('#wpapsk-ssid').val(), 4096, 32);
	pskgen.deriveKey(status, print_psk)
}

function reset_psk() {
	$('.current-progress, .field-wpapsk-psk').addClass('d-none');
}
