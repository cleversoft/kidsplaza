//////////////////////////////////////////////////////////////////
// Add Button button
//////////////////////////////////////////////////////////////////
(function() {  
    tinymce.create('tinymce.plugins.button', {  
        init : function(ed, url) {  
            ed.addButton('button', {  
                title : 'Add a button',  
                image : url+'/button.png',  
                onclick : function() {  
                     ed.selection.setContent('[button link=""][/button]');  
  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('button', tinymce.plugins.button);  
})();
	
//////////////////////////////////////////////////////////////////
// Add Tabs button
//////////////////////////////////////////////////////////////////
(function() {  
    tinymce.create('tinymce.plugins.tabs', {  
        init : function(ed, url) {  
            ed.addButton('tabs', {  
                title : 'Add tabs',  
                image : url+'/tabs.png',  
                onclick : function() {  
                     ed.selection.setContent('[tabs tab1=\"Tab 1\" tab2=\"Tab 2\" tab3=\"Tab 3\"]<br /><br />[tab id=1]Tab content 1[/tab]<br />[tab id=2]Tab content 2[/tab]<br />[tab id=3]Tab content 3[/tab]<br /><br />[/tabs]');  
  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('tabs', tinymce.plugins.tabs);  
})();
//////////////////////////////////////////////////////////////////
// Add Ads button
//////////////////////////////////////////////////////////////////
(function() {  
    tinymce.create('tinymce.plugins.ads', {  
        init : function(ed, url) {  
            ed.addButton('ads', {  
                title : 'Add a ads',  
                image : url+'/ads.png',  
                onclick : function() {  
                     ed.selection.setContent('[ads][/ads]');  
  
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('ads', tinymce.plugins.ads);  
})();