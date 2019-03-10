$("#pwdToggle button").on('click', function(event) {
	$.each($('form').data('yiiActiveForm').attributes, function() {
		this.status = 3;
	});
	event.preventDefault();
	if ($('#pwdToggle input').attr('type') === 'text') {
		$('#pwdToggle input').attr('type', 'password');
		$(this).prop('title', togglePassword.lang.show);
	} else if($('#pwdToggle input').attr('type') === 'password') {
		$('#pwdToggle input').attr('type', 'text');
		$(this).prop('title', togglePassword.lang.hide);
	}
	$('#pwdToggle svg.append').toggleClass('d-none');
});
