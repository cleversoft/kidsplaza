/**
 * @category    MT
 * @package     MT_Location
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

var Locations = Class.create();
Locations.prototype = {
    map: null,
    makers: {},
    info: {},
    initialize: function(list, mapDiv, data){
        this.list = $(list);
        this.mapDiv = $(mapDiv);
        this.locations = data;
        this.initMapAPI();
        this.initLinks();
    },
    initMapAPI: function(){
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&callback=map.initMap';
        document.body.appendChild(script);
    },
    initMap: function(){
        var latlng = this.locations[0].position.split(','),
            opt = {
                zoom: 13,
                center: new google.maps.LatLng(latlng[0].trim(), latlng[1].trim())
            };

        this.map = new google.maps.Map(this.mapDiv, opt);

        this.locations.each(function(location){
            if (!location.id) return;

            var latlng = location.position.split(',');
            if (latlng.length != 2) return;

            this.makers[location.id] = new google.maps.Marker({
                position: new google.maps.LatLng(latlng[0].trim(), latlng[1].trim()),
                map: this.map,
                title: location.address.stripTags(),
                animation: google.maps.Animation.DROP
            });

            if (location.description) {
                this.info[location.id] = new google.maps.InfoWindow({
                    content: location.description
                });
                //this.info[location.id].close();
                google.maps.event.addListener(this.makers[location.id], 'click', function(){
                    this.info[location.id].open(this.map, this.makers[location.id]);
                }.bind(this));
            }
        }.bind(this));
    },
    initLinks: function(){
        this.locations.each(function(location){
            if (!location.id) return;

            var latlng = location.position.split(',');
            if (latlng.length != 2) return;

            var li = new Element('li');
            var a = new Element('a');

            a.innerHTML = location.address;
            a.title = location.address.stripTags();
            a.href = 'javascript:void(0)';

            Event.observe(li, 'click', function(event){
                event.stop();
                this.list.select('li').each(function(li){li.removeClassName('active')});
                li.addClassName('active');
                this.map.setCenter(this.makers[location.id].getPosition());
                this.map.setZoom(14);
                this.removeInfoWindow();
                this.info[location.id] && this.info[location.id].open(this.map, this.makers[location.id]);
            }.bind(this));

            li.insert(a);
            this.list.insert(li);
        }.bind(this));
    },
    removeInfoWindow: function(){
        this.locations.each(function(location){
            if (!location.id) return;
            if (!this.info[location.id]) return;
            this.info[location.id].close();
        }.bind(this));
    }
};
