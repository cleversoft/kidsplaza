<?php global $data; ?>
	<footer>
		<div class="container">
			<div class="row">
				<div class="col-md-3 col-xs-6 clearfix">
					<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer1')): endif;?>
				</div>
				<div class="col-md-3 col-xs-6 clearfix">
					<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer2')): endif;?>
				</div>
				<div class="responsive-menu-footer"></div>
				<div class="col-md-3 col-xs-6 clearfix">
					<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer3')): endif;?>
				</div>
				<div class="col-md-3 col-xs-6 clearfix">
					<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer4')): endif;?>
				</div>
				<div class="responsive-menu-footer desktop-footer"></div>
				<div class="col-md-3 col-xs-6 clearfix">
					<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer5')): endif;?>
				</div>
				<div class="col-md-3 col-xs-6 clearfix">
					<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer6')): endif;?>
				</div>
				<div class="responsive-menu-footer"></div>
				<div class="col-md-3 col-xs-6 clearfix">
					<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer7')): endif;?>
				</div>
				<div class="col-md-3 col-xs-6 clearfix">
					<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer8')): endif;?>
				</div>
				<div class="diachi col-xs-12 clearfix">
					<div class="col-xs-3">
                        <div class="hn widget">
                            <em></em>
                            <h3>Kids plaza Hà Nội</h3>
                        </div>
					</div>
					<div class="diachiso col-xs-3">
						<p><strong>Q.Đống Đa:</strong> Số 20-22 Thái Thịnh</p>
						<p><strong>Q.Ba Đình:</strong> Số 56 Giang Văn Minh</p>
					</div>
					<div class="diachiso col-xs-3">
						<p><strong>Q.Cầu Giấy:</strong> Số 40 Trần Thái Tông</p>
						<p><strong>Q.Hà Đông:</strong> Số 44-TT4A Văn Quán</p>
					</div>
					<div class="diachiso col-xs-3">
						<p><strong>Q.Thanh Xuân:</strong> Số 533 Nguyễn Trãi</p>
						<p><strong>Q.Hai Bà Trưng:</strong> Số 340 Bạch Mai</p>
					</div>
				</div>
				<div class="diachi col-xs-12">
					<div class="col-sm-3 widget col-xs-6">
                        <div class="hcm widget">
                            <em></em>
                            <h3>Kids plaza Hồ Chí Minh</h3>
					    </div>
					</div>
					<div class="diachiso hcm col-sm-9  col-xs-6">
						<p>162L/18 Trường Chinh, P.12, Quận Tân Bình, TPHCM</p>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<div class="copyright">
		<div class="container">
			<div class="row">
				<div class="text-copyright col-lg-5 col-md-5 col-sm-12 col-xs-12">
					<p><?php echo $data['apl_footer_text']; ?></p>
				</div>
				<div class="email-copyright col-lg-3 col-md-3 col-sm-12 col-xs-12">
					<p>Email: <a href="mailto:<?php echo $data['apl_footer_email']; ?>"><?php echo $data['apl_footer_email']; ?></a></p>
				</div>
				<div class="social col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<ul>
					<?php 
					$url = get_template_directory_uri();
					$facebook = $data['apl_facebook'];
					$pinterest = $data['apl_pinterest'];
					$flickr = $data['apl_flickr'];
					$twitter = $data['apl_twitter'];
					$youtube = $data['apl_youtube'];
					$inlink = $data['apl_inlink'];
					$skype = $data['apl_skype'];
					$google = $data['apl_google'];
					if (!empty($facebook)) {
						echo "<li><a href='".$facebook."'><img src='". $url ."/images/facebook.png'></a></li>";
					}
					if (!empty($pinterest)) {
						echo "<li><a href='".$pinterest."'><img src='". $url ."/images/pinterest.png'></a></li>";
					}
					if (!empty($flickr)) {
						echo "<li><a href='".$flickr."'><img src='". $url ."/images/flickr.png'></a></li>";
					}
					if (!empty($twitter)) {
						echo "<li><a href='".$twitter."'><img src='". $url ."/images/twitter.png'></a></li>";
					}
					if (!empty($youtube)) {
						echo "<li><a href='".$youtube."'><img src='". $url ."/images/youtube.png'></a></li>";
					}
					if (!empty($inlink)) {
						echo "<li><a href='".$inlink."'><img src='". $url ."/images/linkedin.png'></a></li>";
					}
					if (!empty($skype)) {
						echo "<li><a href='".$skype."'><img src='". $url ."/images/skype.png'></a></li>";
					}
					if (!empty($google)) {
						echo "<li><a href='".$google."'><img src='". $url ."/images/google.png'></a></li>";
					}
					?>
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