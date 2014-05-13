/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

var VarienFormRegister = Class.create(VarienForm, {
    bindElements: function(){
        var elements = Form.getElements(this.form);
        for (var row in elements){
            if (elements[row].id){
                Event.observe(elements[row], 'focus', this.elementFocus);
                Event.observe(elements[row], 'blur', this.elementBlur);
                if (elements[row].id == 'phone_number'){
                    new KidsPlazaPhone('phone_number', {
                        fn: 'firstname',
                        ln: 'lastname',
                        email: 'email_address',
                        gender: 'gender'
                    });
                }
            }
        }
    }
});

var VarienFormLogin = Class.create(VarienForm, {
    bindElements: function(){
        var elements = Form.getElements(this.form);
        for (var row in elements){
            if (elements[row].id){
                Event.observe(elements[row], 'focus', this.elementFocus);
                Event.observe(elements[row], 'blur', this.elementBlur);
                if (elements[row].id == 'email'){
                    new KidsPlazaPhone('email', {}, function(elm, response){
                        if (response){
                            response.mobile = elm.value;
                            jQuery('.form_register').show();
                            var fields = {
                                fn: 'firstname',
                                ln: 'lastname',
                                email: 'email_address',
                                gender: 'gender',
                                mobile: 'phone_number'
                            };
                            for (var i in fields){
                                var $field = $(fields[i]);
                                if ($field){
                                    $field.value = response[i];
                                    if ($field.up().hasClassName('selector')) {
                                        jQuery.uniform.update();
                                    }
                                    var $mes = $field.up().down('.validation-success');
                                    $mes && $mes.show();
                                }
                            }
                        }
                    });
                }
            }
        }
    }
});

new VarienForm('form-validate');
new VarienFormRegister('form-register');
new VarienFormLogin('login-form');

jQuery(document).ready(function(){
    jQuery('.btn-regisform').click(function(){
        jQuery('.form_register').toggle();
        jQuery.uniform && jQuery.uniform.update();
    });

    //jQuery.fn.equalHeights && jQuery.fn.equalHeights('.col-left .nav', '.col-main');

    if (window.location.href.indexOf('is_register') > 0){
        jQuery('.form_register').show();
        jQuery.uniform && jQuery.uniform.update();
    }
});

if (typeof isSetPasswordForm != 'undefined'){
    setPasswordForm(true);
}

if (typeof regionData != 'undefined' && regionData.length == 7){
    if ($('region_id')){
        $('region_id').setAttribute('defaultValue', regionData[0]);
        new RegionUpdater(regionData[1], regionData[2], regionData[3], regionData[4], regionData[5], regionData[6]);
    }
}