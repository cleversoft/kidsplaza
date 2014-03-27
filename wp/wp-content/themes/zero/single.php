<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); bbit_views(get_the_ID()); ?>

    <?php bbit_breadcrumbs(); ?>
	
    <?php get_template_part( 'includes/templates/top-content', 'single' ); ?>

    <div id="content" class="pad bderTop lineH20">
        <div class="clearfix"></div><br>

        <?php the_content(); ?><?php wp_link_pages() ?>

        <?php if(get_post_meta($post->ID, 'bbit_link_download', true)): ?>
        <a href="<?php echo get_post_meta($post->ID, 'bbit_link_download', true); ?>" rel="nofollow" class="downloadfree">Tải về máy</a>
        <?php endif; ?>
        <span class="right"><i>Đăng <?php the_time(); ?> bởi <?php the_author(); ?></i></span>
        <div class="clearfix"></div><br>

        <span class="left"><?php previous_post_link('%link', __('&larr; %title'), $in_same_cat = true); ?></span>
        <span class="right"><?php next_post_link('%link', __('%title &rarr;'), $in_same_cat = true); ?></span>
        <div class="clearfix"></div>
        <p class="Tag">Từ khóa: <?php the_tags('',''); get_template_part( 'includes/templates/keyword', 'single' );?></p>
		<?php if ( $data['bbit_social']['facebook'] == 1 ) : ?>
		<div id="fb-root"></div>
        <script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/vi_VN/all.js#xfbml=1&appId=199826756851404";fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));</script>
	    <div class="fb-like" data-href="<?php the_permalink(); ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
		<?php endif; ?>
		<?php if ( $data['bbit_social']['googleplus'] == 1 ) : ?>
		<script type="text/javascript" src="https://apis.google.com/js/platform.js">{lang: 'vi'}</script>
        <div class="g-plusone" data-size="medium"></div>
		<?php endif; ?>
    </div>
	<?php if ( $data['bbit_mp3_banner'] == 1 ) : ?>
	<span class="title">Game đang HOT</span>
    <script type="text/javascript" charset="UTF-8" src="http://cdn.adnexus.vn/scripts/bbitcorporation/52d13e709c9d3e1cfcf06482.js?v=4"></script>
	<?php endif; ?>
    <span class="title">Cùng chuyên mục</span>
	<?php
    $related = get_posts(array('category__in' => wp_get_post_categories($post->ID), 'numberposts' => 5, 'post__not_in' => array($post->ID)));
    if( $related ) foreach( $related as $post ) {
    setup_postdata($post);
    get_template_part( 'includes/templates/loop', 'single' ); }
	wp_reset_postdata();?>

	<span class="title">Có thể bạn tìm?</span>
    <?php
	$orig_post = $post;
	global $post; $tags = wp_get_post_tags($post->ID);
    if ($tags) {
		$tag_ids = array();
		foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
		$args=array('tag__in' => $tag_ids,'post__not_in' => array($post->ID),'posts_per_page'=>5,'caller_get_posts'=>1  );
    	$my_query = new wp_query( $args );while( $my_query->have_posts() )
		{
		$my_query->the_post();
    	get_template_part( 'includes/templates/mini-loop', 'single' );
		}
	}
	$post = $orig_post; 
	wp_reset_query();?>

	<?php if ( $data['bbit_show_comments'] == 1 ) : comments_template( '', true ); endif; ?>

<?php endwhile; endif; ?>

<?php get_footer(); ?>