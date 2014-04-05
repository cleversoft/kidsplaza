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
