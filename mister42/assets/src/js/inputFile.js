$('input[id=sourceFile]').change(function() {
	$('#file').val('File "' + $(this).val() + '" selected');
});
