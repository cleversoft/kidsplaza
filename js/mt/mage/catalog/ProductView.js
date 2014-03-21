/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

var RelatedPrice = Class.create(Product.OptionsPrice, {
    initPrices: function(){
        this.containers[0] = 'related-price';
    }
});

document.on('dom:loaded', function(){
    if (window.spConfigDataEnhanced){
        window.spConfig = new Product.EnhancedConfig(window.spConfigDataEnhanced);
    }else if (window.spConfigData){
        window.spConfig = new Product.Config(window.spConfigData);
    }
});

jQuery('.product-view').each(function(i, product){
    var id = jQuery(product).data('product');
    if (!id) return;

    if (window['price' + id]) window.optionsPrice = new Product.OptionsPrice(window['price' + id]);
    if (window['related' + id]){
        window.relatedPrice = new RelatedPrice(window['related' + id]);
        addRelatedToProduct();
        window.relatedProductsCheckFlag = false;
    }

    window.productAddToCartForm = new VarienForm('product_addtocart_form');

    productAddToCartForm.submit = function(button, url){
        if (this.validator.validate()){
            var form = this.form;
            var oldUrl = form.action;

            if (url){
                form.action = url;
            }
            var e = null;
            try {
                this.form.submit();
            } catch (e) {}
            this.form.action = oldUrl;
            if (e){
                throw e;
            }
            if (button && button != 'undefined'){
                button.disabled = true;
            }
        }
    }.bind(productAddToCartForm);

    productAddToCartForm.submitRelated = function(button, url){
        if (this.validator.validate()){
            var hasChecked = false;
            $$('.related-checkbox').each(function(cb){
                if (cb.checked) hasChecked = true;
            });
            if (!hasChecked) alert(Translator.translate('You must select a product!'));
            else{
                if (url) this.form.action = url;
                this.form.submit();
                button.disabled = true;
                button.addClassName('disabled');
            }
        }
    }.bind(productAddToCartForm);

    productAddToCartForm.submitLight = function(button, url){
        if (this.validator) {
            var nv = Validation.methods;
            delete Validation.methods['required-entry'];
            delete Validation.methods['validate-one-required'];
            delete Validation.methods['validate-one-required-by-name'];
            // Remove custom datetime validators
            for (var methodName in Validation.methods){
                if (methodName.match(/^validate-datetime-.*/i)){
                    delete Validation.methods[methodName];
                }
            }

            if (this.validator.validate()){
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

function addRelatedToProduct(){
    var checkboxes = $$('.related-checkbox'),
        values = [],
        prices = [];

    for (var i=0; i<checkboxes.length; i++){
        var product = checkboxes[i].value;

        if (checkboxes[i].checked){
            var price = checkboxes[i].readAttribute('price'),
                includeTax = checkboxes[i].readAttribute('price1'),
                excludeTax = checkboxes[i].readAttribute('price2');

            values.push(product);
            prices.push({product: product, price: price, excludeTax: excludeTax, includeTax: includeTax});
        }else{
            prices.push({product: product, price: 0, excludeTax: 0, includeTax: 0});
        }
    }
    if ($('related-products-field')){
        $('related-products-field').value = values.join(',');
    }
    for (var i=0; i<prices.length; i++){
        relatedPrice && relatedPrice.addCustomPrices('related-' + prices[i].product, prices[i]);
    }
    relatedPrice && relatedPrice.reload();
}

jQuery(function(){
    //init show more/less
    var content_height_limit = 500,
        mainPane = jQuery('#product-description'),
        height = mainPane.height(),
        showMore = jQuery('<div/>')
            .addClass('show-more')
            .css('display','none')
            .html('<a class="btn  btn-login">'+Translator.translate('Show More')+'</a>'),
        showLess = jQuery('<div/>')
            .addClass('show-less')
            .css('display','none')
            .html('<a class="btn  btn-login">'+Translator.translate('Show Less')+'</a>');

    if (height > content_height_limit){
        mainPane.after(showMore);
        mainPane.after(showLess);
        mainPane.css({'max-height': content_height_limit+'px','overflow':'hidden'});
        showMore.show();
        jQuery('a.btn', showMore).click(function(){
            mainPane.css('max-height', 'none');
            showMore.hide();
            showLess.show();
        });
        jQuery('a.btn', showLess).click(function(){
            mainPane.css('max-height', content_height_limit+'px');
            showLess.hide();
            showMore.show();
            jQuery('#product-description').scrollToMe();
        });
    }

    //init table description
    jQuery('table', '#product_tabs_description_contents').addClass('table');

    //init zoom
    jQuery('img.img-zoom').imagezoomsl({
        zoomrange: [1,3],
        magnifiersize: [570, 430],
        magnifierborder: '1px solid #ddd'
    });

    //init more views
    jQuery('.more-views img').on('click',function(){
        jQuery('.more-views img').removeClass('active');
        var item = jQuery(this).addClass('active');
        jQuery('img.img-zoom').fadeOut(100, function(){
            jQuery(this).attr('src', item.attr('data-small')).attr('data-large', item.attr('data-large')).fadeIn(100);
        });
    });

    //init carousel
    jQuery('.owl-carousel').each(function(i, slider){
        var id = jQuery(slider).attr('id');
        if (!id || !window[id]) return;
        jQuery(slider).owlCarousel(window[id]);
    });

    //init media verticle slider
    var carCount = jQuery('.product-img-box #thumbs').find('a').length;
    if (carCount <= 4){
        jQuery('.product-img-box .more-views-nav').hide();
    }
    jQuery(".product-img-box #carousel-up").on("click", function() {
        if (!jQuery(".product-img-box #thumbs").is(':animated')) {
            var bottom = jQuery(".product-img-box #thumbs > a:last-child");
            var clone = jQuery(".product-img-box #thumbs > a:last-child").clone();
            clone.prependTo(".product-img-box #thumbs");
            jQuery(".product-img-box #thumbs").animate({"top" : "-=85"}, 0).stop().animate({"top" : '+=85'}, 250, function() {
                bottom.remove();
            });
            jQuery('.product-img-box .more-views img').bind('click',function(){
                jQuery('.product-img-box .more-views img').removeClass('active');
                var item = jQuery(this).addClass('active');
                jQuery('.product-img-box img.img-zoom').fadeOut(100, function(){
                    jQuery(this).attr('src', item.attr('data-small')).attr('data-large', item.attr('data-large')).fadeIn(100);
                });
            });
        }
    });
    jQuery(".product-img-box #carousel-down").on("click", function() {
        if (!jQuery(".product-img-box #thumbs").is(':animated')) {
            var top = jQuery(".product-img-box #thumbs > a:first-child");
            var clone = jQuery(".product-img-box #thumbs > a:first-child").clone();
            clone.appendTo(".product-img-box #thumbs");
            jQuery(".product-img-box #thumbs").animate({"top" : '-=85'}, 250, function() {
                top.remove();
                jQuery(".product-img-box #thumbs").animate({"top" : "+=85"}, 0);
            });
            jQuery('.product-img-box .more-views img').bind('click',function(){
                jQuery('.product-img-box .more-views img').removeClass('active');
                var item = jQuery(this).addClass('active');
                jQuery('.product-img-box img.img-zoom').fadeOut(100, function(){
                    jQuery(this).attr('src', item.attr('data-small')).attr('data-large', item.attr('data-large')).fadeIn(100);
                });
            });
        }
    });
});