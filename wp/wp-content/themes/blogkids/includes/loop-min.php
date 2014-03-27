<article class="post-related">
	<?php 
		$post_categories_first = get_the_category();
		$first_categories = $post_categories_first[0]->name;
		$link_first_categories = get_category_link( $post_categories_first[0]->term_id );
		$thumb_image = get_post_thumbnail_id();
		$thumb_img_url = wp_get_attachment_url( $thumb_image, 'widget-image' );
		$image = aq_resize( $thumb_img_url, 50, 50, true, false);
	?>
	<a href="<?php the_permalink(); ?>">
		<img class="img-responsive" src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" />
		<h2><?php the_title(); ?></h2>
	</a>
	<div class="blog-item-details">
		<ul class="litst-details">
			<li class="col-sm-3"><i class="fa fa-calendar"></i> <?php the_time("d/m/Y - H:i a") ?></li>
			<li class="col-sm-2"><i class="fa fa-comments"></i> <a href="<?php the_permalink(); ?>/#comment-area"><?php comments_popup_link('0', '1', '%','comment-link'); ?></a> Comment</li>
		</ul>
	</div>
</article>