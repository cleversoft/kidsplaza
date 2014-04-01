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

var ProductOptionPrice = Class.create(Product.OptionsPrice, {
    initPrices: function() {
        this.containers[0] = 'product-price-' + this.productId;
        this.containers[1] = 'bundle-price-' + this.productId;
        this.containers[2] = 'price-including-tax-' + this.productId;
        this.containers[3] = 'price-excluding-tax-' + this.productId;
        this.containers[4] = 'old-price-' + this.productId;
        Event.observe('qty', 'change', function(event){
            var elm = event.findElement('input'),
                qty = elm.value;
            qty = qty <= 0 ? 1 : (qty > 99 ? 99 : qty);
            elm.value = qty;
            this.reload();
        }.bind(this));
    },

    reload: function() {
        var price;
        var formattedPrice;
        var optionPrices = this.getOptionPrices();
        var nonTaxable = optionPrices[1];
        var optionOldPrice = optionPrices[2];
        var priceInclTax = optionPrices[3];
        optionPrices = optionPrices[0];

        $H(this.containers).each(function(pair) {
            var _productPrice;
            var _plusDisposition;
            var _minusDisposition;
            var _priceInclTax;
            if ($(pair.value)) {
                if (pair.value == 'old-price-'+this.productId && this.productOldPrice != this.productPrice) {
                    _productPrice = this.productOldPrice;
                    _plusDisposition = this.oldPlusDisposition;
                    _minusDisposition = this.oldMinusDisposition;
                } else {
                    _productPrice = this.productPrice;
                    _plusDisposition = this.plusDisposition;
                    _minusDisposition = this.minusDisposition;
                }
                _priceInclTax = priceInclTax;

                if (pair.value == 'old-price-'+this.productId && optionOldPrice !== undefined) {
                    price = optionOldPrice+parseFloat(_productPrice);
                } else if (this.specialTaxPrice == 'true' && this.priceInclTax !== undefined && this.priceExclTax !== undefined) {
                    price = optionPrices+parseFloat(this.priceExclTax);
                    _priceInclTax += this.priceInclTax;
                } else {
                    price = optionPrices+parseFloat(_productPrice);
                    _priceInclTax += parseFloat(_productPrice) * (100 + this.currentTax) / 100;
                }

                if (this.specialTaxPrice == 'true') {
                    var excl = price;
                    var incl = _priceInclTax;
                } else if (this.includeTax == 'true') {
                    // tax = tax included into product price by admin
                    var tax = price / (100 + this.defaultTax) * this.defaultTax;
                    var excl = price - tax;
                    var incl = excl*(1+(this.currentTax/100));
                } else {
                    var tax = price * (this.currentTax / 100);
                    var excl = price;
                    var incl = excl + tax;
                }

                var subPrice = 0;
                var subPriceincludeTax = 0;
                Object.values(this.customPrices).each(function(el){
                    if (el.excludeTax && el.includeTax) {
                        subPrice += parseFloat(el.excludeTax);
                        subPriceincludeTax += parseFloat(el.includeTax);
                    } else {
                        subPrice += parseFloat(el.price);
                        subPriceincludeTax += parseFloat(el.price);
                    }
                });
                excl += subPrice;
                incl += subPriceincludeTax;

                if (typeof this.exclDisposition == 'undefined') {
                    excl += parseFloat(_plusDisposition);
                }

                incl += parseFloat(_plusDisposition) + parseFloat(this.plusDispositionTax);
                excl -= parseFloat(_minusDisposition);
                incl -= parseFloat(_minusDisposition);

                //adding nontaxlable part of options
                excl += parseFloat(nonTaxable);
                incl += parseFloat(nonTaxable);

                if (pair.value == 'price-including-tax-'+this.productId) {
                    price = incl;
                } else if (pair.value == 'price-excluding-tax-'+this.productId) {
                    price = excl;
                } else if (pair.value == 'old-price-'+this.productId) {
                    if (this.showIncludeTax || this.showBothPrices) {
                        price = incl;
                    } else {
                        price = excl;
                    }
                } else {
                    if (this.showIncludeTax) {
                        price = incl;
                    } else {
                        price = excl;
                    }
                }

                if (price < 0) price = 0;

                if (price > 0 || this.displayZeroPrice) {
                    formattedPrice = this.formatPrice(price);
                } else {
                    formattedPrice = '';
                }

                if ($(pair.value).select('.price')[0]) {
                    $(pair.value).select('.price')[0].innerHTML = formattedPrice;
                    if ($(pair.value+this.duplicateIdSuffix) && $(pair.value+this.duplicateIdSuffix).select('.price')[0]) {
                        $(pair.value+this.duplicateIdSuffix).select('.price')[0].innerHTML = formattedPrice;
                    }
                } else {
                    $(pair.value).innerHTML = formattedPrice;
                    if ($(pair.value+this.duplicateIdSuffix)) {
                        $(pair.value+this.duplicateIdSuffix).innerHTML = formattedPrice;
                    }
                }
            };
        }.bind(this));

        for (var i = 0; i < this.tierPrices.length; i++) {
            $$('.price.tier-' + i).each(function (el) {
                var price = this.tierPrices[i] + parseFloat(optionPrices);
                el.innerHTML = this.formatPrice(price);
            }, this);
            $$('.price.tier-' + i + '-incl-tax').each(function (el) {
                var price = this.tierPricesInclTax[i] + parseFloat(optionPrices);
                el.innerHTML = this.formatPrice(price);
            }, this);
            $$('.benefit').each(function (el) {
                var parsePrice = function (html) {
                    return parseFloat(/\d+\.?\d*/.exec(html));
                };
                var container = $(this.containers[3]) ? this.containers[3] : this.containers[0];
                var price = parsePrice($(container).innerHTML);
                var tierPrice = $$('.tier-price.tier-' + i+' .price');
                tierPrice = tierPrice.length ? parsePrice(tierPrice[0].innerHTML, 10) : 0;
                var $percent = Selector.findChildElements(el, ['.percent.tier-' + i]);
                $percent.each(function (el) {
                    el.innerHTML = Math.ceil(100 - ((100 / price) * tierPrice));
                });
            }, this);
        }

        if ($('product-price-total')){
            var container = $('product-price-total'),
                qty = $('qty').value;
            if (isNaN(qty)) qty = 1;
            var total = price * qty;
            if (container.select('.price')[0]){
                container.select('.price')[0].innerHTML = this.formatPrice(total);
            }else{
                container.innerHTML = this.formatPrice(total);
            }
        }
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

    if (window['price' + id]) window.optionsPrice = new ProductOptionPrice(window['price' + id]);
    window.optionsPrice.reload();
    if (window['related' + id]){
        window['related' + id].productPrice = 0;
        window['related' + id].priceExclTax = 0;
        window['related' + id].priceInclTax = 0;
        window.relatedPrice = new RelatedPrice(window['related' + id]);
        addRelatedToProduct();
        window.relatedProductsCheckFlag = false;
    }

    window.productAddToCartForm = new VarienForm('product_addtocart_form');

    productAddToCartForm.submit = function(button, url){
        if (this.validator.validate()){
            var form = this.form;
            var oldUrl = form.action;

            var relatedField = $('related-field');
            if (relatedField){
                var relatedValues = [];
                relatedField.value.split(',').each(function(rid){
                    if (id != rid) relatedValues.push(rid);
                });
                relatedField.value = relatedValues.join(',');
            }

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
            var relatedValues = [];
            $$('.related-checkbox').each(function(cb){
                if (cb.checked){
                    relatedValues.push(cb.value);
                }
            });
            if (!relatedValues.length) alert(Translator.translate('You must select a product!'));
            else{
                var first = relatedValues.splice(0,1);
                $('product-field').value = first[0];
                $('related-field').value = relatedValues.join(',');
                if (url) this.form.action = url;
                this.form.submit();
                button.disabled = true;
                button.addClassName('disabled');
            }
        }
    }.bind(productAddToCartForm);

    productAddToCartForm.submitAllRelated = function(button, url){
        if (this.validator.validate()){
            var relatedValues = [];
            $$('.related-checkbox').each(function(cb){
                //if (cb.checked){
                    relatedValues.push(cb.value);
                //}
            });
            if (!relatedValues.length) alert(Translator.translate('You must select a product!'));
            else{
                var first = relatedValues.splice(0, 1);
                $('product-field').value = first[0];
                $('related-field').value = relatedValues.join(',');
                if (url) this.form.action = url;
                this.form.submit();
                button.disabled = true;
                button.addClassName('disabled');
            }
        }
    }.bind(productAddToCartForm);

    productAddToCartForm.submitAllLight = function(button, url){
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
                var wishlistValues = [];
                $$('.related-checkbox').each(function(cb){
                    wishlistValues.push(cb.value);
                });
                $('wistlist-field').value = wishlistValues.join(',');
                this.form.submit();
            }
            Object.extend(Validation.methods, nv);
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
        var product = checkboxes[i].value,
            imgElm = $$('.related-' + product)[0];

        if (checkboxes[i].checked){
            imgElm && imgElm.removeClassName('hide');
            var price = checkboxes[i].readAttribute('price'),
                includeTax = checkboxes[i].readAttribute('price1'),
                excludeTax = checkboxes[i].readAttribute('price2');

            values.push(product);
            prices.push({product: product, price: price, excludeTax: excludeTax, includeTax: includeTax});
        }else{
            imgElm && imgElm.addClassName('hide');
            prices.push({product: product, price: 0, excludeTax: 0, includeTax: 0});
        }
    }
    if ($('related-field')){
        $('related-field').value = values.join(',');
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
    function initImageZoomSl(){
        var parent = jQuery('.product-view');
        if (!parent.length) return;
        var image = parent.find('img.img-main');
        if (!image.length) return;

        if (image.hasClass('img-zoom')){
            if (jQuery(window).width() < 1000){
                image.unbind();
            }else{
                var w = parent.find('.product-shop').width(),
                    h = image.height();

                image.unbind();
                image.imagezoomsl({
                    zoomrange: [3, 3],
                    magnifiersize: [w, h],
                    magnifierborder: '1px solid #ddd',
                    magnifiereffectanimate: "fadeIn",
                    scrollspeedanimate: 2
                });
            }
        }

        if (image.hasClass('img-popup')){
            image.click(function(){
                var images = [];
                jQuery('#thumbs img').each(function(i, thumb){
                    var $thumb = jQuery(thumb);
                    if ($thumb.hasClass('active')){
                        images.unshift({
                            href: $thumb.data('large'),
                            thumb: $thumb.attr('src')
                        });
                    }else{
                        images.push({
                            href: $thumb.data('large'),
                            thumb: $thumb.attr('src')
                        });
                    }
                });
                jQuery.fancybox.open(images, {
                    closeBtn: false,
                    padding: 0,
                    helpers: {
                        thumbs: {
                            width: 80,
                            height: 80,
                            source: function(item){
                                return item.thumb;
                            }
                        }
                    }
                });
            });
        }
    }
    var zoomTimer;
    jQuery(window).resize(function(){
        if (zoomTimer) clearTimeout(zoomTimer);
        zoomTimer = setTimeout(function(){
            initImageZoomSl();
        }, 300);
    });
    initImageZoomSl();

    //init more views
    jQuery('.product-view .more-views img').on('click',function(){
        jQuery('.product-view .more-views img').removeClass('active');
        var item = jQuery(this).addClass('active');
        jQuery('.product-view img.img-main').fadeOut(100, function(){
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

function stockNotifySubmit(btn, url){
    var field = jQuery('#stock-notify-email');
    if (!field.length || !url) return;
    var value = field.val();
    if (!value) alert(Translator.translate('You must enter information'));
    else{
        jQuery.ajax({
            url: url,
            method: 'post',
            dataType: 'json',
            data: {value:value},
            beforeSend: function(){
                jQuery(btn).addClass('disabled');
                jQuery(btn).attr('disabled', true);
            },
            success: function(data){
                jQuery(btn).removeClass('disabled');
                jQuery(btn).removeAttr('disabled');
                if (data.error == 1){
                    alert(data.message);
                }else{
                    window.location.reload();
                }
            }
        });
    }
}