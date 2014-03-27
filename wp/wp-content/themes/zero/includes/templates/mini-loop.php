<article>
    <a href="<?php the_permalink(); ?>" class="post_title" title="<?php the_title(); ?>"><?php the_title(); ?></a>
    <span class="count"> - <?php echo get_bbit_views(get_the_ID()); ?> lượt xem</span>
</article>