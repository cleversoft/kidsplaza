jQuery(document).ready(function($){
    $.fn.extend({
        scrollToMe: function () {
            var x = jQuery(this).offset().top - 100;
            $('html,body').animate({scrollTop: x}, 500);
        }
    });
    var state = false,
        showComment = function (e) {
            $(e).addClass("active");
            var main = $(e).attr('data-form');
            $('#'+main).show();
        },
        hideComment = function(e) {
            $(e).removeClass("active");
            var main = $(e).attr('data-form');
            $('#'+main).hide();
        };

    $(".btn-reply").click(function(e){
        e.preventDefault();
        if(!state){
            showComment(this);
        } else {
            hideComment(this);
        }
        state = !state;
    });
    $(".cancel-reply").click(function(){
        $(this).parents('.comment-box').hide();
        state = false;
    });

    $('.rating-links a.rating-reviews').click(function(){
        $('#customer-reviews').scrollToMe();
        return false;
    });
    $('.rating-links a.rating-form, .no-rating a, button.rating-form').click(function(){
        $('#review-form').scrollToMe();
        return false;
    });
    $('.rating-type-item').each(function(i){
        if(i>0){
            $('.'+$(this).attr('class')+' input.rating-star').rating({
                callback: function(value, link){
                    $(this).attr('checked',true);
                }
            });
        }
    });
    $('.rating-cancel a').on('click',function(){
        var inputs = $(this).parents('.rating-type-item').find('input');
        inputs.attr("checked", false);
    });
});

if(jQuery('#review-form').length){
    var dataForm = new VarienForm('review-form');
    Validation.addAllThese(
        [
            ['validate-rating', reviewFrom.msg, function(v) {
                var trs = $('product-review-table').select('div.rating-type-item');
                var inputs;
                var error = 1;

                for( var j=0; j < trs.length; j++ ) {
                    var tr = trs[j];
                    if( j > 0 ) {
                        inputs = tr.select('input');

                        for( i in inputs ) {
                            if( inputs[i].checked == true ) {
                                error = 0;
                            }
                        }

                        if( error == 1 ) {
                            return false;
                        } else {
                            error = 1;
                        }
                    }
                }
                return true;
            }]
        ]
    );
}

function voteReview(e){
    var url = jQuery(e).attr('href');
    var loading = jQuery('<div/>').addClass('loading');
    var mainHelpful = jQuery(e).parent();
    mainHelpful.html('');
    loading.appendTo(mainHelpful);
    data = '&isAjax=1';
    try {
        jQuery.ajax( {
            url : url,
            dataType : 'json',
            data: data,
            type: 'post',
            success : function(data) {
                if(data.status == 'success'){
                    total = data.total.match(/\d+/);
                    if(total[0] == 1){
                        loading.remove();
                        mainHelpful.html(data.message);
                    }else{
                        mainHelpful.html('<span class="total-helpfull">'+data.total+'</span>');
                    }
                }
            }
        });
    } catch (e) {

    }
    return false;
}
function reportReview(e){
    var url = jQuery(e).attr('href');
    var loading = jQuery('<div/>').addClass('loading');
    jQuery(e).hide();
    loading.appendTo(jQuery(e).parent());
    data = '&isAjax=1';
    try {
        jQuery.ajax( {
            url : url,
            dataType : 'json',
            data: data,
            type: 'post',
            success : function(data) {
                if(data.status == 'success'){
                    jQuery(e).parent().html('<span class="mt-reported">'+data.message+'</span>');
                }
            }
        });
    } catch (e) {
    }
    return false;
}
function commentReview(event,reviewId){
    var content = jQuery("#comment_detail_"+reviewId).val();
    var url = Comment.link+'reviewId/'+reviewId;
    var main = jQuery(event).parents('.mt-review-footer').find('.main-comments');
    if(content){
        jQuery(event).prev().show();
        data = 'content='+content+'&isAjax=1';
        try {
            jQuery.ajax( {
                url : url,
                dataType : 'json',
                data: data,
                type: 'post',
                success : function(data) {
                    if(data.status=='success'){
                        jQuery("#commnet_field_"+reviewId).hide();
                        jQuery("#comment_detail_"+reviewId).val('');
                        jQuery(event).prev().hide();
                        item = jQuery("<div/>").html('<strong>'+data.customer+'</strong><p>'+data.content+'</p>')
                                        .addClass(".item-comment");
                        jQuery(main).prepend(jQuery(item));
                    }
                }
            });
        } catch (e) {
        }
    }
}