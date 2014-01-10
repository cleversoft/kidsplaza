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

var MiniCart = Class.create();
MiniCart.prototype = {
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

var MiniSearch = Class.create(Varien.searchForm, {
    initAutocomplete : function(url, destinationElement){
        new Ajax.Autocompleter(this.field, destinationElement, url, {
            paramName: this.field.name,
            method: 'get',
            minChars: 2,
            updateElement: this._selectAutocompleteItem.bind(this),
            onShow: function(element, update){
                update.style.width = element.getWidth() + 4 + 'px';
                Effect.SlideDown(update, {duration:0.2});
            },
            onHide: function(element, update){
                Effect.SlideUp(update, {duration:0.2});
            }
        });
    }
});

window.KPLocation = new KidsPlazaLocation();
window.miniCart = new MiniCart('cart-top');
window.searchForm = new MiniSearch('search_mini_form', 'search', $('search_mini_form').readAttribute('data-text'));
window.searchForm.initAutocomplete($('search_mini_form').readAttribute('data-suggest'), 'search_autocomplete');