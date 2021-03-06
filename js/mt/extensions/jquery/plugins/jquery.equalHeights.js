/*-------------------------------------------------------------------- 
 * JQuery Plugin: "EqualHeights" & "EqualWidths"
 * by:	Scott Jehl, Todd Parker, Maggie Costello Wachs (http://www.filamentgroup.com)
 *
 * Copyright (c) 2007 Filament Group
 * Licensed under GPL (http://www.opensource.org/licenses/gpl-license.php)
 *
 * Description: Compares the heights or widths of the top-level children of a provided element 
 		and sets their min-height to the tallest height (or width to widest width). Sets in em units 
 		by default if pxToEm() method is available.
 * Dependencies: jQuery library, pxToEm method	(article: http://www.filamentgroup.com/lab/retaining_scalable_interfaces_with_pixel_to_em_conversion/)							  
 * Usage Example: $(element).equalHeights();
 * Optional: to set min-height in px, pass a true argument: $(element).equalHeights(true);
 * Version: 2.0, 07.24.2008
 * Changelog:
 *  08.02.2007 initial Version 1.0
 *  07.24.2008 v 2.0 - added support for widths
--------------------------------------------------------------------*/

(function($){
    $.fn.equalHeights = function() {
        if (!arguments.length) return;
        var currentTallest = 0;
        var targets = [];
        $.each(arguments, function(i, selector){
            var target = $(selector);
            targets.push(target);
            target.css({'min-height': 'auto'});
            if (target.height() > currentTallest) currentTallest = target.height();
        });
        $.each(targets, function(i, target){
            target.css({'min-height': currentTallest});
        });
        return this;
    };
})(jQuery);