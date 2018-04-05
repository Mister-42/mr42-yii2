function print_psk(key) {
	$('#wpapsk .btn').removeClass('disabled');
	$('.progress').addClass('hidden');
	$('.progress-bar').width(0);
	$('.field-psk').removeClass('hidden');
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
	$('.progress').addClass('hidden');
	$('.field-psk').addClass('hidden');
}
