/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_Themes
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
(function($){
    $.fn.extend({
        mtAccordionMenu: function(options) {
            var defaults = {
                accordion: 'true',
                speed: 300,
                closedSign: 'collapse',
                openedSign: 'expand'
            };
            var opts = $.extend(defaults, options);
            var $this = $(this);
            $this.find("li").each(function() {
                if($(this).find("ul").size() != 0){
                    $(this).find("a:first").after("<span class='"+ opts.closedSign +"'>"+ opts.closedSign +"</span>");
                    if($(this).find("a:first").attr('href') == "#"){
                        $(this).find("a:first").click(function(){return false;});
                    }
                }
            });
            $this.find("li.active").each(function() {
                $(this).parents(".childcontent").slideDown(opts.speed, opts.easing);
                $(this).parents(".childcontent").parent("li").find("span:first").html(opts.openedSign).removeClass(opts.closedSign).addClass(opts.openedSign);
                $(this).find("ul:first").slideDown(opts.speed, opts.easing);
                $(this).find("a:first").next().html(opts.openedSign).removeClass(opts.closedSign).addClass(opts.openedSign);
            });
            if(opts.mouseType==0){
                $this.find("li span").click(function() {
                    if($(this).parent().find(".childcontent").size() != 0){
                        if(opts.accordion){
                            //Do nothing when the list is open
                            if(!$(this).parent().find(".childcontent").is(':visible')){
                                parents = $(this).parent().parents(".childcontent");
                                visible = $this.find(".childcontent:visible");
                                visible.each(function(visibleIndex){
                                    var close = true;
                                    parents.each(function(parentIndex){
                                        if(parents[parentIndex] == visible[visibleIndex]){
                                            close = false;
                                            return false;
                                        }
                                    });
                                    if(close){
                                        if($(this).parent().find(".childcontent") != visible[visibleIndex]){
                                            $(visible[visibleIndex]).slideUp(opts.speed, function(){
                                                $(this).parent("li").find("span:first").html(opts.closedSign).addClass(opts.closedSign);
                                            });
                                        }
                                    }
                                });
                            }
                        }

                        if($(this).parent().find(".childcontent:first").is(":visible")){
                            $(this).parent().find(".childcontent:first").slideUp(opts.speed, opts.easing, function(){
                                $(this).parent("li").find("span:first").delay(opts.speed+1000).html(opts.closedSign).removeClass(opts.openedSign).addClass(opts.closedSign);
                            });
                        }else{
                            $(this).parent().find(".childcontent:first").slideDown(opts.speed, opts.easing, function(){
                                $(this).parent("li").find("span:first").delay(opts.speed+1000).html(opts.openedSign).removeClass(opts.closedSign).addClass(opts.openedSign);
                            });
                        }
                    }
                });
            }
            if(opts.mouseType>0){
                $this.find("li a").mouseenter(function() {
                    if($(this).parent().find("ul").size() != 0){
                        if(opts.accordion){
                            if(!$(this).parent().find(".childcontent").is(':visible')){
                                parents = $(this).parent().parents(".childcontent");
                                visible = $this.find(".childcontent:visible");
                                visible.each(function(visibleIndex){
                                    var close = true;
                                    parents.each(function(parentIndex){
                                        if(parents[parentIndex] == visible[visibleIndex]){
                                            close = false;
                                            return false;
                                        }
                                    });
                                    if(close){
                                        if($(this).parent().find(".childcontent") != visible[visibleIndex]){
                                            $(visible[visibleIndex]).slideUp(opts.speed, function(){
                                                $(this).parent("li").find("span:first").html(opts.closedSign).addClass(opts.closedSign);
                                            });
                                        }
                                    }
                                });
                            }
                        }
                        if($(this).parent().find(".childcontent:first").is(":visible")){
                            $(this).parent().find(".childcontent:first").slideUp(opts.speed, function(){
                                $(this).parent("li").find("span:first").delay(opts.speed+1000).html(opts.closedSign).addClass(opts.closedSign);
                            });
                        }else{
                            $(this).parent().find(".childcontent:first").slideDown(opts.speed, function(){
                                $(this).parent("li").find("span:first").delay(opts.speed+1000).html(opts.openedSign).removeClass(opts.closedSign);
                            });
                        }
                    }
                });
            }
        }
    });
})(jQuery);
jQuery(document).ready(function() {
    jQuery("#mt_accordionmenu").mtAccordionMenu({
        accordion:true,
        speed: 400,
        closedSign: 'collapse',
        openedSign: 'expand',
        mouseType: 0
        //easing: 'easeInOutQuad'
    });
});