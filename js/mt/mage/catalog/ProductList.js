(function($){
    $(".groupon-timedown").countdown({
        dataAttr: "cdate",
        leadingZero: true,
        yearText: dataTranslate.year,
        monthText: dataTranslate.month,
        weekText: dataTranslate.weeks,
        dayText: dataTranslate.day,
        hourText: ':',
        minText: ':',
        secText: ''
    });
})(jQuery);