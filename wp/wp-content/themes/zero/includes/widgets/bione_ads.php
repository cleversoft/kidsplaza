<?php
add_action('widgets_init', 'bione_ads_load_widgets');
function bione_ads_load_widgets(){ register_widget('bione_ads_Widget'); }
class bione_ads_Widget extends WP_Widget {

	function bione_ads_Widget()
	{
		$widget_ops = array('classname' => 'bione_ads', 'description' => 'Kéo thả để tạo mục bài mới với khả năng tùy chỉnh đa dạng');
		$control_ops = array('id_base' => 'bione_ads-widget');
		$this->WP_Widget('bione_ads-widget', 'Bione Box Ads', $widget_ops, $control_ops);
	}
	
function widget($args, $instance)
{
global $post;
extract($args);
$ads = $instance['ads'];
$ads2 = $instance['ads2'];
$ads3 = $instance['ads3'];
$ads4 = $instance['ads4'];
$ads5 = $instance['ads5'];
echo $before_widget; ?>
<?php if ($ads): ?>
    <span class="title">Sự kiện</span>

    <?php if ($ads): ?><article><?php echo $ads; ?></article><?php endif; ?>
    <?php if ($ads2): ?><article><?php echo $ads2; ?></article><?php endif; ?>
    <?php if ($ads3): ?><article><?php echo $ads3; ?></article><?php endif; ?>
    <?php if ($ads4): ?><article><?php echo $ads4; ?></article><?php endif; ?>
    <?php if ($ads5): ?><article><?php echo $ads5; ?></article><?php endif; ?>
<?php endif; ?>
<?php
echo $after_widget;
}
	
function update($new_instance, $old_instance)
{
$instance = $old_instance;
$instance['ads'] = $new_instance['ads'];
$instance['ads2'] = $new_instance['ads2'];
$instance['ads3'] = $new_instance['ads3'];
$instance['ads4'] = $new_instance['ads4'];
$instance['ads5'] = $new_instance['ads5'];
return $instance;
}

function form($instance)
{
$defaults = array('ads' => 'Tiêu đề mục','ads2' => 'Tiêu đề mục','ads3' => 'Tiêu đề mục','ads4' => 'Tiêu đề mục','ads5' => 'Tiêu đề mục');
$instance = wp_parse_args((array) $instance, $defaults); ?>
	<p>
		<label for="<?php echo $this->get_field_id('ads'); ?>">Tiêu đề:</label>
		<input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('ads'); ?>" name="<?php echo $this->get_field_name('ads'); ?>" value="<?php echo $instance['ads']; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('ads2'); ?>">Tiêu đề:</label>
		<input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('ads2'); ?>" name="<?php echo $this->get_field_name('ads2'); ?>" value="<?php echo $instance['ads2']; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('ads3'); ?>">Tiêu đề:</label>
		<input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('ads3'); ?>" name="<?php echo $this->get_field_name('ads3'); ?>" value="<?php echo $instance['ads3']; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('ads4'); ?>">Tiêu đề:</label>
		<input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('ads4'); ?>" name="<?php echo $this->get_field_name('ads4'); ?>" value="<?php echo $instance['ads4']; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('ads5'); ?>">Tiêu đề:</label>
		<input class="widefat" style="width: 216px;" id="<?php echo $this->get_field_id('ads5'); ?>" name="<?php echo $this->get_field_name('ads5'); ?>" value="<?php echo $instance['ads5']; ?>" />
	</p>	
<?php }} ?>