$('#formContent').keyup(function() {
	var len = $(this).val().length;
	var char = formCharCount.chars - len;

	if(len > formCharCount.chars) {
		$('#chars').html(formCharCount.lang.overLimit).addClass('alert-danger')
	} else {
		$('#chars').html(formCharCount.lang.charsLeft).removeClass('alert-danger');
	}
	$('#chars span.charcount').text(Math.abs(char));
}).keyup();
