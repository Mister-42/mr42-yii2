$('a').attr('target', function() {
	if(this.host !== location.host) {
		return '_blank';
	}
});
