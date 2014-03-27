<?php
add_action('widgets_init', 'widget_view_post');

function widget_view_post()
{
	register_widget('view_post_widget');
}

class view_post_widget extends WP_Widget {
	
	function view_post_widget()
	{
		$widget_ops = array('classname' => 'view_post_widget', 'description' => 'Kéo thả để tạo mục danh sách tin.');

		$control_ops = array('id_base' => 'view_post_widget');

		$this->WP_Widget('view_post_widget', 'Danh sách tin', $widget_ops, $control_ops);
	}
	
	function widget($args, $instance)
	{
		global $post;
		
		extract($args);
		
		$title = $instance['title'];
		$categories = $instance['categories'];
		$post = $instance['post'];
		echo $before_widget;
		?>
		<h3><?php echo $title; ?></h3>
			
			<?php
			$recent_posts = new WP_Query(array(
				'showposts' => $post,
				'cat' => $categories,
			));
			$count = 0;
			while($recent_posts->have_posts()): $recent_posts->the_post();
				$count++;
				$class = "";
				if ($count % 2 == 0 ) {
                 	$class = "bg-2";
                }
				$thumb_image = get_post_thumbnail_id();
				$thumb_img_url = wp_get_attachment_url( $thumb_image, 'widget-image' );
				$image = aq_resize( $thumb_img_url, 50, 50, true, false);
			?>
			<article class="post-view <?php echo $class; ?>">
				<a href="<?php the_permalink(); ?>">
					<img src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" />
					<h2><?php the_title(); ?></h2>
				</a>
			</article>
			<?php endwhile; ?>
		<?php
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		
		$instance['title'] = $new_instance['title'];
		$instance['categories'] = $new_instance['categories'];
		$instance['post'] = $new_instance['post'];
		return $instance;
	}

	function form($instance)
	{
		$defaults = array('title' => 'Tiêu đề mục', 'categories' => 'all');
		$instance = wp_parse_args((array) $instance, $defaults); ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('categories'); ?>">Hiển thị bài viết trong:</label> 
			<select id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" class="widefat categories" style="width:100%;">
				<option value='all' <?php if ('all' == $instance['categories']) echo 'selected="selected"'; ?>>tất cả các mục</option>
				<?php $categories = get_categories('hide_empty=0&depth=1&type=post'); ?>
				<?php foreach($categories as $category) { ?>
				<option value='<?php echo $category->term_id; ?>' <?php if ($category->term_id == $instance['categories']) echo 'selected="selected"'; ?>><?php echo $category->cat_name; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('post'); ?>">Số bài viết:</label>
			<input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('post'); ?>" name="<?php echo $this->get_field_name('post'); ?>" value="<?php echo $instance['post']; ?>" />
		</p>
	<?php }
}
?>