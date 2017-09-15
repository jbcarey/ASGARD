/**
 * @file
 * Integration file for accordion paragraphs module.
 */

(function($) {
  Drupal.behaviors.e2e_paragraphs = {
    attach: function(context, settings) {
      
      $('.accordion .content-header', context).click(function() {
        $(this).toggleClass('active');
        $(this).next().slideToggle();
      });
      if ($('.accordion', context).length > 1) {
        $('.accordion .content-header').toggleClass('active');
        $('.accordion .content-main').slideToggle();
      }
    }
  };
})(jQuery);