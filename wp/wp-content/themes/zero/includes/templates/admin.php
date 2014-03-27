<span class="title">Quản trị</span>
<article><a href="<?php bloginfo('url'); ?>/wp-admin" target="_blank">Bảng điều khiển</a></article>
<article><a href="<?php bloginfo('url'); ?>/wp-admin/post-new.php" target="_blank">Viết bài mới</a></article>
<?php if (is_single()): ?>
<article><?php edit_post_link('Chỉnh sửa', '', ''); ?></article>
<?php endif;?>
<article><a href="<?php echo wp_logout_url(); ?>" title="Logout">Đăng xuất →</a></article>