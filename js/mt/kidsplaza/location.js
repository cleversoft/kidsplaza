/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

var KidsPlazaLocation = Class.create();
KidsPlazaLocation.prototype = {
    locationVar: 'location',
    initialize: function(){
        this.location = Mage.Cookies ? Mage.Cookies.get(this.locationVar) : null;
        if (!this.location){
            this.showLocationSelect();
        }
    },
    showLocationSelect: function(){
        document.observe('dom:loaded', function(){
            this.triggerLocationOverlay();
        }.bind(this));
    },
    setLocation: function(location){
        var expire = new Date();
        expire.setDate(expire.getDate() + 30);
        Mage.Cookies.set(this.locationVar, location, expire);
        this.location = location;
        window.location.reload();
    },
    triggerLocationOverlay: function(){
        jQuery('#location-modal').modal('show');
    }
};

var KidsPlazaCart = Class.create();
KidsPlazaCart.prototype = {
    initialize: function(id){
        jQuery('#' + id).popover({
            placement: 'bottom',
            html: true,
            trigger: 'hover',
            container: '#' + id,
            content: function(){
                return jQuery('.cart-items', '#' + id).html()
            }
        });
    }
};

var KidsPlazaLoginHeader = Class.create();
KidsPlazaLoginHeader.prototype = {
    initialize: function(id){
        jQuery('#' + id).popover({
            placement: 'bottom',
            html: true,
            trigger: 'hover',
            container: '#' + id,
            content: function(){
                return jQuery('#' + id).parent().find('.login-content').html()
            }
        });
    }
};

var KidsPlazaPhone = Class.create();
KidsPlazaPhone.prototype = {
    initialize: function(id, fields){
        var element = $(id);
        if (!element) return;
        fields = fields || {};
        Event.observe(element, 'change', function(ev){
            var input = Event.findElement(ev, 'input'),
                spinner = input.up().down('.spinner'),
                url = input.readAttribute('data-url'),
                phoneValidator = Validation.get('validate-phoneprefix'),
                form = input.up('form');

            if (!url) return;

            if (phoneValidator && phoneValidator.test(input.value, input)){
                var advice = Validation.getAdvice('validate-phoneprefix', input);
                if (advice) Validation.hideAdvice(input, advice);

                var params = {value: input.value, form_key: Mage.FormKey};

                spinner && spinner.show();
                new Ajax.Request(url, {
                    parameters: params,
                    onSuccess: function(transport){
                        spinner && spinner.hide();
                        try{
                            var response = transport.responseText.evalJSON();
                            for (var i in fields){
                                var elm = fields[i];
                                if (elm && response[i]){
                                    var target = $(elm);
                                    if (!target) return;
                                    target.value = response[i];
                                    switch (i) {
                                        case 'gender':
                                            if (target.up().hasClassName('selector')) {
                                                jQuery.uniform.update();
                                            }
                                            break;
                                    }
                                }
                            }
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
        });
    }
};

var EnhancedAjaxAutocompleter = Class.create(Ajax.Autocompleter, {
    updateChoices: function(choices) {
        if(!this.changed && this.hasFocus) {
            this.update.innerHTML = choices;
            Element.cleanWhitespace(this.update);
            Element.cleanWhitespace(this.update.down());

            if(this.update.firstChild && this.update.down().childNodes) {
                this.entryCount =
                    this.update.down().childNodes.length;
                for (var i = 0; i < this.entryCount; i++) {
                    var entry = this.getEntry(i);
                    entry.autocompleteIndex = i;
                    this.addObservers(entry);
                }
            } else {
                this.entryCount = 0;
            }

            this.stopIndicator();
            this.index = -1; // should not auto select first result

            if(this.entryCount==1 && this.options.autoSelect) {
                this.selectEntry();
                this.hide();
            } else {
                this.render();
            }
        }
    },

    selectEntry: function() {
        var currentEntry = this.getCurrentEntry(),
            a = $(currentEntry).down('a');

        if (a) window.location.href = a.href;
        else{
            this.active = false;
            this.updateElement(currentEntry);
        }
    }
});

var KidsPlazaSearch = Class.create(Varien.searchForm, {
    initAutocomplete : function(url, destinationElement){
        new EnhancedAjaxAutocompleter(this.field, destinationElement, url, {
            callback: function(elm, entry){
                var form = elm.up('form');
                if (form) return form.serialize();
                else return entry;
            },
            frequency: 0.2,
            paramName: this.field.name,
            method: 'get',
            minChars: 3,
            updateElement: this._selectAutocompleteItem.bind(this),
            onShow: function(element, update){
                update.style.width = element.getWidth() + 7 + 'px';
                update.style.height = 'auto';
                update.style.opacity = 1;
                Effect.Appear(update, {duration: 0.2});
            }
        });
    },

    initSearchFilter: function(field, target){
        var field = this.form.down('input[name="'+field+'"]');
        if (!field) return;
        var target = this.form.down(target);
        this.form.select('.dropdown-menu a').each(function(item){
            Event.observe(item, 'click', function(){
                field.value = item.readAttribute('data-value');
                if (target) target.innerHTML = item.innerHTML;
            });
        });
    }
});

function onBuyBtnClick(url){
    if (!url) return;
    if (!Mage.FormKey) return;
    window.location.href = url.indexOf('?') > 0 ? url + '&form_key=' + Mage.FormKey : url + 'form_key/' + Mage.FormKey;
}

function triggerLocationOverlay(){
    var overlay = jQuery('div.overlay');
    if (!overlay.length) return;
    if (overlay.hasClass('open')){
        overlay.removeClass('open');
        overlay.addClass('close');
    }else if (!overlay.hasClass('close')){
        overlay.addClass('open');
    }
}

function setGridItemsEqualHeight(){
    var grid = jQuery('.show-grid');

    if (jQuery.fn.imageready){
        grid.imageready(function(){
            calculateGridItemHeight(grid);
        });
    }else{
        calculateGridItemHeight(grid);
    }

    function calculateGridItemHeight(grid) {
        var winWidth = jQuery(window).width(),
            items = grid.find('.item');

        if (winWidth >= 200) {
            var gridItemMaxHeight = 0;
            grid.removeClass('auto-height');
            items.each(function () {
                jQuery(this).css('height', 'auto');
                gridItemMaxHeight = Math.max(gridItemMaxHeight, jQuery(this).height());
            });
            items.css('height', gridItemMaxHeight + 'px');
        } else {
            grid.addClass('auto-height');
            items.css('height', 'auto');
            items.css('padding-bottom', '20px');
        }
    }

    if (window.ensureEqualHeight) ensureEqualHeight();
}

//init cufont
Cufon.replace('.utm-cookies');
//init login header
new KidsPlazaLoginHeader('topLogin');
//init location
var KPLocation = new KidsPlazaLocation();
//init mini cart
new KidsPlazaCart('cart-top');
//init search
var KPSearch = new KidsPlazaSearch('search_mini_form', 'search', $('search_mini_form').readAttribute('data-text'));
KPSearch.initAutocomplete($('search_mini_form').readAttribute('data-suggest'), 'search_autocomplete');
KPSearch.initSearchFilter('cat', '.category-label');
//init newsletter
new VarienForm('newsletter-validate-detail');
//init sticky header
jQuery('.mt-menu-top').sticky({topSpacing:-1});
//init to-top button
jQuery('.toTop').on('click',function(){jQuery('html,body').animate({scrollTop:0},500);});
jQuery(window).scroll(function(){var elm=jQuery('.toTop');if(!elm.length)return;if(jQuery(window).scrollTop()>=330){if(!elm.data('show')){elm.data('show',true);elm.slideDown('fast');}}else{if(elm.data('show')){elm.data('show',false);elm.fadeOut();}}});
jQuery(document).ready(function(){
    //init social link hover
    jQuery('.social-links a').hover(function(){
        jQuery(this).next().show();
        jQuery(this).next().hover(function(){
            jQuery(this).show();
        },function(){
            jQuery(this).hide();
        });
    },function(){
        jQuery(this).next().hide();
    });
    //init uniform
    jQuery.fn.uniform && jQuery("select, input[type='checkbox']").uniform();
    jQuery.fn.uniform && jQuery("input[type='radio']").not(".rating-star").uniform();
    //calculate product grid height
    setGridItemsEqualHeight();
});
//init twitter js
(function(d,s,id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (!d.getElementById(id)){
        js = d.createElement(s);
        js.id = id;
        js.src = "//platform.twitter.com/widgets.js";
        fjs.parentNode.insertBefore(js, fjs);
    }
}(document, 'script', 'twitter-wjs'));
//init facebook js
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=115245961994281";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
//init google plus js
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = "//apis.google.com/js/plusone.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'google-plus'));
//init phone validation
Validation && Validation.addAllThese([
    ['validate-phoneprefix', 'Please enter a valid phone number in this field.', function(value){
        if (!validatePhonePrefix) return true;

        var phonePrefix = validatePhonePrefix.split(','),
            phoneLen = 7;

        for (var i=0; i<phonePrefix.length; i++) {
            if (value.substring(0, phonePrefix[i].length) == phonePrefix[i] && value.length == (phonePrefix[i].length + phoneLen)){
                return true;
            }
        }

        return Validation.get('IsEmpty').test(value);
    }]
]);
//extend Validation
Object.extend(Validation,{
    insertAdvice : function(elm, advice){
        var container = $(elm).up('.field-row'),
            uniform = $(elm).up();

        if (uniform.hasClassName('selector')){
            Element.insert(uniform, {after: advice});
        }else if (container){
            Element.insert(container, {after: advice});
        } else if (elm.up('td.value')) {
            elm.up('td.value').insert({bottom: advice});
        } else if (elm.advaiceContainer && $(elm.advaiceContainer)) {
            $(elm.advaiceContainer).update(advice);
        } else {
            switch (elm.type.toLowerCase()) {
                case 'checkbox':
                case 'radio':
                    var p = elm.parentNode;
                    if(p) {
                        Element.insert(p, {'bottom': advice});
                    } else {
                        Element.insert(elm, {'after': advice});
                    }
                    break;
                default:
                    Element.insert(elm, {'after': advice});
            }
        }
    }
});