/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

jQuery(function(){
    //init image lazy load
    if (jQuery.fn.lazyload){
        jQuery('img.lazy').lazyload({
            event: 'scroll|widgetnav',
            failure_limit: 10
        });
    }
    //init category banner
    if (jQuery.fn.owlCarousel){
        jQuery('.owl-carousel').owlCarousel({
            items: 1,
            autoPlay: true,
            navigation: false,
            pagination: false,
            transitionStyle: 'fadeUp'
        });
    }
    //init filter scroller && list
    jQuery('.block-layered-nav .panel-body').each(function(i, container){
        initLayerFilterWithScrollAndList(container);
    });
});

function initLayerFilterWithScrollAndList(container){
    if (jQuery(container).find('.filter-item').length > 15){
        jQuery(container).addClass('has-scroll').mCustomScrollbar({
            theme: 'dark-thin',
            set_height: 388,
            mouseWheel: true,
            callbacks: {
                onScrollReady: function(elm){
                    //scroll to selected element
                    elm.mCustomScrollbar('scrollTo', 'li.selected');
                    //init List
                    var container = elm.parent();
                    if (List && container.length){
                        new List(container.get(0), {
                            valueNames: ['filter-item-name', 'filter-item-name-normalize']
                        })
                        .on('updated', function(ins){
                            var list = jQuery(ins.list).parents('.mCustomScrollbar');
                            list.mCustomScrollbar('update');
                            if(ins.visibleItems.length > 15){
                                list.addClass('has-scroll');
                            }else{
                                list.removeClass('has-scroll');
                            }
                        });
                    }
                }
            }
        });
    }
}