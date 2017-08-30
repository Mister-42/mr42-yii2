$('#formContent').keyup(function() {
	len = $(this).val().length;
	char = %max% - len;

	if(len > %max%) {
		$('#chars').text(Math.abs(char)+' characters over the limit.').addClass('alert-danger')
	} else {
		$('#chars').text(char+' characters left').removeClass('alert-danger');
	}
}).keyup();
