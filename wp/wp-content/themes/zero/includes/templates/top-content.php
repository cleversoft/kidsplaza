<div id="top-content" class="hrecipe">
	<article class="hreview-aggregate">
	<?php the_post_thumbnail('image', array('class' => 'photo', 'alt' => get_the_title())); ?>
	<?php if(get_post_meta($post->ID, 'bbit_score', true)): ?>
	<label>Xếp hạng:</label> <span class="rating"><span class="average"><?php echo get_post_meta($post->ID, 'bbit_score', true); ?></span>♥ - <span class="votes"><?php echo get_bbit_views(get_the_ID()); ?></span> phiếu</span><br>
	<?php endif; ?>
    <?php if(get_post_meta($post->ID, 'bbit_phathanh', true)): ?>
    <label>Phát hành:</label> <a href="<?php echo get_post_meta($post->ID, 'bbit_phathanh_link', true); ?>"><?php echo get_post_meta($post->ID, 'bbit_phathanh', true); ?></a><br>
    <?php endif; ?>
	<?php if(get_post_meta($post->ID, 'bbit_theloai', true)): ?>
    <label>Thể loại:</label> <?php echo get_post_meta($post->ID, 'bbit_theloai', true); ?><br>
    <?php endif; ?>
	<?php if(get_post_meta($post->ID, 'bbit_file_size', true)): ?>
    <label>Dung lượng:</label> <?php echo get_post_meta($post->ID, 'bbit_file_size', true); ?><br>
    <?php endif; ?>
	<?php if(get_post_meta($post->ID, 'bbit_support', true)): ?>
    <label>Hỗ trợ:</label> <?php echo get_post_meta($post->ID, 'bbit_support', true); ?><br>
    <?php endif; ?>
	<?php if(get_post_meta($post->ID, 'bbit_link_download', true)): ?>
    <span class="icon_download"><a rel="nofollow" href="<?php echo get_post_meta($post->ID, 'bbit_link_download', true); ?>" title="Tải về máy">Tải về máy</a></span>
    <?php endif; ?>
    </article>
	<h1 class="item fn pad"><?php the_title(); ?></h1>
</div>