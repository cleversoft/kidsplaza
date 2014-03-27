<?php
add_action('widgets_init', 'bione_box_prefix_load_widgets');
function bione_box_prefix_load_widgets(){ register_widget('bione_box_prefix_Widget'); }
class bione_box_prefix_Widget extends WP_Widget {

	function bione_box_prefix_Widget()
	{
		$widget_ops = array('classname' => 'bione_box_prefix', 'description' => 'Kéo thả để tạo mục bài mới với khả năng tùy chỉnh đa dạng');
		$control_ops = array('id_base' => 'bione_box_prefix-widget');
		$this->WP_Widget('bione_box_prefix-widget', 'Bione Box (có tiền tố)', $widget_ops, $control_ops);
	}
	
function widget($args, $instance)
{
global $post;
extract($args);
$title = $instance['title'];
$categories = $instance['categories'];
$postnum = $instance['postnum'];
echo $before_widget; ?>
    <span class="title"><?php echo $title; ?></span>

    <?php $recent_posts = new WP_Query(array( 'showposts' => $postnum, 'cat' => $categories, )); ?>
	<?php while($recent_posts->have_posts()): $recent_posts->the_post(); ?>
	<?php if(has_post_format('image')): ?>
	<article class="format-image">
		<?php $time = get_post_time('G', true, $post);
		$newtime = time() - $time;
		if ( $newtime < 259200 ): ?>
		<span class="ribbon_new"></span>
    	<?php endif; ?>
    	<a href="<?php the_permalink(); ?>" title="<?php get_the_title(); ?>">
    	<img src="<?php echo first_image(); ?>" alt="<?php the_title(); ?>"></a>
    	<h2><span class="prefix"><?php global $post; $category = get_the_category($post->ID); echo $category[0]->name; ?></span> <a href="<?php the_permalink(); ?>" class="post_title" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
		<span class="icon_view"><?php echo get_bbit_views(get_the_ID()); ?></span>
    	<?php global $data; if ( $data['bbit_show_comments'] == 1 ): ?>
		<span class="icon_comment"><?php comments_number(__('0'), __('1'), __('%'));?></span>
    	<?php endif; ?>
	</article>   
	<?php else: ?>
	<article>
		<?php $time = get_post_time('G', true, $post);
		$newtime = time() - $time;
		if ( $newtime < 259200 ): ?>
		<span class="ribbon_new"></span>
    	<?php endif; ?>
    	<a href="<?php the_permalink(); ?>" title="<?php get_the_title(); ?>">
    	<?php the_post_thumbnail('image', array('class' => 'photo', 'alt' => get_the_title())); ?></a>
    	<h2><span class="prefix"><?php global $post; $category = get_the_category($post->ID); echo $category[0]->name; ?></span> <a href="<?php the_permalink(); ?>" class="post_title" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
    	<p><?php echo bbit_string_limit(get_the_excerpt(), 47); ?>...</p>
		<?php if(get_post_meta($post->ID, 'bbit_link_download', true)): ?>
    	<span class="icon_download"><a href="<?php echo get_post_meta($post->ID, 'bbit_link_download', true); ?>" rel="nofollow">Tải về</a></span>
    	<?php endif; ?>
		<span class="icon_view"><?php echo get_bbit_views(get_the_ID()); ?></span>
    	<?php global $data; if ( $data['bbit_show_comments'] == 1 ): ?>
		<span class="icon_comment"><?php comments_number(__('0'), __('1'), __('%'));?></span>
    	<?php endif; ?>
	</article>      
	<?php endif; ?>
	<?php endwhile; ?>

    <div class="pad center"><a href="<?php echo get_category_link($categories); ?>">Xem thêm</a></div>							
<?php
echo $after_widget;
}
	
function update($new_instance, $old_instance)
{
$instance = $old_instance;
$instance['title'] = $new_instance['title'];
$instance['categories'] = $new_instance['categories'];
$instance['postnum'] = $new_instance['postnum'];
return $instance;
}

function form($instance)
{
$defaults = array('title' => 'Tiêu đề mục', 'categories' => 'all', 'postnum' => 5);
$instance = wp_parse_args((array) $instance, $defaults); ?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>">Tiêu đề:</label>
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
		<label for="<?php echo $this->get_field_id('postnum'); ?>">Số bài hiển thị:</label>
		<input class="widefat" style="width: 30px;" id="<?php echo $this->get_field_id('postnum'); ?>" name="<?php echo $this->get_field_name('postnum'); ?>" value="<?php echo $instance['postnum']; ?>" />
	</p>
<?php }} ?>