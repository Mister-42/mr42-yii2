$("#pwdToggle button").on('click', function(event) {
	event.preventDefault();
	if ($('#pwdToggle input').attr('type') == 'text') {
		$('#pwdToggle input').attr('type', 'password');
		$(this).prop('title', 'Show Password');
	} else if($('#pwdToggle input').attr('type') == 'password') {
		$('#pwdToggle input').attr('type', 'text');
		$(this).prop('title', 'Hide Password');
	}
	$('#pwdToggle svg.append').toggleClass('d-none');
});
