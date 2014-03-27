<?php

add_action('init','of_options');

if (!function_exists('of_options'))
{
	function of_options()
	{
		//Access the WordPress Categories via an Array
		$of_categories 		= array();  
		$of_categories_obj 	= get_categories('hide_empty=0');
		foreach ($of_categories_obj as $of_cat) {
		    $of_categories[$of_cat->cat_ID] = $of_cat->cat_name;}
		$categories_tmp 	= array_unshift($of_categories, "Select a category:");    
	       
		//Access the WordPress Pages via an Array
		$of_pages 			= array();
		$of_pages_obj 		= get_pages('sort_column=post_parent,menu_order');    
		foreach ($of_pages_obj as $of_page) {
		    $of_pages[$of_page->ID] = $of_page->post_name; }
		$of_pages_tmp 		= array_unshift($of_pages, "Select a page:");       
	
		//Social
		$apl_social = array("facebook" => "Facebook","googleplus" => "Google Plus","twitter" => "Twitter");

		//Background Images Reader
		$bg_images_path = get_stylesheet_directory(). '/assets/images/bg/'; // change this to where you store your bg images
		$bg_images_url = get_template_directory_uri().'/assets/images/bg/'; // change this to where you store your bg images
		$bg_images = array();
		
		if ( is_dir($bg_images_path) ) {
		    if ($bg_images_dir = opendir($bg_images_path) ) { 
		        while ( ($bg_images_file = readdir($bg_images_dir)) !== false ) {
		            if(stristr($bg_images_file, ".png") !== false || stristr($bg_images_file, ".jpg") !== false) {
		            	natsort($bg_images); //Sorts the array into a natural order
		                $bg_images[] = $bg_images_url . $bg_images_file;
		            }
		        }    
		    }
		}
		
		/*-----------------------------------------------------------------------------------*/
		/* TO DO: Add options/functions that use these */
		/*-----------------------------------------------------------------------------------*/
		
		//More Options
		$uploads_arr 		= wp_upload_dir();
		$all_uploads_path 	= $uploads_arr['path'];
		$all_uploads 		= get_option('of_uploads');
		$other_entries 		= array("Select a number:","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19");
		$body_repeat 		= array("no-repeat","repeat-x","repeat-y","repeat");
		$body_pos 			= array("top left","top center","top right","center left","center center","center right","bottom left","bottom center","bottom right");
		
		// Image Alignment radio box
		$of_options_thumb_align = array("alignleft" => "Left","alignright" => "Right","aligncenter" => "Center"); 
		
		// Image Links to Options
		$of_options_image_link_to = array("image" => "The Image","post" => "The Post"); 

		
/*-----------------------------------------------------------------------------------*/
/* OPTION IN HERE
/*-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* GENERAL
/*-----------------------------------------------------------------------------------*/
global $of_options;
$of_options = array();
$of_options[] = array( 	"name" 		=> "General",
						"type" 		=> "heading"
				);
				
$of_options[] = array( 	"name" 		=> "Hello there!",
						"desc" 		=> "",
						"id" 		=> "introduction",
						"std" 		=> "<h3 style=\"margin: 0 0 10px;\">Chào mừng đến với APL Option Framework.</h3>Để tùy chỉnh các thông số bạn chọn các tab bên tay trái và cài đặt theo ý mình.",
						"icon" 		=> true,
						"type" 		=> "info"
				);

/*-----------------------------------------------------------------------------------*/
/* HEADER
/*-----------------------------------------------------------------------------------*/
$of_options[] = array( 	"name" 		=> "Header",
						"type" 		=> "heading"
				);
				
$img_url = get_template_directory_uri() .'/assets/images';
$of_options[] = array(	"name"		=> "Favicon",
						"desc"		=> "Upload ảnh hoặc điền đường dẫn đến ảnh thay thế cho favicon mặc định." ,
						"id" 		=> "apl_favicon",
						"std"		=> $img_url . "/favicon.ico",
						"type"		=> "upload"
                );
				
$of_options[] = array(	"name"		=> "Apple Touch",
						"desc"		=> "Upload ảnh hoặc điền đường dẫn đến ảnh thay thế cho Apple Touch mặc định." ,
						"id" 		=> "apl_apple_touch",
						"std"		=> $img_url . "/apple-touch-icon.png",
						"type"		=> "upload"
                );
				
$of_options[] = array(	"name"		=> "Logo",
						"desc"		=> "Upload ảnh hoặc điền đường dẫn đến ảnh thay thế cho logo mặc định." ,
						"id" 		=> "apl_logo",
						"std"		=> $img_url . "/logo.png",
						"type"		=> "upload"
                );
$of_options[] = array(	"name"		=> "Link Đăng Nhập",
						"desc"		=> "Điền link đăng nhập của Kidsplaza vào đây." ,
						"id" 		=> "apl_login",
						"std"		=> "#",
						"type"		=> "text"
                );
$of_options[] = array(	"name"		=> "Link facebook",
						"desc"		=> "Điền link facebook của bạn vào đây." ,
						"id" 		=> "apl_facebook",
						"std"		=> "",
						"type"		=> "text"
                );
$of_options[] = array(	"name"		=> "Link Pinterest",
						"desc"		=> "Điền link Pinterest của bạn vào đây." ,
						"id" 		=> "apl_pinterest",
						"std"		=> "",
						"type"		=> "text"
                );
$of_options[] = array(	"name"		=> "Link Flickr",
						"desc"		=> "Điền link Flickr của bạn vào đây." ,
						"id" 		=> "apl_flickr",
						"std"		=> "",
						"type"		=> "text"
                );
$of_options[] = array(	"name"		=> "Link Twitter",
						"desc"		=> "Điền link Twitter của bạn vào đây." ,
						"id" 		=> "apl_twitter",
						"std"		=> "",
						"type"		=> "text"
                );
$of_options[] = array(	"name"		=> "Link Youtube",
						"desc"		=> "Điền link Youtube của bạn vào đây." ,
						"id" 		=> "apl_youtube",
						"std"		=> "",
						"type"		=> "text"
                );
$of_options[] = array(	"name"		=> "Link Inlink",
						"desc"		=> "Điền link Inlink của bạn vào đây." ,
						"id" 		=> "apl_inlink",
						"std"		=> "",
						"type"		=> "text"
                );
$of_options[] = array(	"name"		=> "Link Skype",
						"desc"		=> "Điền link Skype của bạn vào đây." ,
						"id" 		=> "apl_skype",
						"std"		=> "",
						"type"		=> "text"
                );
$of_options[] = array(	"name"		=> "Link Google +",
						"desc"		=> "Điền link Google + của bạn vào đây." ,
						"id" 		=> "apl_google",
						"std"		=> "",
						"type"		=> "text"
                );
/*-----------------------------------------------------------------------------------*/
/* FOOTER
/*-----------------------------------------------------------------------------------*/
$of_options[] = array( 	"name" 		=> "Footer",
						"type" 		=> "heading"
				);
				
$of_options[] = array( 	"name" 		=> "Footer Text",
						"desc" 		=> "Bạn có thể sử dụng các thẻ html ở trong này",
						"id" 		=> "apl_footer_text",
						"std" 		=> "Copyright © 2009-2013 Kidsplaza.vn. Bản quyền của công ty <a href='http://kidsplaza.vn/'>Kids Plaza</a>",
						"type" 		=> "textarea"
				);

$of_options[] = array( 	"name" 		=> "Footer Email",
						"desc" 		=> "Điền email của bạn vào đây",
						"id" 		=> "apl_footer_email",
						"std" 		=> "hotro@kidsplaza.vn",
						"type" 		=> "textarea"
				);

//End oftion				
	}
}
?>
