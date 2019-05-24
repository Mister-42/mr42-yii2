$('input[id=sourceFile]').change(function() {
	$('.custom-file-label').html(inputFile.lang.selected);
	$('.custom-file-label span.filename').text($(this).val().replace(/^.*\\/, ''));
});
