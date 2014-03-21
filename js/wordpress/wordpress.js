var contactForm = new VarienForm('postComment', false);
jQuery(document).ready(function(){
    jQuery('.main-post button.btn-load-more').on('click',function(e){
        callMoreFunc(jQuery(this));
    });
    var checkShowMoreBlog = jQuery('.main-post .pager').find('a.i-next').length;
    if(checkShowMoreBlog > 0){
        jQuery('.main-post button.btn-load-more').show();
    }
});
function sendLoadMorePosts(url)
{
    jQuery.ajax( {
        url : url,
        success : function(data) {
            var items = jQuery(data).find('.main-post ul#post-list li.item');
            var mainPosts = jQuery('#post-list');
            items.each(function(i,el){
                jQuery(el).appendTo(jQuery(mainPosts));
            });
            jQuery('.main-post .loading').css('visibility','hidden');
            if(jQuery(data).find('.next.disable.i-next')[0]){
                jQuery('.main-post button.btn-load-more').hide();
            }else{
                jQuery('.main-post button.btn-load-more').show();
                jQuery('.next.i-next').attr('href', jQuery(data).find('.next.i-next').attr('href'));
            }
        }
    });
}

function callMoreFunc(e)
{
    if(jQuery('.next.i-next')[0]){
        var nextPageUrl = jQuery('.next.i-next').attr("href");
        jQuery('.main-post button.btn-load-more').hide();
        jQuery('.main-post .loading').css('visibility','visible');
        sendLoadMorePosts(nextPageUrl);
    }
    else{
        jQuery('.main-post button.btn-load-more').hide();
    }
}
