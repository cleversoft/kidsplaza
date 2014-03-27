<?php get_header(); ?>
	<div class="breadcrumbs col-lg-12">
	    <?php if(function_exists('bcn_display'))
	    {
	        bcn_display();
	    }?>
	</div>
	<section class="col-sm-9">
		<span class="title-search">Lỗi 404, Không tìm thấy</span>
	</section>
	<aside class="col-sm-3">
		<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar')): endif;?>
	</aside>
	</div>
</div>

<?php get_footer(); ?>