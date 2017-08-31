$('form').bind('keyup change', 'input, textarea', function() {
	$("#output").val(function( index, value ) {
		return toMarkdown( $("#input").val(), { gfm: $("#gfm").prop('checked') } );
	});
}).keyup();
