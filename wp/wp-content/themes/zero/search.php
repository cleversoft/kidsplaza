<?php get_header(); ?>

<?php if (have_posts()) : ?>

    <span class="title">Kết quả cho "<?php echo $_GET['s']; ?>"</span>

	<?php while (have_posts()) : the_post(); get_template_part( 'includes/templates/loop', 'search' ); endwhile; ?>

    <?php bbit_pagination($pages = '', $range = 2); ?>

<?php else : ?>

    <span class="title">Không thể tìm thấy "<?php echo $_GET['s']; ?>"</span>

<?php endif; ?>

<?php get_footer(); ?>