jQuery(document).ready( function($) {
	$('a.neg-cap').attr('title',cmeAdmin.negationCaption);
	$('a.neg-type-caps').attr('title',cmeAdmin.typeCapsNegationCaption);
	$('td.cap-unreg').attr('title',cmeAdmin.typeCapUnregistered);
	$('a.normal-cap').attr('title',cmeAdmin.switchableCaption);
	$('span.cap-x:not([class*="pp-cap-key"])').attr('title',cmeAdmin.capNegated);
	$('table.cme-checklist input[class!="cme-check-all"]').not(':disabled').attr('title',cmeAdmin.chkCaption);

  if ($('.ppc-checkboxes-documentation-link').length > 0) {
    $('.ppc-checkboxes-documentation-link').attr('target', 'blank'); 
  }
	$('table.cme-checklist a.neg-cap').click( function(e) {
		$(this).closest('td').removeClass('cap-yes').removeClass('cap-no').addClass('cap-neg');

		var cap_name_attr = $(this).parent().find('input[type="checkbox"]').attr('name');
		$(this).after('<input type="hidden" class="cme-negation-input" name="'+cap_name_attr+'" value="" />');

		$('input[name="' + cap_name_attr + '"]').closest('td').removeClass('cap-yes').removeClass('cap-no').addClass('cap-neg');
    
    if ($(this).closest('tr').hasClass('unfiltered_upload')) { 
      $('input[name="caps[upload_files]"]').closest('td').addClass('cap-neg');
      $('input[name="caps[upload_files]"]').closest('td').append('<input type="hidden" class="cme-negation-input" name="caps[upload_files]" value="" />');
      $('input[name="caps[upload_files]"]').parent().next('a.neg-cap:visible').click();
    }

		return false;
	});

	//$('table.cme-typecaps span.cap-x,table.cme-checklist span.cap-x,table.cme-checklist td.cap-neg span').live( 'click', function(e) {
	$(document).on( 'click', 'table.cme-typecaps span.cap-x,table.cme-checklist span.cap-x,table.cme-checklist td.cap-neg span', function(e) {
		$(this).closest('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
		$(this).parent().find('input[type="checkbox"]').prop('checked',false);
		$(this).parent().find('input.cme-negation-input').remove();

		// Also apply for any other checkboxes with the same name
		var cap_name_attr = $(this).next('input[type="checkbox"]').attr('name');

		if (!cap_name_attr) {
			cap_name_attr = $(this).next('label').find('input[type="checkbox"]').attr('name');
		}

		$('input[name="' + cap_name_attr + '"]').parent().closest('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
		$('input[name="' + cap_name_attr + '"]').prop('checked',false).parent().find('input.cme-negation-input').remove();

    if ($(this).closest('td').hasClass('capability-checkbox-rotate')) {
      $(this).closest('td').find('input[type="checkbox"]').prop('checked', true);

      if ($(this).closest('td').hasClass('upload_files')) {
        $('tr.unfiltered_upload').find('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
		    $('tr.unfiltered_upload').find('input[type="checkbox"]').prop('checked',false);
        $('tr.unfiltered_upload').find('input.cme-negation-input').remove();
        $('input[name="caps[unfiltered_upload]"]').parent().closest('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
		    $('input[name="caps[unfiltered_upload]"]').prop('checked', true).parent().find('input.cme-negation-input').remove();
      }
    } 

    if ($(this).closest('td').find('input[type="checkbox"]').hasClass('pp-single-action-rotate')) {
      $(this).closest('td').find('input[type="checkbox"]').prop('checked', true);
    }

    if ($(this).closest('tr').hasClass('unfiltered_upload')) {
      $('input[name="caps[upload_files]"]').parent().closest('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
      $('input[name="caps[upload_files]"]').prop('checked', true).parent().find('input.cme-negation-input').remove();
    }
		return false;
	});

	$("#publishpress_caps_form").bind("keypress", function(e) {
		if (e.keyCode == 13) {
		   $(document.activeElement).parent().find('input[type="submit"]').first().click();
		   return false;
		}
	});

	$('input.cme-check-all').click( function(e) {
		$(this).closest('table').find('input[type="checkbox"][disabled!="disabled"]:visible').prop('checked', $(this).is(":checked") );
	});

	$('a.cme-neg-all').click( function(e) {
		$(this).closest('table').find('a.neg-cap:visible').click();
		return false;
	});

	$('a.cme-switch-all').click( function(e) {
		$(this).closest('table').find('td.cap-neg span').click();
		return false;
	});

	$('table.cme-typecaps a.neg-type-caps').click( function(e) {
		$(this).closest('tr').find('td[class!="cap-neg"]').filter('td[class!="cap-unreg"]').each( function() {
			$(this).addClass('cap-neg');

			var cap_name_attr = $(this).find('input[type="checkbox"]').attr('name');
			$(this).append('<input type="hidden" class="cme-negation-input" name="'+cap_name_attr+'" value="" />');

			$('input[name="' + cap_name_attr + '"]').parent().next('a.neg-cap:visible').click();
		});

		return false;
	});

	//http://stackoverflow.com/users/803925/nbrooks
	$('table.cme-typecaps th').click(function(){
		var columnNo = $(this).index();

		var check_val = ! $(this).prop('checked_all');

		if ( $(this).hasClass('term-cap') )
			var class_sel = '[class*="term-cap"]';
		else
			var class_sel = '[class*="post-cap"]';

		var chks = $(this).closest("table")
			.find("tr td" + class_sel + ":nth-child(" + (columnNo+1) + ') input[type="checkbox"]:visible');

		$(chks).each(function(i,e) {
			$('input[name="' + $(this).attr('name') + '"]').prop('checked', check_val);
		});

		$(this).prop('checked_all',check_val);
	});

	$('a.cme-fix-read-cap').click(function(){
		$('input[name="caps[read]"]').prop('checked', true);
		$('input[name="SaveRole"]').trigger('click');
		return false;
	});

	/* Filter Edit, Delete and Read capabilities */

	// Fill the <select> extracting the values and labels from the tables
	$('.ppc-filter-select').each(function(){
	    var filter = $(this)
	    var options = new Array();
	    $(this).parent().siblings('table').find('tbody').find('tr').each(function(){
	        options.push({
	            value : $(this).attr('class'),
	            text : $(this).find('.cap_type').text()
	        });
	    });
	    options.forEach(function(option, index){
	        filter.append($('<option>', {
	            value: option.value,
	            text: option.text
	        }));
	    });
	});

	// Reset select filters on load
	$('.ppc-filter-select').prop('selectedIndex', 0);

	$('.ppc-filter-select-reset').click(function(){
	    $(this).prev('.ppc-filter-select').prop('selectedIndex', 0);
	    $(this).parent().siblings('table').find('tr').show(); // Show all the table rows
	});
	$('.ppc-filter-select').change(function(){
        if($(this).val()){
			$(this).parent().siblings('table').find('tr').hide();
    	    $(this).parent().siblings('table').find('thead tr:first-child').show(); // Show the table heading
    	    $(this).parent().siblings('table').find('tr.' + $(this).val()).show(); // Show only the filtered row
        } else {
            $(this).parent().siblings('table').find('tr').show(); // No value selected; show all the table rows
        }
	});

	/* Filter WordPress core, WooCommerce, Additional capabilities */

	// Reset text filters on load
	$('.ppc-filter-text').val('');

	$('.ppc-filter-text-reset').click(function(){
	    $(this).prev('.ppc-filter-text').val('');
	    $(this).parent().siblings('table').find('tr').show(); // Show all the table rows
		$(this).parent().siblings('.ppc-filter-no-results').hide(); // Hide "no results" message
	});

	$('.ppc-filter-text').keyup(function(){
      	var search_text = $(this).val();
      	var search_class = search_text.trim().replace(/\s+/g, '_');
	    $(this).parent().siblings('table').find('tr').hide();
	    $(this).parent().siblings('table').find('tr[class*="' + search_class + '"]').show(); // Show only the filtered row
	    $(this).parent().siblings('table').find('tr.cme-bulk-select').hide(); // Hide bulk row
	    if($(this).val().length === 0){
	        $(this).parent().siblings('table').find('tr').show(); // Show all the table rows
	    }
	    // Show / Hide the no-results message
	    if($(this).parent().siblings('table').find('tr:visible').length === 0) {
	        $(this).parent().siblings('.ppc-filter-no-results').show(); // Show "no results" message
	    } else {
	        $(this).parent().siblings('.ppc-filter-no-results').hide(); // Hide "no results" message
	    }
  });

  /**
     * Roles tab toggle
     */
   $(document).on('click', '.ppc-roles-tab li', function (event) {
      event.preventDefault();

      var clicked_tab = $(this).attr('data-tab');

      //remove active class from all tabs
      $('.ppc-roles-tab li').removeClass('active');
      //add active class to current tab
      $(this).addClass('active');

      //hide all tabs contents
      $('.pp-roles-tab-tr').hide();
      //show this current tab contents
     $('.pp-roles-' + clicked_tab + '-tab').show();
   });
  
   /**
    * Roles capabilities load more button
    */
    $(document).on('click', '.roles-capabilities-load-more', function (event) {
       event.preventDefault();
 
      $('.roles-capabilities-load-more').hide();
     
      $('.roles-capabilities-load-less').show();
    
      $('ul.pp-roles-capabilities li').show();
   });
  
   /**
    * Capabilities single box click
    */
    $(document).on('change', '.capability-checkbox-rotate input[type="checkbox"]', function (event) {
     
      let clicked_box           = $(this);
      let mark_box_as_x         = false;
      let mark_box_as_checked   = false;
      let mark_box_as_unchecked = false;

      if (!clicked_box.prop('checked')) {
        mark_box_as_unchecked   = true;
      } else if (clicked_box.prop('checked')) {
        mark_box_as_checked   = true;
      }

      if (mark_box_as_checked && clicked_box.hasClass('interacted')) {
        mark_box_as_checked   = false;
        mark_box_as_unchecked = false;
        mark_box_as_x         = true;
      }

      if (mark_box_as_unchecked) {
        clicked_box.prop('checked', false);
        if (clicked_box.closest('td').hasClass('upload_files')) {
          $('tr.unfiltered_upload').find('input[name="caps[unfiltered_upload]"]').prop('checked', false);
        }
      } else if (mark_box_as_checked) {
        clicked_box.prop('checked', true);
        if (clicked_box.closest('td').hasClass('upload_files')) {
          $('tr.unfiltered_upload').find('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
          $('tr.unfiltered_upload').find('input[type="checkbox"]').prop('checked',false);
          $('tr.unfiltered_upload').find('input.cme-negation-input').remove();
          $('input[name="caps[unfiltered_upload]"]').parent().closest('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
          $('input[name="caps[unfiltered_upload]"]').prop('checked', true).parent().find('input.cme-negation-input').remove();
          $('tr.unfiltered_upload').find('input[name="caps[unfiltered_upload]"]').prop('checked', true);
        }
      } else if (mark_box_as_x) {
        if (clicked_box.closest('td').hasClass('upload_files')) {
          $('tr.unfiltered_upload').find('a.neg-cap').trigger('click');
        }
        clicked_box.prop('checked', false);
        //perform X action if state is blank
        var box_parent = clicked_box.closest('td');
        box_parent.addClass('cap-neg');
        var cap_name_attr = box_parent.find('input[type="checkbox"]').attr('name');
        box_parent.append('<input type="hidden" class="cme-negation-input" name="'+cap_name_attr+'" value="" />');
        $('input[name="' + cap_name_attr + '"]').parent().next('a.neg-cap:visible').click();
      }
      clicked_box.addClass('interacted');
   });

   /**
    * Capabilities checkmark rotate
    */
    $(document).on('click', '.pp-row-action-rotate', function (event) {
      event.preventDefault();
      let clicked_box       = $(this);
      var checked_fields     = false;
      var unchecked_fields   = false;
      var all_checkbox      = 0;
      var negative_checkbox = 0;

      //determine if we should check or uncheck based on current input state
      clicked_box.closest('tr').find('input[type="checkbox"]:not(.disabled)').each(function () {
        if (!$(this).hasClass('excluded-input') && !$(this).prop('checked')) {
          all_checkbox++;
          unchecked_fields = true;
        } else if (!$(this).hasClass('excluded-input') && $(this).prop('checked')) {
          all_checkbox++;
          checked_fields = true;
        }
        if ($(this).closest('td').hasClass('cap-neg')) {
          negative_checkbox++;
        }
      });

      if ((checked_fields && unchecked_fields) || (negative_checkbox >= all_checkbox)) {
        checked_fields   = true;
        unchecked_fields = false;
      } else if (!checked_fields && unchecked_fields && !clicked_box.hasClass('interacted')) {
        checked_fields   = true;
        unchecked_fields = false;
      } else if (checked_fields && !unchecked_fields) {
        checked_fields   = false;
        unchecked_fields = true;
      } else {
        checked_fields   = false;
        unchecked_fields = false;
      }


      if (checked_fields) {
        //perform checked action
        clicked_box.closest('tr').find('td').filter('td[class!="cap-unreg"]').each(function () {
          $(this).closest('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
          $(this).parent().find('input[type="checkbox"]:not(.disabled)').prop('checked',true);
          $(this).parent().find('input.cme-negation-input').remove();
          // Also apply for any other checkboxes with the same name
          var cap_name_attr = $(this).next('input[type="checkbox"]').attr('name');
      
          if (!cap_name_attr) {
            cap_name_attr = $(this).next('label').find('input[type="checkbox"]').attr('name');
          }
      
          $('input[name="' + cap_name_attr + '"]').parent().closest('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
          $('input[name="' + cap_name_attr + '"]').prop('checked', true).parent().find('input.cme-negation-input').remove();
          if ($(this).closest('td').hasClass('upload_files')) {
            $('tr.unfiltered_upload').find('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
            $('tr.unfiltered_upload').find('input[type="checkbox"]').prop('checked',false);
            $('tr.unfiltered_upload').find('input.cme-negation-input').remove();
            $('input[name="caps[unfiltered_upload]"]').parent().closest('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
            $('input[name="caps[unfiltered_upload]"]').prop('checked', true).parent().find('input.cme-negation-input').remove();
            $('tr.unfiltered_upload').find('input[name="caps[unfiltered_upload]"]').prop('checked', true);
          }
        });
      } else if (unchecked_fields) {
        //perform blank action if state is checked
        clicked_box.closest('tr').find('td').filter('td[class!="cap-unreg"]').each(function () {
          $(this).closest('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
          $(this).parent().find('input[type="checkbox"]:not(.disabled)').prop('checked',false);
          $(this).parent().find('input.cme-negation-input').remove();
          // Also apply for any other checkboxes with the same name
          var cap_name_attr = $(this).next('input[type="checkbox"]').attr('name');
      
          if (!cap_name_attr) {
            cap_name_attr = $(this).next('label').find('input[type="checkbox"]').attr('name');
          }
      
          $('input[name="' + cap_name_attr + '"]').parent().closest('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
          $('input[name="' + cap_name_attr + '"]').prop('checked', false).parent().find('input.cme-negation-input').remove();
          if ($(this).closest('td').hasClass('upload_files')) {
            $('tr.unfiltered_upload').find('input[name="caps[unfiltered_upload]"]').prop('checked', false);
          }
        });
      } else {
        //perform X action if state is blank
        clicked_box.closest('tr').find('td[class!="cap-neg"]').filter('td[class!="cap-unreg"]').each(function () {
    
          var cap_name_attr = $(this).find('input[type="checkbox"]').attr('name');
          if (cap_name_attr) {
            $(this).addClass('cap-neg');
            $(this).append('<input type="hidden" class="cme-negation-input" name="' + cap_name_attr + '" value="" />');
    
            $('input[name="' + cap_name_attr + '"]:not(.disabled)').parent().next('a.neg-cap:visible').click();
            if ($(this).closest('td').hasClass('upload_files')) {
              $('tr.unfiltered_upload').find('a.neg-cap').trigger('click');
            }
          }
        });
      }

      clicked_box.addClass('interacted');

   });
  
  
   /**
    * unfiltered_upload change sync
    */
    $(document).on('change', 'tr.unfiltered_upload input[name="caps[unfiltered_upload]"]', function (event) {
     
      let clicked_box           = $(this);

      if (clicked_box.prop('checked')) {
        $('input[name="caps[upload_files]"]').parent().closest('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
        $('input[name="caps[upload_files]"]').prop('checked', true).parent().find('input.cme-negation-input').remove();
      } else if (!clicked_box.prop('checked')) {
        $('input[name="caps[upload_files]"]').parent().closest('td').removeClass('cap-neg').removeClass('cap-yes').addClass('cap-no');
        $('input[name="caps[upload_files]"]').prop('checked', false).parent().find('input.cme-negation-input').remove();
      }
      
    });

    /**
     * Other capabilities checkmark rotate
     */
     $(document).on('click', '.pp-single-action-rotate', function (event) {
       
       let clicked_input     = $(this);
       var checked_fields     = false;
       var unchecked_fields   = false;
 
       //determine if we should check or uncheck based on current input state
        if (clicked_input.prop('checked')) {
           unchecked_fields = true;
        } else if (!clicked_input.prop('checked')) {
          checked_fields = true;
        }
 
       if ((checked_fields && unchecked_fields)) {
         checked_fields   = true;
         unchecked_fields = false;
       } else if (!checked_fields && unchecked_fields && !clicked_input.hasClass('interacted')) {
         checked_fields   = true;
         unchecked_fields = false;
       } else if (checked_fields && !unchecked_fields) {
         checked_fields   = false;
         unchecked_fields = true;
       } else {
         checked_fields   = false;
         unchecked_fields = false;
       }
 
 
       if (!checked_fields && !unchecked_fields) {
         //perform X action if state is blank
         event.preventDefault();
         clicked_input.closest('td').find('a.neg-cap').click();
       }
 
       clicked_input.addClass('interacted');
 
       if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
         document.getSelection().empty();
       }
    });

  
  if ($('.pp-capability-menus-wrapper.profile-features').length > 0) {
    /**
     * Make profile features sortable
     */
    $(".pp-capability-menus-wrapper.profile-features table.pp-capability-menus-select tbody").sortable({
      axis: "y",
      update: function (e, ui) {
        var fields_order = [];
        $('.pp-capability-menus-wrapper.profile-features table.pp-capability-menus-select tbody tr.ppc-sortable-row').each(function () {
          var element_key = $(this).attr('data-element_key');
          if (element_key) {
            fields_order.push(element_key);
          }
        });
        $('.capsman_profile_features_elements_order').val(fields_order.join(","));
      }
    });
  }

    // -------------------------------------------------------------
    //   Frontend elements new entry
    // -------------------------------------------------------------
    $(document).on("click", ".frontend-element-form-submit", function (event) {
      event.preventDefault();
      var button        = $(this),
        ajax_action     = 'ppc_submit_frontend_element_by_ajax';
        custom_label    = $('.frontend-element-new-name').val(),
        custom_element  = $('.frontend-element-new-element').val(),
        element_pages   = $('.frontend-element-new-element-pages').val(),
        element_posts   = $('.frontend-element-new-element-posts').val(),
        security        = $('.frontend-element-form-nonce').val(),
        item_section    = $(this).attr('data-section'),
        item_id         = $('.' + item_section + '-form').find('.custom-edit-id').val();
        
      if (custom_label == '' || custom_element == '' || (element_pages.length === 0 && element_posts == '')) {
        button.closest('tr').find('.ppc-post-features-note').html('<div class="ppc-feature-submit-form-error" style="color:red;">' + button.attr('data-required') + '</div>');
        $(".ppc-feature-submit-form-error").delay(2000).fadeOut('slow');
        return;
      }

      $('.ppc-feature-submit-form-error').remove();
      button.attr('disabled', true);
      button.closest('tr').find(".ppc-feature-post-loader").addClass("is-active");

      var data = {
        'action': ajax_action,
        'security': security,
        'custom_label': custom_label,
        'custom_element': custom_element,
        'element_pages': element_pages,
        'element_posts': element_posts,
        'item_id': item_id,
      };

      $.post(ajaxurl, data, function (response) {

        if (response.status == 'error') {
          button.closest('tr').find('.ppc-post-features-note').html('<div class="ppc-feature-submit-form-error" style="color:red;">' + response.message + '</div>');
          $(".ppc-feature-submit-form-error").delay(2000).fadeOut('slow');
        } else {
          var parent_table = $('.parent-menu.frontendelements');
          var parent_child = $('.child-menu.frontendelements');

          $('.frontend-features-save-button-warning').remove();

          $('.frontend-element-new-name').val('');
          $('.frontend-element-new-element').val('');
          $('.frontend-element-new-element-pages').val([]).trigger('chosen:updated');
          $('.frontend-element-new-element-posts').val('');

          button.closest('tr').find('.ppc-post-features-note').html('<div class="ppc-feature-submit-form-error" style="color:green;">' + response.message + '</div>');
          $(".ppc-feature-submit-form-error").delay(5000).fadeOut('slow');
          setTimeout(function () {
            $('.ppc-menu-overlay-item').removeClass('ppc-menu-overlay-item');
          }, 5000);

          if (item_id !== '') {
            $('.cancel-edit').trigger("click");
            $('.custom-item-' + item_id).replaceWith(response.content);
          } else {
            if (parent_child.length > 0) {
              $('.child-menu.frontendelements:last').after(response.content);
            } else {
              parent_table.after(response.content);
            }
          }
        }

        button.closest('form').find('input[type=submit]').attr('disabled', false);
        button.closest('tr').find(".ppc-feature-post-loader").removeClass("is-active");
        button.attr('disabled', false);

      });
  });

    // -------------------------------------------------------------
    //   Body class new entry
    // -------------------------------------------------------------
    $(document).on("click", ".body-class-form-submit", function (event) {
      event.preventDefault();
      var button        = $(this),
        ajax_action     = 'ppc_submit_bodyclass_by_ajax';
        custom_label    = $('.body-class-new-name').val(),
        custom_element  = $('.body-class-new-element').val(),
        element_pages   = $('.body-class-new-element-pages').val(),
        element_posts   = $('.body-class-new-element-posts').val(),
        security        = $('.body-class-form-nonce').val(),
        item_section    = $(this).attr('data-section'),
        item_id         = $('.' + item_section + '-form').find('.custom-edit-id').val();
        
      if (custom_label == '' || custom_element == '' || (element_pages.length === 0 && element_posts == '')) {
        button.closest('tr').find('.ppc-post-features-note').html('<div class="ppc-feature-submit-form-error" style="color:red;">' + button.attr('data-required') + '</div>');
        $(".ppc-feature-submit-form-error").delay(2000).fadeOut('slow');
        return;
      }

      $('.ppc-feature-submit-form-error').remove();
      button.attr('disabled', true);
      button.closest('tr').find(".ppc-feature-post-loader").addClass("is-active");

      var data = {
        'action': ajax_action,
        'security': security,
        'custom_label': custom_label,
        'custom_element': custom_element,
        'element_pages': element_pages,
        'element_posts': element_posts,
        'item_id': item_id,
      };

      $.post(ajaxurl, data, function (response) {

        if (response.status == 'error') {
          button.closest('tr').find('.ppc-post-features-note').html('<div class="ppc-feature-submit-form-error" style="color:red;">' + response.message + '</div>');
          $(".ppc-feature-submit-form-error").delay(2000).fadeOut('slow');
        } else {
          var parent_table = $('.parent-menu.bodyclass');
          var parent_child = $('.child-menu.bodyclass');

          $('.frontend-features-save-button-warning').remove();

          $('.body-class-new-name').val('');
          $('.body-class-new-element').val('');
          $('.body-class-new-element-pages').val([]).trigger('chosen:updated');
          $('.body-class-new-element-posts').val('');

          button.closest('tr').find('.ppc-post-features-note').html('<div class="ppc-feature-submit-form-error" style="color:green;">' + response.message + '</div>');
          $(".ppc-feature-submit-form-error").delay(5000).fadeOut('slow');
          setTimeout(function () {
            $('.ppc-menu-overlay-item').removeClass('ppc-menu-overlay-item');
          }, 5000);

          if (item_id !== '') {
            $('.cancel-edit').trigger("click");
            $('.custom-item-' + item_id).replaceWith(response.content);
          } else {
            if (parent_child.length > 0) {
              $('.child-menu.bodyclass:last').after(response.content);
            } else {
              parent_table.after(response.content);
            }
          }
        }

        button.closest('form').find('input[type=submit]').attr('disabled', false);
        button.closest('tr').find(".ppc-feature-post-loader").removeClass("is-active");
        button.attr('disabled', false);

      });
  });

  // -------------------------------------------------------------
  //   Custom styles new entry
  // -------------------------------------------------------------
  $(document).on("click", ".customstyles-form-submit", function (event) {
    event.preventDefault();
    var button        = $(this),
      ajax_action     = 'ppc_submit_custom_styles_by_ajax';
      custom_label    = $('.customstyles-element-new-name').val(),
      custom_element  = $(".customstyles-element-new-element").val(),
      element_pages   = $('.customstyles-new-element-pages').val(),
      element_posts   = $('.customstyles-new-element-posts').val(),
      security        = $('.customstyles-form-nonce').val(),
      item_section    = $(this).attr('data-section'),
      item_id         = $('.' + item_section + '-form').find('.custom-edit-id').val();

    if (custom_label == '' || custom_element == '' || (element_pages.length === 0 && element_posts == '')) {
      button.closest('tr').find('.ppc-post-features-note').html('<div class="ppc-feature-submit-form-error" style="color:red;">' + button.attr('data-required') + '</div>');
      $(".ppc-feature-submit-form-error").delay(2000).fadeOut('slow');
      return;
    }
    
    $('.ppc-feature-submit-form-error').remove();
    button.attr('disabled', true);
    button.closest('tr').find(".ppc-feature-post-loader").addClass("is-active");

    var data = {
      'action': ajax_action,
      'security': security,
      'custom_label': custom_label,
      'custom_element': custom_element,
      'element_pages': element_pages,
      'element_posts': element_posts,
      'item_id': item_id,
    };

    $.post(ajaxurl, data, function (response) {

      if (response.status == 'error') {
        button.closest('tr').find('.ppc-post-features-note').html('<div class="ppc-feature-submit-form-error" style="color:red;">' + response.message + '</div>');
        $(".ppc-feature-submit-form-error").delay(2000).fadeOut('slow');
      } else {
        var parent_table = $('.parent-menu.customstyles');
        var parent_child = $('.child-menu.customstyles');

        $('.frontend-features-save-button-warning').remove();

        $('.customstyles-element-new-name').val('');
        $(".customstyles-new-element-clear").trigger("click");
        $('.customstyles-new-element-pages').val([]).trigger('chosen:updated');
        $('.customstyles-new-element-posts').val('');

        button.closest('tr').find('.ppc-post-features-note').html('<div class="ppc-feature-submit-form-error" style="color:green;">' + response.message + '</div>');
        $(".ppc-feature-submit-form-error").delay(5000).fadeOut('slow');
        setTimeout(function () {
          $('.ppc-menu-overlay-item').removeClass('ppc-menu-overlay-item');
        }, 5000);

        if (item_id !== '') {
          $('.cancel-edit').trigger("click");
          $('.custom-item-' + item_id).replaceWith(response.content);
        } else {
          if (parent_child.length > 0) {
            $('.child-menu.customstyles:last').after(response.content);
          } else {
            parent_table.after(response.content);
          }
        }
      }

      button.closest('form').find('input[type=submit]').attr('disabled', false);
      button.closest('tr').find(".ppc-feature-post-loader").removeClass("is-active");
      button.attr('disabled', false);

    });
  });

  // -------------------------------------------------------------
  //   Delete frontend features item
  // -------------------------------------------------------------
  $(document).on("click", ".frontend-features-delete-item", function (event) {
      if (confirm(cmeAdmin.deleteWarning)) {
        var item = $(this);
        var item_section = item.attr('data-section');
        var item_id      = item.attr('data-id');
        var security     = item.attr('data-delete-nonce');

        item.closest('.ppc-menu-row').fadeOut(300);

        var data = {
          'action': 'ppc_delete_frontend_feature_item_by_ajax',
          'security': security,
          'item_section': item_section,
          'item_id': item_id
        };

        $.post(ajaxurl, data, function (response) {
          if (response.status == 'error') {
            item.closest('.ppc-menu-row').show();
            alert(response.message);
          }
        });

      }
  });

  // -------------------------------------------------------------
  //   View custom item
  // -------------------------------------------------------------
  $(document).on("click", ".view-custom-item", function (event) {
    event.preventDefault();
    $(this).closest('td').find('.custom-item-output').toggleClass('show');
  });

  // -------------------------------------------------------------
  //   Edit custom item
  // -------------------------------------------------------------
  $(document).on("click", ".edit-custom-item", function (event) {
    event.preventDefault();
    var item          = $(this);
    var item_section  = item.attr('data-section');
    var item_id       = item.attr('data-id');
    var item_label    = item.attr('data-label');
    var item_element  = item.attr('data-element');
    var item_pages    = '';
    var item_form     = $('.' + item_section + '-form');

    if (item_id == '') {
      return;
    }

    item_form.find('.' + item_section + '-form-label').val(item_label);
    item_form.find('.editing-custom-item').show();
    item_form.find('.editing-custom-item .title').html(item_label);
    item_form.find('.submit-button').html(item_form.find('.submit-button').attr('data-edit'));
    item_form.find('.custom-edit-id').val(item_id);

    if (item_section === 'customstyles') {
      item.closest('td').find('.customstyles-new-element-update').trigger("click");
    } else {
      item_form.find('.' + item_section + '-form-element').val(item_element);
    }
    
    if (item_section === 'customstyles' || item_section === 'frontendelements' || item_section === 'bodyclass') {
      //update form pages
      item_pages   = item.attr('data-pages');
      item_pages = item_pages.split(', ');
      var page_items = [];
      var post_items = [];
      item_pages.forEach(function (item_page) {
        if (!isNaN(parseFloat(item_page)) && !isNaN(item_page - 0)) {
          post_items.push(item_page);
        } else {
          page_items.push(item_page);
        }
      });
      item_form.find('.' + item_section + '-form-pages').val(page_items).trigger('chosen:updated');
      item_form.find('.' + item_section + '-form-posts').val(post_items.join(' '));
    }

    //scroll to the form
    $([document.documentElement, document.body]).animate({
      scrollTop: item_form.offset().top - 50
    }, 'fast');
  });

  // -------------------------------------------------------------
  //   Cancel custom item edit
  // -------------------------------------------------------------
  $(document).on("click", ".editing-custom-item .cancel-edit", function (event) {
    event.preventDefault();
    var item          = $(this);
    var item_section  = item.attr('data-section');
    var item_form     = $('.' + item_section + '-form');

    item_form.find('.' + item_section + '-form-label').val('');
    item_form.find('.editing-custom-item').hide();
    item_form.find('.submit-button').html(item_form.find('.submit-button').attr('data-add'));
    item_form.find('.custom-edit-id').val('');
    item_form.find('.' + item_section + '-form-element').val('');

    if (item_section === 'customstyles') {
      $('.customstyles-new-element-clear').trigger("click");
    }

    if (item_section === 'customstyles' || item_section === 'frontendelements' || item_section === 'bodyclass') {
      item_form.find('.' + item_section + '-form-pages').val([]).trigger('chosen:updated');
      item_form.find('.' + item_section + '-form-posts').val('');
    }
  });
  
  	// -------------------------------------------------------------
  	//   Lock Frontend Features 'Save changes' button if unsaved custom items exist
  	// -------------------------------------------------------------
  	$(document).on("keyup paste", ".frontent-form-field", function (event) {
    	var lock_button = false;
    	$('.frontend-features-save-button-warning').remove();

    	$('.frontent-form-field').each(function () {
      	if ($(this).val() !== '' && $(this).val().replace(/\s/g, '').length) {
        	lock_button = true;
      	}
      });

    	if (lock_button) {
      	$(this).closest('form').find('input[type=submit]').attr('disabled', true).after('<span class="frontend-features-save-button-warning">' + cmeAdmin.saveWarning + '</span>');
    	} else {
      	$(this).closest('form').find('input[type=submit]').attr('disabled', false);
    	}
    });

});
