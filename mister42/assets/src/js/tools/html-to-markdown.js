$('form').bind('keyup change', 'input, textarea', function() {
	$("[name='output']").val(function( index, value ) {
		return toMarkdown( $("[name='input']").val(), { gfm: $("[name='gfm']").prop('checked') } );
	});
}).keyup();
