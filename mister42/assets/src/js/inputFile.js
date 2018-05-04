$('input[id=sourceFile]').change(function() {
	$('.custom-file-label').text('File "' + $(this).val().replace(/^.*\\/, "") + '" selected');
});
