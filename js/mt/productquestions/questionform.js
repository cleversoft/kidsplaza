var dataForm = new VarienForm('mt-question-form');
jQuery("#mt-question-form button").click(function(){
    clearTimeout(timeout);
    var timeout = setTimeout(function(){
        var main = jQuery("#advice-required-entry-cptch_input").parent();
        main.find('.validation-advice').each(function(){
            clone = jQuery(this).clone();
            jQuery(this).remove();
            jQuery(clone).css('opacity',1).appendTo(main);
        });
    },40);
});