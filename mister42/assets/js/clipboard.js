$('.clipboard-js-init').on('mouseleave', function() {
	$(this).removeClass('btn-success').addClass('btn-primary').tooltip('hide').attr('data-original-title', genpass.lang.copy);
});

function btnSucces(txt) {
	$('.clipboard-js-init').removeClass('btn-primary').addClass('btn-success').attr('data-original-title', txt).tooltip('show');
}

var cb = new ClipboardJS('.clipboard-js-init');

cb.on('success', function(e) {
	e.clearSelection();
	btnSucces(genpass.lang.copied);
});

cb.on('error', function(e) {
	var actionKey = (action === 'cut' ? 'X' : 'C');
	if (/iPhone|iPad/i.test(navigator.userAgent)) {
		btnSucces('No support');
	} else if (/Mac/i.test(navigator.userAgent)) {
		btnSucces('Press ⌘-' + actionKey + ' to ' + action);
	} else {
		btnSucces('Press Ctrl-' + actionKey + ' to ' + action);
	}
});
