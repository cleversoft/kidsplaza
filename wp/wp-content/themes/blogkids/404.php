<?php get_header(); ?>
	<div class="breadcrumbs col-lg-12">
	    <?php if(function_exists('bcn_display'))
	    {
	        bcn_display();
	    }?>
	</div>
	<section class="col-sm-12">
		<div class="col-xs-12 col-sm-5 col-md-5 col-lg-5 banner">
			<a href="#">
				<img src="<?php echo get_template_directory_uri(); ?>/images/404.png">
			</a>
		</div>
		<div class="tick col-lg-1 col-xs-12 col-sm-1 col-md-1 hidden-xs"></div>
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<p>
			Trang web bạn muốn xem
			<a href="#"> không còn tồn tại hoặc vừa bị xóa</a>
			. Nếu bạn đang muốn tìm sản phẩm, hãy thử sử dụng chức năng tìm kiếm đầy đủ của chúng tôi Bạn sẽ được tự động trở về Trang chủ
			<strong> Kids Plaza </strong>
			trong vòng 10s!
			</p>
		</div>
	</section>
	</div>
</div>
<?php get_footer(); ?>