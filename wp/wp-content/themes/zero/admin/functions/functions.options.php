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
		$bbit_social = array("facebook" => "Facebook","googleplus" => "Google Plus","twitter" => "Twitter");

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
						"std" 		=> "<h3 style=\"margin: 0 0 10px;\">Chào mừng đến với Bbit Option Framework.</h3>Đang cập nhật...",
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
						"id" 		=> "bbit_favicon",
						"std"		=> $img_url . "/favicon.ico",
						"type"		=> "upload"
                );
				
$of_options[] = array(	"name"		=> "Apple Touch",
						"desc"		=> "Upload ảnh hoặc điền đường dẫn đến ảnh thay thế cho Apple Touch mặc định." ,
						"id" 		=> "bbit_apple_touch",
						"std"		=> $img_url . "/apple-touch-icon.png",
						"type"		=> "upload"
                );
				
$of_options[] = array(	"name"		=> "Logo",
						"desc"		=> "Upload ảnh hoặc điền đường dẫn đến ảnh thay thế cho logo mặc định." ,
						"id" 		=> "bbit_logo",
						"std"		=> $img_url . "/logo.png",
						"type"		=> "upload"
                );
/*-----------------------------------------------------------------------------------*/
/* FOOTER
/*-----------------------------------------------------------------------------------*/
$of_options[] = array( 	"name" 		=> "Footer",
						"type" 		=> "heading"
				);
				
$of_options[] = array( 	"name" 		=> "Footer Text(Home)",
						"desc" 		=> "Bạn có thể sử dụng shortcodes trong footer text: [wp-link] [theme-link] [loginout-link] [blog-title] [blog-link] [the-year]",
						"id" 		=> "bbit_footer_text",
						"std" 		=> "<br><a href='http://bbit.vn' title='Thiết kế bởi Bbit'>© Design by Bbit</a><br><a href='https://plus.google.com/106409380513413581404?rel=author'>Bảo Phạm</a>, <a href='http://bbit.vn/game-online/tai-game-than-nong.html'>Tai game than nong</a><br>",
						"type" 		=> "textarea"
				);

$of_options[] = array( 	"name" 		=> "Footer Text",
						"desc" 		=> "Bạn có thể sử dụng shortcodes trong footer text: [wp-link] [theme-link] [loginout-link] [blog-title] [blog-link] [the-year]",
						"id" 		=> "bbit_footer_text2",
						"std" 		=> "<br><a href='http://bbit.vn' title='Thiết kế bởi Bbit'>© Design by Bbit</a><br><a href='https://plus.google.com/106409380513413581404?rel=author'>Bảo Phạm</a>, <a href='http://bbit.vn/game-online/tai-game-than-nong.html'>Tai game than nong</a><br>",
						"type" 		=> "textarea"
				);				
/*-----------------------------------------------------------------------------------*/
/* HOME
/*-----------------------------------------------------------------------------------*/			
$of_options[] = array( 	"name" 		=> "Home",
						"type" 		=> "heading"
				);

$of_options[] = array( 	"name" 		=> "Cho phép bài viết mới",
						"desc" 		=> "Bật cho phép hiển thị bài viết mới(có thể tắt để dùng widgets).",
						"id" 		=> "bbit_newpost",
						"std" 		=> 0,
						"folds" 	=> 1,
						"on" 		=> "Bật",
						"off" 		=> "Tắt",
						"type" 		=> "switch"
				);
				
$of_options[] = array( 	"name" 		=> "Post",
						"type" 		=> "heading"
				);

$of_options[] = array( 	"name" 		=> "Social",
						"desc" 		=> "Like và Share bài viết lên các mạng xã hội.",
						"id" 		=> "bbit_social",
						"std" 		=> array("facebook","googleplus"),
						"type" 		=> "multicheck",
						"options" 	=> $bbit_social
				);
				
$of_options[] = array( 	"name" 		=> "Bình luận",
						"desc" 		=> "Bật/tắt chức năng bình luận cho bài đăng.",
						"id" 		=> "bbit_show_comments",
						"std" 		=> 1,
						"on" 		=> "Bật",
						"off" 		=> "Tắt",
						"type" 		=> "switch"
				);

/*-----------------------------------------------------------------------------------*/
/* STYLE
/*-----------------------------------------------------------------------------------*/					
$of_options[] = array( 	"name" 		=> "Style",
						"type" 		=> "heading"
				);

$of_options[] = array( 	"name" 		=> "Background",
						"desc" 		=> "Chọn ảnh nền.",
						"id" 		=> "bbit_custom_bg",
						"std" 		=> $bg_images_url."bg.png",
						"type" 		=> "tiles",
						"options" 	=> $bg_images,
				);

$of_options[] = array( 	"name" 		=> "",
						"desc" 		=> "Chọn màu cho nền trang (mặc định: #f9f9f9).",
						"id" 		=> "bbit_background",
						"std" 		=> "#f9f9f9",
						"type" 		=> "color"
				);
				
$of_options[] = array( 	"name" 		=> "Tùy chỉnh CSS",
						"desc" 		=> "Bạn có thể tùy chỉnh giao diện bằng cách thêm mã css vào ô dưới đây.",
						"id" 		=> "bbit_custom_css",
						"std" 		=> "",
						"type" 		=> "textarea"
				);
				
//Advanced Settings
$of_options[] = array( 	"name" 		=> "Advanced",
						"type" 		=> "heading"
				);
				
$of_options[] = array( 	"name" 		=> "Nén HTML",
						"desc" 		=> "Nén HTML để tăng tốc độ tải trang, không dùng khi development theme.",
						"id" 		=> "bbit_compress_html",
						"std" 		=> 0,
						"on" 		=> "Bật",
						"off" 		=> "Tắt",
						"type" 		=> "switch"
				);	

$of_options[] = array( 	"name" 		=> "Copy Protected",
						"desc" 		=> "Chống sao chép nội dung dưới mọi hình thức, bảo vệ bản quyền.",
						"id" 		=> "bbit_protected",
						"std" 		=> 0,
						"on" 		=> "Bật",
						"off" 		=> "Tắt",
						"type" 		=> "switch"
				);	
				
$of_options[] = array( 	"name" 		=> "Mobile Navi",
						"desc" 		=> "Menu thông minh hỗ trợ tiết kiệm diện tích website cho smartphone.",
						"id" 		=> "bbit_mobile_navi",
						"std" 		=> 1,
						"on" 		=> "Bật",
						"off" 		=> "Tắt",
						"type" 		=> "switch"
		        );

/*-----------------------------------------------------------------------------------*/
/* SEO and ADS
/*-----------------------------------------------------------------------------------*/
$of_options[] = array( 	"name" 		=> "SEO and Ads",
						"type" 		=> "heading"
				);
				
$of_options[] = array( 	"name" 		=> "Tracking Code",
						"desc" 		=> "Mã theo dõi Google Analytics (hoặc khác) ở đây. Nó sẽ tự động được thêm vào theme.",
						"id" 		=> "bbit_google_analytics",
						"std" 		=> "",
						"type" 		=> "textarea"
				);

$of_options[] = array( 	"name" 		=> "Quảng cáo trong bài viết",
						"desc" 		=> "Quảng cáo trong bài viết.",
						"id" 		=> "bbit_mp3_banner",
						"std" 		=> 0,
						"on" 		=> "Bật",
						"off" 		=> "Tắt",
						"type" 		=> "switch"
				);				
/*-----------------------------------------------------------------------------------*/
/* BACKUP
/*-----------------------------------------------------------------------------------*/
$of_options[] = array( 	"name" 		=> "Backup",
						"type" 		=> "heading",
				);
				
$of_options[] = array( 	"name" 		=> "Sao lưu và Khôi phục",
						"id" 		=> "of_backup",
						"std" 		=> "",
						"type" 		=> "backup",
						"desc" 		=> 'You can use the two buttons below to backup your current options, and then restore it back at a later time. This is useful if you want to experiment on the options but would like to keep the old settings in case you need it back.',
				);
				
$of_options[] = array( 	"name" 		=> "Transfer Theme Options Data",
						"id" 		=> "of_transfer",
						"std" 		=> "",
						"type" 		=> "transfer",
						"desc" 		=> 'You can tranfer the saved options data between different installs by copying the text inside the text box. To import data from another install, replace the data in the text box with the one from another install and click "Import Options".',
				);

/*-----------------------------------------------------------------------------------*/
/* INFORMATION
/*-----------------------------------------------------------------------------------*/
$of_options[] = array( 	"name" 		=> "Info",
						"type" 		=> "heading",
				);

//End oftion				
	}
}
?>
