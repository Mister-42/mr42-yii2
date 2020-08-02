var converter = new showdown.Converter();
$('form').bind('keyup change', 'input, textarea', function() {
    $('[name="output"]').val(function( index, value ) {
        if ($('[name="gfm"]').prop('checked')) {
            converter.setFlavor('github');
        }
        return converter.makeMd( $('[name="input"]').val() );
    });
}).keyup();
