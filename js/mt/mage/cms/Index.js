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
    jQuery('img.lazy').lazyload({event:'scroll|widget',failure_limit:10});
    //init tabbed widget
    jQuery('.category-products .nav-tabs li a').each(function(){
        var widget = jQuery(this).parents('.category-products');
        jQuery(this).on('shown.bs.tab', function(e){
            var tabId = jQuery(e.target).attr('href'),
                tabContent = jQuery(tabId, widget);

            jQuery('img.lazy', tabContent).trigger('widget');
        });
    });
    //init banner
    jQuery('.rev_slider').each(function(){
        var id = jQuery(this).attr('id');
        if (!window[id]) return;
        jQuery('#'+id).show().revolution(window[id]);
    });
});