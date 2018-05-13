$('.clipboard-js-init').on('mouseleave', function() {
	$(this).removeClass('btn-success').addClass('btn-primary').tooltip('hide').attr('data-original-title', 'Copy to Clipboard');
});

var cb = new ClipboardJS('.clipboard-js-init');

cb.on('success', function(e) {
	e.clearSelection();
	btnSucces('Copied!');
});

cb.on('error', function(e) {
	actionKey = (action === 'cut' ? 'X' : 'C');
	if (/iPhone|iPad/i.test(navigator.userAgent)) {
		btnSucces('No support');
	} else if (/Mac/i.test(navigator.userAgent)) {
		btnSucces('Press ⌘-' + actionKey + ' to ' + action);
	} else {
		btnSucces('Press Ctrl-' + actionKey + ' to ' + action);
	}
});

function btnSucces(txt) {
	$('.clipboard-js-init').removeClass('btn-primary').addClass('btn-success').attr('data-original-title', txt).tooltip('show');
}
