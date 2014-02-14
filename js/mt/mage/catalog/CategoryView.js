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
    //init filter quick search
    jQuery('.list-js').each(function(i,list){new List(list,{valueNames:['filter-item-name','filter-item-name-normalize']});});
    //init image lazy load
    jQuery('img.lazy').lazyload({event:'scroll|widgetnav',failure_limit:10});
    //init category banner
    jQuery('.owl-carousel').owlCarousel({items:1,autoPlay:true,navigation:false,pagination:false,transitionStyle:'fadeUp'});
});