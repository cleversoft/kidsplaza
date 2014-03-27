<?php global $data; ?>
    <nav id="nav" role="navigation">
        <span class="title">Danh mục</span>
        <?php wp_nav_menu (array('theme_location' => 'menu', 'container_class' => 'nav_menu'));?>
        <a id="close" href="#top">Đóng</a>
    </nav>

	<?php get_search_form(); ?>

    <nav id="FooterMenu">  
    <ul>	
        <li><a href="http://bbit.vn">Trang chủ</a></li>
        <li><a href="http://bbit.vn/gioi-thieu">Giới thiệu</a></li>
		<li><a href="http://bbit.vn/dieu-khoan-su-dung">Điều khoản</a></li>
        <li class="right"><a href="#top" title="Lên đầu trang">▲</a></li>
    </ul>
    </nav>

    <footer>
	    <?php if (is_home()): ?>
		    <?php echo $data['bbit_footer_text']; ?>
		<?php else: ?>
		    <?php echo $data['bbit_footer_text2']; ?>
		<?php endif; ?>
	</footer>
</div>
<?php wp_footer(); ?>
<?php if ( $data['bbit_mobile_navi'] == 1 ) : ?><script src="<?php bloginfo('template_directory'); ?>/assets/js/main.js"></script><?php endif; ?>
<?php if ( $data['bbit_protected'] == 1 ) : ?><script src="<?php bloginfo('template_directory'); ?>/assets/js/protected.js"></script><?php endif; ?>
</body>
</html>