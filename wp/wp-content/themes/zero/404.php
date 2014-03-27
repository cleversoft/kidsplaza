<?php get_header(); ?>

	<p class="banner">Xin lỗi!, trang này không tồn tại. Vui lòng trở về trang chủ.</p>

    <span class="title">Bài viết mới</span>

    <?php
	$recent_posts = new WP_Query(array('showposts' => 10));
	while($recent_posts->have_posts())
	{
	$recent_posts->the_post();
	get_template_part( 'includes/templates/loop', '404' );
	} ?>

<?php get_footer(); ?>