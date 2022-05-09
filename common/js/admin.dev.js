jQuery(document).ready( function($) {
	$('a.neg-cap').attr('title',cmeAdmin.negationCaption);
	$('a.neg-type-caps').attr('title',cmeAdmin.typeCapsNegationCaption);
	$('td.cap-unreg').attr('title',cmeAdmin.typeCapUnregistered);
	$('a.normal-cap').attr('title',cmeAdmin.switchableCaption);
	$('span.cap-x').attr('title',cmeAdmin.capNegated);
	$('table.cme-checklist input[class!="cme-check-all"]').not(':disabled').attr('title',cmeAdmin.chkCaption);

	$('table.cme-checklist a.neg-cap').click( function(e) {
		$(this).closest('td').removeClass('cap-yes').removeClass('cap-no').addClass('cap-neg');

		var cap_name_attr = $(this).parent().find('input[type="checkbox"]').attr('name');
		$(this).after('<input type="hidden" class="cme-negation-input" name="'+cap_name_attr+'" value="" />');

		$('input[name="' + cap_name_attr + '"]').closest('td').removeClass('cap-yes').removeClass('cap-no').addClass('cap-neg');

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
    * Roles capabilities load less button
    */
    $(document).on('click', '.roles-capabilities-load-less', function (event) {
       event.preventDefault();
 
      $('.roles-capabilities-load-less').hide();
     
      $('.roles-capabilities-load-more').show();
    
      $('ul.pp-roles-capabilities li').hide();

      $('ul.pp-roles-capabilities').children().slice(0, 6).show();

      window.scrollTo({ top: 0, behavior: 'smooth' });
   });
  
  /**
   * Capabilities role slug validation
   */
  $('.ppc-roles-tab-content input[name="role_slug"]').on('keyup', function (e) {
    is_role_slug_exist();
  });

  if ($('#pp-role-slug-exists').length > 0) {
    is_role_slug_exist();
  }

  function is_role_slug_exist() {
    if ($('.ppc-roles-tab-content input[name="role_slug"]').attr('readonly') !== 'readonly') {
      var value = $('.ppc-roles-tab-content input[name="role_slug"]').val();
      var slugexists = $('#pp-role-slug-exists')
      var all_roles = $('.ppc-roles-all-roles').val();
      var role_array = all_roles.split(',');
      if (role_array.includes(value)) {
        slugexists.show();
      } else {
        slugexists.hide();
      }
    }
  }

});
