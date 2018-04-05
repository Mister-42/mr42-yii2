function print_psk(key) {
	$('#wpapsk .btn').removeClass('disabled');
	$('.progress, .field-psk').toggleClass('hidden');
	$('.progress-bar').width('');
	$("[name='psk']").val(key);
}

function status(done) {
	$('.progress-bar').width(Math.round(done) + '%');
}

function cal_psk() {
	var ssid = $('#dynamicmodel-ssid').val(), pass = $('#dynamicmodel-pass').val();

	if(!ssid || !pass) {
		return false;
	} else {
		$('#wpapsk .btn').addClass('disabled');
		$('.field-psk').addClass('hidden');
		$('.progress').removeClass('hidden');
	}

	var pskgen = new PBKDF2(pass, ssid, 4096, 32);
	pskgen.deriveKey(status, print_psk)
}

function reset_psk() {
	$('.progress, .field-psk').addClass('hidden');
}
