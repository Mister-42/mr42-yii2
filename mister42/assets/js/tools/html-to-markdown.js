var turndownService = new TurndownService();
$('form').bind('keyup change', 'input, textarea', function() {
	$('[name="output"]').val(function( index, value ) {
		return turndownService.turndown( $('[name="input"]').val(), { gfm: $('[name="gfm"]').prop('checked') } );
	});
}).keyup();
