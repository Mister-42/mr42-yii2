$('.clipboard-js-init').on('mouseleave', function() {
	$(this).removeClass('btn-success').addClass('btn-primary').tooltip('hide').attr('data-original-title', 'Copy to Clipboard').tooltip('fixTitle');
});

cb = new Clipboard('.clipboard-js-init');
cb.on('success', function(e) {
	e.clearSelection();
	$('.clipboard-js-init').removeClass('btn-primary').addClass('btn-success').attr('data-original-title', 'Copied!').tooltip('fixTitle').tooltip('show');
});

cb.on('error', function(e) {
	actionKey = (action === 'cut' ? 'X' : 'C');
	if (/iPhone|iPad/i.test(navigator.userAgent)) {
		actionMsg = 'No support :(';
	} else if (/Mac/i.test(navigator.userAgent)) {
		actionMsg = 'Press ⌘-' + actionKey + ' to ' + action;
	} else {
		actionMsg = 'Press Ctrl-' + actionKey + ' to ' + action;
	}

	$('.clipboard-js-init').removeClass('btn-primary').addClass('btn-success').attr('data-original-title', actionMsg).tooltip('fixTitle').tooltip('show');
});
