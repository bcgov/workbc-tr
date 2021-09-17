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
                console.log(label);
                splashGradeArray.push(label);
                console.log(splashGradeArray);
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
        $('.view-solr-results').once('snowplow').each(function() {
            count_result = 0;
            var action_event = "load";
            count_result = $('.show-result-wrapper .view-header').text().trim().split(" ")[1];
            var keyword = $('.search_keyword input').val();
            console.log(keyword);
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
            });
        });
        

        //Track resource and lesson plans click
        $('.leftnavbar a, .download-worksheets-files-wrapper a, .go-to-resource-wrapper a, a[target="_blank"],'+
        '.related_news_wrapper_title a, .sharethis-wrapper span').on('click', function(e) {
               var path =  window.location.pathname; 
               var category = path.split("/")[1];   
               var link_text = '';
               if($(this).parents('.leftnavbar--items').length > 0) {
                  count++;
                  var click_type = $(this).parents('.leftnavbar--items').find('.leftnavbar--title a').text();
                  click_type = "nav_"+click_type.replace(' ','_')+"_"+$(this).text().replace(' ','_').replace(':','_');
                  snowplow_tracker(category, click_type, link_text, count)
               }
               if($(this).parent().hasClass("file-url") && $(this).text() == "Download All") {
                   count++;
                   click_type = 'download_all_worksheets'
                   snowplow_tracker(category, click_type, link_text, count)
               } 
               if($(this).parent().hasClass("file-url") && $(this).text() == 'Download'){
                   count++;
                   click_type = 'download_file'
                   link_text = $(this).attr('href');
                   snowplow_tracker(category, click_type, link_text, count)
               } 
               if ($(this).attr('target') == '_blank') {
                   count++;
                   click_type = 'external_link';
                   link_text = $(this).attr('href');
                   snowplow_tracker(category, click_type, link_text, count)
               }
               if($(this).parent().hasClass('related_news_wrapper_title')) {
                   console.log('hello');
                    count++;
                    click_type = 'related_resource';
                    link_text = $(this).attr('href');
                    snowplow_tracker(category, click_type, link_text, count)
               }

               if($(this).parents('.sharethis-wrapper')) {
                   if($(this).parent().attr('displaytext') == 'email') {
                      link_text = $(this).parents().attr('displaytext');
                   } else {
                      link_text = $(this).attr('displaytext');
                   }
                   count++;
                   click_type = 'social';
                   snowplow_tracker(category, click_type, link_text, count);
               }
               return true;
        });
        
        function snowplow_tracker(category, click_type, link_text, count) {
            $(this).once('num_'+count).each(function() {
                    window.snowplow('trackSelfDescribingEvent', {"schema":"iglu:ca.bc.gov.workbc/resource_click/jsonschema/1-0-0",
                    "data": {
                        "category": category.replace('-','_'),
                        "resource_id": "Career Compass",
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
  