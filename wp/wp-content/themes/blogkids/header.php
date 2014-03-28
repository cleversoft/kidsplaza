<?php global $data; ?>
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
    <link href="<?php echo $data['apl_favicon']; ?>" rel="shortcut icon" />
    <link href="<?php echo $data['apl_apple_touch']; ?>" rel="apple-touch-icon" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Roboto+Slab:700&subset=latin,vietnamese' rel='stylesheet' type='text/css'>
	<?php if (is_singular()) wp_enqueue_script('comment-reply'); ?>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<div class="top-header">
		<div class="container">
			<div class="row">
				<div class="social col-lg-3 col-md-4 col-sm-5">
					<ul>
					<?php 
					$url = get_template_directory_uri();
					$facebook = $data['apl_facebook'];
					$pinterest = $data['apl_pinterest'];
					$flickr = $data['apl_flickr'];
					$twitter = $data['apl_twitter'];
					$youtube = $data['apl_youtube'];
					$inlink = $data['apl_inlink'];
					$skype = $data['apl_skype'];
					$google = $data['apl_google'];
					if (!empty($facebook)) {
						echo "<li><a href='".$facebook."'><img src='". $url ."/images/facebook.png'></a></li>";
					}
					if (!empty($pinterest)) {
						echo "<li><a href='".$pinterest."'><img src='". $url ."/images/pinterest.png'></a></li>";
					}
					if (!empty($flickr)) {
						echo "<li><a href='".$flickr."'><img src='". $url ."/images/flickr.png'></a></li>";
					}
					if (!empty($twitter)) {
						echo "<li><a href='".$twitter."'><img src='". $url ."/images/twitter.png'></a></li>";
					}
					if (!empty($youtube)) {
						echo "<li><a href='".$youtube."'><img src='". $url ."/images/youtube.png'></a></li>";
					}
					if (!empty($inlink)) {
						echo "<li><a href='".$inlink."'><img src='". $url ."/images/linkedin.png'></a></li>";
					}
					if (!empty($skype)) {
						echo "<li><a href='".$skype."'><img src='". $url ."/images/skype.png'></a></li>";
					}
					if (!empty($google)) {
						echo "<li><a href='".$google."'><img src='". $url ."/images/google.png'></a></li>";
					}
					?>
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
					<a href="<?php echo $data['apl_login']; ?>">
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
						<a href="http://kidsplaza.vn/">
							<img src="<?php echo $data['apl_logo']; ?>">
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
			