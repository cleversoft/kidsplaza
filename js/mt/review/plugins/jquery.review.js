jQuery(document).ready(function($){
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

});
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