(function($) {
  Drupal.behaviors.e2e_paragraphs_gallery = {
    attach: function(context, settings) {
      
      baguetteBox.run('.gallery', {
        captions: function(element) {
          return element.getElementsByTagName('img')[0].title;
        }
      });
    }
  };
})(jQuery);
