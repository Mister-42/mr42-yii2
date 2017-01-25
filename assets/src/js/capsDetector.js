$(function () {
	var isShiftPressed = false;
	var isCapsOn = null;

	$("#login-form-password").bind("keydown", function (e) {
		var keyCode = e.keyCode ? e.keyCode : e.which;
		if (keyCode == 16) {
			isShiftPressed = true;
		}
	});

	$("#login-form-password").bind("keyup", function (e) {
		var keyCode = e.keyCode ? e.keyCode : e.which;
		if (keyCode == 16) {
			isShiftPressed = false;
		}
		if (keyCode == 20) {
			if (isCapsOn == true) {
				isCapsOn = false;
				$("#caps").addClass('hidden');
			} else if (isCapsOn == false) {
				isCapsOn = true;
				$("#caps").removeClass('hidden');
			}
		}
	});

	$("#login-form-password").bind("keypress", function (e) {
		var keyCode = e.keyCode ? e.keyCode : e.which;
		if (keyCode >= 65 && keyCode <= 90 && !isShiftPressed) {
			isCapsOn = true;
			$("#caps").removeClass('hidden');
		} else {
			$("#caps").addClass('hidden');
		}
	});
});
