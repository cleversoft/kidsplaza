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
    jQuery('img.lazy').lazyload({event:'scroll|widgetnav',failure_limit:10});
});
