 <?php get_header(); ?>
	<div class="breadcrumbs col-lg-12">
	    <?php if(function_exists('bcn_display'))
	    {
	        bcn_display();
	    }?>
	</div>
	<section class="col-md-9">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<article class="post-blog">
				<?php 
					$post_categories_first = get_the_category();
					$first_categories = $post_categories_first[0]->name;
					$link_first_categories = get_category_link( $post_categories_first[0]->term_id );
				?>
				<a href="<?php the_permalink(); ?>"><h1><?php the_title(); ?></h1></a>
				<div class="blog-item-details-single">
					<ul class="litst-details">
						<li class="col-md-1 col-sm-2 col-xs-4"><i class="fa fa-user"></i> <?php the_author(); ?></li>
						<li class="col-md-3 col-sm-4 col-xs-8"><i class="fa fa-bookmark"></i> <a href="<?php echo $link_first_categories ?>"><?php echo $first_categories ?></a></li>
						<li class="col-md-2 col-sm-3 col-xs-6 no-pading-left-phone"><i class="fa fa-clock-o"></i> <?php the_time("d/m/Y") ?></li>
						<li class="col-md-1 col-sm-2 col-xs-3"><i class="fa fa-comment-o"></i> <a href="<?php the_permalink(); ?>/#comment">
						<?php
						    $url = get_permalink();
						    $urlfb='http://api.ak.facebook.com/restserver.php?v=1.0&method=links.getStats&urls=' . urlencode($url) . '&format=json';
						    $content=@file_get_contents($urlfb);
						    if ($content) {
						        $likeInfo=json_decode($content);
						        echo '' . $likeInfo[0]->comment_count;
						    } else {
						        echo '0';
						    }
						?>
						</a></li>
						<li class="col-md-1 col-sm-1 col-xs-3">
							<?php if (function_exists( 'lip_love_it_link' )) {
									echo lip_love_it_link(get_the_ID(), '<i class="fa-heart-o"></i>', '<i class="fa-heart"></i>', false);
								} ?>
						</li>
					</ul>
					<div class="content-blog">
						<?php the_content(); ?>
					</div>
					<div class="share">
						<p class="col-lg-1 col-sm-2">Share</p>
						<!-- AddThis Button BEGIN -->
						<div class="addthis_toolbox addthis_default_style col-lg-11 col-sm-10">
							<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
							<a class="addthis_button_facebook_send"></a>
							<a class="addthis_button_google_plusone" g:plusone:size="medium"></a> 
							<a class="addthis_button_tweet"></a>
							<a class="addthis_button_pinterest_pinit" pi:pinit:layout="horizontal"></a>
							<a class="addthis_counter addthis_pill_style"></a>
						</div>
						<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
						<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-533233bf3d1ea5a0"></script>
						<!-- AddThis Button END -->
					</div>
				</div>
			</article>
		<?php endwhile; endif; ?>
			<div class="related">
				<?php
					$related = get_posts(array('category__in' => wp_get_post_categories($post->ID), 'numberposts' => 5, 'post__not_in' => array($post->ID)));
					if( $related ) foreach( $related as $post ) {
					setup_postdata($post);
					get_template_part( 'includes/loop-min', 'single' ); }
					wp_reset_postdata();
				?>
			</div>
			<div class="comment" id="comment">
				<div class="fb-comments" data-href="<?php the_permalink(); ?>" data-width="800" data-numposts="5" data-colorscheme="light">
			</div>
			<script type="text/javascript">
				/* resize facebook comments */
				(function(window){
				    var dh = null;
				    $(window).on("resize",function(){
				        if ( dh ) {
				            clearTimeout(dh);
				        }
				        dh = setTimeout(function(){
				            var $fbc = $(".fb-comments");
				            var $stc = $(".comment");
				            dh = null;
				            if ( $fbc.attr("data-width") != $stc.width() ) {
				                $stc.css({height:$stc.height()});
				                $fbc.attr("data-width", $stc.width());
				                FB.XFBML.parse($stc[0],function(){
				                    $stc.css({height:'auto'});
				                });
				            }
				        },300);
				    }).trigger("resize");
				})(this);
			</script>
	</section>
	<aside class="col-md-3">
		<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar')): endif;?>
	</aside>
	</div>
</div>
<?php get_footer(); ?>