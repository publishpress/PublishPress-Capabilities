jQuery(document).ready(function(e){var{__:t,_x:a,_n:n,_nx:s}=wp.i18n;function i(a="success"){setTimeout(function(){var n="ppc-floating-msg-"+Math.round(new Date().getTime()+100*Math.random()),s="success"===a?t("Changes saved!","capability-manager-enhanced"):t(" Error: changes can't be saved.","capability-manager-enhanced"),i=e(".ppc-floating-status").length;e("#wpbody-content").after('<span class="ppc-floating-status ppc-floating-status--'+a+" "+n+'">'+s+"</span>"),e("."+n).css("bottom",45*i).fadeIn(1e3).delay(1e4).fadeOut(1e3,function(){e(this).remove()})},500)}function c(t,a){var n=e("#toplevel_page_pp-capabilities-dashboard"),s=e(n).find("li."+t+"-menu-item");s.length&&(1==a?s.removeClass("ppc-hide-menu-item").find("a").removeClass("ppc-hide-menu-item"):s.addClass("ppc-hide-menu-item").find("a").addClass("ppc-hide-menu-item"))}function l(e){return null===e||null!==e.match(/^ *$/)}e("a.neg-cap").attr("title",cmeAdmin.negationCaption),e("a.neg-type-caps").attr("title",cmeAdmin.typeCapsNegationCaption),e("a.normal-cap").attr("title",cmeAdmin.switchableCaption),e('span.cap-x:not([class*="pp-cap-key"])').html(cmeAdmin.capNegated),e('table.cme-checklist input[class!="cme-check-all"]').not(":disabled").attr("title",cmeAdmin.chkCaption),e(".ppc-checkboxes-documentation-link").length>0&&e(".ppc-checkboxes-documentation-link").attr("target","blank"),e("table.cme-checklist a.neg-cap").click(function(t){e(this).closest("td").removeClass("cap-yes").removeClass("cap-no").addClass("cap-neg");var a=e(this).parent().find('input[type="checkbox"]').attr("name");return e(this).after('<input type="hidden" class="cme-negation-input" name="'+a+'" value="" />'),e('input[name="'+a+'"]').closest("td").removeClass("cap-yes").removeClass("cap-no").addClass("cap-neg"),e(this).closest("tr").hasClass("unfiltered_upload")&&(e('input[name="caps[upload_files]"]').closest("td").addClass("cap-neg"),e('input[name="caps[upload_files]"]').closest("td").append('<input type="hidden" class="cme-negation-input" name="caps[upload_files]" value="" />'),e('input[name="caps[upload_files]"]').parent().next("a.neg-cap:visible").click()),!1}),e(document).on("click","table.cme-typecaps span.cap-x,table.cme-checklist span.cap-x,table.cme-checklist td.cap-neg span",function(t){e(this).closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e(this).closest("td").find('input[type="checkbox"]').prop("checked",!1),e(this).closest("td").find("input.cme-negation-input").remove();var a=e(this).next('input[type="checkbox"]').attr("name");return a||(a=e(this).next("label").find('input[type="checkbox"]').attr("name")),e('input[name="'+a+'"]').closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="'+a+'"]').prop("checked",!1).closest("td").find("input.cme-negation-input").remove(),e(this).closest("td").hasClass("capability-checkbox-rotate")&&(e(this).closest("td").find('input[type="checkbox"]').prop("checked",!0),e(this).closest("td").hasClass("upload_files")&&(e("tr.unfiltered_upload").find("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e("tr.unfiltered_upload").find('input[type="checkbox"]').prop("checked",!1),e("tr.unfiltered_upload").find("input.cme-negation-input").remove(),e('input[name="caps[unfiltered_upload]"]').closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="caps[unfiltered_upload]"]').prop("checked",!0).closest("td").find("input.cme-negation-input").remove())),e(this).closest("td").find('input[type="checkbox"]').hasClass("pp-single-action-rotate")&&e(this).closest("td").find('input[type="checkbox"]').prop("checked",!0),e(this).closest("tr").hasClass("unfiltered_upload")&&(e('input[name="caps[upload_files]"]').closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="caps[upload_files]"]').prop("checked",!0).closest("td").find("input.cme-negation-input").remove()),!1}),e("#publishpress_caps_form").bind("keypress",function(t){if(13==t.keyCode)return e(document.activeElement).parent().find('input[type="submit"]').first().click(),!1}),e("input.cme-check-all").click(function(t){e(this).closest("table").find('input[type="checkbox"][disabled!="disabled"]:visible').prop("checked",e(this).is(":checked"))}),e("a.cme-neg-all").click(function(t){return e(this).closest("table").find("a.neg-cap:visible").click(),!1}),e("a.cme-switch-all").click(function(t){return e(this).closest("table").find("td.cap-neg span").click(),!1}),e("table.cme-typecaps a.neg-type-caps").click(function(t){return e(this).closest("tr").find('td[class!="cap-neg"]').filter('td[class!="cap-unreg"]').each(function(){e(this).addClass("cap-neg");var t=e(this).find('input[type="checkbox"]').attr("name");e(this).append('<input type="hidden" class="cme-negation-input" name="'+t+'" value="" />'),e('input[name="'+t+'"]').parent().next("a.neg-cap:visible").click()}),!1}),e("table.cme-typecaps th").click(function(){var t=e(this).index(),a=!e(this).prop("checked_all");if(e(this).hasClass("term-cap"))var n='[class*="term-cap"]';else var n='[class*="post-cap"]';var s=e(this).closest("table").find("tr td"+n+":nth-child("+(t+1)+') input[type="checkbox"]:visible');e(s).each(function(t,n){e('input[name="'+e(this).attr("name")+'"]').prop("checked",a)}),e(this).prop("checked_all",a)}),e("a.cme-fix-read-cap").click(function(){return e('input[name="caps[read]"]').prop("checked",!0),e('input[name="caps[read]"].cme-negation-input').remove(),e('input[name="SaveRole"]').trigger("click"),!1}),e(".ppc-filter-select").each(function(){var t=e(this),a=[];e(this).parent().siblings("table").find("tbody").find("tr").each(function(){a.push({value:e(this).attr("class"),text:e(this).find(".cap_type").text()})}),a.forEach(function(a,n){t.append(e("<option>",{value:a.value,text:a.text}))})}),e(".ppc-filter-select").prop("selectedIndex",0),e(".ppc-filter-select-reset").click(function(){e(this).prev(".ppc-filter-select").prop("selectedIndex",0),e(this).parent().siblings("table").find("tr").show()}),e(".ppc-filter-select").change(function(){e(this).val()?(e(this).parent().siblings("table").find("tr").hide(),e(this).parent().siblings("table").find("thead tr:first-child").show(),e(this).parent().siblings("table").find("tr."+e(this).val()).show()):e(this).parent().siblings("table").find("tr").show()}),e(".ppc-filter-text").val(""),e(".ppc-filter-text-reset").click(function(){e(this).prev(".ppc-filter-text").val(""),e(this).parent().siblings("table").find("tr").show(),e(this).parent().siblings(".ppc-filter-no-results").hide()}),e(".ppc-filter-text").keyup(function(){var t=e(this).val().trim().replace(/\s+/g,"_");e(this).parent().siblings("table").find("tr").hide(),e(this).parent().siblings("table").find('tr[class*="'+t+'"]').show(),e(this).parent().siblings("table").find("tr.cme-bulk-select").hide(),0===e(this).val().length&&e(this).parent().siblings("table").find("tr").show(),0===e(this).parent().siblings("table").find("tr:visible").length?e(this).parent().siblings(".ppc-filter-no-results").show():e(this).parent().siblings(".ppc-filter-no-results").hide()}),e(document).on("click",".ppc-tool-tip.click-tooltip",function(t){t.preventDefault(),e(this).toggleClass("is-active")}),e(document).on("click",".ppc-roles-tab li",function(t){t.preventDefault();var a=e(this).attr("data-tab");e(".ppc-roles-tab li").removeClass("active"),e(this).addClass("active"),e(".pp-roles-tab-tr").hide(),e(".pp-roles-"+a+"-tab").show()}),e(document).on("click",".ppc-redirects-tab li",function(t){t.preventDefault();var a=e(this).attr("data-tab");e(".ppc-redirects-tab li").removeClass("active"),e(this).addClass("active"),e(".pp-redirects-tab-tr").hide(),e(".pp-redirects-"+a+"-tab").show()}),e(document).on("change",".login-redirect-option #referer_redirect",function(){e(".login-redirect-option .custom-url-wrapper").hide(),e(".login-redirect-option #custom_redirect").prop("checked",!1)}),e(document).on("change",".login-redirect-option #custom_redirect",function(t){e(this).prop("checked")?e(".login-redirect-option .custom-url-wrapper").show():e(".login-redirect-option .custom-url-wrapper").hide(),e(".login-redirect-option #referer_redirect").prop("checked",!1)}),e(".pp-roles-internal-links-wrapper .base-input input").on("keyup",function(t){var a=e(this),n=a.closest(".pp-roles-internal-links-wrapper"),s=a.val();n.find(".base-input input").attr("data-base",s).attr("data-entry",n.find(".base-input input").attr("data-home_url")+s)}),e(".pp-roles-internal-links-wrapper .base-url a").on("click",function(e){return e.preventDefault(),!1}),e(".pp-capability-roles-wrapper .submit-role-form").on("click",function(t){let a="",n=!1;e(".role-submit-response").html(""),e("#custom_redirect").prop("checked")&&l(e("#login_redirect").val())&&(n=!0,a+="- "+e("#login_redirect").attr("data-required_message")+"<br />"),e(".pp-roles-internal-links-wrapper .base-input input").each(function(){var t=e(this).attr("data-base");!l(t)&&t.includes("://")&&(n=!0,a+="- "+e(this).attr("data-message")+"<br />")}),e(".allowed-editor-toggle").prop("checked")&&0===e("#role_editor-select").val().length&&(n=!0,a+="- "+e("#role_editor-select").attr("data-message")+"<br />"),n&&(t.preventDefault(),e(".role-submit-response").html(a))}),e(document).on("click",".roles-capabilities-load-more",function(t){t.preventDefault(),e(".roles-capabilities-load-more").hide(),e(".roles-capabilities-load-less").show(),e("ul.pp-roles-capabilities li").show()}),e(document).on("change",'.capability-checkbox-rotate input[type="checkbox"]',function(t){let a=e(this),n=!1,s=!1,i=!1;if(a.prop("checked")?a.prop("checked")&&(s=!0):i=!0,s&&a.hasClass("interacted")&&(s=!1,i=!1,n=!0),i)a.prop("checked",!1),a.closest("td").hasClass("upload_files")&&e("tr.unfiltered_upload").find('input[name="caps[unfiltered_upload]"]').prop("checked",!1);else if(s)a.prop("checked",!0),a.closest("td").hasClass("upload_files")&&(e("tr.unfiltered_upload").find("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e("tr.unfiltered_upload").find('input[type="checkbox"]').prop("checked",!1),e("tr.unfiltered_upload").find("input.cme-negation-input").remove(),e('input[name="caps[unfiltered_upload]"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="caps[unfiltered_upload]"]').prop("checked",!0).parent().find("input.cme-negation-input").remove(),e("tr.unfiltered_upload").find('input[name="caps[unfiltered_upload]"]').prop("checked",!0));else if(n){a.closest("td").hasClass("upload_files")&&e("tr.unfiltered_upload").find("a.neg-cap").trigger("click"),a.prop("checked",!1);var c=a.closest("td");c.addClass("cap-neg");var l=c.find('input[type="checkbox"]').attr("name");c.append('<input type="hidden" class="cme-negation-input" name="'+l+'" value="" />'),e('input[name="'+l+'"]').parent().next("a.neg-cap:visible").click()}a.addClass("interacted")}),e(document).on("click",'input[name="pp_toggle_all"]',function(t){e(this).closest("table.cme-typecaps").find('input[type="checkbox"]:visible').not(".excluded-input").not(".disabled").prop("checked",e(this).prop("checked"))}),e(document).on("click",".pp-row-action-rotate",function(t){t.preventDefault();let a=e(this);var n=!1,s=!1,i=0,c=0;a.closest("tr").find('input[type="checkbox"]:not(.disabled)').each(function(){e(this).hasClass("excluded-input")||e(this).prop("checked")?!e(this).hasClass("excluded-input")&&e(this).prop("checked")&&(i++,n=!0):(i++,s=!0),e(this).closest("td").hasClass("cap-neg")&&c++}),n&&s||c>=i?(n=!0,s=!1):n||!s||a.hasClass("interacted")?n&&!s?(n=!1,s=!0):(n=!1,s=!1):(n=!0,s=!1),n?a.closest("tr").find("td").filter('td[class!="cap-unreg"]').each(function(){e(this).closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e(this).parent().find('input[type="checkbox"]:not(.disabled)').prop("checked",!0),e(this).parent().find("input.cme-negation-input").remove();var t=e(this).next('input[type="checkbox"]').attr("name");t||(t=e(this).next("label").find('input[type="checkbox"]').attr("name")),e('input[name="'+t+'"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="'+t+'"]').prop("checked",!0).parent().find("input.cme-negation-input").remove(),e(this).closest("td").hasClass("upload_files")&&(e("tr.unfiltered_upload").find("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e("tr.unfiltered_upload").find('input[type="checkbox"]').prop("checked",!1),e("tr.unfiltered_upload").find("input.cme-negation-input").remove(),e('input[name="caps[unfiltered_upload]"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="caps[unfiltered_upload]"]').prop("checked",!0).parent().find("input.cme-negation-input").remove(),e("tr.unfiltered_upload").find('input[name="caps[unfiltered_upload]"]').prop("checked",!0))}):s?a.closest("tr").find("td").filter('td[class!="cap-unreg"]').each(function(){e(this).closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e(this).parent().find('input[type="checkbox"]:not(.disabled)').prop("checked",!1),e(this).parent().find("input.cme-negation-input").remove();var t=e(this).next('input[type="checkbox"]').attr("name");t||(t=e(this).next("label").find('input[type="checkbox"]').attr("name")),e('input[name="'+t+'"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="'+t+'"]').prop("checked",!1).parent().find("input.cme-negation-input").remove(),e(this).closest("td").hasClass("upload_files")&&e("tr.unfiltered_upload").find('input[name="caps[unfiltered_upload]"]').prop("checked",!1)}):a.closest("tr").find('td[class!="cap-neg"]').filter('td[class!="cap-unreg"]').each(function(){var t=e(this).find('input[type="checkbox"]').attr("name");t&&(e(this).addClass("cap-neg"),e(this).append('<input type="hidden" class="cme-negation-input" name="'+t+'" value="" />'),e('input[name="'+t+'"]:not(.disabled)').parent().next("a.neg-cap:visible").click(),e(this).closest("td").hasClass("upload_files")&&e("tr.unfiltered_upload").find("a.neg-cap").trigger("click"))}),a.addClass("interacted")}),e(document).on("change",'tr.unfiltered_upload input[name="caps[unfiltered_upload]"]',function(t){let a=e(this);a.prop("checked")?(e('input[name="caps[upload_files]"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="caps[upload_files]"]').prop("checked",!0).parent().find("input.cme-negation-input").remove()):a.prop("checked")||(e('input[name="caps[upload_files]"]').parent().closest("td").removeClass("cap-neg").removeClass("cap-yes").addClass("cap-no"),e('input[name="caps[upload_files]"]').prop("checked",!1).parent().find("input.cme-negation-input").remove())}),e(document).on("click",".pp-single-action-rotate",function(t){let a=e(this);var n=!1,s=!1;a.prop("checked")?s=!0:a.prop("checked")||(n=!0),n&&s?(n=!0,s=!1):n||!s||a.hasClass("interacted")?n&&!s?(n=!1,s=!0):(n=!1,s=!1):(n=!0,s=!1),n||s||(t.preventDefault(),a.closest("td").find("a.neg-cap").click()),a.addClass("interacted"),navigator.userAgent.toLowerCase().indexOf("firefox")>-1&&document.getSelection().empty()}),e(".pp-capability-menus-wrapper.profile-features").length>0&&e(".pp-capability-menus-wrapper.profile-features table.pp-capability-menus-select tbody").sortable({axis:"y",update:function(t,a){var n=[];e(".pp-capability-menus-wrapper.profile-features table.pp-capability-menus-select tbody tr.ppc-sortable-row").each(function(){var t=e(this).attr("data-element_key");t&&n.push(t)}),e(".capsman_profile_features_elements_order").val(n.join(","))}}),e(document).on("click",".ppc-sidebar-panel .postbox-header",function(){e(this).closest(".ppc-sidebar-panel").hasClass("closed")?(e(this).closest(".ppc-sidebar-panel").find(".metabox-state").val("opened"),e(this).closest(".ppc-sidebar-panel").toggleClass("closed")):(e(this).closest(".ppc-sidebar-panel").find(".metabox-state").val("closed"),e(this).closest(".ppc-sidebar-panel").toggleClass("closed"))}),e(document).on("click",".ppc-button-group label",function(){var t=e(this),a=t.find("input").val(),n=t.closest(".ppc-button-group"),s=n.attr("data-hide-selector");n.find("label.selected").removeClass("selected"),e(s).addClass("hidden-element"),e(a).removeClass("hidden-element"),t.addClass("selected"),".frontend-element-styles"===a&&e(".ppc-code-editor-refresh-editor").trigger("click")}),e(document).on("click",".frontend-element-form-submit",function(t){t.preventDefault();var a=e(this);if(custom_label=e(".frontend-element-new-name").val(),custom_element_selector=e(".frontend-element-new-element").val(),custom_element_styles=e(".frontendelements-form-styles").val(),custom_element_bodyclass=e(".frontendelements-form-bodyclass").val(),element_pages=e(".frontend-element-new-element-pages").val(),element_post_types=e(".frontend-element-new-element-post-types").val(),security=e(".frontend-element-form-nonce").val(),item_section=e(this).attr("data-section"),item_id=e("."+item_section+"-form").find(".custom-edit-id").val(),".frontend-element-whole-site"===e('input[name="frontend_feature_pages"]:checked').val()&&(element_pages=["whole_site"]),""==custom_label||""==custom_element_selector&&""==custom_element_styles&&""==custom_element_bodyclass){a.closest("tr").find(".ppc-post-features-note").html('<div class="ppc-feature-submit-form-error updated notice error"><p>'+a.attr("data-required")+"</p></div>"),e(".ppc-feature-submit-form-error").delay(2e3).fadeOut("slow");return}e(".ppc-feature-submit-form-error").remove(),a.attr("disabled",!0),a.closest("tr").find(".ppc-feature-post-loader").addClass("is-active");var n={action:"ppc_submit_frontend_element_by_ajax",security:security,custom_label:custom_label,custom_element_selector:custom_element_selector,custom_element_styles:custom_element_styles,custom_element_bodyclass:custom_element_bodyclass,element_pages:element_pages,element_post_types:element_post_types,item_id:item_id};e.post(ajaxurl,n,function(t){if("error"==t.status)a.closest("tr").find(".ppc-post-features-note").html('<div class="ppc-feature-submit-form-error updated notice error"><p>'+t.message+"</p></div>"),e(".ppc-feature-submit-form-error").delay(2e3).fadeOut("slow");else{var n=e("table.frontendelements-table");e(".frontend-features-save-button-warning").remove(),e(".frontend-element-new-name").val(""),e(".frontend-element-new-element").val(""),e(".frontendelements-form-bodyclass").val(""),e(".css-new-element-clear").trigger("click"),e(".frontend-element-new-element-pages").val([]).trigger("chosen:updated"),e(".frontend-element-new-element-post-types").val([]).trigger("chosen:updated"),a.closest("tr").find(".ppc-post-features-note").html('<div class="ppc-feature-submit-form-error updated notice notice-success"><p>'+t.message+"</p></div>"),e(".ppc-feature-submit-form-error").delay(5e3).fadeOut("slow"),setTimeout(function(){e(".ppc-menu-overlay-item").removeClass("ppc-menu-overlay-item")},5e3),""!==item_id?(e(".cancel-custom-item-edit").trigger("click"),e(".custom-item-"+item_id).replaceWith(t.content)):(n.find(".custom-items-table tbody").append(t.content),n.find(".temporarily.hidden-element").removeClass("temporarily hidden-element")),e("table.frontendelements-table table.custom-items-table tr.custom-item-row").length>1?e("table.frontendelements-table .custom-item-toggle-row").removeClass("hidden-element"):e("table.frontendelements-table .custom-item-toggle-row").addClass("hidden-element")}a.closest("form").find("input[type=submit]").attr("disabled",!1),a.closest("tr").find(".ppc-feature-post-loader").removeClass("is-active"),a.attr("disabled",!1)})}),e(document).on("click",".frontend-features-delete-item",function(t){if(confirm(cmeAdmin.deleteWarning)){var a=e(this),n=a.attr("data-id"),s=a.attr("data-delete-nonce"),i=a.attr("data-section");a.closest(".ppc-menu-row").fadeOut(300),e.post(ajaxurl,{action:"ppc_delete_frontend_feature_item_by_ajax",security:s,item_id:n},function(t){"error"==t.status?(a.closest(".ppc-menu-row").show(),alert(t.message)):(a.closest(".ppc-menu-row").remove(),e("table."+i+"-table table.custom-items-table tr.custom-item-row").length>1?e("table."+i+"-table .custom-item-toggle-row").removeClass("hidden-element"):e("table."+i+"-table .custom-item-toggle-row").addClass("hidden-element"))})}}),e(document).on("click",".view-custom-item",function(t){t.preventDefault(),e(this).closest(".custom-item-row").find(".custom-item-output").toggleClass("show")}),e(document).on("click",".edit-custom-item",function(t){t.preventDefault();var a=e(this),n=a.attr("data-section"),s=a.attr("data-id"),i=a.attr("data-label"),c=a.attr("data-selector"),l=a.attr("data-bodyclass"),p=a.attr("data-element"),o="",r="",d=e("."+n+"-form");if(""!=s){if(d.find("."+n+"-form-label").val(i).trigger("change"),d.find(".editing-custom-item").show(),d.find(".cancel-custom-item-edit").attr("style","visibility: visible"),d.find(".editing-custom-item .title").html(i),d.find(".submit-button").html(d.find(".submit-button").attr("data-edit")),d.find(".custom-edit-id").val(s),"frontendelements"===n){if(a.closest(".custom-item-row").find(".css-new-element-update").trigger("click"),d.find("."+n+"-form-element").val(c),d.find("."+n+"-form-bodyclass").val(l),""!==c?e(".frontend-element-toggle .ppc-button-group label.element-classes").trigger("click"):""!==l?e(".frontend-element-toggle .ppc-button-group label.body-class").trigger("click"):e(".frontend-element-toggle .ppc-button-group label.custom-css").trigger("click"),(o=(o=a.attr("data-pages")).split(", ")).includes("whole_site"))e(".frontend-element-toggle .ppc-button-group label.whole-site").trigger("click");else{e(".frontend-element-toggle .ppc-button-group label.other-pages").trigger("click");var u=[];o.forEach(function(e){u.push(e)}),d.find("."+n+"-form-pages").val(u).trigger("chosen:updated")}r=(r=a.attr("data-post-types")).split(", ");var m=[];r.forEach(function(e){m.push(e)}),d.find("."+n+"-form-post-types").val(m).trigger("chosen:updated")}else d.find("."+n+"-form-element").val(p);e([document.documentElement,document.body]).animate({scrollTop:d.offset().top-50},"fast")}}),e(document).on("click",".cancel-custom-item-edit",function(t){t.preventDefault();var a=e(this).attr("data-section"),n=e("."+a+"-form");n.find("."+a+"-form-label").val(""),n.find(".editing-custom-item").hide(),n.find(".cancel-custom-item-edit").attr("style",""),n.find(".submit-button").html(n.find(".submit-button").attr("data-add")),n.find(".custom-edit-id").val(""),n.find("."+a+"-form-element").val(""),"frontendelements"===a&&(e(".css-new-element-clear").trigger("click"),n.find("."+a+"-form-element").val(""),n.find("."+a+"-form-bodyclass").val(""),n.find("."+a+"-form-pages").val([]).trigger("chosen:updated"),n.find("."+a+"-form-post-types").val([]).trigger("chosen:updated")),n.find("."+a+"-form-label").trigger("change")}),e(document).on("keyup paste change",".frontent-form-field",function(t){var a=!1;e(".frontend-features-save-button-warning").remove(),e(".frontent-form-field").each(function(){""!==e(this).val()&&e(this).val().replace(/\s/g,"").length&&(a=!0)}),a?e(this).closest("form").find("input[type=submit]").attr("disabled",!0).after('<span class="frontend-features-save-button-warning">'+cmeAdmin.saveWarning+"</span>"):e(this).closest("form").find("input[type=submit]").attr("disabled",!1)}),e(document).on("click",".ppc-settings-subtab a",function(t){t.preventDefault();var a=e(this).attr("data-content");e(".ppc-settings-subtab a").removeClass("active"),e(".ppc-settings-tab-content").addClass("hidden-element"),e(this).addClass("active"),e(a).removeClass("hidden-element")}),e(".dashboard-settings-control .slider").bind("click",function(t){try{if(t.preventDefault(),e(this).hasClass("slider--disabled"))return!1;var a=e(this).parent().find("input"),n=a.is(":checked")?1:0,s=1==n?0:1,l=a.data("feature"),p=a.parent().find(".slider");e.ajax({url:cmeAdmin.ajaxurl,method:"POST",data:{action:"save_dashboard_feature_by_ajax",feature:l,new_state:s,nonce:cmeAdmin.nonce},beforeSend:function(){p.css("opacity",.5)},success:function(){(1==s?a.prop("checked",!0):a.prop("checked",!1),p.css("opacity",1),"capabilities"===l)?c("pp-"+l,s):c("pp-capabilities-"+l,s),statusMsgNotification=i()},error:function(e,t,a){console.error(e.responseText),statusMsgNotification=i("error")}})}catch(o){console.error(o)}})});