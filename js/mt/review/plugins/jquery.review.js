jQuery(document).ready(function($){
    $.fn.extend({
        scrollToMe: function () {
            var x = jQuery(this).offset().top - 100;
            $('html,body').animate({scrollTop: x}, 500);
        }
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
    $('#customer-reviews button.btn-load-more').on('click',function(e){
        callbackFunc($(this));
    });
    $('#review_field').on('click',function(){
        $(this).next().show();
    });
});
function showComment(e) {
    jQuery(e).addClass("active");
    var main = jQuery(e).attr('data-form');
    jQuery('#'+main).show();
}
function hideComment(e) {
    jQuery(e).removeClass("active");
    var main = jQuery(e).attr('data-form');
    console.log(main);
    jQuery('#'+main).hide();
}
function sendLoadMoreRequest(url)
{
    jQuery.ajax( {
        url : url,
        success : function(data) {
            var items = jQuery(data).find('#customer-reviews li.item');
            var mainReview = jQuery('#customer-reviews ul.item_reviews');
            items.each(function(i,el){
                jQuery(el).appendTo(jQuery(mainReview));
            });
            jQuery('#customer-reviews .loading').css('visibility','hidden');
            if(!jQuery(data).find('.next.i-next')[0]){
                jQuery('#customer-reviews button.btn-load-more').hide();
            }else{
                jQuery('#customer-reviews button.btn-load-more').show();
                jQuery('.next.i-next').attr('href', jQuery(data).find('.next.i-next').attr('href'));
            }
        }
    });
}

function callbackFunc(e)
{
    if(jQuery('.next.i-next')[0]){
        var nextPageUrl = jQuery('.next.i-next').attr("href");
        jQuery('#customer-reviews button.btn-load-more').hide();
        jQuery('#customer-reviews .loading').css('visibility','visible');
        sendLoadMoreRequest(nextPageUrl);
    }
    else{
        jQuery('#customer-reviews button.btn-load-more').hide();
    }
}

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
    var main = jQuery(event).parents('.mt-review-footer').find('.comments-list');
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

                        var li = jQuery("<li/>").addClass('media');
                        jQuery("<span/>").addClass('img pull-left').html('<img src="'+dataComment.pathUrl+'"/>').appendTo(jQuery(li));
                        var mediaBody = jQuery("<div/>").addClass('media-body').appendTo(jQuery(li));
                        var spanMainReview = jQuery("<span/>").addClass("main-review").appendTo(jQuery(mediaBody));
                        jQuery("<span/>").addClass("created_by")
                                            .html('<span class="created">'+data.customer+'</span><small class="date"> '+data.date+'</small>')
                                            .appendTo(jQuery(spanMainReview));
                        jQuery("<span/>").addClass("review-detail").html(data.content).appendTo(jQuery(mediaBody));
                        jQuery(main).prepend(jQuery(li));
                    }
                }
            });
        } catch (e) {
        }
    }
}
function showMoreComments(el,reviewId)
{
    var url = dataComment.moreUrl+'review/'+reviewId;
    var main = jQuery(el).parents('.mt-review-footer').find('.comments-list');
    var page = jQuery('#curent_page_review_'+reviewId);
    var pagesize = jQuery(main).find('li.media').length;
    var more = jQuery(el).parent();
    jQuery(el).hide();
    jQuery(el).prev().css('visibility','visible');
    data = '&pagesize='+pagesize+'&isAjax=1';
    try {
        jQuery.ajax( {
            url : url,
            dataType : 'json',
            data: data,
            type: 'post',
            success : function(data) {
                if(data.page){
                    jQuery(page).val(data.page)
                    jQuery(el).prev().css('visibility','hidden');
                    jQuery(el).show()
                    jQuery(data.output).not('.comments-more').each(function(){
                        jQuery(more).before(jQuery(this));
                    });
                }else{
                    jQuery(more).hide();
                }
            }
        });
    } catch (e) {
    }
}