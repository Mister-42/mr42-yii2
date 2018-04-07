function print_psk(key) {
	$('#wpapsk .btn').removeClass('disabled');
	$('.progress, .field-wpapsk-psk').toggleClass('hidden');
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
	$('.field-wpapsk-psk').addClass('hidden');
	$('.progress').removeClass('hidden');
	var pskgen = new PBKDF2($('#wpapsk-pass').val(), $('#wpapsk-ssid').val(), 4096, 32);
	pskgen.deriveKey(status, print_psk)
}

function reset_psk() {
	$('.progress, .field-wpapsk-psk').addClass('hidden');
}
