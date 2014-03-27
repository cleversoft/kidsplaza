<?php
/* File nay de loi, de nghi khong chinh sua */
// Raw Shortcode
function my_formatter($content) {
	$new_content = '';
	$pattern_full = '{(\[raw\].*?\[/raw\])}is';
	$pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
	$pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

	foreach ($pieces as $piece) {
		if (preg_match($pattern_contents, $piece, $matches)) {
			$new_content .= $matches[1];
		} else {
			$new_content .= wptexturize(wpautop($piece));
		}
	}

	return $new_content;
}

remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');

add_filter('the_content', 'my_formatter', 99);

// Button shortcode
add_shortcode('button', 'shortcode_button');
	function shortcode_button($atts, $content = null) {
		$atts = shortcode_atts(
			array(
				'link' => '#',
			), $atts);
		
			return '[raw]<a href="' . $atts['link'] . '" rel="nofollow" class="downloadfree">Tải về máy</a>[/raw]';
	}

// Tabs shortcode
add_shortcode('tabs', 'shortcode_tabs');
	function shortcode_tabs( $atts, $content = null ) {
	extract(shortcode_atts(array(
    ), $atts));

	$out .= '[raw]<div class="download"><div class="dl_menu">Tải về</div>[/raw]';
	
	$out .= '<ul>';
	$out .= do_shortcode($content) .'[raw]</ul></div>[/raw]';
	
	return $out;
}

add_shortcode('tab', 'shortcode_tab');
	function shortcode_tab( $atts, $content = null ) {
	extract(shortcode_atts(array(
    ), $atts));
	
	$out .= '[raw]<li id="tab' . $atts['id'] . '" class="item">[/raw]' . do_shortcode($content) .'</li>';
	
	return $out;
}

// Ads
add_shortcode('ads', 'shortcode_ads');
	function shortcode_ads($atts, $content = null) {	
	return '<div class="banner">' .do_shortcode($content). '</div>';
	}
	
// Add buttons to tinyMCE
add_action('init', 'add_button');

function add_button() {  
   if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  
   {  
     add_filter('mce_external_plugins', 'add_plugin');  
     add_filter('mce_buttons_3', 'register_button');  
   }  
}  

function register_button($buttons) {  
   array_push($buttons, "button", "tabs", "ads");  
   return $buttons;  
}  

function add_plugin($plugin_array) {  
   $plugin_array['button'] = get_template_directory_uri().'/includes/tinymce/customcodes.js';
   $plugin_array['tabs'] = get_template_directory_uri().'/includes/tinymce/customcodes.js';
   $plugin_array['ads'] = get_template_directory_uri().'/includes/tinymce/customcodes.js';
   
   return $plugin_array;  
}