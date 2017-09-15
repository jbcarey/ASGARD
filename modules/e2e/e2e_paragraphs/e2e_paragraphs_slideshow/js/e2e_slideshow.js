(function($) {
  Drupal.behaviors.e2e_paragraphs_slideshow = {
    attach: function(context, settings) {
      
      
      if(settings.e2e_paragraphs_slideshow.prevButton === null)
        $('.swiper-button-prev').hide();
      
      if(settings.e2e_paragraphs_slideshow.nextButton === null)
        $('.swiper-button-next').hide();
      
      /*
       * Swiper slider
       */
      var mySwiper = new Swiper ('.swiper-container', settings.e2e_paragraphs_slideshow) ;
      
    
      
      
    }
  };
})(jQuery);