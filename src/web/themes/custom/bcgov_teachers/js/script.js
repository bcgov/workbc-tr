(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.themeJS = {
    attach: function (context, settings) {
      if (typeof context.location !== 'undefined') { // Only fire on document load.
        // Insert theme js specific lines here.
      }


      //add placeholder in subscription form
      if(jQuery('.simplenews-subscriptions-block-simple-new-teachers').length){
        jQuery('.simplenews-subscriptions-block-simple-new-teachers input').attr('placeholder', 'Enter your email address');
      }
      

      //add placeholder in subscription form end

      $.fn.renameTag = function (replaceWithTag) {
        this.each(function () { 
            var outerHtml = this.outerHTML; 
            var tagName = $(this).prop("tagName"); 
            var regexStart = new RegExp("^<" + tagName, "i"); 
            var regexEnd = new RegExp("</" + tagName + ">$", "i");     
            outerHtml = outerHtml.replace(regexStart, "<" + replaceWithTag);     
            outerHtml = outerHtml.replace(regexEnd, "</" + replaceWithTag + ">"); 
            
            $(this).replaceWith(outerHtml); 
       
       }); 
       return this; 

      }

      var resource_title = jQuery('.resource_wrapper__intro__title').text().trim();
      var lesson_title = jQuery('.lesson_wrapper__intro__title').text().trim();
      var site_title = jQuery('a.site-title').text().trim();
      
      $('.sharethis-wrapper .st_email_large').renameTag('a'); 
      $('.sharethis-wrapper .st_email_large').attr("href", "mailto:?subject="+site_title +" " + lesson_title+resource_title + "&body=Check out the" +lesson_title+resource_title+ "for" +site_title+ "Resources here:\n" + window.location.href);


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
      if ($('.left-nav:not(.mobiletab)').length > 0) {
        $('leftnavbar .leftnavbar--items.active .leftnavbar-item ul li:first-child a').addClass('active');

        var div_top = $('.left-nav:not(.mobiletab)').offset().top;
        var right_height = $('.right-nav .main-section.active').height();
        var right_top = $('.right-nav .main-section.active').offset().top;
        var winHeight = $(window).height();
        var differ = right_height + right_top - 450;

        function stickynavbar(){
          var window_top = $(window).scrollTop() + 200;

          if ((window_top > div_top) && (window_top < differ)) {
           $('.left-nav:not(.mobiletab)').addClass('sticky');
          }
          else {
           $('.left-nav:not(.mobiletab)').removeClass('sticky');
          }
        }

        $(window).scroll(function() {
          if (window.matchMedia("(min-width: 768px)").matches) {
            stickynavbar()
          }
        });
        $(window).resize(function() {
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
          });
        });

        //scrolling selecion js
        $(window).scroll(function(){
          var scrollTop = $(document).scrollTop();
          var anchors = $('body').find('.main-section.active .lesson_wrapper_main_para_item .lesson_wrapper_main_para_item__title');

          for (var i = 0; i < anchors.length; i++){
            var mainsectionheight = $(anchors[i]).closest('.lesson_wrapper_main_para_item').height();

            if (scrollTop > $(anchors[i]).offset().top - 200 && scrollTop < $(anchors[i]).offset().top + mainsectionheight - 200) {
                $('a[href="#' + $(anchors[i]).closest('.lesson_wrapper_main_para_item').attr('id') + '"]').addClass('active');
            } else {
                $('a[href="#' + $(anchors[i]).closest('.lesson_wrapper_main_para_item').attr('id') + '"]').removeClass('active');
            }

            var lastmainsectionheight = $('.main-section.active .lesson_wrapper_main_para_item:last-child').height();

            if(scrollTop > ($('.main-section.active .lesson_wrapper_main_para_item:last-child .lesson_wrapper_main_para_item__title').offset().top + lastmainsectionheight) - 800){
              $('.left-nav').addClass('align-end');
            }
            else {
              $('.left-nav').removeClass('align-end');
            }
          }
        });
      }
      //left nav tab js end

      //related news slider
      if ($('.related_news_slider .related_news_wrapper--item').length > 3) {
        $('.related_news_slider .row').slick({
          infinite: false,
          dots: true,
          slidesToShow: 3,
          slidesToScroll: 1,
          speed: 300,
          arrow: true,
          responsive: [
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
              }
            },
            {
              breakpoint: 767,
              settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                arrow: false
              }
            }
          ]
        });
      }
      if ($('.resource_wrapper_left__img .resource-media').length > 1) {
        $('.resource_wrapper_left__img').slick({
          infinite: false,
          dots: true,
          slidesToShow: 1,
          slidesToScroll: 1,
          speed: 300,
          arrow: false,
        });
      }

      //equal height for related plan box
      function setEqualHeight(arr) {
          var x = new Array([]);
          for (i = 0; i < arr.length; i++) {
              x[i] = jQuery(arr[i]).height('auto');
              x[i] = jQuery(arr[i]).outerHeight();
          }
          Max_Value = Array.max(x) + 20;
          for (i = 0; i < arr.length; i++) {
              x[i] = jQuery(arr[i]).outerHeight(Max_Value);
          }
      }

      Array.min = function(array) {
          return Math.min.apply(Math, array);
      };

      Array.max = function(array) {
          return Math.max.apply(Math, array);
      };

      if (window.matchMedia("(min-width: 768px)").matches) {
        setEqualHeight(jQuery('.related_news_slider .related_news_wrapper--item-single'));
        jQuery('.search_wrapper .row').each(function(){
          setEqualHeight(jQuery(this).find('.related_news_wrapper--item'));
        });
      }

      jQuery(window).resize(function(){
        if (window.matchMedia("(min-width: 768px)").matches) {
          setEqualHeight(jQuery('.related_news_slider .related_news_wrapper--item'));

          jQuery('.search_wrapper .row').each(function(){
            setEqualHeight(jQuery(this).find('.related_news_wrapper--item'));
          });
        }
      });

      //equal height for related plan box end

      //filter open close js
      jQuery('.search-solr-box--wrapper .card-header').on('click', function (event) {
        event.stopPropagation();
        if(jQuery(this).hasClass('open')){
          jQuery(this).removeClass('open');
        } 
        else{
          jQuery('.search-solr-box--wrapper .card-header').removeClass('open');
          jQuery(this).addClass('open'); 
        }
      });

      var $target = jQuery('.search-solr-box--wrapper .card-header');
      jQuery(document).mouseup(e => {
        if (!$target.is(e.target) && $target.has(e.target).length === 0) 
        {
          jQuery('.search-solr-box--wrapper .card-header').removeClass('open');
        }
      });


      jQuery('.filterbox__title.hide_title').hide();
      jQuery('.filterbox__title.show_title').on('click',function(){
          jQuery(this).closest('.filterbox').addClass('open');
          jQuery(this).hide();
          jQuery('.filterbox__title.hide_title').show();
      });
      jQuery('.filterbox__title.hide_title').on('click',function(){
        jQuery(this).closest('.filterbox').removeClass('open');
        jQuery(this).hide();
        jQuery('.filterbox__title.show_title').show();
    });
      //filter open close js end 
    }
  }

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
      $('.paragraph--type--grade-access-blocks .field--name-field-grade-access-block-title, .paragraph--type--grade-access-blocks .field--name-field-grade-access-body').matchHeight({remove: true});

      $('.paragraph--type--grade-access-blocks .field--name-field-grade-access-block-title').matchHeight();
      $('.paragraph--type--grade-access-blocks .field--name-field-grade-access-body').matchHeight();
    }
    else {
      $('.paragraph--type--grade-access-blocks .field--name-field-grade-access-block-title, .paragraph--type--grade-access-blocks .field--name-field-grade-access-body').matchHeight({remove: true});
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
    if ($(window).width() >= 576) {
      // Reset before applying
      $('.featured-resources-item, .featured-resources-item .views-field-title, .featured-resources-item .views-field-field-resource-card-summary, .featured-resources-item .views-field-field-term-resource-grade, .featured-resources-item .views-field-field-term-resource-stage').matchHeight({remove: true});

      $('.featured-resources-item .views-field-title').matchHeight({byRow:false});
      $('.featured-resources-item .views-field-field-resource-card-summary').matchHeight({byRow:false});
      $('.featured-resources-item .views-field-field-term-resource-grade').matchHeight({byRow:false});
      $('.featured-resources-item .views-field-field-term-resource-stage').matchHeight({byRow:false});
      $('.featured-resources-item').matchHeight({byRow:false});
    }
    else {
      $('.featured-resources-item, .featured-resources-item .views-field-title, .featured-resources-item .views-field-field-resource-card-summary, .featured-resources-item .views-field-field-term-resource-grade, .featured-resources-item .views-field-field-term-resource-stage').matchHeight({remove: true});
    }
  }

  // For demo purpose only
  // $($('.featured-resources-item')[0]).clone(true).appendTo('.featured-carousel'); // +1 item
  // $('.featured-resources-item').clone(true).appendTo('.featured-carousel'); // +n items

  var itemLength = $('.featured-resources-item').length;
  if (itemLength > 1) {
    // Resize the featured elements to have the same height
    var featuredResizeTimeout;
    $(window).resize(function() {
      clearTimeout(featuredResizeTimeout);
      featuredResizeTimeout = setTimeout(featuredElementSizes,150);
    });

    // Carousel only on mobile screens for 2-3 items
    if (itemLength == 2 || itemLength == 3) {
      $(window).on('load resize orientationchange', function() {
        $('.featured-carousel').each(function() {
          var $carousel = $(this);
          if ($(window).outerWidth() >= 768) {
            if ($carousel.hasClass('slick-initialized')) {
              $carousel.slick('unslick');
            }
          }
          else {
            if (!$carousel.hasClass('slick-initialized')) {
              $carousel.slick({
                dots: true,
                infinite: true,
                slidesToShow: 3,
                slidesToScroll: 3,
                responsive: [
                  {
                    breakpoint: 768,
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
                      adaptiveHeight: true,
                      arrows: false
                    }
                  }
                ]
              });
            }
          }
        });
      });
    }

    // Carousel on all screen sizes for 4+ items
    if (itemLength > 3) {
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
              adaptiveHeight: true,
              arrows: false
            }
          }
        ]
      });
    }

    // On edge and init hit, it resizes the featured elements to have the same height
    $('.featured-carousel').on('edge init', function(event, slick, direction) {
      featuredElementSizes();
    });

    // Resize the featured elements to have the same height
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