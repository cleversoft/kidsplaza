<?php get_header(); ?>

    <?php if (have_posts()) : while (have_posts()) : the_post(); bbit_views(get_the_ID()); ?>

    <?php bbit_breadcrumbs(); ?>

    <div id="top-content">
		<h1 class="pad bderTop"><?php the_title(); ?></h1>
    </div>

    <div id="content" class="pad bderTop lineH20">
        <br><?php the_content(); ?><br>
    </div>
    <?php endwhile; endif; ?>

<?php get_footer(); ?>