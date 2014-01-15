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

    },
    setLocation: function(location){
        var expire = new Date();
        expire.setDate(expire.getDate() + 30);
        Mage.Cookies.set(this.locationVar, location, expire);
        this.location = location;
        window.location.reload();
    }
};

var KidsPlazaCart = Class.create();
KidsPlazaCart.prototype = {
    initialize: function(id){
        jQuery('#' + id).find('a.cart-content').popover({
            placement: 'bottom',
            html: true,
            trigger: 'click',
            content: function(){
                return jQuery('.cart-items', '#' + id).html()
            }
        });
    }
};

var KidsPlazaSearch = Class.create(Varien.searchForm, {
    initAutocomplete : function(url, destinationElement){
        new Ajax.Autocompleter(this.field, destinationElement, url, {
            paramName: this.field.name,
            method: 'get',
            minChars: 2,
            updateElement: this._selectAutocompleteItem.bind(this),
            onShow: function(element, update){
                update.style.width = element.getWidth() + 4 + 'px';
                Effect.SlideDown(update, {duration:0.2});
            }
        });
    }
});

//init location
var KPLocation = new KidsPlazaLocation();
//init mini cart
new KidsPlazaCart('cart-top');
//init search
var KPSearch = new KidsPlazaSearch('search_mini_form', 'search', $('search_mini_form').readAttribute('data-text'));
KPSearch.initAutocomplete($('search_mini_form').readAttribute('data-suggest'), 'search_autocomplete');
//init newsletter
new VarienForm('newsletter-validate-detail');
//init sticky header
jQuery('.mt-menu-top').sticky({topSpacing:-1});
//init to-top button
jQuery('.toTop').on('click',function(){jQuery('html,body').animate({scrollTop:0},500);});
jQuery(window).scroll(function(){var elm=jQuery('.toTop');if(!elm.length)return;if(jQuery(window).scrollTop()>=330){if(!elm.data('show')){elm.data('show',true);elm.slideDown('fast');}}else{if(elm.data('show')){elm.data('show',false);elm.fadeOut();}}});
