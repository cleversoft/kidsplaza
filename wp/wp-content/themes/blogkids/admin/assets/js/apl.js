jQuery.noConflict();jQuery(document).ready(function(e){function t(e){return decodeURI((RegExp(e+"="+"(.+?)(&|$)").exec(location.search)||[,""])[1])}function i(t){var n=t;if(this.timer){clearTimeout(n.timer)}this.timer=setTimeout(function(){e(n).parent().prev().find("strong").text(n.value)},100);return true}function s(t,n){var r=e(t).val();var i="style_link_"+n;var s=n+"_ggf_previewer";if(r){e("."+s).fadeIn();if(r!=="none"&&r!=="Select a font"){e("."+i).remove();var o=r.replace(/\s+/g,"+");e("head").append('<link href="http://fonts.googleapis.com/css?family='+o+'" rel="stylesheet" type="text/css" class="'+i+'">');e("."+s).css("font-family",r+", sans-serif")}else{e("."+s).css("font-family","");e("."+s).fadeOut()}}}function o(t,n){var r=e(".uploaded-file"),i;var s=e(this);t.preventDefault();if(i){i.open();return}i=wp.media({title:s.data("choose"),button:{text:s.data("update"),close:false}});i.on("select",function(){var e=i.state().get("selection").first();i.close();n.find(".upload").val(e.attributes.url);if(e.attributes.type=="image"){n.find(".screenshot").empty().hide().append('<img class="of-option-image" src="'+e.attributes.url+'">').slideDown("fast")}n.find(".media_upload_button").unbind();n.find(".remove-image").show().removeClass("hide");n.find(".of-background-properties").slideDown();a()});i.open()}function u(t){t.find(".remove-image").hide().addClass("hide");t.find(".upload").val("");t.find(".of-background-properties").hide();t.find(".screenshot").slideUp();t.find(".remove-file").unbind();if(e(".section-upload .upload-notice").length>0){e(".media_upload_button").remove()}a()}function a(){e(".remove-image, .remove-file").on("click",function(){u(e(this).parents(".section-upload, .section-media, .slide_body"))});e(".media_upload_button").unbind("click").click(function(t){o(t,e(this).parents(".section-upload, .section-media, .slide_body"))})}jQuery(".fld").click(function(){var t=".f_"+this.id;e(t).slideToggle("normal","swing")});e(".of-color").wpColorPicker();e("#js-warning").hide();e(".group").hide();if(t("tab")!=""){e.cookie("of_current_opt","#"+t("tab"),{expires:7,path:"/"})}if(e.cookie("of_current_opt")===null){e(".group:first").fadeIn("fast");e("#of-nav li:first").addClass("current")}else{var n=e("#hooks").html();n=jQuery.parseJSON(n);e.each(n,function(t,n){if(e.cookie("of_current_opt")=="#of-option-"+n){e(".group#of-option-"+n).fadeIn();e("#of-nav li."+n).addClass("current")}})}e("#of-nav li a").click(function(t){e("#of-nav li").removeClass("current");e(this).parent().addClass("current");var n=e(this).attr("href");e.cookie("of_current_opt",n,{expires:7,path:"/"});e(".group").hide();e(n).fadeIn("fast");return false});var r=0;e("#expand_options").click(function(){if(r==0){r=1;e("#of_container #of-nav").hide();e("#of_container #content").width(755);e("#of_container .group").add("#of_container .group h2").show();e(this).removeClass("expand");e(this).addClass("close");e(this).text("Close")}else{r=0;e("#of_container #of-nav").show();e("#of_container #content").width(595);e("#of_container .group").add("#of_container .group h2").hide();e("#of_container .group:first").show();e("#of_container #of-nav li").removeClass("current");e("#of_container #of-nav li:first").addClass("current");e(this).removeClass("close");e(this).addClass("expand");e(this).text("Expand")}});e.fn.center=function(){this.animate({top:(e(window).height()-this.height()-200)/2+e(window).scrollTop()+"px"},100);this.css("left",250);return this};e("#of-popup-save").center();e("#of-popup-reset").center();e("#of-popup-fail").center();e(window).scroll(function(){e("#of-popup-save").center();e("#of-popup-reset").center();e("#of-popup-fail").center()});e(".of-radio-img-img").click(function(){e(this).parent().parent().find(".of-radio-img-img").removeClass("of-radio-img-selected");e(this).addClass("of-radio-img-selected")});e(".of-radio-img-label").hide();e(".of-radio-img-img").show();e(".of-radio-img-radio").hide();e(".of-radio-tile-img").click(function(){e(this).parent().parent().find(".of-radio-tile-img").removeClass("of-radio-tile-selected");e(this).addClass("of-radio-tile-selected")});e(".of-radio-tile-label").hide();e(".of-radio-tile-img").show();e(".of-radio-tile-radio").hide();(function(e){styleSelect={init:function(){e(".select_wrapper").each(function(){e(this).prepend("<span>"+e(this).find(".select option:selected").text()+"</span>")});e(".select").live("change",function(){e(this).prev("span").replaceWith("<span>"+e(this).find("option:selected").text()+"</span>")});e(".select").bind(e.browser.msie?"click":"change",function(t){e(this).prev("span").replaceWith("<span>"+e(this).find("option:selected").text()+"</span>")})}};e(document).ready(function(){styleSelect.init()})})(jQuery);e(".slide_body").hide();e(".slide_edit_button").live("click",function(){e(this).parent().toggleClass("active").next().slideToggle("fast");return false});e(".of-slider-title").live("keyup",function(){i(this)});e(".slide_delete_button").live("click",function(){var t=confirm("Are you sure you wish to delete this slide?");if(t){var n=e(this).parents("li");n.animate({opacity:.25,height:0},500,function(){e(this).remove()});return false}else{return false}});e(".slide_add_button").live("click",function(){var t=e(this).prev();var n=t.attr("id");var r=e("#"+n+" li").find(".order").map(function(){var e=this.id;e=e.replace(/\D/g,"");e=parseFloat(e);return e}).get();var i=Math.max.apply(Math,r);if(i<1){i=0}var s=i+1;var o='<li class="temphide"><div class="slide_header"><strong>Slide '+s+'</strong><input type="hidden" class="slide of-input order" name="'+n+"["+s+'][order]" id="'+n+"_slide_order-"+s+'" value="'+s+'"><a class="slide_edit_button" href="#">Edit</a></div><div class="slide_body" style="display: none; "><label>Title</label><input class="slide of-input of-slider-title" name="'+n+"["+s+'][title]" id="'+n+"_"+s+'_slide_title" value=""><label>Image URL</label><input class="upload slide of-input" name="'+n+"["+s+'][url]" id="'+n+"_"+s+'_slide_url" value=""><div class="upload_button_div"><span class="button media_upload_button" id="'+n+"_"+s+'">Upload</span><span class="button remove-image hide" id="reset_'+n+"_"+s+'" title="'+n+"_"+s+'">Remove</span></div><div class="screenshot"></div><label>Link URL (optional)</label><input class="slide of-input" name="'+n+"["+s+'][link]" id="'+n+"_"+s+'_slide_link" value=""><label>Description (optional)</label><textarea class="slide of-input" name="'+n+"["+s+'][description]" id="'+n+"_"+s+'_slide_description" cols="8" rows="8"></textarea><a class="slide_delete_button" href="#">Delete</a><div class="clear"></div></div></li>';t.append(o);var u=t.find(".temphide");u.fadeIn("fast",function(){e(this).removeClass("temphide")});a();return false});jQuery(".slider").find("ul").each(function(){var t=jQuery(this).attr("id");e("#"+t).sortable({placeholder:"placeholder",opacity:.6,handle:".slide_header",cancel:"a"})});jQuery(".sorter").each(function(){var t=jQuery(this).attr("id");e("#"+t).find("ul").sortable({items:"li",placeholder:"placeholder",connectWith:".sortlist_"+t,opacity:.6,update:function(){e(this).find(".position").each(function(){var n=e(this).parent().attr("id");var r=e(this).parent().parent().attr("id");r=r.replace(t+"_","");var i=e(this).parent().parent().parent().attr("id");e(this).prop("name",i+"["+r+"]["+n+"]")})}})});e("#of_backup_button").live("click",function(){var t=confirm("Click OK to backup your current saved options.");if(t){var n=e(this);var r=e(this).attr("id");var i=e("#security").val();var s={action:"of_ajax_post_action",type:"backup_options",security:i};e.post(ajaxurl,s,function(t){if(t==-1){var n=e("#of-popup-fail");n.fadeIn();window.setTimeout(function(){n.fadeOut()},2e3)}else{var r=e("#of-popup-save");r.fadeIn();window.setTimeout(function(){location.reload()},1e3)}})}return false});e("#of_restore_button").live("click",function(){var t=confirm("'Warning: All of your current options will be replaced with the data from your last backup! Proceed?");if(t){var n=e(this);var r=e(this).attr("id");var i=e("#security").val();var s={action:"of_ajax_post_action",type:"restore_options",security:i};e.post(ajaxurl,s,function(t){if(t==-1){var n=e("#of-popup-fail");n.fadeIn();window.setTimeout(function(){n.fadeOut()},2e3)}else{var r=e("#of-popup-save");r.fadeIn();window.setTimeout(function(){location.reload()},1e3)}})}return false});e("#of_import_button").live("click",function(){var t=confirm("Click OK to import options.");if(t){var n=e(this);var r=e(this).attr("id");var i=e("#security").val();var s=e("#export_data").val();var o={action:"of_ajax_post_action",type:"import_options",security:i,data:s};e.post(ajaxurl,o,function(t){var n=e("#of-popup-fail");var r=e("#of-popup-save");if(t==-1){n.fadeIn();window.setTimeout(function(){n.fadeOut()},2e3)}else{r.fadeIn();window.setTimeout(function(){location.reload()},1e3)}})}return false});e("#of_save").live("click",function(){var t=e("#security").val();e(".ajax-loading-img").fadeIn();var n=e('#of_form :input[name][name!="security"][name!="of_reset"]').serialize();e("#of_form :input[type=checkbox]").each(function(){if(!this.checked){n+="&"+this.name+"=0"}});var r={type:"save",action:"of_ajax_post_action",security:t,data:n};e.post(ajaxurl,r,function(t){var n=e("#of-popup-save");var r=e("#of-popup-fail");var i=e(".ajax-loading-img");i.fadeOut();if(t==1){n.fadeIn()}else{r.fadeIn()}window.setTimeout(function(){n.fadeOut();r.fadeOut()},2e3)});return false});e("#of_reset").click(function(){var t=confirm("Click OK to reset. All settings will be lost and replaced with default settings!");if(t){var n=e("#security").val();e(".ajax-reset-loading-img").fadeIn();var r={type:"reset",action:"of_ajax_post_action",security:n};e.post(ajaxurl,r,function(t){var n=e("#of-popup-reset");var r=e("#of-popup-fail");var i=e(".ajax-reset-loading-img");i.fadeOut();if(t==1){n.fadeIn();window.setTimeout(function(){location.reload()},1e3)}else{r.fadeIn();window.setTimeout(function(){r.fadeOut()},2e3)}})}return false});if(jQuery().tipsy){e(".tooltip, .typography-size, .typography-height, .typography-face, .typography-style, .of-typography-color").tipsy({fade:true,gravity:"s",opacity:.7})}jQuery(".apl_sliderui").each(function(){var e=jQuery(this);var t="#"+e.data("id");var n=parseInt(e.data("val"));var r=parseInt(e.data("min"));var i=parseInt(e.data("max"));var s=parseInt(e.data("step"));e.slider({value:n,min:r,max:i,step:s,range:"min",slide:function(e,n){jQuery(t).val(n.value)}})});jQuery(".cb-enable").click(function(){var t=e(this).parents(".switch-options");jQuery(".cb-disable",t).removeClass("selected");jQuery(this).addClass("selected");jQuery(".main_checkbox",t).attr("checked",true);var n=jQuery(this);var r=".f_"+n.data("id");jQuery(r).slideDown("normal","swing")});jQuery(".cb-disable").click(function(){var t=e(this).parents(".switch-options");jQuery(".cb-enable",t).removeClass("selected");jQuery(this).addClass("selected");jQuery(".main_checkbox",t).attr("checked",false);var n=jQuery(this);var r=".f_"+n.data("id");jQuery(r).slideUp("normal","swing")});if(e.browser.msie&&e.browser.version<10||e.browser.opera){e(".cb-enable span, .cb-disable span").find().attr("unselectable","on")}jQuery(".google_font_select").each(function(){var e=jQuery(this).attr("id");s(this,e)});jQuery(".google_font_select").change(function(){var e=jQuery(this).attr("id");s(this,e)});a()})