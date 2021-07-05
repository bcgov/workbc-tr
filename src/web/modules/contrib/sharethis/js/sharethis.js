/**
 * @file
 * This file contains most of the code for the configuration page.
 */

(function ($, drupalSettings) {
  'use strict';
  Drupal.behaviors.shareThis = {
    attach: function (context) {
      if (typeof stLight !== 'undefined') {
        stLight.options(drupalSettings.sharethis);
      }
      stButtons.locateElements();
    }
  };
})(jQuery, drupalSettings);
