/**
 * @category    MT
 * @package     MT_Widget
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

jQuery('.flexslider').each(function(i,slider){
    var id = slider.id;
    if (!id && !window[id]) return;

    var itemsCount = getFlexSliderItemWidth(id, window[id].responsive, 'column'),
        config = {
            minItems: itemsCount,
            maxItems: itemsCount,
            animation: 'slide',
            selector: '.slides > .slide',
            itemWidth: getFlexSliderItemWidth(id, window[id].responsive, 'width'),
            onResize: function(slider){
                var itemsCount = getFlexSliderItemWidth(id, window[id].responsive, 'column');
                slider.vars.minItems = itemsCount;
                slider.vars.maxItems = itemsCount;
            }
        };

    jQuery('#' + id).flexslider(jQuery.extend(config, window[id].config));
});

function getFlexSliderItemWidth(id, data, returnType){
    if (data && data.type){
        var containerW = Math.floor(jQuery('#' + id).width());
        switch (data.type){
            case 'width':
                if (returnType === 'width') return data.data;
                else if (returnType === 'column'){
                    return Math.floor(containerW / (data.data + data.margin * 2)) || 1;
                }
                break;
            case 'breakpoint':
                var breakpoints = data.data.split(' '),
                    BKP = [],
                    column = null;
                breakpoints.each(function(breakpoint){
                    if (breakpoint){
                        var config = breakpoint.split(':');
                        if (config.length === 2 && !isNaN(config[0]) && !isNaN(config[1])){
                            var obj = {
                                width: config[0],
                                column: config[1]
                            };
                            BKP.push(obj);
                        }
                    }
                });
                BKP.sort(function(a, b){ return a.width - b.width});
                BKP.each(function(bkp){
                    if (!column && bkp.width > containerW){
                        column = bkp.column;
                    }
                });
                column = column ? column : BKP.pop().column;
                if (returnType === 'width'){
                    return Math.floor(containerW / column) || 1;
                }else if (returnType === 'column'){
                    return column;
                }
                break;
        }
    }
}