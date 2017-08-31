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
	$("#password").attr("class", "well well-sm alert-success text-center form-control").html(rndpass($("#length").val()));
	return false
}
