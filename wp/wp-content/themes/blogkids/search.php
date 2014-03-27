<?php get_header(); ?>
	<div class="breadcrumbs col-lg-12">
	    <?php if(function_exists('bcn_display'))
	    {
	        bcn_display();
	    }?>
	</div>
<?php if (have_posts()) : ?>
	<div class="col-lg-12 search-title">
		<span class="title-search">Kết quả cho "<?php echo $_GET['s']; ?>"</span>
	</div>
	    <section class="col-sm-9">
			<?php while (have_posts()) : the_post(); get_template_part( 'includes/loop', 'search' ); endwhile; ?>
		    <?php wp_pagenavi(); ?>
	    </section>
		<aside class="col-sm-3">
			<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar')): endif;?>
		</aside>
		</div>
	</div>
<?php else : ?>
	<section class="col-sm-9">
	    <div class="search-title">
			<span class="title-search">Kết quả cho "<?php echo $_GET['s']; ?>"</span>
		</div>
    </section>
	<aside class="col-sm-3">
		<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar')): endif;?>
	</aside>
	</div>
</div>
<?php endif; ?>

<?php get_footer(); ?>