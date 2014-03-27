<article class="post-blog">
	<?php 
		$post_categories_first = get_the_category();
		$first_categories = $post_categories_first[0]->name;
		$link_first_categories = get_category_link( $post_categories_first[0]->term_id );
		$thumb_image = get_post_thumbnail_id();
		$thumb_img_url = wp_get_attachment_url( $thumb_image, 'widget-image' );
		$image = aq_resize( $thumb_img_url, 870, 300, true, false);
	?>
	<figure class="animated-overlay overlay-alt">
		<img width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" alt="pacific_rim_fight_scene-wallpaper" src="<?php echo $image[0]; ?>" itemprop="image">
		<a class="link-to-post" href="http://192.168.1.203/zt_zone/designs-by-mother-nature-biomimetic-products/"></a>
		<figcaption>
		<div class="thumb-info thumb-info-alt">
		<i>+</i>
		</div>
	</figcaption>
</figure>
	<a href="<?php the_permalink(); ?>"><h2><?php the_title(); ?></h2></a>
	<div class="blog-item-details">
		<ul class="litst-details">
			<li class="col-md-1 col-sm-2 col-xs-4"><i class="fa fa-user"></i> <?php the_author(); ?></li>
			<li class="col-md-2 col-sm-3 col-xs-4"><i class="fa fa-clock-o"></i> <?php the_time("d/m/Y") ?></li>
			<li class="col-md-3 col-sm-3 col-xs-4"><i class="fa fa-bookmark"></i> <a href="<?php echo $link_first_categories ?>"><?php echo $first_categories ?></a></li>
			<li class="col-md-1 col-sm-2 col-xs-4"><i class="fa fa-comment-o"></i> <a href="<?php the_permalink(); ?>/#comment">
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
			    </a>
			</li>
			<li class="col-md-1 col-sm-2 col-xs-4">
				<?php if (function_exists( 'lip_love_it_link' )) {
						echo lip_love_it_link(get_the_ID(), '<i class="fa-heart-o"></i>', '<i class="fa-heart"></i>', false);
			
					} ?>
			</li>
		</ul>
		<?php the_excerpt(); ?>
	</div>
</article>