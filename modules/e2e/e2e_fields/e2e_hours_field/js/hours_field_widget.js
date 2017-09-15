
(function($) {
  Drupal.behaviors.e2e_hours_field_widget = {
    attach: function(context, settings) {
      
      //$.getScript('jquery.timeentry.min.js');
		
      $('div.hours').each(function() {
        $('.timefield',this).timeEntry({show24Hours: true, spinnerImage: ''});
      });

    }
  };
})(jQuery);
