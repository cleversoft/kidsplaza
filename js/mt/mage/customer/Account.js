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
    bindElements: function (){
        var elements = Form.getElements(this.form);
        for (var row in elements){
            if (elements[row].id){
                Event.observe(elements[row], 'focus', this.elementFocus);
                Event.observe(elements[row], 'blur', this.elementBlur);
                if (elements[row].id == 'phone_number'){
                    Event.observe(elements[row], 'change', function(ev){
                        var input = Event.findElement(ev, 'input'),
                            spinner = input.up().down('.spinner'),
                            url = input.readAttribute('data-url'),
                            phoneValidator = Validation.get('validate-phoneprefix');

                        if (!url) return;

                        if (phoneValidator && phoneValidator.test(input.value, input)){
                            var advice = Validation.getAdvice('validate-phoneprefix', input);
                            if (advice) Validation.hideAdvice(input, advice);

                            var params = {value: input.value, form_key: Mage.FormKey},
                                elms = 'firstname|lastname|email|gender'.split('|');

                            spinner && spinner.show();
                            new Ajax.Request(url, {
                                parameters: params,
                                onSuccess: function(transport){
                                    spinner && spinner.hide();
                                    try{
                                        var response = transport.responseText.evalJSON();
                                        elms.each(function(elm){
                                            switch (elm){
                                                case 'gender':
                                                    var target = this.form.down('select[name="'+elm+'"]');
                                                    if (!target) return;
                                                    target.value = response[elm];
                                                    if (target.up().hasClassName('selector')){
                                                        jQuery.uniform.update();
                                                    }
                                                    break;
                                                default:
                                                    var target = this.form.down('input[name="'+elm+'"]');
                                                    if (target) target.value = response[elm];
                                                    break;
                                            }
                                        }.bind(this));
                                    }catch (e){}
                                }.bind(this)
                            });
                        }else{
                            var advice = Validation.getAdvice('validate-phoneprefix', input);
                            if (!advice){
                                advice = Validation.createAdvice('validate-phoneprefix', input);
                            }
                            Validation.showAdvice(input, advice, 'validate-phoneprefix');
                        }
                    }.bind(this));
                }
            }
        }
    }
});

new VarienForm('login-form', true);
new VarienForm('form-validate', true);
new VarienFormRegister('form-register');

jQuery(document).ready(function(){
    jQuery(".btn-regisform").click(function(){
        jQuery(".form_register").toggle();
    });

    jQuery.fn.equalHeights && jQuery.fn.equalHeights('.col-left .nav', '.col-main');

    if (window.location.href.indexOf('is_register') > 0){
        jQuery('.form_register').toggle();
    }
});

if (typeof isSetPasswordForm != 'undefined') setPasswordForm(true);
if (typeof regionData != 'undefined' && regionData.length == 7){
    if ($('region_id')){
        $('region_id').setAttribute('defaultValue', regionData[0]);
        new RegionUpdater(regionData[1], regionData[2], regionData[3], regionData[4], regionData[5], regionData[6]);
    }
}