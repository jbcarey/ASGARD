document.addEventListener('DOMContentLoaded', (function() {

  // init smoothScroll
  smoothScroll.init();

  /*
   * Swiper slider
   */
  var slider = new Swiper ('.swiper-container', {
    
    // lazy load
    preloadImages: false,
    lazyLoading: true,
    lazyLoadingClass: 'lazy',
    lazyStatusLoadedClass: 'lazy-loaded',
    lazyLoadingInPrevNextAmount: 2,
  
    // Optional parameters
    direction: 'horizontal',
    loop: true,
    
    // pagination
    pagination: '.swiper-pagination',
    
    // Navigation arrows
    nextButton: '.swiper-button-next',
    prevButton: '.swiper-button-prev',

  });
  
}));
