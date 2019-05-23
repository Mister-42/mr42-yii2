function printPsk(key) {
	$('#wpapsk .suppress').removeAttr('disabled');
	$('.current-progress, .field-wpapsk-psk').toggleClass('d-none');
	$('.progress-bar').width('');
	$('#wpapsk-psk').val(key);
}

function status(done) {
	$('.progress-bar').width(Math.round(done) + '%');
}

function calcPsk() {
	$.each($('#wpapsk').data('yiiActiveForm').attributes, function() {
		this.status = 3;
	});
	$('#wpapsk').yiiActiveForm('validate');

	if ($('#wpapsk').find('.is-invalid').length) {
		return false;
	}

	$('#wpapsk .suppress').attr('disabled', true);
	$('.field-wpapsk-psk').addClass('d-none');
	$('.current-progress').removeClass('d-none');
	var pskgen = new PBKDF2($('#wpapsk-password').val(), $('#wpapsk-ssid').val(), 4096, 32);
	pskgen.deriveKey(status, printPsk);
}

function resetPsk() {
	$('.current-progress, .field-wpapsk-psk').addClass('d-none');
}
