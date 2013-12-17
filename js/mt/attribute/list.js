/**
 * @category    MT
 * @package     MT_Widget
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
document.observe('dom:loaded', function(){
    $$('.attr-image img').each(function(img){
        img.observe('mouseover', function(ev){
            var photo = img.readAttribute('origin');
            if (photo){
                var target = img.up('.item').down('a img');
                if (target) target.src = photo;
            }
        });
    });
});