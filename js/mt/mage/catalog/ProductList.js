(function($){
    $(".groupon-timedown").countdown({
        dataAttr: "cdate",
        leadingZero: false,
        yearText: dataTranslate.year,
        monthText: dataTranslate.month,
        weekText: dataTranslate.weeks,
        dayText: dataTranslate.day,
        hourText: ':',
        minText: ':',
        secText: ''
    });
})(jQuery);