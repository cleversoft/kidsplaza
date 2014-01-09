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
        this.div = $(id);
        jQuery('#'+id).find('a.cart-content').popover({
            placement: 'bottom',
            html: true,
            trigger: 'click',
            content: function(){
                return jQuery('.cart-items', '#'+id).html()
            }
        });
    }
}

window.KPLocation = new KidsPlazaLocation();
window.miniCart = new MiniCart('cart-top');