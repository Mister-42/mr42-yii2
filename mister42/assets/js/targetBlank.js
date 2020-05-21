$('a').attr('target', function() {
    if(this.host !== location.host && this.host !== 'www.mister42.de' && this.host !== 'www.mister42.me' && this.host !== 'www.xn--42-mlclt0afi.xn--p1ai') {
        return '_blank';
    }
});
