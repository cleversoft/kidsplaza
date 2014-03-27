<?php global $data; if ( $data['bbit_compress_html'] == 1 ) : include_once('includes/bbit-compress.php'); endif; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title><?php bloginfo('name'); wp_title(); if ((is_home()|| is_front_page())); if ( $paged >= 2 || $page >= 2 ) echo ' - ' . sprintf( __( 'Trang %s'), max( $paged, $page ) ); ?></title>
    <?php wp_head(); ?>
    <link href="<?php bloginfo('template_directory'); ?>/style.php" rel="stylesheet" type="text/css" />
    <link href="<?php echo $data['bbit_favicon']; ?>" rel="shortcut icon" />
    <link href="<?php echo $data['bbit_apple_touch']; ?>" rel="apple-touch-icon" />
    <link href="<?php bloginfo('rss2_url'); ?>" rel="alternate" type="application/rss+xml" />
	<?php echo $data['bbit_google_analytics']; ?>
</head>
<body>
<div id="body">

    <header>		
	<a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>">
	    <img src="<?php echo $data['bbit_logo']; ?>" alt="Logo <?php bloginfo('name'); ?>">
	</a>
	<?php if (is_home() || is_archive()): ?>
	   <h1><?php bloginfo('description'); ?></h1>
	<?php else: ?>
	   <h2><?php bloginfo('description'); ?></h2>
	<?php endif; ?>
    </header>

    <nav id="Menu">
	<ul>
        <li><a href="http://bbit.vn">Trang chủ</a></li>
        <li><a href="http://story.bbit.vn">Story</a></li>
        <li><a href="http://photo.bbit.vn">Photo</a></li>
		<li><a id="open" href="#nav">Menu</a></li>
	</ul>
    </nav>

    <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Header')): endif;?>
	<?php if (is_user_logged_in()): get_template_part( 'includes/templates/admin', 'header' ); endif; ?>