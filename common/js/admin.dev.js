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

	// -------------------------------------------------------------
	//   Prevent ENTER on input from submitting whole Editor feature settings
	// -------------------------------------------------------------
	$(document).on("keydown", ".ppc-add-custom-row-body input[type='text']", function (event) {
	  return event.keyCode !== 13;
	});
  
	// -------------------------------------------------------------
	//   Submit new item for editor feature
	// -------------------------------------------------------------
	$(document).on("click", ".ppc-feature-gutenberg-new-submit, .ppc-feature-classic-new-submit", function (event) {
	  event.preventDefault();
	  var ajax_action,
		security,
		custom_label,
		custom_element,
		button = $(this);
  
	  $('.ppc-feature-submit-form-error').remove();
	  button.attr('disabled', true);
	  $(".ppc-feature-post-loader").addClass("is-active");
  
	  if (button.hasClass('ppc-feature-gutenberg-new-submit')) {
		ajax_action = 'ppc_submit_feature_gutenberg_by_ajax';
		custom_label = $('.ppc-feature-gutenberg-new-name').val();
		custom_element = $('.ppc-feature-gutenberg-new-ids').val();
	  } else {
		ajax_action = 'ppc_submit_feature_classic_by_ajax';
		custom_label = $('.ppc-feature-classic-new-name').val();
		custom_element = $('.ppc-feature-classic-new-ids').val();
	  }
  
	  security = $('.ppc-feature-submit-form-nonce').val();
  
	  var data = {
		'action': ajax_action,
		'security': security,
		'custom_label': custom_label,
		'custom_element': custom_element,
	  };
  
	  $.post(ajaxurl, data, function (response) {
  
		if (response.status == 'error') {
		  button.closest('tr').find('.ppc-post-features-note').html('<div class="ppc-feature-submit-form-error" style="color:red;">' + response.message + '</div>');
		  $(".ppc-feature-submit-form-error").delay(2000).fadeOut('slow');
		}else if (response.status == 'promo') {
		  button.closest('tr').find('.ppc-post-features-note').html('<div class="ppc-feature-submit-form-error" style="color:red;">' + response.message + '</div>');
		} else {
  
		  if (button.hasClass('ppc-feature-gutenberg-new-submit')) {
			$('.ppc-feature-gutenberg-new-name').val('');
			$('.ppc-feature-gutenberg-new-ids').val('');
		  } else {
			$('.ppc-feature-classic-new-name').val('');
			$('.ppc-feature-classic-new-ids').val('');
		  }
  
		  button.closest('tr').find('.ppc-post-features-note').html('<div class="ppc-feature-submit-form-error" style="color:green;">' + response.message + '</div>');
		  $(".ppc-feature-submit-form-error").delay(5000).fadeOut('slow');
		  setTimeout(function () {
			$('.ppc-menu-overlay-item').removeClass('ppc-menu-overlay-item');
		  }, 5000);
		  button.closest('table').find('.ppc-add-custom-row-header').before(response.content);
		}
  
		$(".ppc-feature-post-loader").removeClass("is-active");
		button.attr('disabled', false);
  
	  });
  
  
	});
  
	// -------------------------------------------------------------
	//   Delete custom added post features item
	// -------------------------------------------------------------
	$(document).on("click", ".ppc-custom-features-delete", function (event) {
	  if (confirm(cmeAdmin.deleteWarning)) {
		var item = $(this);
		var delete_id = item.attr('data-id');
		var delete_parent = item.attr('data-parent');
		var security = $('.ppc-feature-submit-form-nonce').val();
  
		item.closest('.ppc-menu-row').fadeOut(300);
  
		var data = {
		  'action': 'ppc_delete_custom_post_features_by_ajax',
		  'security': security,
		  'delete_id': delete_id,
		  'delete_parent': delete_parent,
		};
  
		$.post(ajaxurl, data, function (response) {
		  if (response.status == 'error') {
			item.closest('.ppc-menu-row').show();
			alert(response.message);
		  }
		});
  
	  }
	});
});