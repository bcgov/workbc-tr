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
          return false;
        });
      }

      if($('.download-worksheets-button').length){
        $('.download-worksheets-button').on('click',function(){
          $('.download-worksheets-files-wrapper').addClass('open');
          return false;
        });
      }
      
      //left nav tab js 
      var div_top = jQuery('.left-nav').offset().top;
      var right_height = jQuery('.right-nav .main-section.active').height();
      var right_top = jQuery('.right-nav .main-section.active').offset().top;
      var winHeight = jQuery(window).height();
      var differ = right_height + right_top - 450;
      
      function stickynavbar(){
        var window_top = jQuery(window).scrollTop() + 200;
          
        if ((window_top > div_top) && (window_top < differ)) {
         jQuery('.left-nav').addClass('sticky');
        } 
        else {
         jQuery('.left-nav').removeClass('sticky');
        }



        if (window_top > differ) {
          jQuery('.left-nav').addClass('align-end');
        }
        else{
          jQuery('.left-nav').removeClass('align-end');
        }

      }

      jQuery(window).scroll(function() {
        if (window.matchMedia("(min-width: 768px)").matches) {
          stickynavbar()
        }
       });
       jQuery(window).resize(function() {
        if (window.matchMedia("(min-width: 768px)").matches) {
          stickynavbar();
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



       //scrolling selecion js
       window.addEventListener('load', () => {

        const observer = new IntersectionObserver(entries => {
          entries.forEach(entry => {
            const id = entry.target.getAttribute('id');
            if (entry.intersectionRatio > 0) {
              document.querySelector(`a[href="#${id}"]`).classList.add('active');
            } else {
              document.querySelector(`a[href="#${id}"]`).classList.remove('active');
            }
          });
        });
      
        // Track all sections that have an `id` applied
        document.querySelectorAll('.lesson_wrapper_main_para_item').forEach((section) => {
          observer.observe(section);
        });
        
      });

      //left nav tab js end

    }
  };

  if ($('#block-bcgov-teachers-increasetextsize').length > 0) {
    $('#block-bcgov-teachers-increasetextsize a[class^="size"]').click(function() {
      var elem = $(this);
      var size = elem.attr('class').substr(5);
      $('html').attr('data-font-size',size);
      adjustDynamicElements();
      return false;
    });
  }

  // Fix the position of the banner in IE only
  var bannerContainer = $('.banner-content');
  var bannerContent = $('.banner-content .banner-text');

  function bannerContentPosition() {
    bannerContainer.css({
      'height': '',
      'min-height': ''
    });

    if (bannerContent.outerHeight() > bannerContainer.outerHeight()) {
      bannerContainer.css({
        'height': 'auto',
        'min-height': bannerContainer.outerHeight()
      });
    }
    else {
      bannerContainer.css({
        'height': bannerContainer.outerHeight(),
        'min-height': 'auto'
      });
    }
  }

  if ($('.banner-content').length > 0 && !document.currentScript) {
    bannerContentPosition();

    var resizeTimeout;
    $(window).resize(function() {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(bannerContentPosition,150);
    });
  }

  function gradeElementSizes() {
    if ($(window).width() >= 768) {
      $('.paragraph--type--grade-access-blocks .field--name-field-grade-access-block-title').matchHeight();
      $('.paragraph--type--grade-access-blocks .field--name-field-grade-access-body').matchHeight();
    }
    else {
      $('.paragraph--type--grade-access-blocks .field--name-field-grade-access-block-title').matchHeight({remove: true});
      $('.paragraph--type--grade-access-blocks .field--name-field-grade-access-body').matchHeight({remove: true});
    }
  }

  // Resize the elements to have the same height
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
  }

  // Resize the elements to have the same height
  function featuredElementSizes() {
    if ($(window).width() >= 768) {
      $('.featured-resources-item').matchHeight({byRow:false});
      $('.featured-resources-item .views-field-title').matchHeight({byRow:false});
      $('.featured-resources-item .views-field-field-resource-card-summary').matchHeight({byRow:false});
      $('.featured-resources-item .views-field-field-term-resource-grade').matchHeight({byRow:false});
      $('.featured-resources-item .views-field-field-term-resource-stage').matchHeight({byRow:false});
    }
    else {
      $('.featured-resources-item').matchHeight({remove: true});
      $('.featured-resources-item .views-field-title').matchHeight({remove: true});
      $('.featured-resources-item .views-field-field-resource-card-summary').matchHeight({remove: true});
      $('.featured-resources-item .views-field-field-term-resource-grade').matchHeight({remove: true});
      $('.featured-resources-item .views-field-field-term-resource-stage').matchHeight({remove: true});
    }
  }

  // Resize the featured elements to have the same height
  if ($('.featured-resources-item').length > 0) {

    var featuredResizeTimeout;
    $(window).resize(function() {
      clearTimeout(featuredResizeTimeout);
      featuredResizeTimeout = setTimeout(featuredElementSizes,150);
    });

    // For demo purpose only
    $('.featured-resources-item').clone(true).appendTo('.featured-carousel');

    if ($('.featured-resources-item').length > 3) {
      $('.featured-carousel').slick({
        dots: true,
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 3,
        responsive: [
          {
            breakpoint: 1196,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 3,
            }
          },
          {
            breakpoint: 992,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2
            }
          },
          {
            breakpoint: 576,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
              adaptiveHeight: true
            }
          }
        ]
      });

      // On edge hit
      $('.featured-carousel').on('edge', function(event, slick, direction) {
        featuredElementSizes();
      });
    }

    featuredElementSizes();
  }


  function adjustDynamicElements() {
    if ($('.paragraph--type--grade-access-blocks').length > 0) {
      gradeElementSizes();
    }

    if ($('.banner-content').length > 0 && !document.currentScript) {
      bannerContentPosition();
    }

    if ($('.featured-resources-item').length > 0) {
      featuredElementSizes();
    }
  }
})(jQuery, Drupal, drupalSettings);