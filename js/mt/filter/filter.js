/**
 * @category    MT
 * @package     MT_Filter
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

var MTFilter = Class.create();
MTFilter.prototype = {
    container: null,
    layer: null,
    name: null,
    loadMoreUrl: null,
    isLoading: false,
    reInitLayer: false,
    initialize: function(name, config){
        this.name = name;
        this.config = config;

        if (this.config.enable){
            if (this.config.bar){
                NProgress.configure({
                    showSpinner: false
                });
            }
            document.observe('dom:loaded', function(){
                this.collect();
            }.bind(this));
        }

        document.observe('dom:loaded', function(){
            this.injectLoadMore();
        }.bind(this));
    },
    getDOM: function(selector, isLast){
        var DOMs = $$(selector);
        if (DOMs){
            return isLast ? DOMs[DOMs.length-1] : DOMs[0];
        }
    },
    getLoadMoreUrl: function(dom){
        //TODO try get url from Pages in Toolbar element
        if (dom){
            var url, curPage = 1, lis = dom.select('li');
            lis.each(function(li, index){
                if (!li.hasClassName('current')){
                    if (!url) url = li.down('a').href;
                }else{
                    if (index == lis.length - 1) curPage = null;
                    else curPage = parseInt(li.innerHTML.stripTags());
                }
            }, this);
            if (url && curPage){
                var URL = new URI(url);
                URL.setQuery('p', curPage + 1);
                return URL.toString();
            }
        }
    },
    isElementInViewport: function(elm){
        if (!elm) return;
        var rect = elm.getBoundingClientRect(),
            innerH = window.innerHeight || document.documentElement.clientHeight;

        return innerH - rect.bottom > 150;
    },
    moreCallbacks: function(){
        this.container.select('img.lazy').each(function(img){
            jQuery.fn.lazyload && jQuery(img).lazyload({
                event: 'scroll|widgetnav',
                failure_limit: 10
            });
        });
        this.reInitLayer && this.layer.select('div.panel-body').each(function(container){
            initLayerFilterWithScrollAndList(container);
        });
        ensureEqualHeight('.catalog-category-view .col2-left-layout > .row');
    },
    injectLoadMore: function(){
        var container = this.getDOM(this.config.loadDOM || '.toolbar .pages', true);
        this.loadMoreUrl = this.getLoadMoreUrl(container);

        $$('.toolbar .amount').each(function(elm){
            elm.hide();
        });

        if (container && this.config.loadMore){
            if (this.config.loadAuto){
                $$('.toolbar .pages').each(function(elm){
                    elm.hide();
                });

                if (window.bindedAutoLoadMore) return;
                window.bindedAutoLoadMore = true;

                Event.observe(window, 'scroll', function(){
                    if (this.loadMoreUrl && this.isElementInViewport(this.lastRow)){
                        this.sendRequest(this.loadMoreUrl, function(response){
                            this.updateHtml(response, false);
                        }.bind(this));
                    }
                }.bind(this));
            }else{
                var pages = $$('.toolbar .pages');
                pages.each(function(elm, index){
                    if (index < pages.length - 1) elm.hide();
                });

                if (this.loadMoreUrl){
                    var button = new Element('button', {'type':'button', 'class':'button'});
                    button.update(new Element('span').update(new Element('span').update(this.config.loadText || 'Load More')));
                    button.observe('click', function(ev){
                        ev.stop();
                        this.sendRequest(this.loadMoreUrl, function(response){
                            this.updateHtml(response, false);
                        }.bind(this));
                    }.bind(this));
                    container.update(button);
                }else{
                    container.update('');
                }
            }
        }
    },
    collect: function(){
        this.container = $$(this.config.mainDOM || '.col-main')[0];
        this.layer = $$(this.config.layerDOM || '.block-layered-nav')[0];
        this.initLinkFilter();
        setTimeout(function(){
            this.initLastRow();
        }.bind(this));
    },
    setConfig: function(obj){
        Object.extend(this.config, obj);
    },
    initLastRow: function(){
        var gridRows = this.container.select('.products-grid'),
            listRows = this.container.select('.products-list');

        if (gridRows.length){
            this.lastRow = this.container.down('.products-grid.last');
        }else if (listRows.length){
            this.lastRow = this.container.down('.item.last');
        }
    },
    initLinkFilter: function(){
        if (this.container){
            this.container.select('a').each(function(a){
                $(a).observe('click', function(ev){
                    var a = Event.findElement(ev, 'a'),
                        URL = new URI(a.href);

                    if (URL.hasQuery('p') || URL.hasQuery('order') || URL.hasQuery('mode') || URL.hasQuery('limit')){
                        Event.stop(ev);
                        URL.addQuery('toolbar', 1);
                        this.sendRequest(URL.toString(), function(response){
                            this.updateHtml(response, true);
                        }.bind(this));
                    }
                }.bind(this));
            }, this);
        }

        if (this.layer){
            this.layer.select('a').each(function(a){
                $(a).observe('click', function(ev){
                    if ($(ev.target).hasClassName('skip-ajax')) return;
                    Event.stop(ev);
                    var a = Event.findElement(ev, 'a'),
                        URL = new URI(a.href);

                    if (URL.hasQuery('toolbar')) URL.removeQuery('toolbar');
                    this.sendRequest(URL.toString(), function(response){
                        this.updateHtml(response, true);
                    }.bind(this));
                }.bind(this));
            }, this);
        }
    },
    initPriceFilter: function(obj){
        var slider      = $(obj.id),
            handles     = slider.select('.price-slider-handle'),
            minText     = $('layer-price-min'),
            maxText     = $('layer-price-max'),
            range       = $R(obj.min, obj.max),
            URL         = new URI(obj.url);

        minText.update(obj.values[0]);
        maxText.update(obj.values[1]);
        var sliderObj = new Control.Slider(handles, slider, {
            range: range,
            sliderValue: obj.values,
            spans: slider.select('.price-slider-span'),
            restricted: true,
            onSlide: function(values){
                minText.update(Math.floor(values[0]));
                maxText.update(Math.ceil(values[1]));
            },
            onChange: function(values){
                var priceMin = Math.floor(values[0]),
                    priceMax = Math.ceil(values[1]);

                sliderObj.setDisabled();
                URL.setQuery('price', priceMin + '-' + priceMax);
                this.sendRequest(URL.toString(), function(response){
                    this.updateHtml(response, true);
                    sliderObj.setEnabled();
                }.bind(this));
            }.bind(this)
        });
    },
    getParams: function(){
        return {
            isAjax: true
        };
    },
    setAjaxLocation: function(url){
        var url = arguments[0],
            URL = new URI(url);

        if (URL.hasQuery('limit') || URL.hasQuery('order')){
            this.sendRequest(url, function(response){
                this.updateHtml(response, true);
            }.bind(this));
        }else setLocation(url);
    },
    sendRequest: function(url, success, error){
        if (!url) return;

        if (this.config.enable){
            if (this.isLoading) return;
            this.isLoading = true;

            if (this.config.bar) NProgress.start();

            new Ajax.Request(url, {
                method: 'get',
                parameters: this.getParams(),
                onSuccess: function(transport){
                    try{
                        var response = transport.responseText.evalJSON();
                        if (success) success(response);
                    }catch(e){
                        console.error(e.message);
                    }
                }.bind(this),
                onFailure: function(transport){
                    if (error) error(transport);
                    this.isLoading = false;
                },
                onComplete: function(){
                    if (this.config.bar) NProgress.done();
                    this.isLoading = false;
                }.bind(this)
            });
        }else{
            setLocation(url);
        }
    },
    updateRows: function(data, rowSelector){
        var rows = this.container.select(rowSelector),
            lastRow = rows[rows.length - 1],
            html = '';

        lastRow.removeClassName('last');
        data.each(function(row){
            html += row.outerHTML;
        });
        lastRow.insert({after: html});
    },
    updateHtml: function(response, replace){
        if (!response) return;
        var main = response.main ? response.main.replace(/setLocation/g, this.name+'.setAjaxLocation') : null,
            layer = response.layer || null;

        if (main && this.container){
            if (replace){
                this.container.update(main);
            }else{
                var dom = document.createElement('div');
                dom.update(main);

                var gridRows = dom.select('.products-grid'),
                    listRows = dom.select('.products-list');

                if (gridRows.length){
                    this.updateRows(gridRows, '.products-grid');
                }else if (listRows.length){
                    this.updateRows(listRows, '.item');
                }

                this.loadMoreUrl = this.getLoadMoreUrl(dom);

                if (this.config.loadAuto){
                    if (gridRows.length){
                        this.lastRow = this.container.down('.products-grid.last', true);
                    }else if (listRows.length){
                        this.lastRow = this.container.down('.item.last', true);
                    }
                }else{
                    if (!this.loadMoreUrl){
                        var loadMoreContainer = this.getDOM(this.config.loadDOM || '.toolbar .pages', true);
                        loadMoreContainer && loadMoreContainer.update('');
                    }
                }
            }
        }
        if (layer && this.layer){
            this.layer.replace(layer);
            this.reInitLayer = true;
        }else this.reInitLayer = false;

        setTimeout(function(){
            this.collect();
            replace && this.goTop(function(){
                this.injectLoadMore();
                this.moreCallbacks();
            }.bind(this));
        }.bind(this));
    },
    goTop: function(callback){
        Effect.ScrollTo(this.container, {duration: 0.5});
        callback && setTimeout(function(){
            callback();
        }, 500);
    }
};