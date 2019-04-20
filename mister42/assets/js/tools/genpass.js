function rndpass(length) {
	var chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	var pass = '';
	for(var x=0; x<length; x++) {
		var i = Math.floor(Math.random() * chars.length);
		pass += chars.charAt(i);
	}
	return pass;
}

function get() {
	$('[name="password"]').val(rndpass($('[name="length"]').val()));
	return false;
}
