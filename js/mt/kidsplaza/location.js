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
                //update.style.height = 'auto';
                update.style.opacity = 1;
                Effect.SlideDown(update, {duration: 0.2});
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
KPSearch.initSearchFilter('category_ids', '.category-label');
//init newsletter
new VarienForm('newsletter-validate-detail');
//init sticky header
jQuery('.mt-menu-top').sticky({topSpacing:-1});
//init to-top button
jQuery('.toTop').on('click',function(){jQuery('html,body').animate({scrollTop:0},500);});
jQuery(window).scroll(function(){var elm=jQuery('.toTop');if(!elm.length)return;if(jQuery(window).scrollTop()>=330){if(!elm.data('show')){elm.data('show',true);elm.slideDown('fast');}}else{if(elm.data('show')){elm.data('show',false);elm.fadeOut();}}});
//init style select input
$$('select').each(function(select){ select.addClassName('form-control input-sm');});
jQuery(document).ready(function() {
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
});
!function(d,s,id){
    var js,fjs=d.getElementsByTagName(s)[0];
    if(!d.getElementById(id)){
        js=d.createElement(s);
        js.id=id;
        js.src="//platform.twitter.com/widgets.js";
        fjs.parentNode.insertBefore(js,fjs);
    }
}(document,"script","twitter-wjs");
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=115245961994281";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));