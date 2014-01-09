/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */

var isBootstrapEvent = false;
if (window.jQuery) {
    jQuery.noConflict();
    jQuery('*').on('hide.bs.popover', function(){ isBootstrapEvent = true; })
    jQuery('*').on('hide.bs.dropdown', function(){ isBootstrapEvent = true; });
    jQuery('*').on('hide.bs.collapse', function(){ isBootstrapEvent = true; });
    jQuery('*').on('hide.bs.modal', function(){ isBootstrapEvent = true; });
}
