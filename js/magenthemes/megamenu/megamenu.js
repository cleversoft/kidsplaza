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
            mm_timeout: 0
        }, options);
        $(this).children('li.parent').bind('mouseenter', function(e){
            $(this).prev().addClass('bgnone');
            var mm_item_obj = $(this).children('div');
            mm_item_obj.show();
            $(this).addClass('over');

        });
        $(this).children('li.parent').bind('mouseleave', function(e){
            $(this).prev().removeClass('bgnone');
            var mm_item_obj = $(this).children('div');
            mm_item_obj.hide();
            $(this).removeClass('over');
        });
    };
    $(function(){
        $("#mt_megamenu").megamenu();
    });
})(jQuery);