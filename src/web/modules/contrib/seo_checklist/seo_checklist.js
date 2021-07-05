(function ($) {
  "use strict";

  /**
   * Causes external links to open in a new window.
   */
  Drupal.behaviors.seo_checklistOpenExternalLinksInNewWindow = {
    attach: function (context) {
      // Open external links in a new window.
      $('#checklistapi-checklist-form details a', context).filter(function () {
        // Ignore non-HTTP (e.g. mailto:) link.
        return this.href.indexOf('http') === 0;
      }).filter(function () {
        // Filter out links to the same domain.
        return this.hostname && this.hostname !== location.hostname;
      }).each(function () {
        // Send all links to drupal.org to the same window. Open others in their
        // own windows.
        $(this).attr('target', (this.hostname === 'drupal.org') ? 'drupal_org' : '_blank');
      });
    }
  };

  /**
   * Adds dynamic toggling of CLI commands display.
   */
  Drupal.behaviors.seo_checklistToggleCliCommandsDisplay = {
    attach: function (context) {
      var checkbox = $('#edit-checklistapi-be-efficient-show-cli-commands', context);
      var commands = $('.cli-commands', context);
      checkbox.is(':checked') || commands.hide();
      checkbox.click(function () {
        commands.toggle(this.checked);
      });
    }
  };

})(jQuery);
