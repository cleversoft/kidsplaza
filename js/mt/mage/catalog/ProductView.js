/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

jQuery('.product-view').each(function(i, product){
    var id = jQuery(product).data('product');
    if (!id) return;

    window.optionsPrice = new Product.OptionsPrice(window['price' + id]);
    window.relatedProductsCheckFlag = false;
    window.productAddToCartForm = new VarienForm('product_addtocart_form');

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
});

function selectAllRelated(txt){
    if (relatedProductsCheckFlag == false) {
        $$('.related-checkbox').each(function(elem){
            elem.checked = true;
        });
        window.relatedProductsCheckFlag = true;
        txt.innerHTML="unselect all";
    } else {
        $$('.related-checkbox').each(function(elem){
            elem.checked = false;
        });
        window.relatedProductsCheckFlag = false;
        txt.innerHTML="select all";
    }
    addRelatedToProduct();
}

function addRelatedToProduct(){
    var checkboxes = $$('.related-checkbox'),
        values = [], prices = [];

    for(var i=0; i<checkboxes.length; i++){
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
    for(var i=0; i<prices.length; i++){
        optionsPrice.addCustomPrices('related-' + prices[i].product, prices[i]);
    }
    optionsPrice.reload();
}

jQuery(document).ready(function(){
    var count = moreViewOptions.count;
    jQuery('#moreViews').flexslider({
        namespace: 'more-views-',
        slideshow: false,
        animation: "slide",
        itemWidth: getMoreViewsItemWidth('moreViews', count, moreViewOptions.itemMargin),
        itemMargin: moreViewOptions.itemMargin,
        minItems: 1,
        maxItems: count,
        selector: ".slides > li",
        controlNav: false
    });
    /******* Product Tabs*/
    jQuery('#mt_product_tabs a').click(function (e) {
        e.preventDefault();
        if(jQuery(this).attr('href')=='#review-form'){
            jQuery('.mt-review-main').scrollToMe();
        }else{
            jQuery(this).tab('show');
        }
    })
});
function getMoreViewsItemWidth(id, column, margin){
    var width = jQuery('#'+id).width();
    return (width/column).toFixed(2) - margin*2;
}
