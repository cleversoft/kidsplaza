'use strict';

var Location = Class.create();
Location.prototype = {
    locationVar: 'location',
    initialize: function(){
        this.location = Mage.Cookies ? Mage.Cookies.get(this.locationVar) : null;
        if (!this.location){
            this.showLocationSelect();
        }
    },
    showLocationSelect: function(){
        jQuery.fancybox({
            type: 'html',
            content: 'Choose location:<br><button type="button" class="button" onclick="window.loc.setLocation(\'hn\')"><span><span>Ha Noi</span></span></button>&nbsp;<button type="button" class="button" onclick="window.loc.setLocation(\'hcm\')"><span><span>Ho Chi Minh</span></span></button>',
            modal: true
        });
    },
    setLocation: function(location){
        var expire = new Date();
        expire.setDate(expire.getDate() + 30);
        Mage.Cookies.set(this.locationVar, location, expire);
        this.location = location;
        jQuery.fancybox.close();
        window.location.reload();
    }
};

jQuery(function(){
    window.loc = new Location();
});