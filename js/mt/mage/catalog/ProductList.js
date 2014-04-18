(function($){
    initCountDown();
})(jQuery);
function initCountDown(){
    jQuery(".groupon-timedown").countdown({
        dataAttr: "cdate",
        leadingZero: false,
        yearText: dataTranslate.year,
        monthText: dataTranslate.month,
        weekText: dataTranslate.weeks,
        dayText: dataTranslate.day,
        daySingularText: dataTranslate.day,
        hourSingularText: ':',
        minSingularText: ':',
        hourText: ':',
        minText: ':',
        secText: ''
    });
}
jQuery(window).load(function(){
    setGridItemsEqualHeight();
});
function setGridItemsEqualHeight(){
    var winWidth = jQuery(window).width();
    if (winWidth >= 350)
    {
        jQuery('.show-grid').removeClass("auto-height");
        var gridItemMaxHeight = 0;
        jQuery('.show-grid > .item').each(function() {
            jQuery(this).css("height", "auto");
            gridItemMaxHeight = Math.max(gridItemMaxHeight, jQuery(this).height());
        });
        jQuery('.show-grid > .item').css("height", gridItemMaxHeight + "px");
    }
        else
    {
        jQuery('.show-grid').addClass("auto-height");
        jQuery('.show-grid > .item').css("height", "auto");
        jQuery('.show-grid > .item').css("padding-bottom", "20px");
    }
}
