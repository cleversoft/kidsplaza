/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */

new VarienForm('login-form', true);
new VarienForm('form-validate', true);

jQuery(document).ready(function(){
    jQuery(".btn-regisform").click(function(){
        jQuery(".form_register").toggle();
    });
});

if (typeof isSetPasswordForm != 'undefined') setPasswordForm(true);
if (typeof regionData != 'undefined' && regionData.length == 7){
    $('region_id').setAttribute('defaultValue', regionData[0]);
    new RegionUpdater(regionData[1], regionData[2], regionData[3], regionData[4], regionData[5], regionData[6]);
}