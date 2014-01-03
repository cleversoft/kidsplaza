/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

var id = jQuery('.product-view').data('product');
if (id){
    var optionsPrice = new Product.OptionsPrice(window['price' + id]);
    var relatedProductsCheckFlag = false;
    var productAddToCartForm = new VarienForm('product_addtocart_form');

    productAddToCartForm.submit = function(button, url) {
        if (this.validator.validate()) {
            var form = this.form;
            var oldUrl = form.action;

            if (url) {
                form.action = url;
            }
            var e = null;
            try {
                this.form.submit();
            } catch (e) {
            }
            this.form.action = oldUrl;
            if (e) {
                throw e;
            }

            if (button && button != 'undefined') {
                button.disabled = true;
            }
        }
    }.bind(productAddToCartForm);

    productAddToCartForm.submitLight = function(button, url){
        if(this.validator) {
            var nv = Validation.methods;
            delete Validation.methods['required-entry'];
            delete Validation.methods['validate-one-required'];
            delete Validation.methods['validate-one-required-by-name'];
            // Remove custom datetime validators
            for (var methodName in Validation.methods) {
                if (methodName.match(/^validate-datetime-.*/i)) {
                    delete Validation.methods[methodName];
                }
            }

            if (this.validator.validate()) {
                if (url) {
                    this.form.action = url;
                }
                this.form.submit();
            }
            Object.extend(Validation.methods, nv);
        }
    }.bind(productAddToCartForm);

    $$('.related-checkbox').each(function(elem){
        Event.observe(elem, 'click', addRelatedToProduct)
    });
}

function selectAllRelated(txt){
    if (relatedProductsCheckFlag == false) {
        $$('.related-checkbox').each(function(elem){
            elem.checked = true;
        });
        relatedProductsCheckFlag = true;
        txt.innerHTML="unselect all";
    } else {
        $$('.related-checkbox').each(function(elem){
            elem.checked = false;
        });
        relatedProductsCheckFlag = false;
        txt.innerHTML="select all";
    }
    addRelatedToProduct();
}

function addRelatedToProduct(){
    var checkboxes = $$('.related-checkbox');
    var values = [], prices = [];
    for(var i=0;i<checkboxes.length;i++){
        var product = checkboxes[i].value;

        if(checkboxes[i].checked){
            var price = checkboxes[i].readAttribute('price'),
                includeTax = checkboxes[i].readAttribute('price1'),
                excludeTax = checkboxes[i].readAttribute('price2');

            values.push(product);
            prices.push({product: product, price: price, excludeTax: excludeTax, includeTax: includeTax});
        }else{
            prices.push({product: product, price: 0, excludeTax: 0, includeTax: 0});
        }
    }
    if($('related-products-field')){
        $('related-products-field').value = values.join(',');
    }
    for(var i=0;i<prices.length;i++){
        optionsPrice.addCustomPrices('related-' + prices[i].product, prices[i]);
    }
    optionsPrice.reload();
}