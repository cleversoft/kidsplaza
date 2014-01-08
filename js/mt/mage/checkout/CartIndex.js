/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

var discountForm = new VarienForm('discount-coupon-form');
discountForm.submit = function (isRemove) {
    if (isRemove) {
        $('coupon_code').removeClassName('required-entry');
        $('remove-coupone').value = "1";
    } else {
        $('coupon_code').addClassName('required-entry');
        $('remove-coupone').value = "0";
    }
    return VarienForm.prototype.submit.bind(discountForm)();
}
