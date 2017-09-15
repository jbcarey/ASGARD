document.addEventListener('DOMContentLoaded', function() {

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
  

});


document.addEventListener("scroll", function() {
  if (document.activeElement != document.body) {
    document.activeElement.blur();
  }
});




