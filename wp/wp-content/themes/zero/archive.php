<?php get_header(); ?>

    <?php bbit_breadcrumbs(); ?>

    <?php while (have_posts()) : the_post(); get_template_part( 'includes/templates/loop', 'archive' ); endwhile; ?>

    <?php bbit_pagination($pages = '', $range = 2); ?>

<?php get_footer(); ?>