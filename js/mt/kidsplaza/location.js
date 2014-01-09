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

window.KPLocation = new KidsPlazaLocation();