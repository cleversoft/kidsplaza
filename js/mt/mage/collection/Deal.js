/**
 * @category    MT
 * @package     MT_Collection
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

jQuery(function(){
    //init carousel
    jQuery('.owl-carousel').each(function(i, slider){
        var id = jQuery(slider).attr('id');
        if (!id || !window[id]) return;
        jQuery(slider).owlCarousel(window[id]);
    });
    //init banner
    jQuery('.rev_slider').each(function(){
        var id = jQuery(this).attr('id');
        if (!window[id]) return;
        jQuery('#'+id).show().revolution(window[id]);
    });
});