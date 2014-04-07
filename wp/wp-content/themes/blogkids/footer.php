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
				<div class="text-copyright col-lg-7 col-md-5 col-sm-12 col-xs-12">
					<p><?php echo $data['apl_footer_text']; ?></p>
				</div>
				<div class="email-copyright col-lg-3 col-md-3 col-sm-12 col-xs-12">
					<p>Email: <a href="mailto:<?php echo $data['apl_footer_email']; ?>"><?php echo $data['apl_footer_email']; ?></a></p>
				</div>
				<div class="social col-lg-2 col-md-4 col-sm-12 col-xs-12">
					<ul class="footer-social">
					<?php
					$url = get_template_directory_uri();
					?>
                        <li>
                            <a href="#facebook">
                                <img src="<?php echo $url ?>/images/facebook.png">
                            </a>
                            <div id="facebook" class="top so-social-share">
                                <div class="marker"></div>
                                <div id="fb-root"></div>
                                <div class="fb-like" data-href="http://kidsplaza.vn" data-send="false" data-layout="button_count" data-width="20" data-show-faces="false"></div>
                            </div>
                        </li>
                        <li>
                            <a href="#google">
                                <img src="<?php echo $url ?>/images/google.png">
                            </a>
                            <div id="google" class="so-plusone so-social-share">
                                <div class="marker"></div>
                                <div class="g-plusone" data-size="medium"></div>
                                <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
                            </div>
                        </li>
                        <li>
                            <a href="#twitter">
                                <img src="<?php echo $url ?>/images/twitter.png">
                            </a>
                            <div id="twitter" class="so-twitter so-social-share">
                                <div class="marker"></div>
                                <a href="https://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-dnt="true">Tweet</a>
                            </div>
                        </li>
					</ul>
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $('.footer-social a').hover(function(){
                                $(this).next().show();
                                $(this).next().hover(function(){
                                    $(this).show();
                                },function(){
                                    $(this).hide();
                                });
                            },function(){
                                $(this).next().hide();
                            });
                        });
                        (function(d, s, id) {
                            var js, fjs = d.getElementsByTagName(s)[0];
                            if (d.getElementById(id)) return;
                            js = d.createElement(s);
                            js.id = id;
                            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=115245961994281";
                            fjs.parentNode.insertBefore(js, fjs);
                        }(document, 'script', 'facebook-jssdk'));
                        !function(d,s,id){
                            var js,fjs=d.getElementsByTagName(s)[0];
                            if(!d.getElementById(id)){
                                js=d.createElement(s);
                                js.id=id;
                                js.src="//platform.twitter.com/widgets.js";
                                fjs.parentNode.insertBefore(js,fjs);
                            }
                        }(document,"script","twitter-wjs");
                    </script>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
        /* resize facebook comments */
        (function(window){
            resize();
        })(this);
        $(document).ready(function(){
            resize();
        });
        function resize(){
            var kl = null;
            $(window).resize(function(){
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
            });
        }
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
	<?php wp_footer(); ?>
</body>

</html>