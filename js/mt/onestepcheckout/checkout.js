/**
 * @category    MT
 * @package     MT_OneStepCheckout
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

var MT = MT || {};
MT.Utils = {
    disableElement: function(elem){
        if (!elem) return;
        elem.disabled = true;
        elem.addClassName('disabled');
    },
    enableElement: function(elem){
        if (!elem) return;
        elem.disabled = false;
        elem.removeClassName('disabled');
    }
};
MT.OneStepCheckout = Class.create();
MT.OneStepCheckout.prototype = {
    initialize: function(config){
        this.cart = $(config.cart);
        this.cartRemoveUrl = config.cartRemoveUrl;
        this.showLoading = config.showLoading;
        this.count = this.cart && this.cart.select('tbody tr').length;
        this.loader = config.loader;
        this.orderUrl = config.orderUrl;
        this.successUrl = config.successUrl;
        this.failureUrl = config.failureUrl;
        this.blocks = 'billing|shipping|shippingMethod|payment|review'.split('|');

        this.billing = config.billing;
        this.shipping = config.shipping;
        document.observe('address:beforeSend', function(){
            this.addressBeforeSend();
        }.bind(this));
        document.observe('address:afterSend', function(ev){
            this.addressAfterSend(ev.memo.transport);
        }.bind(this));

        this.shippingMethod = config.shippingMethod;
        if (this.shippingMethod) this.shippingMethod.getLoadIndicator = this.getLoadIndicator.bindAsEventListener(this);
        document.observe('shippingMethod:beforeSend', function(){
            this.shippingMethodBeforeSend();
        }.bind(this));
        document.observe('shippingMethod:afterSend', function(ev){
            this.shippingMethodAfterSend(ev.memo.transport);
        }.bind(this));

        this.payment = config.payment;
        if (this.payment) this.payment.getLoadIndicator = this.getLoadIndicator.bindAsEventListener(this);
        document.observe('payment:beforeSend', function(){
            this.paymentBeforeSend();
        }.bind(this));
        document.observe('payment:afterSend', function(ev){
            this.paymentAfterSend(ev.memo.transport);
        }.bind(this));

        this.review = config.review;
        if (this.review) this.review.getLoadIndicator = this.getLoadIndicator.bindAsEventListener(this);

        this.terms = config.terms;

        document.observe('cart:error', function(){
            this.onCartError();
        }.bind(this));
        document.observe('cart:update', function(){
            this.onCartUpdate();
        }.bind(this));

        if (this.showLoading && NProgress){
            NProgress.configure({
                showSpinner: false
            });
        }

        this.cart && this.initCartLinks();
        this.cart && this.initCartButtons();

        if (window.Windows) Windows.overlayShowEffectOptions = {duration: 0};
        if (window.Windows) Windows.overlayHideEffectOptions = {duration: 0};
    },

    showLoginForm: function(){
        var loginForm = $('checkout-login-form'),
            forgotForm = $('checkout-forgot-form'),
            success = loginForm.up().down('.alert-success'),
            error = loginForm.up().down('.alert-danger');

        loginForm.removeClassName('hidden');
        forgotForm.addClassName('hidden');
        success.update('');
        success.addClassName('hidden');
        error.update('');
        error.addClassName('hidden');
    },

    showForgotForm: function(){
        var loginForm = $('checkout-login-form'),
            forgotForm = $('checkout-forgot-form'),
            success = loginForm.up().down('.alert-success'),
            error = loginForm.up().down('.alert-danger');

        loginForm.addClassName('hidden');
        forgotForm.removeClassName('hidden');
        success.update('');
        success.addClassName('hidden');
        error.update('');
        error.addClassName('hidden');
    },

    login: function(btn){
        var form = $(btn).up('form');
        if (!form) return;
        var VF = new VarienForm(form);
        var error = form.up().down('.alert-danger');
        error && error.addClassName('hidden');
        var success = form.up().down('.alert-success');
        success && success.addClassName('hidden');
        var loader = form.down('.checkout-login-loading');
        if (VF.validator.validate()){
            loader && loader.removeClassName('hidden');
            MT.Utils.disableElement(btn);
            new Ajax.Request(form.action, {
                method: 'post',
                parameters: form.serialize(true),
                onComplete: function(){
                    loader && loader.addClassName('hidden');
                    MT.Utils.enableElement(btn);
                },
                onSuccess: function(transport){
                    if (transport.responseText){
                        var response = transport.responseText.evalJSON();
                        if (response && response.success){
                            if (response.forgot){
                                success.innerHTML = response.message;
                                success.removeClassName('hidden');
                            }else{
                                window.location.reload();
                            }
                        }else{
                            if (error){
                                error.innerHTML = response.message;
                                error.removeClassName('hidden');
                            }
                        }
                    }else{
                        form.show();
                    }
                },
                onFailure: function(){

                }
            });
        }
    },

    setConfig: function(config){
        for (var i in config){
            this[i] = config[i];
            this[i].getLoadIndicator = this.getLoadIndicator.bindAsEventListener(this);
        }
    },

    initCartButtons: function(){
        this.cart.select('tr').each(function(tr){
            if (!tr.select('button.button-qty').length){
                this.renderCartQtyButtons(tr.down('input.qty'));
            }
        }, this);
    },

    renderCartQtyButtons: function(input){
        if (!input) return;

        if ($(input).up().select('button.btn').length) return;

        $(input).wrap('div', {'class':'input-group'});
        var minusBtn = new Element('button', {'class':'btn btn-info btn-qty', type:'button'});
        minusBtn.update('-');
        minusBtn.observe('click', function(ev){
            ev.stop();
            var button = Event.findElement(ev, 'button'),
                inputElm = button.up('td').down('input.qty');
            this.handleCartQty(inputElm, '-');
        }.bind(this));

        var plusBtn = new Element('button', {'class':'btn btn-info btn-qty', type:'button'});
        plusBtn.update('+');
        plusBtn.observe('click', function(ev){
            ev.stop();
            var button = Event.findElement(ev, 'button'),
                inputElm = button.up('td').down('input.qty');
            this.handleCartQty(inputElm, '+');
        }.bind(this));

        $(input).insert({before: minusBtn.wrap('span',{'class':'input-group-btn'})});
        $(input).insert({after: plusBtn.wrap('span',{'class':'input-group-btn'})});
        $(input).addClassName('a-center');
    },

    handleCartQty: function(inputElm, action){
        var qty = inputElm.value,
            table = inputElm.up('table'),
            params = {};

        switch (action){
            case '-':
                qty = qty - 1 <= 0 ? 0 : qty - 1;
                break;
            case '+':
                qty++;
                break;
        }
        inputElm.value = qty;
        params[inputElm.name] = inputElm.value;
        if (table.hasClassName('cart-mobile')) params.isMobile = 1;
        this.request(this.cart.action, params, function(transport){
            this.onUpdateCartItem(transport, inputElm);
        }.bind(this));
    },

    getLoadIndicator: function(){
        var div = new Element('div', {'class': 'loading-indicator'});
        var img = this.loader ? new Element('img', {src: this.loader}): '<span>'+Translator.translate('Loading...')+'</span>';
        div.update(img);
        return div;
    },

    onCartUpdate: function(){
        if (this.billing.useForShipping) this.billing.save();
        else this.shipping.save();
        this.review.disableSubmit(false);
    },

    onCartError: function(){
        this.review.disableSubmit(true);
    },

    addressBeforeSend: function(){
        this.shippingMethod.busy(true);
        this.payment.busy(true);
        this.review.busy(true);
    },

    addressAfterSend: function(transport){
        //try{
            var response = transport.responseText.evalJSON();
            this.updateBlocks(response);
        //}catch(e){
        //    console.log(e);
        //}
    },

    updateBlocks: function(response){
        if (typeof response === 'object'){
            this.blocks.each(function(block){
                if (!this[block]) return;
                if (response.error){
                    if (this[block].isBusy) this[block].busy(false);
                }
                if (response[block]){
                    this[block].update(response[block]);
                }
            }, this);
        }
    },

    shippingMethodBeforeSend: function(){
        this.review.busy(true);
    },

    shippingMethodAfterSend: function(transport){
        try{
            this.updateBlocks(transport.responseText.evalJSON());
        }catch(e){}
    },

    paymentBeforeSend: function(){
        this.review.busy(true);
    },

    paymentAfterSend: function(transport){
        try{
            var response = transport.responseText.evalJSON();
            //if (response.redirect) window.location.href = response.redirect;
            this.updateBlocks(response);
        }catch(e){}
    },

    submit: function(){
        var v1 = this.billing ? this.billing.validate() : true,
            v2 = this.shipping ? this.shipping.validate() : true,
            v3 = this.shippingMethod ? this.shippingMethod.validate() : true,
            v4 = this.payment ? this.payment.validate() : true,
            v5 = this.terms ? this.terms.validate() : true;

        if (v1 && v2 && v3 && v4 && v5){
            var params = Object.extend({}, this.billing.f.form.serialize(true));
            Object.extend(params, this.shipping && this.shipping.f.form.serialize(true));
            Object.extend(params, this.shippingMethod && this.shippingMethod.f.form.serialize(true));
            Object.extend(params, this.payment && this.payment.f.form.serialize(true));
            Object.extend(params, this.terms && this.terms.f.form.serialize(true));

            this.beforeSubmit();

            new Ajax.Request(this.orderUrl, {
                parameters: params,
                onComplete: function(){
                    //
                }.bind(this),
                onSuccess: function(transport){
                    try{
                        var response = transport.responseText.evalJSON();
                        if (response['success']){
                            window.location.href = response['redirect'] || this.successUrl;
                        }else{
                            this.afterSubmit();
                            if (response['message']) alert(response['message']);
                            if (response['error_messages']) alert(response['error_messages']);
                            if (response['redirect']) window.location.href = response['redirect'];
                            this.updateBlocks(response);
                        }
                    }catch(e){}
                }.bind(this),
                onFailure: function(){
                    window.location.href = '';
                }
            });
        }
    },

    beforeSubmit: function(){
        this.review.toggleSubmit(true);
    },

    afterSubmit: function(){
        this.review.toggleSubmit(false);
    },

    useShipping: function(flag){
        var shippingAddressSelect = $('customer-shipping-address-select');

        if (flag){
            this.shipping.save();
            this.billing.useForShipping = false;
            $('co-shipping-form').show();
            $('text-same-billing').hide();
        }else{
            this.billing.save();
            this.billing.useForShipping = true;
            $('co-shipping-form').hide();
            $('text-same-billing').show();
        }
    },

    initCartLinks: function(){
        this.cart.select('a.btn-remove').each(function(a){
            if (a.binded) return;
            a.binded = true;
            Event.observe(a, 'click', function(ev){
                Event.stop(ev);
                if (confirm(Translator.translate('Are you sure you would like to remove this item from the shopping cart?'))){
                    var elm = Event.findElement(ev, 'a'),
                        url = elm.href.replace(/checkout\/cart\/delete/g, 'mtonestepcheckout/cart/delete');

                    this.request(url, {}, function(transport){
                        this.removeCartItemHtml(elm);
                        var response = transport.responseText.evalJSON();
                        if (response.count > 0) Event.fire(document, 'cart:update');
                        else window.location.reload();
                    }.bind(this));
                }
            }.bind(this));
        }, this);
    },

    onUpdateCartItem: function(transport, elm){
        try{
            var response = transport.responseText.evalJSON();
            if (response.error == 1){
                this.showCartMessages('error', response.msg);
                Event.fire(document, 'cart:error');
            }else{
                if (response.count === 0) window.location.reload();
                else{
                    if (elm.value == 0) this.removeCartItemHtml(elm);
                    if (response.items) this.updateCartItemHtml(response.items, response.isMobile);
                    this.showCartMessages('success', response.msg);
                    Event.fire(document, 'cart:update');
                }
            }
        }catch(e){
            console.log(e);
        }
    },

    updateCartItemHtml: function(items, isMobile){
        var cart = isMobile == 1 ? $$('.cart-mobile')[0] : $$('.cart-table')[0];

        for (var id in items){
            var name = 'cart[' + id + '][qty]',
                input = cart.down('input[name="'+name+'"]');

            if (input){
                var tr = input.up('tr');
                if (tr) tr.replace(items[id]);
            }
        }
        setTimeout(function(){
            this.initCartLinks();
            this.initCartButtons();
        }.bind(this));
    },

    removeCartItemHtml: function(elm){
        var tr = elm.up('tr');
        if (tr) tr.remove();
    },

    showCartMessages: function(type, messages){
        var container = this.cart.down('ul.messages');
        if (container) container.update('');
        else container = new Element('ul', {'class':'messages'});

        if (messages){
            if (type == 'error') var html = '<li class="alert alert-danger"><ul>';
            else var html = '<li class="alert alert-success"><ul>';
            if (typeof messages !== 'string'){
                messages.each(function(message){
                    html += '<li><span>'+message+'</span></li>';
                });
            }else html += '<li><span>'+messages+'</span></li>';
            html += '</ul>';

            container.update(html);
            this.cart.insert({top:container});
        }
    },

    updateCart: function(type){
        var params = this.cart.serialize(true);
        params.update_cart_action = type;
        this.request(this.cart.action, params, function(transport){
            try{
                var response = transport.responseText.evalJSON();
                if (response.error == 1){
                    this.showCartMessages('error', response.msg);
                    Event.fire(document, 'cart:error');
                }else{
                    Event.fire(document, 'cart:update');
                    this.showCartMessages('success', response.msg);
                    if (type === 'empty_cart') window.location.reload();
                    this.cart.select('input.qty').each(function(input){
                        if (input.value == 0) this.removeCartItemHtml(input);
                    }, this);
                    if (response.items) this.updateCartItemHtml(response.items, response.isMobile);
                }
            }catch(e){
                console.log(e);
            }
        }.bind(this));
    },

    request: function(url, params, success, error){
        if (this.showLoading == 1) NProgress.start();
        new Ajax.Request(url, {
            parameters: params || {},
            onComplete: function(){
                if (this.showLoading == 1) NProgress.done();
            }.bind(this),
            onSuccess: function(transport){
                if (success) success(transport);
            },
            onFailure: function(transport){
                if (error) error(transport);
            }
        });
    }
};

MT.Billing = Class.create();
MT.Billing.prototype = {
    initialize: function(form, addressUrl, saveUrl){
        this.beforeSend = function(){};
        this.afterSend = function(){};
        this.useForShipping = false;
        this.form = form;
        this.f = new VarienForm(form);
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){this.save();Event.stop(event);}.bind(this));
        }
        this.addressUrl = addressUrl;
        this.saveUrl = saveUrl;
        this.initActions();
    },

    validate: function(){
        return this.f.validator.validate();
    },

    initActions: function(){
        var register = $('billing:register'),
            registerDiv = $('register-customer-password'),
            fields = 'billing:postcode|billing:country_id|billing:region|billing:city';

        if (register && registerDiv){
            register.observe('click', function(){
                registerDiv.toggle();
            });
        }

        fields.split('|').each(function(field){
            var elm = $(field);
            if (elm){
                elm.observe('change', function(){
                    this.save();
                }.bind(this));
            }
        }, this);
    },

    setAddress: function(addressId){
        if (addressId) {
            new Ajax.Request(this.addressUrl + addressId, {
                method: 'get',
                onSuccess: this.onAddressLoad,
                onFailure: checkout.ajaxFailure.bind(checkout)
            });
        } else {
            this.fillForm(false);
        }
    },

    newAddress: function(value){
        if (value == 0) {
            //this.resetSelectedAddress();
            Element.show('billing-new-address-form');
        } else {
            this.save();
            Element.hide('billing-new-address-form');
        }
    },

    resetSelectedAddress: function(){
        var selectElement = $('billing-address-select')
        if (selectElement) {
            selectElement.value='';
        }
    },

    fillForm: function(transport){
        var elementValues = {};
        if (transport && transport.responseText){
            try{
                elementValues = eval('(' + transport.responseText + ')');
            } catch (e) {
                elementValues = {};
            }
        } else {
            this.resetSelectedAddress();
        }
        var arrElements = Form.getElements(this.form);
        for (var elemIndex in arrElements) {
            if (arrElements[elemIndex].id) {
                var fieldName = arrElements[elemIndex].id.replace(/^billing:/, '');
                arrElements[elemIndex].value = elementValues[fieldName] ? elementValues[fieldName] : '';
                if (fieldName == 'country_id' && billingForm){
                    billingForm.elementChildLoad(arrElements[elemIndex]);
                }
            }
        }
    },

    setUseForShipping: function(flag){
        $('shipping:same_as_billing').checked = flag;
    },

    needSave: function(){
        return $('billing:use_for_shipping_yes') ? $('billing:use_for_shipping_yes').checked : false;
    },

    save: function(force){
        if (this.needSave() || force){
            Event.fire(document, 'address:beforeSend');
            new Ajax.Request(this.saveUrl, {
                method: 'post',
                //onComplete: this.onComplete,
                onSuccess: function(transport){ Event.fire(document, 'address:afterSend', {transport:transport})},
                onFailure: function(){ window.location.href = '';},
                parameters: Form.serialize(this.form)
            });
        }
    },

    resetLoadWaiting: function(transport){
        checkout.setLoadWaiting(false);
        document.body.fire('billing-request:completed', {transport: transport});
    }
};

MT.Shipping = Class.create();
MT.Shipping.prototype = {
    initialize: function(form, addressUrl, saveUrl){
        this.beforeSend = function(){};
        this.afterSend = function(){};
        this.form = form;
        this.f = new VarienForm(form);
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){this.save();Event.stop(event);}.bind(this));
        }
        this.addressUrl = addressUrl;
        this.saveUrl = saveUrl;
        this.initActions();
    },

    initActions: function(){
        var fields = 'shipping:postcode|shipping:country_id|shipping:region|shipping:city';

        fields.split('|').each(function(field){
            var elm = $(field);
            if (elm){
                elm.observe('change', function(){
                    this.save();
                }.bind(this));
            }
        }, this);
    },

    setAddress: function(addressId){
        if (addressId) {
            new Ajax.Request(
                this.addressUrl+addressId,
                {method:'get', onSuccess: this.onAddressLoad, onFailure: checkout.ajaxFailure.bind(checkout)}
            );
        } else {
            this.fillForm(false);
        }
    },

    newAddress: function(value){
        if (value == 0) {
            //this.resetSelectedAddress();
            Element.show('shipping-new-address-form');
        } else {
            Element.hide('shipping-new-address-form');
            this.save();
        }
        this.setSameAsBilling(false);
    },

    resetSelectedAddress: function(){
        var selectElement = $('shipping-address-select');
        if (selectElement) {
            selectElement.value='';
        }
    },

    fillForm: function(transport){
        var elementValues = {};
        if (transport && transport.responseText){
            try{
                elementValues = eval('(' + transport.responseText + ')');
            } catch (e) {
                elementValues = {};
            }
        } else{
            this.resetSelectedAddress();
        }
        var arrElements = Form.getElements(this.form);
        for (var elemIndex in arrElements) {
            if (arrElements[elemIndex].id) {
                var fieldName = arrElements[elemIndex].id.replace(/^shipping:/, '');
                arrElements[elemIndex].value = elementValues[fieldName] ? elementValues[fieldName] : '';
                if (fieldName == 'country_id' && shippingForm){
                    shippingForm.elementChildLoad(arrElements[elemIndex]);
                }
            }
        }
    },

    setSameAsBilling: function(flag) {
        $('shipping:same_as_billing').checked = flag;
        if (flag) {
            $('shipping-new-address-form').hide();
            this.syncWithBilling();
            this.save();
        }else{
            $('shipping-new-address-form').show();
        }
    },

    syncWithBilling: function () {
        //$('billing-address-select') && this.newAddress(!$('billing-address-select').value);
        //$('shipping:same_as_billing').checked = true;
        if (!$('billing-address-select') || !$('billing-address-select').value) {
            var arrElements = Form.getElements(this.form);
            for (var elemIndex in arrElements) {
                if (arrElements[elemIndex].id) {
                    var sourceField = $(arrElements[elemIndex].id.replace(/^shipping:/, 'billing:'));
                    if (sourceField){
                        arrElements[elemIndex].value = sourceField.value;
                    }
                }
            }
            //$('shipping:country_id').value = $('billing:country_id').value;
            if (typeof shippingRegionUpdater !== 'undefined') shippingRegionUpdater.update();
            if ($('shipping:region_id') && $('billing:region_id')) $('shipping:region_id').value = $('billing:region_id').value;
            if ($('shipping:region') && $('billing:region_id')) $('shipping:region').value = $('billing:region').value;
            //shippingForm.elementChildLoad($('shipping:country_id'), this.setRegionValue.bind(this));
        } else {
            $('shipping-address-select').value = $('billing-address-select').value;
        }
    },

    setRegionValue: function(){
        $('shipping:region').value = $('billing:region').value;
    },

    save: function(){
        Event.fire(document, 'address:beforeSend');
        new Ajax.Request(this.saveUrl, {
            method: 'post',
            //onComplete: this.onComplete,
            onSuccess: function(transport){ Event.fire(document, 'address:afterSend', {transport:transport})},
            onFailure: function(){ window.location.href = '';},
            parameters: Form.serialize(this.form)
        });
    },

    resetLoadWaiting: function(transport){
        checkout.setLoadWaiting(false);
    },

    validate: function(){
        return this.f.validator.validate();
    }
};

MT.ShippingMethod = Class.create();
MT.ShippingMethod.prototype = {
    initialize: function(form, saveUrl, noMethod){
        this.beforeSend = function(){};
        this.afterSend = function(){};
        this.getLoadIndicator = function(){};
        this.form = form;
        this.main = $(this.form);
        this.noMethod = noMethod;
        this.container = this.main.up();
        this.f = new VarienForm(form);
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){
                this.save();
                Event.stop(event);
            }.bind(this));
        }
        this.saveUrl = saveUrl;
        this.isBusy = false;
        this.initActions();
    },

    setNoMethod: function(flag){
        this.noMethod = flag;
    },

    busy: function(flag){
        this.loading = this.container.down('.loading-indicator');
        if (!this.loading){
            this.loading = this.getLoadIndicator();
            this.loading.hide();
            this.container.insert({top:this.loading});
        }
        this.isBusy = flag;
        if (flag){
            this.loading.show();
            this.main.hide();
        }else{
            this.loading.hide();
            this.main.show();
        }
    },

    update: function(html){
        this.loading.hide();
        this.main.replace(html).show();
    },

    initActions: function(){
        $$('input[type="radio"][name="shipping_method"]').each(function(input){
            Event.observe(input, 'click', function(ev){
                var elm = Event.findElement(ev, 'input');
                if (elm.checked){
                    this.save();
                }
            }.bind(this));
        }, this);
    },

    validate: function() {
        if (this.noMethod) return true;

        var methods = document.getElementsByName('shipping_method');
        if (methods.length==0) {
            alert(Translator.translate('Your order cannot be completed at this time as there is no shipping methods available for it. Please make necessary changes in your shipping address.').stripTags());
            return false;
        }

        if (!this.f.validator.validate()) {
            return false;
        }

        for (var i=0; i<methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        alert(Translator.translate('Please specify shipping method.').stripTags());
        return false;
    },

    save: function(){
        Event.fire(this.container, 'shippingMethod:beforeSend');
        new Ajax.Request(this.saveUrl, {
            method:'post',
            //onComplete: this.onComplete,
            onSuccess: function(transport){ Event.fire(this.container, 'shippingMethod:afterSend', {transport:transport})}.bind(this),
            onFailure: function(){ window.location.href = '';},
            parameters: Form.serialize(this.form)
        });
    }
};

MT.Payment = Class.create();
MT.Payment.prototype = {
    beforeInitFunc: $H({}),
    afterInitFunc: $H({}),
    beforeValidateFunc: $H({}),
    afterValidateFunc: $H({}),
    initialize: function(form, saveUrl){
        this.beforeSend = function(){};
        this.afterSend = function(){};
        this.getLoadIndicator = function(){};
        this.form = form;
        this.container = $(this.form).up('div#checkout-step-payment');
        this.f = new VarienForm(this.form);
        this.saveUrl = saveUrl;
        this.isBusy = false;
    },

    busy: function(flag){
        var loading = this.container.down('.loading-indicator');
        var main = this.container.down('div#co-payment-main');
        if (!loading){
            loading = this.getLoadIndicator();
            loading.hide();
            this.container.insert({top:loading});
        }
        this.isBusy = flag;
        if (flag){
            loading.show();
            main.hide();
        }else{
            loading.hide();
            main.show();
        }
    },

    update: function(html){
        this.container.update(html);
    },

    addBeforeInitFunction : function(code, func) {
        this.beforeInitFunc.set(code, func);
    },

    beforeInit : function() {
        (this.beforeInitFunc).each(function(init){
            (init.value)();;
        });
    },

    init : function(init) {
        this.beforeInit();
        var elements = Form.getElements(this.form);
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){this.save();Event.stop(event);}.bind(this));
        }
        var method = null;
        for (var i=0; i<elements.length; i++) {
            if (elements[i].name=='payment[method]') {
                if (elements[i].checked) {
                    method = elements[i].value;
                }
            } else {
                elements[i].disabled = true;
            }
            elements[i].setAttribute('autocomplete','off');
        }
        if (method) this.switchMethod(method, init);
        this.afterInit();
    },

    addAfterInitFunction : function(code, func) {
        this.afterInitFunc.set(code, func);
    },

    afterInit : function() {
        (this.afterInitFunc).each(function(init){
            (init.value)();
        });
    },

    switchMethod: function(method, init){
        if (this.currentMethod && $('payment_form_'+this.currentMethod)) {
            this.changeVisible(this.currentMethod, true);
            $('payment_form_'+this.currentMethod).fire('payment-method:switched-off', {method_code : this.currentMethod});
        }
        if ($('payment_form_'+method)){
            this.changeVisible(method, false);
            $('payment_form_'+method).fire('payment-method:switched', {method_code : method});
        } else {
            //Event fix for payment methods without form like "Check / Money order"
            document.body.fire('payment-method:switched', {method_code : method});
        }
        if (method) {
            this.lastUsedMethod = method;
            init && this.save();
        }
        this.currentMethod = method;
    },

    changeVisible: function(method, mode) {
        var block = 'payment_form_' + method;
        [block + '_before', block, block + '_after'].each(function(el) {
            var element = $(el);
            if (element) {
                element.style.display = (mode) ? 'none' : '';
                element.select('input', 'select', 'textarea', 'button').each(function(field) {
                    field.disabled = mode;
                });
            }
        });
    },

    addBeforeValidateFunction : function(code, func) {
        this.beforeValidateFunc.set(code, func);
    },

    beforeValidate : function() {
        var validateResult = true;
        var hasValidation = false;
        (this.beforeValidateFunc).each(function(validate){
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        }.bind(this));
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },

    validate: function() {
        if (!this.f.validator.validate()){
            return false;
        }
        var result = this.beforeValidate();
        if (result) {
            return true;
        }
        var methods = document.getElementsByName('payment[method]');
        if (methods.length==0) {
            alert(Translator.translate('Your order cannot be completed at this time as there is no payment methods available for it.').stripTags());
            return false;
        }
        for (var i=0; i<methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        result = this.afterValidate();
        if (result) {
            return true;
        }
        alert(Translator.translate('Please specify payment method.').stripTags());
        return false;
    },

    addAfterValidateFunction : function(code, func) {
        this.afterValidateFunc.set(code, func);
    },

    afterValidate : function() {
        var validateResult = true;
        var hasValidation = false;
        (this.afterValidateFunc).each(function(validate){
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        }.bind(this));
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },

    save: function(){
        Event.fire(document, 'payment:beforeSend');
        new Ajax.Request(this.saveUrl, {
            method: 'post',
            onSuccess: function(transport){ Event.fire(document, 'payment:afterSend', {transport:transport})},
            onFailure: function(){ window.location.href = '';},
            parameters: Form.serialize(this.form)
        });
    }
};

MT.Review = Class.create();
MT.Review.prototype = {
    initialize: function(container, saveUrl, successUrl){
        this.getLoadIndicator = function(){};
        this.container = $(container);
        this.saveUrl = saveUrl;
        this.successUrl = successUrl;
        this.isBusy = false;
    },

    busy: function(flag){
        var loading = this.container.down('.loading-indicator');
        var main = this.container.down('#checkout-review-load');
        if (!loading){
            loading = this.getLoadIndicator();
            loading.hide();
            this.container.insert({top:loading});
        }

        this.isBusy = flag;
        if (flag){
            loading.show();
            main.hide();
        }else{
            loading.hide();
            main.show();
        }
    },

    update: function(html){
        this.container.update(html);
    },

    disableSubmit: function(flag){
        var button = this.container.down('button[type="submit"]');
        if (flag) button && MT.Utils.disableElement(button);
        else button && MT.Utils.enableElement(button);
    },

    toggleSubmit: function(flag){
        var button = this.container.down('button[type="submit"]'),
            text = this.container.down('span#review-please-wait');

        if (flag){
            button.hide();
            text.show();
        }else{
            button.show();
            text.hide();
        }
    },

    isSuccess: false
};

MT.Coupon = Class.create();
MT.Coupon.prototype = {
    initialize: function(form){
        this.form = new VarienForm(form);
        this.container = $(form);
        this.input = this.container.down('#coupon_code');
        this.submitBtn = this.container.down('button#coupon-submit');
        this.cancelBtn = this.container.down('button#coupon-cancel');
        this.initActions();
    },

    initActions: function(){
        this.submitBtn && this.submitBtn.observe('click', function(ev){
            Event.stop(ev);
            if (this.form.validator.validate()){
                this.busy(true);
                new Ajax.Request(this.form.form.action, {
                    parameters: this.form.form.serialize(true),
                    onComplete: function(){
                        this.busy(false);
                    }.bind(this),
                    onSuccess: function(transport){
                        try{
                            var response = transport.responseText.evalJSON();
                            if (response.error){
                                alert(response.msg);
                                this.input.value = '';
                                this.input.focus();
                            }
                            if (response.review) review.update(response.review);
                        }catch (e){}
                    }.bind(this)
                });
            }
        }.bind(this));

        this.cancelBtn && this.cancelBtn.observe('click', function(ev){
            Event.stop(ev);
            $('remove-coupone').value = 1;
            this.busy(true);
            new Ajax.Request(this.form.form.action, {
                parameters: this.form.form.serialize(true),
                onComplete: function(){
                    this.busy(false);
                }.bind(this),
                onSuccess: function(transport){
                    try{
                        var response = transport.responseText.evalJSON();
                        if (response.error) alert(response.msg);
                        else{
                            if (response.review) review.update(response.review);
                        }
                    }catch (e){}
                }
            });
        }.bind(this));
    },

    busy: function(flag){
        var loading = this.container.down('#coupon-loading');
        if (loading){
            if (flag){
                loading.removeClassName('hidden');
                MT.Utils.disableElement(this.submitBtn);
                MT.Utils.disableElement(this.cancelBtn);
            }else{
                loading.addClassName('hidden');
                MT.Utils.enableElement(this.submitBtn);
                MT.Utils.enableElement(this.cancelBtn);
            }
        }
    }
};

MT.Terms = Class.create();
MT.Terms.prototype = {
    initialize: function(form){
        this.f = new VarienForm(form);
        this.initActions();
    },

    initActions: function(){

    },

    showTerm: function(id){
        var term = $('checkout-agreement-' + id);
        term && Dialog.alert(term.innerHTML, {
            id: 'checkout-term-popup',
            width: 400,
            showEffect: Element.show,
            hideEffect: Element.hide,
            buttonClass: 'button'
        });
    },

    validate: function(){
        return this.f.validator.validate();
    }
};