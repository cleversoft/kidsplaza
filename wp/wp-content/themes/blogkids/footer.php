	<footer>
		<div class="container">
			<div class="row">
				<div class="col-md-3 ">
					<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer1')): endif;?>
				</div>
				<div class="col-md-3">
					<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer2')): endif;?>
				</div>
				<div class="col-md-3">
					<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer3')): endif;?>
				</div>
				<div class="col-md-3">
					<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer4')): endif;?>
				</div>
				<div class="diachi col-md-12 clearfix">
					<div class="col-md-3">
						<h3>Kids plaza Hà Nội</h3>
					</div>
					<div class="diachiso col-md-3">
						<p><strong>Q.Đống Đa:</strong> Số 20-22 Thái Thịnh</p>
						<p><strong>Q.Ba Đình:</strong> Số 56 Giang Văn Minh</p>
					</div>
					<div class="diachiso col-md-3">
						<p><strong>Q.Cầu Giấy:</strong> Số 40 Trần Thái Tông</p>
						<p><strong>Q.Hà Đông:</strong> Số 44-TT4A Văn Quán</p>
					</div>
					<div class="diachiso col-md-3">
						<p><strong>Q.Thanh Xuân:</strong> Số 533 Nguyễn Trãi</p>
						<p><strong>Q.Hai Bà Trưng:</strong> Số 340 Bạch Mai</p>
					</div>
				</div>
				<div class="diachi col-md-12">
					<div class="col-md-3">
						<h3>Kids plaza Hồ Chí Minh</h3>
					</div>
					<div class="diachiso hcm col-md-9">
						<p>162L/18 Trường Chinh, P.12, Quận Tân Bình, TPHCM</p>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<div class="copyright">
		<div class="container">
			<div class="row">
				<div class="text-copyright col-lg-6 col-md-5">
					<p>Copyright © 2009-2013 Kidsplaza.vn. Bản quyền của công ty Kids Plaza</p>
				</div>
				<div class="email-copyright col-sm-3">
					<p>Email: <a href="mailto:hotro@kidsplaza.vn">hotro@kidsplaza.vn</a></p>
				</div>
				<div class="social col-lg-3 col-md-4">
					<ul>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/facebook.png"></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/pinterest.png"></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/flickr.png"></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/twitter.png"></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/youtube.png"></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/linkedin.png"></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/skype.png"></a></li>
						<li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/images/google.png"></a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/vi_VI/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
	<?php wp_footer(); ?>
	<!-- Don't forget analytics -->
</body>

</html>