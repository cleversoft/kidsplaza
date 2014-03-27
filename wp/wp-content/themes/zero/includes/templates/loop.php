<?php if(has_post_format('image')): ?>
<article class="format-image">
	<?php $time = get_post_time('G', true, $post);
	$newtime = time() - $time;
	if ( $newtime < 259200 ): ?>
	<span class="ribbon_new"></span>
    <?php endif; ?>
    <a href="<?php the_permalink(); ?>" title="<?php get_the_title(); ?>">
    <img src="<?php echo first_image(); ?>" alt="<?php the_title(); ?>"></a>
    <h2><a href="<?php the_permalink(); ?>" class="post_title" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
	<span class="icon_view"><?php echo get_bbit_views(get_the_ID()); ?></span>
    <?php global $data; if ( $data['bbit_show_comments'] == 1 ): ?>
	<span class="icon_comment"><?php comments_number(__('0'), __('1'), __('%'));?></span>
    <?php endif; ?>
</article>   
<?php else: ?>
<article>
	<?php $time = get_post_time('G', true, $post);
	$newtime = time() - $time;
	if ( $newtime < 259200 ): ?>
	<span class="ribbon_new"></span>
    <?php endif; ?>
    <a href="<?php the_permalink(); ?>" title="<?php get_the_title(); ?>">
    <?php the_post_thumbnail('image', array('class' => 'photo', 'alt' => get_the_title())); ?></a>
    <h2><a href="<?php the_permalink(); ?>" class="post_title" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
    <p><?php echo bbit_string_limit(get_the_excerpt(), 47); ?>...</p>
	<?php if(get_post_meta($post->ID, 'bbit_link_download', true)): ?>
    <span class="icon_download"><a href="<?php echo get_post_meta($post->ID, 'bbit_link_download', true); ?>" rel="nofollow">Tải về</a></span>
    <?php endif; ?>
	<span class="icon_view"><?php echo get_bbit_views(get_the_ID()); ?></span>
    <?php global $data; if ( $data['bbit_show_comments'] == 1 ): ?>
	<span class="icon_comment"><?php comments_number(__('0'), __('1'), __('%'));?></span>
    <?php endif; ?>
</article>      
<?php endif; ?>