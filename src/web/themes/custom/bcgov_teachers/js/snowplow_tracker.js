(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.snowplow = {
      attach: function (context, settings) {

        if (typeof context.location !== 'undefined') { // Only fire on document load.
            var action_event = "load";
            var count = 0;
            
        }
        //var count_results = $('.search_wrapper .row > div').length;
        var splashGradeArray = new Array();
        var splashStageArray = new Array();
        var splashCompetencyArray = new Array();
        var splashAudienceArray = new Array();

        $('.filterbox__selectgroup .filterbox__dd-grade input[type=checkbox]:checked').each(function () {
            if(this.checked){
                var label = $(this).parent().find('label').text();
                splashGradeArray.push(label);
            }
        });

        $('.filterbox__selectgroup .filterbox__dd-stage input[type=checkbox]:checked').each(function () {
            if(this.checked){
                var label = $(this).parent().find('label').text();
                splashStageArray.push(label);
            }
        });

        $('.filterbox__selectgroup .filterbox__dd-competency input[type=checkbox]:checked').each(function () {
            if(this.checked){
                var label = $(this).parent().find('label').text();
                splashCompetencyArray.push(label);
            }
        });

        $('.filterbox__selectgroup .filterbox__dd-audience input[type=checkbox]:checked').each(function () {
            if(this.checked){
                var label = $(this).parent().find('label').text();
                splashAudienceArray.push(label);
            }
        });
        
        var grades = stage = competency = audience = "All";

        var category = $('input[name="field_term_resource_asset_type"]:checked').parent().find('label').text();
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

        waitForElement(".show-result-wrapper .view-header",".search_keyword input", function(){
        // $('.view-solr-results').once('snowplow').each(function() {
        //     count_result = 0;
        //     var action_event = "load";
        //     count_result = $('.show-result-wrapper .view-header').text().trim().split(" ")[1];
        //     var keyword = $('.search_keyword input').val();
        //     if(keyword == "") {
        //         keyword = null;
        //     }
        //     console.log(keyword);
        //         window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/find_resources/jsonschema/1-0-0",
        //         "data": {
        //             "action": action_event,
        //             "count": parseInt(count_result),
        //             "filters": {
        //             "focus_area": grades,
        //             "lifecycle_stage": stage,
        //             "competencies": competency,
        //             "audiences": audience,
        //             "show_category": category,
        //             "keyword": keyword
        //             }
        //         }
        //         });
        //     });
        });
        
        
        if($('.lesson_wrapper__intro__title').length > 0) {
            var resource_id = $('.lesson_wrapper__intro__title').text().trim();
        } else if('resource_wrapper__intro__title') {
            var resource_id = $('.resource_wrapper__intro__title').text().trim();
        } else {
            var resource_id = 'content';
        }
        var path =  window.location.pathname; 
        var category = path.split("/")[1];   
        var link_text = null;

        //navbar scroll click event moved
        $('.leftnavbar-item li a').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            e.stopPropagation();
            //call snowplow
            link_text = null;
            if($(this).is('[class^=left-paragraph-]')) {
              count++;
              var click_type = $(this).parents('.leftnavbar--items').find('.leftnavbar--title a').text();
              click_type = "nav_"+click_type.replace(' ','_')+"_"+$(this).text().replace(' ','_').replace(':','_');
              console.log(click_type);
              snowplow_tracker(category, click_type, link_text, count)
            }
            var hrefattr = $(this).attr('href');
            $('html,body').animate({
              scrollTop: $(hrefattr).offset().top - 100
            });
          });
          setTimeout(function(){
            $('.leftnavbar .leftnavbar--items.active ul li:first-child a').addClass('active');
          },100);

        //Track resource and lesson plans click
        $('.page-node-type-resource .leftnavbar a,'+
          '.page-node-type-resource .download-worksheets-files-wrapper a,' +
          '.page-node-type-resource .go-to-resource-wrapper a,' +
          '.page-node-type-resource .main_section a[target="_blank"],'+
          '.page-node-type-resource .related_news_wrapper_title a',context).once('num_'+count).on('click', function() {  
               if($(this).parent().hasClass("file-url") && $(this).text() == "Download All") {
                   count++;
                   click_type = 'download_all_worksheets'
                   snowplow_tracker(category, click_type, link_text, count)
               } 
               if($(this).parent().hasClass("file-title")){
                   count++;
                   click_type = 'download_file'
                   link_text = $(this).attr('title');
                   snowplow_tracker(category, click_type, link_text, count)
               } 
               if ($(this).attr('target') == '_blank' && !$(this).parent().hasClass('file-title')) {
                   count++;
                   click_type = 'external_link';
                   link_text = $(this).attr('href');
                   snowplow_tracker(category, click_type, link_text, count)
               }
               if($(this).parent().hasClass('related_news_wrapper_title')) {
                    count++;
                    click_type = 'related_resource';
                    link_text = $(this).text();
                    snowplow_tracker(category, click_type, link_text, count)
               }
               return true;
        });

        //Social share links
        $('.page-node-type-resource .sharethis-wrapper span',context).once('num_'+count).on('click', function() {
            link_text = $(this).attr('displaytext');
            count++;
            click_type = 'social';
            snowplow_tracker(category, click_type, link_text, count);
        });

        //Proceed links
        $('.proceed-nex-link a',context).once('num_'+count).on('click', function() {
            count++;
            link_text = $(this).text();
            click_type = "nav_"+link_text;
            snowplow_tracker(category, click_type, link_text, count);
        });

        //Download worksheets
        $('.download-worksheets-button',context).once('num_'+count).on('click', function() {
            click_type = 'download_worksheets';
            snowplow_tracker(category, click_type, link_text, count);
        });
        
        //Email social link
        $('.page-node-type-resource .sharethis-wrapper > a').on('click', function() {
            count++;
            click_type = 'social';
            link_text = $(this).attr('displaytext');
            snowplow_tracker(category, click_type, link_text, count);
        });
        
        function snowplow_tracker(category, click_type, link_text, count) {
            $(this, context).once('num_'+count).each(function() {
                    window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/resource_click/jsonschema/1-0-0",
                    "data": {
                        "category": category.replace('-','_'),
                        "resource_id": resource_id,
                        "click_type": click_type,
                        "text": link_text
                    }
                });
            });
        } 

        function waitForElement(elementPath, searchbox, callBack){
            window.setTimeout(function(){
              if($(elementPath).length && $(searchbox).length){
                callBack(elementPath, $(elementPath));
              }else{
                waitForElement(elementPath, searchbox, callBack);
              }
            },1000)
          }
    }
}
})(jQuery, Drupal, drupalSettings);
