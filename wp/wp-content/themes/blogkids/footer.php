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
                            <h3><?php echo $data['apl_footer_dc_hn']; ?></h3>
                        </div>
					</div>
					<div class="diachiso col-xs-3">
						<p><?php echo $data['apl_footer_cn_1']; ?></p>
						<p><?php echo $data['apl_footer_cn_2']; ?></p>
					</div>
					<div class="diachiso col-xs-3">
						<p><?php echo $data['apl_footer_cn_3']; ?></p>
						<p><?php echo $data['apl_footer_cn_4']; ?></p>
					</div>
					<div class="diachiso col-xs-3">
						<p><?php echo $data['apl_footer_cn_5']; ?></p>
						<p><?php echo $data['apl_footer_cn_6']; ?></p>
					</div>
				</div>
				<div class="diachi col-xs-12">
					<div class="col-sm-3 widget col-xs-6">
                        <div class="hcm widget">
                            <em></em>
                            <h3><?php echo $data['apl_footer_dc_hcm']; ?></h3>
					    </div>
					</div>
					<div class="diachiso hcm col-sm-9  col-xs-6">
						<p><?php echo $data['apl_footer_hcm_1']; ?></p>
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
	<script type="text/javascript">
	/* resize facebook comments */
	(function(window){
	    var dh = null;
	    $(window).on("resize",function(){
	        if ( dh ) {
	            clearTimeout(dh);
	        }
	        dh = setTimeout(function(){
	            var $fbc = $(".fb-like-box");
	            var $stc = $(".textwidget");
	            dh = null;
	            if ( $fbc.attr("data-width") != $stc.width() ) {
	                $stc.css({height:$stc.height()});
	                $fbc.attr("data-width", $stc.width());
	                FB.XFBML.parse($stc[0],function(){
	                    $stc.css({height:'auto'});
	                });
	            }
	        },300);
	    }).trigger("resize");

	   	var kl = null;
	    $(window).on("resize",function(){
	        if ( kl ) {
	            clearTimeout(kl);
	        }
	        kl = setTimeout(function(){
	            var $fbl = $(".fb-comments");
	            var $stl = $(".comment");
	            dh = null;
	            if ( $fbl.attr("data-width") != $stl.width() ) {
	                $stl.css({height:$stl.height()});
	                $fbl.attr("data-width", $stl.width());
	                FB.XFBML.parse($stl[0],function(){
	                    $stl.css({height:'auto'});
	                });
	            }
	        },300);
	    }).trigger("resize");
	})(this);

	$(".postform").selectbox({
	onOpen: function (inst) {
		//console.log("open", inst);
	},
	onClose: function (inst) {
		//console.log("close", inst);
	},
	onChange: function (val, inst) {
		$.ajax({
			type: "GET",
			data: {country_id: val},
			url: "ajax.php",
			success: function (data) {
				$(".postform").html(data);
				$(".postform").selectbox();
			}
		});
	},
	effect: "slide"
	});
	$(".all-catgory").selectbox({
		effect: "fade"
	});
	$(".all-catgory").selectbox({
		speed: 0
	});
	</script>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/vi_VI/all.js#xfbml=1";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<?php wp_footer(); ?>
</body>

</html>