/**
 * @category    MT
 * @package     MT_OneStepCheckout
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
Validation.addAllThese([
    ['validate-phoneprefix', 'Please enter a valid phone number in this field.', function(value) {
        var prefix = jQuery("#" +'phone_prefix').val(),
            phonePrefix = prefix.split(','),
            phoneLen = jQuery("#" +'phone_len').val(),
            msg = '',
            i;
        if (!phoneLen || isNaN(phoneLen) || phoneLen <= 0) {
            msg = 'test1';
            return true;
        }
        for (i=0; i < phonePrefix.length; i++) {
            if (value.substring(0,phonePrefix[i].length) == phonePrefix[i] && value.length == (phonePrefix[i].length + parseInt(phoneLen))) {
                msg = 'test2';
                return true;
            }
        }
        return Validation.get('IsEmpty').test(value);
    }]
]);