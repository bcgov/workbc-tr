(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.themeJS = {
    attach: function (context, settings) {
      if (typeof context.location !== 'undefined') { // Only fire on document load.
        // Insert theme js specific lines here.
      }

      //close download popup js
      if($('.close-popup').length){
        $('.close-popup').on('click',function(){
          $('.download-worksheets-files-wrapper').removeClass('open');
        });
      }
      
      if($('.download-worksheets-button').length){
        $('.download-worksheets-button').on('click',function(){
          $('.download-worksheets-files-wrapper').addClass('open');
        });
      }
      
    }
  };

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

    var resizeTimeout;
    $(window).resize(function() {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(bannerContentPosition,150);
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
})(jQuery, Drupal, drupalSettings);