<?php
add_action('widgets_init', 'bione_mini_box_load_widgets');
function bione_mini_box_load_widgets(){ register_widget('bione_mini_box_Widget'); }
class bione_mini_box_Widget extends WP_Widget {

	function bione_mini_box_Widget()
	{
		$widget_ops = array('classname' => 'bione_mini_box', 'description' => 'Kéo thả để tạo mục bài mới với khả năng tùy chỉnh đa dạng');
		$control_ops = array('id_base' => 'bione_mini_box-widget');
		$this->WP_Widget('bione_mini_box-widget', 'Bione Mini Box', $widget_ops, $control_ops);
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

    <?php $recent_posts = new WP_Query(array( 'showposts' => $postnum, 'cat' => $categories, ));
	while($recent_posts->have_posts()): $recent_posts->the_post();
	get_template_part( 'includes/templates/mini-loop');
	endwhile; ?>

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