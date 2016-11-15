$('#formContent').keyup(function() {
	len = $(this).val().length;
	char = %max% - len;

	if(len > %max%) {
		$('#chars').text('You are '+Math.abs(char)+' characters over the limit.').addClass('alert-danger')
	} else {
		$('#chars').text('You have '+char+' characters left').removeClass('alert-danger');
	}
}).keyup();
