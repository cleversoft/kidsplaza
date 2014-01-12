/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
'use strict';

var billing, billingRegionUpdater, shipping, shippingRegionUpdater, shippingMethod, payment, review, terms,
    coupon, mtOneStepCheckout;

if (window.billingConfig){
    billing = new MT.Billing(billingConfig.id, billingConfig.addressUrl, billingConfig.saveUrl);
    if (billingConfig.useForShipping) billing.useForShipping = true;
}
if (window.billingRegionUpdaterConfig){
    billingRegionUpdater = new RegionUpdater(
        billingRegionUpdaterConfig.countryEl,
        billingRegionUpdaterConfig.regionTextEl,
        billingRegionUpdaterConfig.regionSelectEl,
        billingRegionUpdaterConfig.regions,
        billingRegionUpdaterConfig.disableAction,
        billingRegionUpdaterConfig.zipEl
    );
}
if (window.shippingConfig){
    shipping = new MT.Shipping(shippingConfig.id, shippingConfig.addressUrl, shippingConfig.saveUrl);
}
if (window.shippingRegionUpdaterConfig){
    shippingRegionUpdater = new RegionUpdater(
        shippingRegionUpdaterConfig.countryEl,
        shippingRegionUpdaterConfig.regionTextEl,
        shippingRegionUpdaterConfig.regionSelectEl,
        shippingRegionUpdaterConfig.regions,
        shippingRegionUpdaterConfig.disableAction,
        shippingRegionUpdaterConfig.zipEl
    );
}
if (window.shippingMethodConfig){
    shippingMethod = new MT.ShippingMethod(shippingMethodConfig.id, shippingMethodConfig.saveUrl);
}
if (window.paymentConfig){
    payment = new MT.Payment(paymentConfig.id, paymentConfig.saveUrl);
}
if (window.reviewConfig){
    review = new MT.Review(reviewConfig.id, reviewConfig.saveUrl, reviewConfig.successUrl);
}
if (window.termsConfig){
    terms = new MT.Terms(termsConfig.id);
}
if (window.couponConfig){
    coupon = new MT.Coupon(couponConfig.id);
}
mtOneStepCheckout = new MT.OneStepCheckout(Object.extend(checkoutConfig, {
    billing: billing,
    shipping: shipping,
    shippingMethod: shippingMethod,
    payment: payment,
    review: review,
    terms: terms
}));