<?php get_header(); ?> 

<?php if ( $data['bbit_newpost'] == 1 ): ?>

	<span class="title">Bài viết mới</span>
    <?php while (have_posts()) : the_post(); get_template_part( 'includes/templates/loop', 'index' ); endwhile; ?>

	<?php bbit_pagination($pages = '', $range = 2); ?>

<?php endif; ?>

<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Home')): endif;?>

<?php get_footer(); ?>