function print_psk(key) {
	$('.btn').removeClass('disabled');
	$('.clipboard-js-init').removeClass('hidden');
	$('#psk').attr('class', 'well well-sm alert-success text-center').html(key).parent().removeClass('col-md-12').addClass('col-md-11');
}

function status(done) {
	$('.progress-bar').width(Math.round(done) + '%');
}

function cal_psk() {
	var ssid = $('#ssid').val(), pass = $('#pass').val();

	if(!ssid || !pass) {
		return $('#psk').attr('class', 'well well-sm alert-danger').html('Please fill in all fields.');
	} else {
		$('.btn').addClass('disabled');
		$('.clipboard-js-init').addClass('hidden');
		$('#psk').attr('class', 'progress progress-striped').html('<div class="progress-bar progress-bar-info active"></div>').parent().removeClass('col-md-11').addClass('col-md-12');
	}

	var pskgen = new PBKDF2(pass, ssid, 4096, 32);
	pskgen.deriveKey(status, print_psk)
}

function reset_psk() {
	$('#psk').attr('class', 'well well-sm').html('Not calculated yet.');
	$('.clipboard-js-init').addClass('hidden');
	$('#psk').parent().removeClass('col-md-11').addClass('col-md-12');
}
