/******************************************************
 * @package Megamenu module for Magento 1.4.x.x and Magento 1.5.x.x
 * @version 1.5.0.4
 * @author http://www.9magentothemes.com
 * @copyright (C) 2011- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
(function($){
    $.fn.megamenu = function(options) {
        options = $.extend({
            animation: "show",
            mm_timeout: 300
        }, options);
        var $timer = 0;
        $(this).children('li.parent').bind('mouseenter', function(e){
            var mm_item_obj = $(this).children('div');
            $(this).addClass('over');
            clearTimeout($timer);
            $timer = setTimeout(function(){
                switch(options.animation) {
                    case "show":
                        mm_item_obj.show().addClass("shown-sub");
                        break;
                    case "slide":
                        mm_item_obj.height("auto");
                        mm_item_obj.delay(200).slideDown('fast', function(){
                            mm_item_obj.css("overflow","inherit");
                        }).addClass("shown-sub");
                        break;
                    case "fade":
                        mm_item_obj.delay(200).fadeTo('fast', 1).addClass("shown-sub");
                        break;
                }
            }, options.mm_timeout);
        });
        $(this).children('li.parent').bind('mouseleave', function(e){
            clearTimeout($timer);
            var mm_item_obj = $(this).children('div');
            $(this).removeClass('over');
            switch(options.animation) {
                case "show":
                    mm_item_obj.hide();
                    break;
                case "slide":
                    mm_item_obj.delay(100).slideUp( 'fast',  function() {});
                    break;
                case "fade":
                    mm_item_obj.delay(100).fadeOut( 'fast', function() {});
                    break;
            }
        });
        this.show();
    };
})(jQuery);
jQuery('#mt_megamenu li.root.parent').hoverIntent(
    function(){
        jQuery(this).prev().addClass('bgnone');
        jQuery(this).addClass('over');
    },
    function(){
        jQuery(this).prev().removeClass('bgnone');
        jQuery(this).removeClass('over');
    }
);