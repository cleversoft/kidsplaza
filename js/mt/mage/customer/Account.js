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
