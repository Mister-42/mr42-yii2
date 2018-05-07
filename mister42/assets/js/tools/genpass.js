function rndpass(length) {
	chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
	pass = "";
	for(x=0; x<length; x++) {
		i = Math.floor(Math.random() * chars.length);
		pass += chars.charAt(i)
	}
	return pass
}

function get() {
	$("[name='password']").val(rndpass($("[name='length']").val()));
	return false
}
