/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

//init filter quick search
jQuery('.list-js').each(function(i,list){
    new List(list, {
        valueNames: ['filter-item-name','filter-item-name-normalize']
    });
});

//init image lazy load
jQuery('img.lazy').lazyload({
    event: 'scroll|widgetnav'
});