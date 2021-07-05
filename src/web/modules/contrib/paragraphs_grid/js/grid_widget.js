(function (Drupal, $) {

  'use strict';

  Drupal.behaviors.pgWidget = {

    // Debounce action
    debounce: function (func, wait) {
      if (window.pgWidgetTimeout) {
        clearTimeout(window.pgWidgetTimeout);
      }
      window.pgWidgetTimeout = setTimeout(func, wait);
    },

    attach: function (context) {
      var pgWidget = this;
      // Toggle widget form element.
      $('[data-toggle]', context).each(function (i, obj) {
        var $obj = $(obj);
        var elementClassSelector = $obj.data('toggle');
        var $statusIndicator = $obj.parent().find('[type="hidden"]');
        var $element = $obj.parent().find('.' + elementClassSelector);
        if ($statusIndicator.val() === 'open') {
          $element.addClass('pg-open');
        }

        $obj.on('click', function (e) {
          e.preventDefault();
          pgWidget.debounce(function () {
            if ($element.is('.pg-open')) {
              $element.removeClass('pg-open');
              $statusIndicator.val('');
            }
            else {
              $element.addClass('pg-open');
              $statusIndicator.val('open');
            }
          }, 200);
        });
      });
    }
  };

})(Drupal, jQuery);
