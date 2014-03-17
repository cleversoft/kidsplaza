/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (typeof Product !== 'undefined'){
    Product.EnhancedConfig = Class.create(Product.Config, {
        initialize: function(config){
            //console.log(config);
            this.config         = config;
            this.taxConfig      = this.config.taxConfig;
            this.settings       = [];
            this.images         = $$('.attribute-option-image');
            this.originImage    = null;
            for (var i=0; i < this.images.length; i++){
                var attributeId = this.getAttributeId(this.images[i].id);
                if ($('attribute' + attributeId)){
                    this.settings.push($('attribute' + attributeId));
                }else{
                    this.settings.push(this.images[i]);
                }
            }
            this.state          = new Hash();
            this.priceTemplate  = new Template(this.config.template);
            this.prices         = config.prices;

            // Set default values from config
            if (config.defaultValues) {
                this.values = config.defaultValues;
            }

            // Overwrite defaults by inputs values if needed
            if (config.inputsInitialized) {
                this.values = {};
                this.settings.each(function(element) {
                    if (element.value) {
                        var attributeId = this.getAttributeId(element.id);
                        this.values[attributeId] = element.value;
                    }
                }.bind(this));
            }

            // Put events to check select reloads
            this.settings.each(function(element){
                if (element.options){
                    Event.observe(element, 'change', this.configure.bind(this))
                }
            }.bind(this));

            // fill state
            this.settings.each(function(element){
                var attributeId = this.getAttributeId(element.id);
                if(attributeId && this.config.attributes[attributeId]) {
                    element.config = this.config.attributes[attributeId];
                    element.attributeId = attributeId;
                    this.state.set(attributeId, false);
                }
            }.bind(this));

            // Init settings dropdown
            var childSettings = [];
            for(var i=this.settings.length-1; i>=0; i--){
                var prevSetting = this.settings[i-1] ? this.settings[i-1] : false;
                var nextSetting = this.settings[i+1] ? this.settings[i+1] : false;
                if (i == 0){
                    this.fillSelect(this.settings[i]);
                } else {
                    this.settings[i].disabled = true;
                }
                $(this.settings[i]).childSettings = childSettings.clone();
                $(this.settings[i]).prevSetting   = prevSetting;
                $(this.settings[i]).nextSetting   = nextSetting;
                if (!this.settings[i].options){
                    var imageSelect = $('attribute-image-' + this.getAttributeId(this.settings[i].id));
                    if (imageSelect){
                        imageSelect.childSettings   = childSettings.clone();
                        imageSelect.prevSetting     = prevSetting;
                        imageSelect.nextSetting     = nextSetting;
                    }
                }
                childSettings.push(this.settings[i]);
            }

            // Set values to inputs
            this.configureForValues();
        },

        configureForValues: function(){
            if (this.values) {
                this.settings.each(function(element){
                    var attributeId = element.attributeId;
                    if (element.options){
                        element.value = this.values[attributeId] ? this.values[attributeId] : '';
                        element.disabled = false;
                        this.configureElement(element);
                    }else{
                        element.select('a').each(function(a){
                            if (a.readAttribute('option') == this.values[attributeId]){
                                this.handleImageSelectionClick(a);
                            }
                        }.bind(this));
                    }
                }.bind(this));
            }else{
                var first = false;
                this.settings.each(function(element){
                    if (!first){
                        first = true;
                        if (element.options){
                            element.options[1].selected = true;
                            this.configureElement(element);
                        }else{
                            var a = element.down('a');
                            a.addClassName('active');
                            this.handleImageSelectionClick(a);
                        }
                    }
                }.bind(this));
            }
        },

        getAttributeId: function(id){
            if (isNaN(id)){
                id = id.replace(/[a-zA-Z-]*/, '');
            }
            return id;
        },

        clearImageSelection: function(id){
            id = this.getAttributeId(id);
            if ($('attribute-image-' + id)){
                $('attribute-image-' + id).update('');
            }
        },

        configureElement : function(element) {
            this.reloadOptionLabels(element);
            if(element.value){
                this.reloadMainPhoto(element.attributeId, element.value);
                this.state.set(element.config.id, element.value);
                if(element.nextSetting){
                    if (element.nextSetting.options) element.nextSetting.disabled = false;
                    this.fillSelect(element.nextSetting);
                    this.resetChildren(element.nextSetting);
                }
                var container = $('attribute-image-' + element.attributeId);
                if (container && container.innerHTML){
                    container.select('img').each(function(image){
                        if (image.readAttribute('option') == element.value){
                            this.handleImageSelectionClick(image);
                        }
                    }.bind(this));
                }
            } else {
                this.resetChildren(element);
                this.resetMainPhoto();
                this.resetImageSelection(element.attributeId);
            }
            this.reloadPrice();
        },

        resetImageSelection: function(id){
            var container = $('attribute-image-' + id);
            if (container && container.innerHTML){
                container.select('img').each(function(image){
                    image.removeClassName('active');
                });
            }
        },

        imageSelectionClick: function(event){
            event.stop();
            //find a tag, not img tag
            var element = Event.findElement(event, 'a');
            this.handleImageSelectionClick(element);
        },

        handleImageSelectionClick: function(element){
            var optionId = element.readAttribute('option');
            // if valid element
            if (optionId){
                var attributeId = element.readAttribute('attribute');
                // reset all selected
                $('attribute-image-' + attributeId).select('a').each(function(a){
                    a.removeClassName('active');
                });
                // mark it selected
                element.addClassName('active');
                // hander next selection
                var parent = element.up('div.attribute-option-image');
                if (parent && parent.nextSetting){
                    parent.nextSetting.disabled = false;
                    this.fillSelect(parent.nextSetting);
                    this.resetChildren(parent.nextSetting);
                }
                // check if SELECT element exist then update it
                var select = $('attribute' + attributeId);
                if (select){
                    select.value = optionId;
                    this.reloadOptionLabels(select);
                }
                // set value to global array
                this.state.set(attributeId, optionId);
                // add or update hidden input hold value for submit
                var hiddenInput = $('hidden' + attributeId);
                if (!hiddenInput){
                    hiddenInput = new Element('input', {
                        type: 'hidden',
                        name: 'super_attribute['+attributeId+']',
                        value: optionId,
                        id: 'hidden'+attributeId
                    });
                    element.insert({after: hiddenInput});
                }else{
                    hiddenInput.value = optionId;
                }
                // reload main image
                this.reloadMainPhoto(attributeId, optionId);
                // finally, re-calculate price
                this.reloadPrice();
            }
        },

        resetMainPhoto: function(){
            if (this.originImage){
                var image = $$('p.product-image img')[0];
                if (image){
                    image.src = this.originImage;
                }
            }
        },

        reloadMainPhoto: function(attributeId, optionId){
            if (attributeId && optionId){
                var options = this.getAttributeOptions(attributeId);
                if (!this.container){
                    this.container = $('product_addtocart_form').down('.product-img-box');
                }
                if (this.container && options){
                    var mainElm = this.container.down('.product-image img');
                    if (mainElm){
                        options.each(function(option){
                            if (option.id == optionId && option.products){
                                var target;

                                //find any product match option value
                                option.allowedProducts.each(function(product){
                                    if (!target && this.config.images[product]){
                                        target = product;
                                    }
                                }.bind(this));

                                if (!target) return;

                                var origins= $H(this.config.origins[target]),
                                    images = $H(this.config.images[target]),
                                    thumbs = $H(this.config.thumbs[target]),
                                    images_keys = images.keys(),
                                    thumbs_keys = thumbs.keys(),
                                    image_first = images_keys.first();

                                mainElm.src = images.get(image_first);
                                mainElm.writeAttribute('data-large', origins.get(image_first));

                                if (!thumbs_keys.length) return;

                                var moreElm = this.container.down('#thumbs');
                                if (!moreElm) return;
                                moreElm.innerHTML = '';

                                var defaultImg = image_first; //select default image
                                //generate more views
                                thumbs_keys.each(function(thumb){
                                    var aElm = new Element('a', {'class':'mv-thumb', href:'javascript:void(0)'}),
                                        imgElm = new Element('img');

                                    if (defaultImg == thumb) imgElm.addClassName('active');
                                    imgElm.writeAttribute('data-small', images.get(thumb));
                                    imgElm.writeAttribute('data-large', origins.get(thumb));
                                    imgElm.writeAttribute('width', 85);
                                    imgElm.writeAttribute('height', 85);
                                    imgElm.src = thumbs.get(thumb);

                                    aElm.insert(imgElm);
                                    moreElm.insert(aElm);

                                    //bind thumb click event
                                    Event.observe(imgElm, 'click', this.clickThumb.bind(this));
                                }.bind(this));
                            }
                        }.bind(this));
                    }
                }
            }
        },

        clickThumb: function(event){
            event.stop();
            var elm = Event.findElement(event, 'img');
            this.container.select('a.mv-thumb img').each(function(a){
                a.removeClassName('active');
            });
            elm.addClassName('active');
            var mainElm = this.container.down('.product-image img');
            if (!mainElm) return;
            mainElm.src = elm.readAttribute('data-small');
            mainElm.writeAttribute('data-large', elm.readAttribute('data-large'));
        },

        reloadOptionLabels: function(element){
            var selectedPrice;
            if (element.options){
                if(element.options[element.selectedIndex].config && !this.config.stablePrices){
                    selectedPrice = parseFloat(element.options[element.selectedIndex].config.price)
                }else{
                    selectedPrice = 0;
                }
                for(var i=0; i<element.options.length; i++){
                    if(element.options[i].config){
                        element.options[i].text = this.getOptionLabel(element.options[i].config, element.options[i].config.price-selectedPrice);
                    }
                }
            }
        },

        resetChildren : function(element){
            if(element.childSettings) {
                for(var i=0;i<element.childSettings.length;i++){
                    element.childSettings[i].selectedIndex = 0;
                    element.childSettings[i].disabled = true;
                    // reset image selection
                    this.clearImageSelection(element.childSettings[i].id);
                    if(element.config){
                        this.state.set(element.config.id, false);
                    }
                }
            }
        },

        fillSelect: function(element){
            var attributeId = this.getAttributeId(element.id);
            var options = this.getAttributeOptions(attributeId);
            this.clearSelect(element);
            if (element.options) element.options[0] = new Option(this.config.chooseText, '');

            var prevConfig = false;
            if (element.prevSetting){
                if (element.prevSetting.options){
                    prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
                }else{
                    prevConfig = element.prevSetting.down('a.active');
                }
            }
            if (options) {
                var index = 1;
                var imageSelection = $('attribute-image-' + attributeId);
                for (var i=0; i<options.length; i++){
                    var allowedProducts = [];
                    if (prevConfig) {
                        for (var j=0; j<options[i].products.length; j++){
                            if(prevConfig.config.allowedProducts && prevConfig.config.allowedProducts.indexOf(options[i].products[j]) > -1){
                                allowedProducts.push(options[i].products[j]);
                            }
                        }
                    } else {
                        allowedProducts = options[i].products.clone();
                    }
                    if (allowedProducts.size() > 0){
                        options[i].allowedProducts = allowedProducts;
                        if (element.options){
                            element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);
                            if (typeof options[i].price != 'undefined') {
                                element.options[index].setAttribute('price', options[i].price);
                            }
                            element.options[index].config = options[i];
                            index++;
                        }
                        if (this.config.show != 1){
                            //create new link
                            var link = new Element('a', {
                                href: '#',
                                option: options[i].id,
                                price: options[i].price,
                                attribute: attributeId,
                                title: options[i].label,
                                'class': 'attr-img attr-' + attributeId + '-' + options[i].id
                            });
                            if (options[i].image){ //if this option has image thumb, use it
                                var image = new Element('img', {
                                    src: options[i].image,
                                    width: this.config.imgWidth ? this.config.imgWidth : 50
                                });
                                link.insert(image);
                            }else{ //or echo label for custom style
                                link.innerHTML = options[i].label;
                            }
                            //bind event for click
                            Event.observe(link, 'click', this.imageSelectionClick.bind(this));
                            //show link
                            imageSelection.insert(link);
                            //zip data for later use
                            link.config = options[i];
                        }
                    }
                }
            }
        },

        clearSelect: function(element){
            if (element.options){
                for(var i=element.options.length-1; i>=0; i--){
                    element.remove(i);
                }
            }
            this.clearImageSelection(element.id);
        },

        reloadPrice: function(){
            if (this.config.disablePriceReload) {
                return;
            }

            var price    = 0;
            var oldPrice = 0;

            for (var i=this.settings.length-1; i>=0; i--){
                if (this.settings[i].options){
                    var selected = this.settings[i].options[this.settings[i].selectedIndex];
                    if (selected.config){
                        price    += parseFloat(selected.config.price);
                        oldPrice += parseFloat(selected.config.oldPrice);
                    }
                }else{
                    if (this.settings[i].innerHTML){
                        var image = this.settings[i].down('.attr-img.active');
                        if (image){
                            price += parseFloat(image.readAttribute('price'));
                        }
                    }
                }
            }

            optionsPrice.changePrice('config', {'price': price, 'oldPrice': oldPrice});
            optionsPrice.reload();

            return price;
        }
    });
}

Object.extend(Validation, {
    validate: function(elm, options){
        options = Object.extend({
            useTitle : false,
            onElementValidate : function(result, elm) {}
        }, options || {});
        elm = $(elm);
        var cn = $w(elm.className);
        return cn.all(function(value) {
            var test = Validation.test(value, elm, options.useTitle);
            options.onElementValidate(test, elm);
            return test && moreValidate(elm.up('form'));
        });
    }
});

function moreValidate(form){
    var result = true;
    form.select('.attribute-option-image').each(function(container){
        if (container.innerHTML){
            var id = container.id.replace(/[a-zA-Z-]*/, '');
            if (id && !container.down('#hidden' + id)){
                container.type = 'div';
                var advice = Validation.getAdvice('custom', container);
                if (!advice)
                    advice = Validation.createAdvice(
                    'custom',
                    container,
                    false,
                    Translator.translate('This is a required selection.')
                );
                Validation.showAdvice(container, advice);
                result = false;
            }else{
                var advice = Validation.getAdvice('custom', container);
                if (advice) Validation.hideAdvice(container, advice);
            }
        }
    });
    return result;
}