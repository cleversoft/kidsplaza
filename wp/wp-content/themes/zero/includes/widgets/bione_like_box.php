<?php
add_action('widgets_init', 'bione_like_box_load_widgets');
function bione_like_box_load_widgets(){ register_widget('bione_like_box_Widget'); }
class bione_like_box_Widget extends WP_Widget {

	function bione_like_box_Widget()
	{
		$widget_ops = array('classname' => 'bione_like_box', 'description' => 'Kéo thả để tạo mục bài mới với khả năng tùy chỉnh đa dạng');
		$control_ops = array('id_base' => 'bione_like_box-widget');
		$this->WP_Widget('bione_like_box-widget', 'Bione Like Box', $widget_ops, $control_ops);
	}
	
function widget($args, $instance)
{
global $post;
extract($args);
echo $before_widget; ?>
    <span class="title">Bbit trên Mạng xã hội</span>
	<div class="pad">
	<div id="fb-root"></div>
    <script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/vi_VN/all.js#xfbml=1&appId=199826756851404";fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));</script>
	<script type="text/javascript" src="https://apis.google.com/js/platform.js">{lang: 'vi'}</script>
    <div class="g-plusone" data-size="medium"></div>
	<div class="fb-like" data-href="<?php bloginfo('url'); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
	</div>
<?php
echo $after_widget;
}
	
function update($new_instance, $old_instance)
{
$instance = $old_instance;
return $instance;
}

function form($instance)
{
$defaults = array();
$instance = wp_parse_args((array) $instance, $defaults); ?>
<?php }} ?>