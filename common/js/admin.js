jQuery(document).ready(function(e){e("a.neg-cap").attr("title",cmeAdmin.negationCaption),e("a.neg-type-caps").attr("title",cmeAdmin.typeCapsNegationCaption),e("a.normal-cap").attr("title",cmeAdmin.switchableCaption),e('span.cap-x:not([class*="pp-cap-key"])').attr("title",cmeAdmin.capNegated),e('table.cme-checklist input[class!="cme-check-all"]').not(":disabled").attr("title",cmeAdmin.chkCaption),e(".ppc-checkboxes-documentation-link").length>0&&e(".ppc-checkboxes-documentation-link").attr("target","blank"),e("table.cme-checklist a.neg-cap").click(function(t){e(this).closest("td").removeClass("cap-yes").removeClass("cap-no").addClass("cap-neg");var s=e(this).parent().find('input[type="checkbox"]').attr("name");return e(this).after('<input type="hidden" class="cme-negation-input" name="'+s+'" value="" />'),e('input[name="'+s+'"]').closest("td").removeClass("cap-yes").removeClass("cap-no").addClass("cap-neg"),e(this).closest("tr").hasClass("unfiltered_upload")&&(e('input[name="caps[upload_files]"]').closest("td").addClass("cap-neg"),e('input[name="caps[upload_files]"]').closest("td").append('<input type="hidden" class="cme-negation-input" name="caps[upload_files]" value="" />'),e('input[name="caps[upload_files]"]').parent().next("a.neg-cap:visible").click()),!1}),e(document).on("click","table.cme-typecaps span.cap-x,table.cme-checklist span.cap-x,table.cme-checklist td.cap-neg span",function(t){e(this).closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e(this).parent().find('input[type="checkbox"]').prop("checked",!1),e(this).parent().find("input.cme-negation-input").remove();var s=e(this).next('input[type="checkbox"]').attr("name");return s||(s=e(this).next("label").find('input[type="checkbox"]').attr("name")),e('input[name="'+s+'"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="'+s+'"]').prop("checked",!1).parent().find("input.cme-negation-input").remove(),e(this).closest("td").hasClass("capability-checkbox-rotate")&&(e(this).closest("td").find('input[type="checkbox"]').prop("checked",!0),e(this).closest("td").hasClass("upload_files")&&(e("tr.unfiltered_upload").find("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e("tr.unfiltered_upload").find('input[type="checkbox"]').prop("checked",!1),e("tr.unfiltered_upload").find("input.cme-negation-input").remove(),e('input[name="caps[unfiltered_upload]"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="caps[unfiltered_upload]"]').prop("checked",!0).parent().find("input.cme-negation-input").remove())),e(this).closest("td").find('input[type="checkbox"]').hasClass("pp-single-action-rotate")&&e(this).closest("td").find('input[type="checkbox"]').prop("checked",!0),e(this).closest("tr").hasClass("unfiltered_upload")&&(e('input[name="caps[upload_files]"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="caps[upload_files]"]').prop("checked",!0).parent().find("input.cme-negation-input").remove()),!1}),e("#publishpress_caps_form").bind("keypress",function(t){if(13==t.keyCode)return e(document.activeElement).parent().find('input[type="submit"]').first().click(),!1}),e("input.cme-check-all").click(function(t){e(this).closest("table").find('input[type="checkbox"][disabled!="disabled"]:visible').prop("checked",e(this).is(":checked"))}),e("a.cme-neg-all").click(function(t){return e(this).closest("table").find("a.neg-cap:visible").click(),!1}),e("a.cme-switch-all").click(function(t){return e(this).closest("table").find("td.cap-neg span").click(),!1}),e("table.cme-typecaps a.neg-type-caps").click(function(t){return e(this).closest("tr").find('td[class!="cap-neg"]').filter('td[class!="cap-unreg"]').each(function(){e(this).addClass("cap-neg");var t=e(this).find('input[type="checkbox"]').attr("name");e(this).append('<input type="hidden" class="cme-negation-input" name="'+t+'" value="" />'),e('input[name="'+t+'"]').parent().next("a.neg-cap:visible").click()}),!1}),e("table.cme-typecaps th").click(function(){var t=e(this).index(),s=!e(this).prop("checked_all");if(e(this).hasClass("term-cap"))var a='[class*="term-cap"]';else var a='[class*="post-cap"]';var n=e(this).closest("table").find("tr td"+a+":nth-child("+(t+1)+') input[type="checkbox"]:visible');e(n).each(function(t,a){e('input[name="'+e(this).attr("name")+'"]').prop("checked",s)}),e(this).prop("checked_all",s)}),e("a.cme-fix-read-cap").click(function(){return e('input[name="caps[read]"]').prop("checked",!0),e('input[name="SaveRole"]').trigger("click"),!1}),e(".ppc-filter-select").each(function(){var t=e(this),s=[];e(this).parent().siblings("table").find("tbody").find("tr").each(function(){s.push({value:e(this).attr("class"),text:e(this).find(".cap_type").text()})}),s.forEach(function(s,a){t.append(e("<option>",{value:s.value,text:s.text}))})}),e(".ppc-filter-select").prop("selectedIndex",0),e(".ppc-filter-select-reset").click(function(){e(this).prev(".ppc-filter-select").prop("selectedIndex",0),e(this).parent().siblings("table").find("tr").show()}),e(".ppc-filter-select").change(function(){e(this).val()?(e(this).parent().siblings("table").find("tr").hide(),e(this).parent().siblings("table").find("thead tr:first-child").show(),e(this).parent().siblings("table").find("tr."+e(this).val()).show()):e(this).parent().siblings("table").find("tr").show()}),e(".ppc-filter-text").val(""),e(".ppc-filter-text-reset").click(function(){e(this).prev(".ppc-filter-text").val(""),e(this).parent().siblings("table").find("tr").show(),e(this).parent().siblings(".ppc-filter-no-results").hide()}),e(".ppc-filter-text").keyup(function(){var t=e(this).val().trim().replace(/\s+/g,"_");e(this).parent().siblings("table").find("tr").hide(),e(this).parent().siblings("table").find('tr[class*="'+t+'"]').show(),e(this).parent().siblings("table").find("tr.cme-bulk-select").hide(),0===e(this).val().length&&e(this).parent().siblings("table").find("tr").show(),0===e(this).parent().siblings("table").find("tr:visible").length?e(this).parent().siblings(".ppc-filter-no-results").show():e(this).parent().siblings(".ppc-filter-no-results").hide()}),e(document).on("click",".ppc-roles-tab li",function(t){t.preventDefault();var s=e(this).attr("data-tab");e(".ppc-roles-tab li").removeClass("active"),e(this).addClass("active"),e(".pp-roles-tab-tr").hide(),e(".pp-roles-"+s+"-tab").show()}),e(document).on("click",".roles-capabilities-load-more",function(t){t.preventDefault(),e(".roles-capabilities-load-more").hide(),e(".roles-capabilities-load-less").show(),e("ul.pp-roles-capabilities li").show()}),e(document).on("change",'.capability-checkbox-rotate input[type="checkbox"]',function(t){let s=e(this),a=!1,n=!1,i=!1;if(s.prop("checked")?s.prop("checked")&&(n=!0):i=!0,n&&s.hasClass("interacted")&&(n=!1,i=!1,a=!0),i)s.prop("checked",!1),s.closest("td").hasClass("upload_files")&&e("tr.unfiltered_upload").find('input[name="caps[unfiltered_upload]"]').prop("checked",!1);else if(n)s.prop("checked",!0),s.closest("td").hasClass("upload_files")&&(e("tr.unfiltered_upload").find("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e("tr.unfiltered_upload").find('input[type="checkbox"]').prop("checked",!1),e("tr.unfiltered_upload").find("input.cme-negation-input").remove(),e('input[name="caps[unfiltered_upload]"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="caps[unfiltered_upload]"]').prop("checked",!0).parent().find("input.cme-negation-input").remove(),e("tr.unfiltered_upload").find('input[name="caps[unfiltered_upload]"]').prop("checked",!0));else if(a){s.closest("td").hasClass("upload_files")&&e("tr.unfiltered_upload").find("a.neg-cap").trigger("click"),s.prop("checked",!1);var l=s.closest("td");l.addClass("cap-neg");var c=l.find('input[type="checkbox"]').attr("name");l.append('<input type="hidden" class="cme-negation-input" name="'+c+'" value="" />'),e('input[name="'+c+'"]').parent().next("a.neg-cap:visible").click()}s.addClass("interacted")}),e(document).on("click",".pp-row-action-rotate",function(t){t.preventDefault();let s=e(this);var a=!1,n=!1,i=0,l=0;s.closest("tr").find('input[type="checkbox"]:not(.disabled)').each(function(){e(this).hasClass("excluded-input")||e(this).prop("checked")?!e(this).hasClass("excluded-input")&&e(this).prop("checked")&&(i++,a=!0):(i++,n=!0),e(this).closest("td").hasClass("cap-neg")&&l++}),a&&n||l>=i?(a=!0,n=!1):a||!n||s.hasClass("interacted")?a&&!n?(a=!1,n=!0):(a=!1,n=!1):(a=!0,n=!1),a?s.closest("tr").find("td").filter('td[class!="cap-unreg"]').each(function(){e(this).closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e(this).parent().find('input[type="checkbox"]:not(.disabled)').prop("checked",!0),e(this).parent().find("input.cme-negation-input").remove();var t=e(this).next('input[type="checkbox"]').attr("name");t||(t=e(this).next("label").find('input[type="checkbox"]').attr("name")),e('input[name="'+t+'"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="'+t+'"]').prop("checked",!0).parent().find("input.cme-negation-input").remove(),e(this).closest("td").hasClass("upload_files")&&(e("tr.unfiltered_upload").find("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e("tr.unfiltered_upload").find('input[type="checkbox"]').prop("checked",!1),e("tr.unfiltered_upload").find("input.cme-negation-input").remove(),e('input[name="caps[unfiltered_upload]"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="caps[unfiltered_upload]"]').prop("checked",!0).parent().find("input.cme-negation-input").remove(),e("tr.unfiltered_upload").find('input[name="caps[unfiltered_upload]"]').prop("checked",!0))}):n?s.closest("tr").find("td").filter('td[class!="cap-unreg"]').each(function(){e(this).closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e(this).parent().find('input[type="checkbox"]:not(.disabled)').prop("checked",!1),e(this).parent().find("input.cme-negation-input").remove();var t=e(this).next('input[type="checkbox"]').attr("name");t||(t=e(this).next("label").find('input[type="checkbox"]').attr("name")),e('input[name="'+t+'"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="'+t+'"]').prop("checked",!1).parent().find("input.cme-negation-input").remove(),e(this).closest("td").hasClass("upload_files")&&e("tr.unfiltered_upload").find('input[name="caps[unfiltered_upload]"]').prop("checked",!1)}):s.closest("tr").find('td[class!="cap-neg"]').filter('td[class!="cap-unreg"]').each(function(){var t=e(this).find('input[type="checkbox"]').attr("name");t&&(e(this).addClass("cap-neg"),e(this).append('<input type="hidden" class="cme-negation-input" name="'+t+'" value="" />'),e('input[name="'+t+'"]:not(.disabled)').parent().next("a.neg-cap:visible").click(),e(this).closest("td").hasClass("upload_files")&&e("tr.unfiltered_upload").find("a.neg-cap").trigger("click"))}),s.addClass("interacted")}),e(document).on("change",'tr.unfiltered_upload input[name="caps[unfiltered_upload]"]',function(t){let s=e(this);s.prop("checked")?(e('input[name="caps[upload_files]"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="caps[upload_files]"]').prop("checked",!0).parent().find("input.cme-negation-input").remove()):s.prop("checked")||(e('input[name="caps[upload_files]"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="caps[upload_files]"]').prop("checked",!1).parent().find("input.cme-negation-input").remove())}),e(document).on("click",".pp-single-action-rotate",function(t){let s=e(this);var a=!1,n=!1;s.prop("checked")?n=!0:s.prop("checked")||(a=!0),a&&n?(a=!0,n=!1):a||!n||s.hasClass("interacted")?a&&!n?(a=!1,n=!0):(a=!1,n=!1):(a=!0,n=!1),a||n||(t.preventDefault(),s.closest("td").find("a.neg-cap").click()),s.addClass("interacted"),navigator.userAgent.toLowerCase().indexOf("firefox")>-1&&document.getSelection().empty()}),e(".pp-capability-menus-wrapper.profile-features").length>0&&e(".pp-capability-menus-wrapper.profile-features table.pp-capability-menus-select tbody").sortable({axis:"y",update:function(t,s){var a=[];e(".pp-capability-menus-wrapper.profile-features table.pp-capability-menus-select tbody tr.ppc-sortable-row").each(function(){var t=e(this).attr("data-element_key");t&&a.push(t)}),e(".capsman_profile_features_elements_order").val(a.join(","))}}),e(document).on("click",".ppc-sidebar-panel .postbox-header",function(){e(this).closest(".ppc-sidebar-panel").hasClass("closed")?(e(this).closest(".ppc-sidebar-panel").find(".metabox-state").val("opened"),e(this).closest(".ppc-sidebar-panel").toggleClass("closed")):(e(this).closest(".ppc-sidebar-panel").find(".metabox-state").val("closed"),e(this).closest(".ppc-sidebar-panel").toggleClass("closed"))}),e(document).on("click",".frontend-element-form-submit",function(t){t.preventDefault();var s=e(this);if(custom_label=e(".frontend-element-new-name").val(),custom_element=e(".frontend-element-new-element").val(),element_pages=e(".frontend-element-new-element-pages").val(),element_post_types=e(".frontend-element-new-element-post-types").val(),security=e(".frontend-element-form-nonce").val(),item_section=e(this).attr("data-section"),item_id=e("."+item_section+"-form").find(".custom-edit-id").val(),""==custom_label||""==custom_element){s.closest("tr").find(".ppc-post-features-note").html('<div class="ppc-feature-submit-form-error" style="color:red;">'+s.attr("data-required")+"</div>"),e(".ppc-feature-submit-form-error").delay(2e3).fadeOut("slow");return}e(".ppc-feature-submit-form-error").remove(),s.attr("disabled",!0),s.closest("tr").find(".ppc-feature-post-loader").addClass("is-active");var a={action:"ppc_submit_frontend_element_by_ajax",security:security,custom_label:custom_label,custom_element:custom_element,element_pages:element_pages,element_post_types:element_post_types,item_id:item_id};e.post(ajaxurl,a,function(t){if("error"==t.status)s.closest("tr").find(".ppc-post-features-note").html('<div class="ppc-feature-submit-form-error" style="color:red;">'+t.message+"</div>"),e(".ppc-feature-submit-form-error").delay(2e3).fadeOut("slow");else{var a=e("table.frontendelements-table");e(".frontend-features-save-button-warning").remove(),e(".frontend-element-new-name").val(""),e(".frontend-element-new-element").val(""),e(".frontend-element-new-element-pages").val([]).trigger("chosen:updated"),e(".frontend-element-new-element-post-types").val([]).trigger("chosen:updated"),s.closest("tr").find(".ppc-post-features-note").html('<div class="ppc-feature-submit-form-error" style="color:green;">'+t.message+"</div>"),e(".ppc-feature-submit-form-error").delay(5e3).fadeOut("slow"),setTimeout(function(){e(".ppc-menu-overlay-item").removeClass("ppc-menu-overlay-item")},5e3),""!==item_id?(e(".cancel-custom-item-edit").trigger("click"),e(".custom-item-"+item_id).replaceWith(t.content)):(a.find(".custom-items-table tbody").append(t.content),a.find(".temporarily.hidden-element").removeClass("temporarily hidden-element")),e("table.frontendelements-table table.custom-items-table tr.custom-item-row").length>1?e("table.frontendelements-table .custom-item-toggle-row").removeClass("hidden-element"):e("table.frontendelements-table .custom-item-toggle-row").addClass("hidden-element")}s.closest("form").find("input[type=submit]").attr("disabled",!1),s.closest("tr").find(".ppc-feature-post-loader").removeClass("is-active"),s.attr("disabled",!1)})}),e(document).on("click",".body-class-form-submit",function(t){t.preventDefault();var s=e(this);if(custom_label=e(".body-class-new-name").val(),custom_element=e(".body-class-new-element").val(),element_pages=e(".body-class-new-element-pages").val(),element_post_types=e(".body-class-new-element-post-types").val(),security=e(".body-class-form-nonce").val(),item_section=e(this).attr("data-section"),item_id=e("."+item_section+"-form").find(".custom-edit-id").val(),""==custom_label||""==custom_element){s.closest("tr").find(".ppc-post-features-note").html('<div class="ppc-feature-submit-form-error" style="color:red;">'+s.attr("data-required")+"</div>"),e(".ppc-feature-submit-form-error").delay(2e3).fadeOut("slow");return}e(".ppc-feature-submit-form-error").remove(),s.attr("disabled",!0),s.closest("tr").find(".ppc-feature-post-loader").addClass("is-active");var a={action:"ppc_submit_bodyclass_by_ajax",security:security,custom_label:custom_label,custom_element:custom_element,element_pages:element_pages,element_post_types:element_post_types,item_id:item_id};e.post(ajaxurl,a,function(t){if("error"==t.status)s.closest("tr").find(".ppc-post-features-note").html('<div class="ppc-feature-submit-form-error" style="color:red;">'+t.message+"</div>"),e(".ppc-feature-submit-form-error").delay(2e3).fadeOut("slow");else{var a=e("table.bodyclass-table");e(".frontend-features-save-button-warning").remove(),e(".body-class-new-name").val(""),e(".body-class-new-element").val(""),e(".body-class-new-element-pages").val([]).trigger("chosen:updated"),e(".body-class-new-element-post-types").val([]).trigger("chosen:updated"),s.closest("tr").find(".ppc-post-features-note").html('<div class="ppc-feature-submit-form-error" style="color:green;">'+t.message+"</div>"),e(".ppc-feature-submit-form-error").delay(5e3).fadeOut("slow"),setTimeout(function(){e(".ppc-menu-overlay-item").removeClass("ppc-menu-overlay-item")},5e3),""!==item_id?(e(".cancel-custom-item-edit").trigger("click"),e(".custom-item-"+item_id).replaceWith(t.content)):(a.find(".custom-items-table tbody").append(t.content),a.find(".temporarily.hidden-element").removeClass("temporarily hidden-element")),e("table.bodyclass-table table.custom-items-table tr.custom-item-row").length>1?e("table.bodyclass-table .custom-item-toggle-row").removeClass("hidden-element"):e("table.bodyclass-table .custom-item-toggle-row").addClass("hidden-element")}s.closest("form").find("input[type=submit]").attr("disabled",!1),s.closest("tr").find(".ppc-feature-post-loader").removeClass("is-active"),s.attr("disabled",!1)})}),e(document).on("click",".customstyles-form-submit",function(t){t.preventDefault();var s=e(this);if(custom_label=e(".customstyles-element-new-name").val(),custom_element=e(".customstyles-element-new-element").val(),element_pages=e(".customstyles-new-element-pages").val(),element_post_types=e(".customstyles-new-element-post-types").val(),security=e(".customstyles-form-nonce").val(),item_section=e(this).attr("data-section"),item_id=e("."+item_section+"-form").find(".custom-edit-id").val(),""==custom_label||""==custom_element){s.closest("tr").find(".ppc-post-features-note").html('<div class="ppc-feature-submit-form-error" style="color:red;">'+s.attr("data-required")+"</div>"),e(".ppc-feature-submit-form-error").delay(2e3).fadeOut("slow");return}e(".ppc-feature-submit-form-error").remove(),s.attr("disabled",!0),s.closest("tr").find(".ppc-feature-post-loader").addClass("is-active");var a={action:"ppc_submit_custom_styles_by_ajax",security:security,custom_label:custom_label,custom_element:custom_element,element_pages:element_pages,element_post_types:element_post_types,item_id:item_id};e.post(ajaxurl,a,function(t){if("error"==t.status)s.closest("tr").find(".ppc-post-features-note").html('<div class="ppc-feature-submit-form-error" style="color:red;">'+t.message+"</div>"),e(".ppc-feature-submit-form-error").delay(2e3).fadeOut("slow");else{var a=e("table.customstyles-table");e(".frontend-features-save-button-warning").remove(),e(".customstyles-element-new-name").val(""),e(".customstyles-new-element-clear").trigger("click"),e(".customstyles-new-element-pages").val([]).trigger("chosen:updated"),e(".customstyles-new-element-post-types").val([]).trigger("chosen:updated"),s.closest("tr").find(".ppc-post-features-note").html('<div class="ppc-feature-submit-form-error" style="color:green;">'+t.message+"</div>"),e(".ppc-feature-submit-form-error").delay(5e3).fadeOut("slow"),setTimeout(function(){e(".ppc-menu-overlay-item").removeClass("ppc-menu-overlay-item")},5e3),""!==item_id?(e(".cancel-custom-item-edit").trigger("click"),e(".custom-item-"+item_id).replaceWith(t.content)):(a.find(".custom-items-table tbody").append(t.content),a.find(".temporarily.hidden-element").removeClass("temporarily hidden-element")),e("table.customstyles-table table.custom-items-table tr.custom-item-row").length>1?e("table.customstyles-table .custom-item-toggle-row").removeClass("hidden-element"):e("table.customstyles-table .custom-item-toggle-row").addClass("hidden-element")}s.closest("form").find("input[type=submit]").attr("disabled",!1),s.closest("tr").find(".ppc-feature-post-loader").removeClass("is-active"),s.attr("disabled",!1)})}),e(document).on("click",".frontend-features-delete-item",function(t){if(confirm(cmeAdmin.deleteWarning)){var s=e(this),a=s.attr("data-section"),n=s.attr("data-id"),i=s.attr("data-delete-nonce");s.closest(".ppc-menu-row").fadeOut(300),e.post(ajaxurl,{action:"ppc_delete_frontend_feature_item_by_ajax",security:i,item_section:a,item_id:n},function(t){"error"==t.status?(s.closest(".ppc-menu-row").show(),alert(t.message)):(s.closest(".ppc-menu-row").remove(),e("table."+a+"-table table.custom-items-table tr.custom-item-row").length>1?e("table."+a+"-table .custom-item-toggle-row").removeClass("hidden-element"):e("table."+a+"-table .custom-item-toggle-row").addClass("hidden-element"))})}}),e(document).on("click",".view-custom-item",function(t){t.preventDefault(),e(this).closest(".custom-item-row").find(".custom-item-output").toggleClass("show")}),e(document).on("click",".edit-custom-item",function(t){t.preventDefault();var s=e(this),a=s.attr("data-section"),n=s.attr("data-id"),i=s.attr("data-label"),l=s.attr("data-element"),c="",o="",r=e("."+a+"-form");if(""!=n){if(r.find("."+a+"-form-label").val(i),r.find(".editing-custom-item").show(),r.find(".cancel-custom-item-edit").attr("style","visibility: visible"),r.find(".editing-custom-item .title").html(i),r.find(".submit-button").html(r.find(".submit-button").attr("data-edit")),r.find(".custom-edit-id").val(n),"customstyles"===a?s.closest(".custom-item-row").find(".customstyles-new-element-update").trigger("click"):r.find("."+a+"-form-element").val(l),"customstyles"===a||"frontendelements"===a||"bodyclass"===a){c=(c=s.attr("data-pages")).split(", ");var p=[];c.forEach(function(e){p.push(e)}),r.find("."+a+"-form-pages").val(p).trigger("chosen:updated");var d=[];(o=(o=s.attr("data-post-types")).split(", ")).forEach(function(e){d.push(e)}),r.find("."+a+"-form-post-types").val(d).trigger("chosen:updated")}e([document.documentElement,document.body]).animate({scrollTop:r.offset().top-50},"fast")}}),e(document).on("click",".cancel-custom-item-edit",function(t){t.preventDefault();var s=e(this).attr("data-section"),a=e("."+s+"-form");a.find("."+s+"-form-label").val(""),a.find(".editing-custom-item").hide(),a.find(".cancel-custom-item-edit").attr("style",""),a.find(".submit-button").html(a.find(".submit-button").attr("data-add")),a.find(".custom-edit-id").val(""),a.find("."+s+"-form-element").val(""),"customstyles"===s&&e(".customstyles-new-element-clear").trigger("click"),("customstyles"===s||"frontendelements"===s||"bodyclass"===s)&&(a.find("."+s+"-form-pages").val([]).trigger("chosen:updated"),a.find("."+s+"-form-post-types").val([]).trigger("chosen:updated"))}),e(document).on("keyup paste",".frontent-form-field",function(t){var s=!1;e(".frontend-features-save-button-warning").remove(),e(".frontent-form-field").each(function(){""!==e(this).val()&&e(this).val().replace(/\s/g,"").length&&(s=!0)}),s?e(this).closest("form").find("input[type=submit]").attr("disabled",!0).after('<span class="frontend-features-save-button-warning">'+cmeAdmin.saveWarning+"</span>"):e(this).closest("form").find("input[type=submit]").attr("disabled",!1)})});