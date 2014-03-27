<?php

class BioneThemeFrameworkMetaboxes {
	
	public function __construct()
	{
		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
		add_action('save_post', array($this, 'save_meta_boxes'));
	}
	
	public function add_meta_boxes()
	{
		$this->add_meta_box('form', 'Chức năng nâng cao', 'post');
	}
	
	public function add_meta_box($id, $label, $post_type)
	{
	    add_meta_box( 
	        'bbit_' . $id,
	        $label,
	        array($this, $id),
	        $post_type
	    );
	}
	
	public function save_meta_boxes($post_id)
	{
		if(defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		
		foreach($_POST as $key => $value) {
			if(strstr($key, 'bbit_')) {
				update_post_meta($post_id, $key, $value);
			}
		}
	}
	
	public function form()
	{	
?>
	<style type='text/css'>
	.bbit_metabox{padding:10px 10px}
	.bbit_metabox_field{margin-bottom:15px;width:100%;overflow:hidden}
	.bbit_metabox_field label{font-weight:bold;float:left;width:15%}
	.bbit_metabox_field .field{float:left;width:75%}
	.bbit_metabox_field input[type=text]{width:100%}
	</style>
	<div class="bbit_metabox">
	<?php
	$this->select('score','Xếp hạng',array('0' => '0','0.5' => '0.5','1' => '1','1.5' => '1.5','2' => '2','2.5' => '2.5','3' => '3','3.5' => '3.5','4' => '4','4.5' => '4.5','5' => '5'),'Xếp hạng(từ 0 đến 10)');		
	$this->text('phathanh', 'Phát hành');
	$this->text('phathanh_link', 'Link phát hành');
	$this->text('theloai', 'Thể loại');		
	$this->text('file_size', 'Dung lượng');			
	$this->text('support', 'Hỗ trợ');
	$this->text('link_download', 'Link download');			
	?>
	</div>
	<?php
	}
	
	public function post_options()
	{ ?>
	<style type='text/css'>
	.bbit_metabox{padding:10px 10px}
	.bbit_metabox_field{margin-bottom:15px;width:100%;overflow:hidden}
	.bbit_metabox_field label{font-weight:bold;float:left;width:15%}
	.bbit_metabox_field .field{float:left;width:75%}
	.bbit_metabox_field input[type=text]{width:100%}
	</style>
	<?php
	}
	
	public function text($id, $label, $desc = '')
	{
		global $post;
		
		$html .= '<div class="bbit_metabox_field">';
			$html .= '<label for="bbit_' . $id . '">';
			$html .= $label;
			$html .= '</label>';
			$html .= '<div class="field">';
				$html .= '<input type="text" id="bbit_' . $id . '" name="bbit_' . $id . '" value="' . get_post_meta($post->ID, 'bbit_' . $id, true) . '" />';
				if($desc) {
					$html .= '<p>' . $desc . '</p>';
				}
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}
	
	public function select($id, $label, $options, $desc = '')
	{
		global $post;
		
		$html .= '<div class="bbit_metabox_field">';
			$html .= '<label for="bbit_' . $id . '">';
			$html .= $label;
			$html .= '</label>';
			$html .= '<div class="field">';
				$html .= '<select id="bbit_' . $id . '" name="bbit_' . $id . '">';
				foreach($options as $key => $option) {
					if(get_post_meta($post->ID, 'bbit_' . $id, true) == $key) {
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}
					
					$html .= '<option ' . $selected . 'value="' . $key . '">' . $option . '</option>';
				}
				$html .= '</select>';
				if($desc) {
					$html .= '<p>' . $desc . '</p>';
				}
			$html .= '</div>';
		$html .= '</div>';
		
		echo $html;
	}

	
}

$metaboxes = new BioneThemeFrameworkMetaboxes;