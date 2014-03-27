<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
die ('Please do not load this page directly. Thanks!'); ?>
	<span class="title">Bình luận (<?php comments_number(__('0'), __('1'), __('%'));?>)</span>
<?php if ( have_comments() ) : ?>
	<?php wp_list_comments('type=comment&avatar_size=60&callback=bbit_comment'); ?>	
	<span class="left"><?php previous_comments_link(); ?></span>
	<span class="right"><?php next_comments_link(); ?></span>
	<br>
<?php else : ?>

<?php endif; ?>

<?php if ( comments_open() ) : ?>

<div class="pad" id="respond">
	<p class="cancel-comment-reply"><?php cancel_comment_reply_link(); ?></p>
	<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
	<p>Vui lòng <a href="<?php echo wp_login_url( get_permalink() ); ?>">đăng nhập</a> để bình luận.</p>
	<?php else : ?>

	<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">
		<?php if ( is_user_logged_in() ) : ?>
		Chào <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a><br>
			<textarea placeholder="Bình luận của bạn..." name="comment" id="comment" rows="3" tabindex="4"></textarea>
			<button type="submit">Gửi</button>
			<?php comment_id_fields(); ?>
			<?php do_action('comment_form', $post->ID); ?>
		
		<?php else : ?>
			<input placeholder="Tên bạn" type="text" name="author" id="author" value="<?php echo esc_attr($comment_author); ?>" /><br>
			<input placeholder="Email" type="text" name="email" id="email" value="<?php echo esc_attr($comment_author_email); ?>" /><br>
			<textarea placeholder="Bình luận của bạn..." name="comment" id="comment" rows="3" tabindex="4"></textarea><br>
			<button type="submit">Gửi</button>
			<?php comment_id_fields(); ?>
			<?php do_action('comment_form', $post->ID); ?>
		<?php endif; ?>
	</form>
	<?php endif;?>
</div>

<?php endif; ?>