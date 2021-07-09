(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.themeJS = {
    attach: function (context, settings) {
      if (typeof context.location !== 'undefined') { // Only fire on document load.
        // Insert theme js specific lines here.
      }
    }
  };

  // Feature to increase text size
  if ($('#block-bcgov-teachers-increasetextsize').length > 0) {
    $('#block-bcgov-teachers-increasetextsize a[class^="size"]').click(function() {
      var elem = $(this);
      var size = elem.attr('class').substr(5);
      $('html').attr('data-font-size',size);
      return false;
    });
  }

  // Fix the position of the banner in IE only
  if ($('.banner-content').length > 0 && !document.currentScript) {
    var container = $('.banner-content');
    var content = $('.banner-content .banner-text');

    bannerContentPosition();

    var bannerResizeTimeout;
    $(window).resize(function() {
      clearTimeout(bannerResizeTimeout);
      bannerResizeTimeout = setTimeout(bannerContentPosition,150);
    });

    function bannerContentPosition() {
      container.css({
        'height': '',
        'min-height': ''
      });

      if (content.outerHeight() > container.outerHeight()) {
        container.css({
          'height': 'auto',
          'min-height': container.outerHeight()
        });
      }
      else {
        container.css({
          'height': container.outerHeight(),
          'min-height': 'auto'
        })
      }
    }
  }

  if ($('.paragraph--type--grade-access-blocks').length > 0) {
    // Make the whole block clickable
    $('.paragraph--type--grade-access-blocks').click(function() {
      window.location = $(this).find("a").attr("href");
      return false;
    });

    gradeElementSizes();

    var gradeResizeTimeout;
    $(window).resize(function() {
      clearTimeout(gradeResizeTimeout);
      gradeResizeTimeout = setTimeout(gradeElementSizes,150);
    });

    // Resize the elements to have the same height
    function gradeElementSizes() {
      if ($(window).width() >= 768) {
        $('.paragraph--type--grade-access-blocks .field--name-field-grade-access-block-title').matchHeight();
        $('.paragraph--type--grade-access-blocks .field--name-field-grade-access-body').matchHeight();
      } else {
        $('.paragraph--type--grade-access-blocks .field--name-field-grade-access-block-title').matchHeight({remove: true});
        $('.paragraph--type--grade-access-blocks .field--name-field-grade-access-body').matchHeight({remove: true});
      }
    }
  }
})(jQuery, Drupal, drupalSettings);