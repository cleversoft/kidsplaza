<?php get_header(); ?>
	<div class="breadcrumbs col-lg-12">
	    <?php if(function_exists('bcn_display'))
	    {
	        bcn_display();
	    }?>
	</div>
	<section class="col-sm-9">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<article class="post-blog">
				<?php 
					$post_categories_first = get_the_category();
					$first_categories = $post_categories_first[0]->name;
					$link_first_categories = get_category_link( $post_categories_first[0]->term_id );
					$thumb_image = get_post_thumbnail_id();
					$thumb_img_url = wp_get_attachment_url( $thumb_image, 'widget-image' );
					$image = aq_resize( $thumb_img_url, 870, 300, true, false);
				?>
				<img class="img-responsive thumbnails" src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" />
				<figcaption>
					<div class="thumb-info thumb-info-alt">
						<i class="ss-navigateright"></i>
					</div>
				</figcaption>
				<a href="<?php the_permalink(); ?>"><h2><?php the_title(); ?></h2></a>
				<div class="blog-item-details-single">
					<ul class="litst-details">
						<li class="col-sm-1"><i class="fa fa-user"></i> <?php the_author(); ?></li>
						<li class="col-sm-3"><i class="fa fa-bookmark"></i> <a href="<?php echo $link_first_categories ?>"><?php echo $first_categories ?></a></li>
						<li class="col-sm-2"><i class="fa fa-clock-o"></i> <?php the_time("d/m/Y") ?></li>
						<li class="col-sm-1"><i class="fa fa-comment-o"></i> <a href="<?php the_permalink(); ?>/#comment-area"><?php comments_popup_link('0', '1', '%','comment-link'); ?></a></li>
						<li class="col-sm-1">
							<?php if (function_exists( 'lip_love_it_link' )) {
									echo lip_love_it_link(get_the_ID(), '<i class="fa-heart-o"></i>', '<i class="fa-heart"></i>', false);
						
								} ?>
						</li>
					</ul>
					<div class="content-blog">
						<?php the_content(); ?>
					</div>
					<div class="share">
						<p class="col-lg-1 col-md-2">Share</p>
						<!-- AddThis Button BEGIN -->
						<div class="addthis_toolbox addthis_default_style col-lg-11 col-md-10">
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
			<div class="comment">
				<div class="fb-comments" data-href="<?php the_permalink(); ?>" data-width="800" data-numposts="5" data-colorscheme="light">
			</div>
	</section>
	<aside class="col-sm-3">
		<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar')): endif;?>
	</aside>
	</div>
</div>
<?php get_footer(); ?>