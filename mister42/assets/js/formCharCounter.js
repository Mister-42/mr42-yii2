$('#formContent').keyup(function() {
	len = $(this).val().length;
	char = formCharCount.chars - len;

	if(len > formCharCount.chars) {
		$('#chars').html(formCharCount.lang.overLimit).addClass('alert-danger')
	} else {
		$('#chars').html(formCharCount.lang.charsLeft).removeClass('alert-danger');
	}
	$('#chars span.charcount').text(Math.abs(char));
}).keyup();
