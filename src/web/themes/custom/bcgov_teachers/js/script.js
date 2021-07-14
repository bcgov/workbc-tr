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
      
      //left nav tab js 
      var div_top = jQuery('.left-nav').offset().top;
      var right_height = jQuery('.right-nav').height();
      var right_top = jQuery('.right-nav').offset().top;
      var differ = right_height + right_top - 400;

      
      jQuery(window).scroll(function() {
        if (window.matchMedia("(min-width: 768px)").matches) {
          var window_top = jQuery(window).scrollTop();
          
          if ((window_top > div_top) && (window_top < differ)) {
           jQuery('.left-nav').addClass('sticky');
          } 
          else {
           jQuery('.left-nav').removeClass('sticky');
          }
        }
       });

       $('.leftnavbar--title a').on('click', function(){
        $('.right-nav .main-section').removeClass('active');
        $('.leftnavbar .leftnavbar--items').removeClass('active');
        $(this).closest('.leftnavbar--items').addClass('active');
        var hrefattr = $(this).attr('href');
        $('.right-nav '+hrefattr).addClass('active');
       });

       $('.proceed-nex-link').on('click',function(){
        $('.leftnavbar .leftnavbar--items.active').removeClass('active').next('.leftnavbar--items').addClass('active');
        $('.right-nav .main-section.active').removeClass('active').next('.main-section').addClass('active'); 
        $('html,body').animate({
          scrollTop: $('.right-nav').offset().top
        })
       });

       
      //left nav tab js end

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