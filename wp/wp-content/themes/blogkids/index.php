<?php get_header(); ?>
	<div class="breadcrumbs col-lg-12">
	    <?php if(function_exists('bcn_display'))
	    {
	        bcn_display();
	    }?>
	</div>
	<section class="col-sm-9">
		<?php while (have_posts()) : the_post(); get_template_part( 'includes/loop', 'index' ); endwhile; ?>
		<?php wp_pagenavi(); ?>
	</section>
	<aside class="col-sm-3">
		<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar')): endif;?>
	</aside>
	</div>
</div>

<?php get_footer(); ?>