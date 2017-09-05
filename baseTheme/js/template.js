document.addEventListener('DOMContentLoaded', (function() {

    smoothScroll.init();
    gumshoe.init();
    modals.init();
    dropdownMenu.init({
        selector: 'dropdown-menu',
        activeClass: 'active',
        openClass: 'open',
        test: {
            az: 3,
            er: 2 
        }
    });

/*
    lory(document.querySelector('.slideshow'), {
        classNameSlideContainer: 'slides',
        classNameFrame: 'frame',
        classNamePrevCtrl: 'prev',
        classNameNextCtrl: 'next',
        enableMouseEvents: true,
    });
*/

}));


document.addEventListener("scroll", (function() {
  if (document.activeElement != document.body) {
    document.activeElement.blur();
  }
}));




