for (var links = document.links, i = 0, a; a = links[i]; i++) {
    if (a.host !== location.host && a.host !== 'www.mister42.de' && a.host !== 'www.mister42.eu' && a.host !== 'www.xn--42-glceu4aeait.xn--p1ai') {
        a.target = '_blank';
    }
}
