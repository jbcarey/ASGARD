/**
 * @file
 * Integration file for hours_days paragraph.
 */

(function($) {
  Drupal.behaviors.e2e_hours_days = {
    attach: function(context, settings) {

      // PERIOD ACCORDEONS
      $('.inactive-period').click(function() {
        $(this).toggleClass('active');
      });
    }
  };
}
)(jQuery);
