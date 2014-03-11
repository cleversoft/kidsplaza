var contactForm = new VarienForm('postComment', false);
jQuery(document).ready(function(){
    jQuery('.main-post button.btn-load-more').on('click',function(e){
        callMoreFunc(jQuery(this));
    });
    jQuery('.post-view-comments button.btn-load-more').on('click',function(e){
        callMoreCommentFunc(jQuery(this));
    });
    jQuery('textarea#comment').on('click',function(e){
        jQuery('.main-input-box').show();
    });
    var checkShowMoreBlog = jQuery('.main-post .toolbar-pages').find('a.i-next').length;
    if(checkShowMoreBlog > 0){
        jQuery('.main-post button.btn-load-more').show();
    }
    var checkShowMorePost = jQuery('.post-view-comments .toolbar-pages').find('a.i-next').length;
    if(checkShowMorePost > 0){
        jQuery('.post-view-comments button.btn-load-more').show();
    }
});
function sendLoadMorePosts(url)
{
    jQuery.ajax( {
        url : url,
        success : function(data) {
            var items = jQuery(data).find('.main-post ul.blog-grid');
            var mainPosts = jQuery('.grid-view');
            items.each(function(i,el){
                jQuery(el).appendTo(jQuery(mainPosts));
            });
            jQuery('.main-post .loading').css('visibility','hidden');
            if(!jQuery(data).find('.next.i-next')[0]){
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


function sendLoadMoreComments(url)
{
    jQuery.ajax( {
        url : url,
        success : function(data) {
            var items = jQuery(data).find('.post-view-comments li.item-list');
            var mainComments = jQuery('.comments-list');
            items.each(function(i,el){
                jQuery(el).appendTo(jQuery(mainComments));
            });
            jQuery('.post-view-comments .loading').css('visibility','hidden');
            if(!jQuery(data).find('.next.i-next')[0]){
                jQuery('.post-view-comments button.btn-load-more').hide();
            }else{
                jQuery('.post-view-comments button.btn-load-more').show();
                jQuery('.next.i-next').attr('href', jQuery(data).find('.next.i-next').attr('href'));
            }
        }
    });
}

function callMoreCommentFunc(e)
{
    if(jQuery('.next.i-next')[0]){
        var nextPageUrl = jQuery('.next.i-next').attr("href");
        jQuery('.post-view-comments button.btn-load-more').hide();
        jQuery('.post-view-comments .loading').css('visibility','visible');
        sendLoadMoreComments(nextPageUrl);
    }
    else{
        jQuery('.post-view-comments button.btn-load-more').hide();
    }
}
