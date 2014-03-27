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
	<div class="blog-item-details">
		<ul class="litst-details">
			<li class="col-sm-1"><i class="fa fa-user"></i> <?php the_author(); ?></li>
			<li class="col-sm-2"><i class="fa fa-clock-o"></i> <?php the_time("d/m/Y") ?></li>
			<li class="col-sm-3"><i class="fa fa-bookmark"></i> <a href="<?php echo $link_first_categories ?>"><?php echo $first_categories ?></a></li>
			<li class="col-sm-1"><i class="fa fa-comment-o"></i> <a href="<?php the_permalink(); ?>/#comment-area"><?php comments_popup_link('0', '1', '%','comment-link'); ?></a></li>
			<li class="col-sm-1">
				<?php if (function_exists( 'lip_love_it_link' )) {
						echo lip_love_it_link(get_the_ID(), '<i class="fa-heart-o"></i>', '<i class="fa-heart"></i>', false);
			
					} ?>
			</li>
		</ul>
		<?php the_excerpt(); ?>
	</div>
</article>