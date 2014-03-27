<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	
	<?php if (is_search()) { ?>
	   <meta name="robots" content="noindex, nofollow" /> 
	<?php } ?>

	<title>
		   <?php
		      if (function_exists('is_tag') && is_tag()) {
		         single_tag_title("Tag Archive for &quot;"); echo '&quot; - '; }
		      elseif (is_archive()) {
		         wp_title(''); echo ' Archive - '; }
		      elseif (is_search()) {
		         echo 'Search for &quot;'.wp_specialchars($s).'&quot; - '; }
		      elseif (!(is_404()) && (is_single()) || (is_page())) {
		         wp_title(''); echo ' - '; }
		      elseif (is_404()) {
		         echo 'Not Found - '; }
		      if (is_home()) {
		         bloginfo('name'); echo ' - '; bloginfo('description'); }
		      else {
		          bloginfo('name'); }
		      if ($paged>1) {
		         echo ' - page '. $paged; }
		   ?>
	</title>
	
	<link rel="shortcut icon" href="/favicon.ico">
	
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
	
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

	<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300&subset=vietnamese' rel='stylesheet' type='text/css'>
	<?php if ( is_singular() ) wp_enqueue_script('comment-reply'); ?>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<div class="top-header">
		<div class="container">
			<div class="row">
				<div class="social col-lg-3 col-md-4 col-sm-5">
					<ul>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/facebook.png"></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/pinterest.png"></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/flickr.png"></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/twitter.png"></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/youtube.png"></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/linkedin.png"></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/skype.png"></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/google.png"></a></li>
					</ul>
				</div>
				<nav class="page-menu col-lg-7 col-md-5 col-sm-9">
					<?php
					$defaults = array(
						'theme_location'  => '',
						'menu'            => 'Page Menu',
						'container'       => '',
						'container_class' => '',
						'container_id'    => '',
						'menu_class'      => 'menu',
						'menu_id'         => '',
						'echo'            => true,
						'fallback_cb'     => 'wp_page_menu',
						'before'          => '',
						'after'           => '',
						'link_before'     => '',
						'link_after'      => '',
						'items_wrap'      => '<ul id="%1$s" class="nav-pills">%3$s</ul>',
						'depth'           => 0,
						'walker'          => ''
					);

					wp_nav_menu( $defaults );

					?>
				</nav>
				<div class="account col-lg-2 col-sm-3">
					<a href="#">
						<img src="<?php echo get_template_directory_uri(); ?>/images/user.png">
						<span>Đăng nhập</span>
						<p>Tài khoản và đơn hàng</p>
					</a>
				</div>
			</div>
		</div>
		
	</div>
	<div class="container">
		<div class="row">
			<header>
				<div class="main-header">
					<div class="logo col-sm-4">
						<a href="<?php bloginfo('url'); ?>">
							<img src="<?php echo get_template_directory_uri(); ?>/images/logo.png">
						</a>
					</div>
					<div class="search-category col-lg-8">
						<?php if(function_exists('sbc')){ 
						    sbc();
						} ?>
					</div>
				</div>
				<nav class="main-navigation">
			      	<div class="navbar-header">
			          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			            <span class="sr-only">Toggle navigation</span>
			            <span class="icon-bar"></span>
			            <span class="icon-bar"></span>
			            <span class="icon-bar"></span>
			          </button>
			          <a class="navbar-brand" href="#">Menu</a>
			        </div>
			        <div class="navbar-collapse collapse">
			        	<?php
						$defaults = array(
							'theme_location'  => '',
							'menu'            => 'Main Menu',
							'container'       => '',
							'container_class' => '',
							'container_id'    => '',
							'menu_class'      => 'menu',
							'menu_id'         => '',
							'echo'            => true,
							'fallback_cb'     => 'wp_page_menu',
							'before'          => '',
							'after'           => '',
							'link_before'     => '',
							'link_after'      => '',
							'items_wrap'      => '<ul id="%1$s" class="nav navbar-nav">%3$s</ul>',
							'depth'           => 0,
							'walker'          => ''
						);

						wp_nav_menu( $defaults );

						?>
			        </div>
				</nav>
			</header>
			