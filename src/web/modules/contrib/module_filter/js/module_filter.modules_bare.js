(function($, Drupal) {

  'use strict';

  Drupal.ModuleFilter = Drupal.ModuleFilter || {};
  var ModuleFilter = Drupal.ModuleFilter;

  /**
   * Filter enhancements.
   */
  Drupal.behaviors.moduleFilterModulesBare = {
    attach: function(context) {
      if (ModuleFilter.input != undefined) {
        var $details = ModuleFilter.modulesWrapper.children('details');

        ModuleFilter.input.bind('winnow:start', function() {
          // Note that we first open all <details> to be able to use ':visible'.
          // Mark the <details> elements that were closed before filtering, so
          // they can be reclosed when filtering is removed.
          $details.show().not('[open]').attr('data-module_filter-state', 'forced-open');
        });
        ModuleFilter.input.bind('winnow:finish', function() {
          // Hide the package <details> if they don't have any visible rows.
          // Note that we first show() all <details> to be able to use ':visible'.
          $details.attr('open', true).each(function(index, element) {
            var $group = $(element);
            var $visibleRows = $group.find('tbody tr:visible');
            $group.toggle($visibleRows.length > 0);
          });

          // Return <details> elements that had been closed before filtering
          // to a closed state.
          $details.filter('[data-module_filter-state="forced-open"]')
            .removeAttr('data-module_filter-state')
            .attr('open', false);
        });
      }
    }
  };

})(jQuery, Drupal);
