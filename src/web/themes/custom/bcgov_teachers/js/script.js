(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.themeJS = {
    attach: function (context, settings) {
      if (typeof context.location !== 'undefined') { // Only fire on document load.
        // Insert theme js specific lines here.
      }

      //Cancel action on subscription changes
      $('.simplenews-confirm-removal #edit-cancel, .simplenews-confirm-multi #edit-cancel').on('click', function(e) {
        e.preventDefault();
        window.location.href = '/';
      });

      if ($('.sort-box .card-body .form-radios').length){
        $('.sort-box .card-body .form-radios .js-form-type-radio input[type=radio]').each(function() {
          if (this.checked) {
            var label = $(this).next('label').text();
            $('.sort-box .card-header .fieldset-legend').text(label);
          }

        });
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

      var resource_title = jQuery('.resource_wrapper__intro__title').text().trim().replace(':', ' -');
      var lesson_title = jQuery('.lesson_wrapper__intro__title').text().trim().replace(':', ' -');
      var site_title = jQuery('a.site-title').text().trim();

      var email_body = 'Check out the ' +lesson_title+resource_title+ ' : \n '+ window.location.href;

      $('.sharethis-wrapper .st_email_large').renameTag('a');
      $('.sharethis-wrapper .st_email_large').attr("href", "mailto:?subject=WorkBC’s "+site_title +" - Share a Lesson Plan or Resource" + "&body=" + encodeURIComponent(email_body));



    //subscription  button js
      var actionbtn = $('#simplenews-subscriptions-block-simple-new-teachers .form-actions').detach();
      $('#simplenews-subscriptions-block-simple-new-teachers .js-form-type-email').append(actionbtn);
    //subscription  button js end
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

      $(window).resize(function () {
        // if($('.left-nav:not(.mobiletab)').length){
        //   setTimeout(function () {
        //     $('.left-nav:not(.mobiletab)').removeClass('sticky');
        //   }, 1000);

        //   if (window.matchMedia("(min-width: 768px)").matches) {
        //     var lefttop = $('.left-nav:not(.mobiletab)').offset().top;
        //     var window_top = $(window).scrollTop() + 200;

        //     if ((window_top > lefttop)) {
        //       $('.left-nav:not(.mobiletab)').addClass('sticky');
        //     }
        //     else {
        //       $('.left-nav:not(.mobiletab)').removeClass('sticky');
        //     }
        //   }
        // }
      });

      if ($('.left-nav:not(.mobiletab)').length > 0) {
        $('leftnavbar .leftnavbar--items.active .leftnavbar-item ul li:first-child a').addClass('active');
        $('.leftnavbar--title a').click(function(e){
          e.stopImmediatePropagation();
          e.preventDefault();
          $('.right-nav .main-section').removeClass('active');
          $('.leftnavbar .leftnavbar--items').removeClass('active');
          $(this).closest('.leftnavbar--items').addClass('active');
          var hrefattr = $(this).attr('href');
          $('.right-nav '+hrefattr).addClass('active');
          $('html,body').animate({
            scrollTop: $('.right-nav').offset().top
          });
          $(this).parent('.leftnavbar--title').next('.leftnavbar-item').find('ul li:first-child a').click();
        });

        $('.proceed-nex-link').on('click',function(e){
          e.preventDefault();
          $('.leftnavbar .leftnavbar--items').removeClass('active');
          $('.main-section').removeClass('active');
          $(this).closest('.main-section').next('.main-section').addClass('active');
          var targetid =   $('.right-nav .main-section.active').attr('id');

          $('.left-'+targetid).closest('.leftnavbar--items').addClass('active');
          $('.left-' + targetid).closest('.leftnavbar--items').find('ul li:first-child a').addClass('active');

          if (window.matchMedia("(min-width: 768px)").matches) {
            $('html,body').animate({
              scrollTop: $('.right-nav').offset().top
            });
          }
          else{
            $('html,body').animate({
              scrollTop: $('.left-nav').offset().top
            });
          }
        });

        //scrolling selecion js
          $(window).scroll(function(){
            var winscrolltop = $(window).scrollTop();
            if (winscrolltop == 0) {
              $('.left-nav:not(.mobiletab)').removeClass('sticky');
              $('.leftnavbar .leftnavbar--items.active ul li:first-child a').addClass('active');
            }
            if (window.matchMedia("(min-width: 768px)").matches) {
              var div_top = $('.left-nav:not(.mobiletab)').offset().top;
              var window_top = $(window).scrollTop() + 200;

              if ((window_top > div_top)) {
                $('.left-nav:not(.mobiletab)').addClass('sticky');
              }
              else {
                $('.left-nav:not(.mobiletab)').removeClass('sticky');
              }
            }

            if (window.matchMedia("(min-width: 768px)").matches) {
              var scrollTop = $(document).scrollTop();
              var anchors = $('body').find('.main-section.active .lesson_wrapper_main_para_item .lesson_wrapper_main_para_item__title');

              for (var i = 0; i < anchors.length; i++) {
                var mainsectionheight = $(anchors[i]).closest('.lesson_wrapper_main_para_item').height();

                if (scrollTop > $(anchors[i]).offset().top - 200 && scrollTop < $(anchors[i]).offset().top + mainsectionheight - 200) {
                  $('a[href="#' + $(anchors[i]).closest('.lesson_wrapper_main_para_item').attr('id') + '"]').addClass('active');
                } else {
                  $('a[href="#' + $(anchors[i]).closest('.lesson_wrapper_main_para_item').attr('id') + '"]').removeClass('active');
                }

                if (scrollTop < $(anchors[0]).offset().top) {
                  $('.leftnavbar .leftnavbar--items.active ul li:first-child a').addClass('active');
                }
                
                var footer_top = $("#block-views-block-related-resource-and-lesson-plan-block-1").offset().top;
                var nav_height = $(".main_section .row .left-nav > .leftnavbar").height();
                
                if (scrollTop + nav_height + 500 > footer_top) {
                  $('.left-nav:not(.mobiletab)').addClass('align-end');
                  $('.left-nav:not(.mobiletab)').removeClass('sticky');
                }
                else {
                  $('.left-nav:not(.mobiletab)').removeClass('align-end');
                }
              }
            }

          });
      }
      //left nav tab js end

      //related news slider
      if ($('.related_news_slider .related_news_wrapper--item').length > 3) {
        $('.related_news_slider .row').not('.slick-initialized').slick({
          infinite: false,
          dots: true,
          slidesToShow: 3,
          slidesToScroll: 3,
          speed: 300,
          arrow: true,
          responsive: [
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 2,
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
      if (window.matchMedia("(max-width: 767px)").matches) {
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
      }

      //equal height for related plan box
      function setEqualHeight(arr) {
          var x = new Array([]);
          for (i = 0; i < arr.length; i++) {
              x[i] = jQuery(arr[i]).height('auto');
              x[i] = jQuery(arr[i]).outerHeight();
          }
          Max_Value = Array.max(x);
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

      //equal height for related plan box end
      if ($('body.page-node-type-resource').length > 0) {
        function setEqualHeight(arr) {
          var x = new Array([]);
          for (i = 0; i < arr.length; i++) {
            x[i] = jQuery(arr[i]).height('auto');
            x[i] = jQuery(arr[i]).outerHeight();
          }
          Max_Value = Array.max(x);
          for (i = 0; i < arr.length; i++) {
            x[i] = jQuery(arr[i]).outerHeight(Max_Value);
          }
        }

        Array.min = function (array) {
          return Math.min.apply(Math, array);
        };

        Array.max = function (array) {
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
      }

      //filter open close js
      setTimeout(function(){
        jQuery('.search-assest .card-body .bef-toggle').text('All Lesson Plans & Resources');
      },1000);

      jQuery('.search-solr-box--wrapper .card-header').on('click', function (event) {
        event.stopImmediatePropagation();
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

      //show resut position replacement
      var result = jQuery('.view-solr-results .view-header').text();
      jQuery('.show-result-wrapper .container .view-header').text(result);

      if (jQuery('.search_keyword .search-btn').length > 0) {
        jQuery('.search_keyword .search-btn').on('click', function () {
          jQuery('form#views-exposed-form-solr-results-page-1').submit();
        });
      }
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
                infinite: false,
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
        infinite: false,
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


function departmentFunction(){
  jQuery('.search_filter__results').show();
  jQuery('.dept-appned').remove();
    jQuery('.filterbox__selectgroup input[type=checkbox]:checked').each(function () {
      if(this.checked){
        var selectedValue = jQuery(this).val();
        var response = jQuery('label[for="' + this.id + '"]').html();
        var value = this.value;
        var name = this.name;
        jQuery(".search_filter__results .search_filter__results-inner").append("<div class='dept-appned'><span class='dept-title-section'>" + response + "</span><span class='deptclose' data-removed='" + this.id + "' data-name='" + name + "' data-value='" + value +"'>x</span></div>" );
      }
    });

    var searchkeyword = jQuery('.search_keyword input').val();
    if (searchkeyword != ''){
      jQuery(".search_filter__results .search_filter__results-inner").append("<div class='dept-appned'><span class='dept-title-section'>" + searchkeyword + "</span><span class='key-title deptclose' data-removed='" + this.id + "'>x</span></div>");
    }


}

function removeparam(){
  var uri = window.location.toString();
  if (uri.indexOf("?") > 0) {
    var clean_uri = uri.substring(0, uri.indexOf("?"));
    window.history.replaceState({}, document.title, clean_uri);
  }
  location.reload();
}

jQuery(document).ajaxComplete(function(event, xhr, settings) {
  
  departmentFunction();

  jQuery( ".deptclose" ).on('click', function(event) {
    event.stopPropagation();
    var self = this;
    var ValueRemoved = jQuery(self).attr('data-removed');
    jQuery('#' + ValueRemoved).click();
    //setTimeout(function() {
    jQuery(self).parent().hide();
    if (jQuery(self).hasClass('key-title')) {
      jQuery('.search_keyword input').val('');
      jQuery('form#views-exposed-form-solr-results-page-1').submit();
    }
    var inputvalue = jQuery(self).attr('data-value');
    var inputname = jQuery(self).attr('data-name');

    var param = inputname + '=' + inputvalue;
    if (window.location.search.indexOf('&') > 1) {
      var searchquery = decodeURIComponent(window.location.search).split('&');
      if (jQuery.inArray(param, searchquery) == 1) {
        searchquery = jQuery.grep(searchquery, function (value) {
          return value != param;
        });
        searchquery = searchquery.join('&');
        window.history.pushState({}, document.title, "?" + searchquery);
        jQuery('form#views-exposed-form-solr-results-page-1').submit();
      }
    }
    else {
      var searchquery = decodeURIComponent(window.location.search).split('?');
      if (param == searchquery[1]) {
        window.history.pushState({}, document.title, "?" + searchquery);
        jQuery('form#views-exposed-form-solr-results-page-1').submit();
      }
    }
  });




  if (jQuery('.dept-appned').length > 0) {
    jQuery(".clear-all").show();
  }
  else {
    jQuery('.search_filter__results').hide();
    jQuery(".clear-all").hide();
  }

  jQuery( ".clear-all" ).on('click', function(event) {
    jQuery('.filterbox__selectgroup input[type=checkbox]:checked').click();
    jQuery('.search_filter__results').hide();
    jQuery('.search_keyword input').val('');
    jQuery('form#views-exposed-form-solr-results-page-1').submit();
    jQuery('.search-assest .card-body .form-radios .form-item:first-child input').click();
    removeparam();
  });

});

//search filter parent child relation js
function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

jQuery(document).ready(function(){  
  //Wait for the count div to load
  function waitForElement(elementPath, searchbox, callBack){
    window.setTimeout(function(){
      if(jQuery(elementPath).length && jQuery(searchbox).length){
        callBack(elementPath, jQuery(elementPath));
      }else{
        waitForElement(elementPath, searchbox, callBack);
      }
    },1000)
  }

  /*** Initial Load ***/
  waitForElement(".show-result-wrapper .view-header",".search_keyword input", function(){
    //Send tracking info to snowplow
    s_tracker('load');
  });

/////////////Snowplow Tracker for search results/////////////////

function s_tracker(action_event) {
  var splashGradeArray = new Array();
  var splashStageArray = new Array();
  var splashCompetencyArray = new Array();
  var splashAudienceArray = new Array();

  jQuery('.filterbox__selectgroup .filterbox__dd-grade input[type=checkbox]:checked').each(function () {
    if(this.checked){
        var label = jQuery(this).parent().find('label').text();
        splashGradeArray.push(label);
    }
  });

  jQuery('.filterbox__selectgroup .filterbox__dd-stage input[type=checkbox]:checked').each(function () {
    if(this.checked){
        var label = jQuery(this).parent().find('label').text();
        splashStageArray.push(label);
    }
  });

  jQuery('.filterbox__selectgroup .filterbox__dd-competency input[type=checkbox]:checked').each(function () {
    if(this.checked){
        var label = jQuery(this).parent().find('label').text();
        splashCompetencyArray.push(label);
    }
  });

  jQuery('.filterbox__selectgroup .filterbox__dd-audience input[type=checkbox]:checked').each(function () {
    if(this.checked){
        var label = jQuery(this).parent().find('label').text();
        splashAudienceArray.push(label);
    }
  });
   
  var grades = stage = competency = audience = "All";

  var category = jQuery('input[name="field_term_resource_asset_type"]:checked').parent().find('label').text();
  if(category == '') {
      category = "All Lesson Plans & Resources";
  }

  var keyword = null;
  
  if(splashGradeArray.length !== 0) {
      grades = splashGradeArray.join(',')
  }

  if(splashStageArray.length !== 0) {
      stage = splashStageArray.join(',')
  }

  if(splashCompetencyArray.length !== 0) {
      competency = splashCompetencyArray.join(',')
  }

  if(splashAudienceArray.length !== 0) {
      audience = splashAudienceArray.join(',')
  }

  //var action_event = "update";
  
  var count_result = jQuery('.show-result-wrapper .view-header').text().trim().split(" ")[1];
  console.log(count_result);
  var keyword = jQuery('.search_keyword input').val();
  if(keyword == "") {
      keyword = null;
  }
    window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/find_resources/jsonschema/1-0-0",
      "data": {
          "action": action_event,
          "count": parseInt(count_result),
          "filters": {
          "focus_area": grades,
          "lifecycle_stage": stage,
          "competencies": competency,
          "audiences": audience,
          "show_category": category,
          "keyword": keyword
          }
      }
    });
  }

  setTimeout(function(){
    jQuery('.search-solr-box__inner .card-body .form-checkboxes .form-item:first-child').addClass('parent-item');
    jQuery('.search-solr-box__inner .card-body .form-checkboxes .form-item + .form-item').addClass('child-item');

    jQuery(".parent-item").each(function () {
      jQuery(this).nextUntil(".parent-item").addBack().wrapAll('<div class="checkbox-item parent-checkbox-item"></div>');
    });
    jQuery(".child-item").each(function () {
      jQuery(this).nextUntil(".child-item").addBack().wrapAll('<div class="checkbox-item child-checkbox-item"></div>');
    });

    jQuery('.parent-checkbox-item > .form-item input').on('change', function () {
      jQuery(this).closest('.parent-checkbox-item').find('.checkbox-item input[type="checkbox"]').prop('checked', this.checked);
      var count_p = 0;
      jQuery(document).ajaxComplete(function(){
        if(count_p == 0)
        s_tracker('update');
        count_p++;
      });
    });

    jQuery('.search-assest .form-item input').on('change', function () {
      var count_a = 0;
      jQuery(document).ajaxComplete(function(event, xhr, settings){
        if(count_a == 0)
        s_tracker('update');
        count_a++;
      });
    });

    jQuery('.child-checkbox-item .form-item input').on('change', function () {
      var count_c = 0;
      jQuery(document).ajaxComplete(function(event, xhr, settings){
        if(count_c == 0)
        s_tracker('update');
        count_c++;
      });
   
      var totalcheckbox = jQuery(this).closest('.parent-checkbox-item').find('input').length;
      var chechedchekbox = jQuery(this).closest('.parent-checkbox-item').find('input:checked').length;
      var notcheckedbox = totalcheckbox - chechedchekbox;
      if (notcheckedbox == 0) {
        jQuery(this).closest('.parent-checkbox-item').find('> .form-item input').prop('checked', 'checked');
      }
      else {
        jQuery(this).closest('.parent-checkbox-item').find('.parent-item').removeClass('selected');
        jQuery(this).closest('.parent-checkbox-item').find('> .form-item input').prop('checked', '');
      }
    });

    jQuery('.filterbox__selectgroup .filterbox__dd').each(function () {
      if (!jQuery(this).find('input:checked').length > 0) {
        jQuery(this).find('.parent-checkbox-item > .parent-item').addClass('selected');
      }
      else {
        jQuery(this).find('.parent-item').removeClass('selected');
      }
    });

    departmentFunction();
    if (jQuery('.dept-appned').length > 0) {
      jQuery(".clear-all").show();
    }
    else {
      jQuery('.search_filter__results').hide();
      jQuery(".clear-all").hide();
    }

    jQuery(".deptclose").on('click', function (event) {
      event.stopPropagation();
      var self = this;
      var ValueRemoved = jQuery(self).attr('data-removed');

      jQuery('#' + ValueRemoved).click();
      //setTimeout(function() {
      jQuery(self).parent().hide();
      if (jQuery(self).hasClass('key-title')) {
        jQuery('.search_keyword input').val('');
        jQuery('form#views-exposed-form-solr-results-page-1').submit();
      }

      var inputvalue = jQuery(self).attr('data-value');
      var inputname = jQuery(self).attr('data-name');

      var param = inputname + '=' + inputvalue;
      if (window.location.search.indexOf('&') > 1) {
        var searchquery = decodeURIComponent(window.location.search).split('&');
        if (jQuery.inArray(param, searchquery) == 1) {
          searchquery = jQuery.grep(searchquery, function (value) {
            return value != param;
          });
          searchquery = searchquery.join('&');
          window.history.pushState({}, document.title, "?" + searchquery);
          jQuery('form#views-exposed-form-solr-results-page-1').submit();
        }
      }
      else{
        var searchquery = decodeURIComponent(window.location.search).split('?');
        if ( param == searchquery[1]) {
          window.history.pushState({}, document.title, "?" + searchquery);
          jQuery('form#views-exposed-form-solr-results-page-1').submit();
        }
      }

    });

    jQuery(".clear-all").on('click', function (event) {
      event.preventDefault();
      jQuery('.filterbox__selectgroup input[type=checkbox]:checked').click();
      jQuery('.search_filter__results').hide();
      jQuery('.search_keyword input').val('');
      // jQuery('form#views-exposed-form-solr-results-page-1').submit();
      jQuery('.search-assest .card-body .form-radios .form-item:first-child input').click();
      removeparam();
   });

    jQuery("#views-exposed-form-solr-results-page-1").submit(function (e) {
      departmentFunction();
    });


    //subscription form popup js

    jQuery('#simplenews-subscriptions-block-simple-new-teachers .form-actions input').attr('type', 'button');
    jQuery('#simplenews-subscriptions-block-simple-new-teachers .form-actions input').on('click', function () {
      if(isEmail(jQuery('#simplenews-subscriptions-block-simple-new-teachers .form-email').val())){
        //jQuery('#simplenews-subscriptions-block-simple-new-teachers .form-email').removeClass('error');
        jQuery('#confirm-submit').addClass('in');
        jQuery('#confirm-submit .modal-dialog').scrollTop(0);
        jQuery('html').addClass('o-hidden');
      }
      else{
        jQuery('#simplenews-subscriptions-block-simple-new-teachers .form-email').focus();
        //jQuery('#simplenews-subscriptions-block-simple-new-teachers .form-email').addClass('error');
        return false;
      }

    });

    jQuery('#submit-form').on('click', function () {
      if (jQuery("#terms").is(":checked")){
        //jQuery('.term-agree').removeClass('error');
        jQuery('#simplenews-subscriptions-block-simple-new-teachers').submit();
        jQuery('#confirm-submit').removeClass('in');
      }
      else{
        jQuery('.term-agree').addClass('error');
        return false;
      }
    });

    jQuery('.cancel-form').on('click',function(){
      jQuery('#confirm-submit').removeClass('in');
      jQuery('html').removeClass('o-hidden');
      if (jQuery("#terms").is(":checked")) {
        jQuery("#terms").prop("checked", false);
      }
      //jQuery('.term-agree').removeClass('error');
    });
  //subscription form popup js end

  },100);

  jQuery(window).resize(function () {
    if (jQuery(window).width() <= 767) {
      if (jQuery('.resource_wrapper_left__img .resource-media').length > 1) {
        jQuery('.resource_wrapper_left__img').slick({
          infinite: false,
          dots: true,
          slidesToShow: 1,
          slidesToScroll: 1,
          speed: 300,
          arrow: false,
        });
      }
      // jQuery('.navbar-brand .site-logo').removeClass('d-block');
      // jQuery('.navbar-brand .site-title').removeClass('d-block');
      // jQuery('.navbar-brand .site-logo').removeClass('d-table-cell');
      // jQuery('.navbar-brand .site-title').removeClass('d-table-cell');
    }
    else {
      jQuery('.resource_wrapper_left__img').slick('unslick');
    }

    if (jQuery('.related_news_slider .related_news_wrapper--item').length > 3) {
      jQuery('.related_news_slider .row').not('.slick-initialized').slick({
        infinite: false,
        dots: true,
        slidesToShow: 3,
        slidesToScroll: 3,
        speed: 300,
        arrow: true,
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2,
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
  });

  if (jQuery('.path-search').length > 0) {
    jQuery('.search-assest .card-body .form-radios .form-item:first-child input').click();
  }
});
