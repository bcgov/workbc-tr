(function ($, Drupal, drupalSettings) {
  // Example of Drupal behavior loaded.
  Drupal.behaviors.themeJS = {
    attach: function (context, settings) {
      if (typeof context.location !== 'undefined') { // Only fire on document load.
        // Insert theme js specific lines here.
      }
    }
  };
})(jQuery, Drupal, drupalSettings);