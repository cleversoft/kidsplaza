<?php

/*-----------------------------------------------------------------------------------*/
/* Add Theme Options, Include Modun
/*-----------------------------------------------------------------------------------*/
include_once('admin/index.php');
include_once('includes/index.php');

/*-----------------------------------------------------------------------------------*/
/* Add Theme Support
/*-----------------------------------------------------------------------------------*/
add_theme_support('post-thumbnails', array('post'));
add_theme_support('post-formats', array('image', 'video', 'audio'));
//add_image_size('image', 55, 55, true);

register_nav_menu('menu', __('Danh mục'));

register_sidebar(array(
	'name' => 'Header',
	'before_widget' => '',
	'after_widget' => '',
	'before_title' => '<span class="title">',
	'after_title' => '</span>',
));

register_sidebar(array(
	'name' => 'Home',
	'before_widget' => '',
	'after_widget' => '',
	'before_title' => '<span class="title">',
	'after_title' => '</span>',
));

register_sidebar(array(
	'name' => 'Footer',
	'before_widget' => '',
	'after_widget' => '',
	'before_title' => '<span class="title">',
	'after_title' => '</span>',
));	

/*-----------------------------------------------------------------------------------*/
/* Get First Image
/*-----------------------------------------------------------------------------------*/
function first_image() {
  global $post, $posts;
  $first_img = '';
  ob_start();
  ob_end_clean();
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  $first_img = $matches [1] [0];
  return $first_img;
}

/*-----------------------------------------------------------------------------------*/
/* Post view
/*-----------------------------------------------------------------------------------*/
function get_bbit_views($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0";
    }
    return $count;
}

function bbit_views($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

/*-----------------------------------------------------------------------------------*/
/* Pagenavi
/*-----------------------------------------------------------------------------------*/
function bbit_pagination($pages = '', $range = 2)
{
    $showitems = ($range * 2)+1;  

    global $paged;
    if(empty($paged)) $paged = 1;

    if($pages == '')
    {
        global $wp_query;
        $pages = $wp_query->max_num_pages;
        if(!$pages)
		{ 
		$pages = 1; 
		}
    }

    if(1 != $pages)
    {
        echo "<div class='pad pagenavi'>";
        if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'> <<</a>";
        if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'> <</a>";

        for ($i=1; $i <= $pages; $i++)
        {
            if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
            {
            echo ($paged == $i)? "<span class='NowPage'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='NextPage' >".$i."</a>";
            }
        }

        if ($paged < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($paged + 1)."'>> </a>";  
        if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>>> </a>";
        echo "</div>\n";
    }
}

/*-----------------------------------------------------------------------------------*/
/* Limited Text
/*-----------------------------------------------------------------------------------*/
function bbit_string_limit($string, $word_limit)
{
    $words = explode(' ', $string, ($word_limit + 1));
    if(count($words) > $word_limit) {
        array_pop($words);
    }
    return implode(' ', $words);
}

/*-----------------------------------------------------------------------------------*/
/* Time Ago
/*-----------------------------------------------------------------------------------*/
function bbit_time_ago() {

	global $post;

	$date = get_post_time('G', true, $post);

	$chunks = array(
		array( 60 * 60 * 24 * 365 , __( 'năm'), __( 'năm') ),
		array( 60 * 60 * 24 * 30 , __( 'tháng'), __( 'tháng') ),
		array( 60 * 60 * 24 * 7, __( 'tuần'), __( 'tuần') ),
		array( 60 * 60 * 24 , __( 'ngày'), __( 'ngày') ),
		array( 60 * 60 , __( 'giờ'), __( 'giờ') ),
		array( 60 , __( 'phút'), __( 'phút') ),
		array( 1, __( 'giây'), __( 'giây') )
	);

	if ( !is_numeric( $date ) ) {
		$time_chunks = explode( ':', str_replace( ' ', ':', $date ) );
		$date_chunks = explode( '-', str_replace( ' ', '-', $date ) );
		$date = gmmktime( (int)$time_chunks[1], (int)$time_chunks[2], (int)$time_chunks[3], (int)$date_chunks[1], (int)$date_chunks[2], (int)$date_chunks[0] );
	}

	$current_time = current_time( 'mysql', $gmt = 0 );
	$newer_date = strtotime( $current_time );

	$since = $newer_date - $date;

	if ( 0 > $since )
		return __( 'sometime');

	for ( $i = 0, $j = count($chunks); $i < $j; $i++) {
		$seconds = $chunks[$i][0];

		if ( ( $count = floor($since / $seconds) ) != 0 )
			break;
	}

	$output = ( 1 == $count ) ? '1 '. $chunks[$i][1] : $count . ' ' . $chunks[$i][2];
 

	if ( !(int)trim($output) ){
		$output = '0 ' . __( 'giây');
	}
 
	$output .= __(' trước');
 
	return $output;
}
add_filter('the_time', 'bbit_time_ago');

/*-----------------------------------------------------------------------------------*/
/* Modun Not Outlink
/*-----------------------------------------------------------------------------------*/
class bbit_outlink {
	public function __construct() {
	add_action( 'wp', array( $this, 'call_wp' ) );
	}

	function call_wp() {
		if ( ! is_admin() && ! is_feed() ) {
		$priority = 1000000000;
		add_filter( 'the_title', array( $this, 'call_filter_content' ), $priority );
		add_filter( 'the_content', array( $this, 'call_filter_content' ), $priority );
		add_filter( 'get_the_excerpt', array( $this, 'call_filter_content' ), $priority );
		}
		do_action('wpel_ready', array($this, 'call_filter_content'), $this);
	}

	function call_filter_content( $content ) {
		return $this->filter( $content );
	}

	function is_external( $href, $rel ) {
		for ( $x = 0, $count = count($this->ignored); $x < $count; $x++ ) {
			if ( strrpos( $href, $this->ignored[ $x ] ) !== FALSE )
				return FALSE;
		}

		return ( isset( $href ) AND ( strpos( $rel, 'external' ) !== FALSE
			OR  ( strpos( $href, strtolower( get_bloginfo( 'wpurl' ) ) ) === FALSE )
			AND ( substr( $href, 0, 7 ) == 'http://'
			OR substr( $href, 0, 8 ) == 'https://'
			OR substr( $href, 0, 6 ) == 'ftp://' ) ) );
	}

	function filter( $content ) {
		$content = preg_replace_callback( '/<a[^A-Za-z](.*?)>(.*?)<\/a[\s+]*>/is', array( $this, 'call_parse_link' ), $content );
		return $content;
	}

	function call_parse_link( $matches ) {
		$attrs = $matches[ 1 ];
		$attrs = stripslashes( $attrs );
		$attrs = shortcode_parse_atts( $attrs );

		$rel = ( isset( $attrs[ 'rel' ] ) ) ? strtolower( $attrs[ 'rel' ] ) : '';
		$href = $attrs[ 'href' ];
		$href = strtolower( $href );
		$href = trim( $href );
		if ( ! $this->is_external( $href, $rel ) )
			return $matches[ 0 ];
			$this->add_attr_value( $attrs, 'rel', 'nofollow' );

		$link = '<a ';

		foreach ( $attrs AS $key => $value )
		$link .= $key .'="'. $value .'" ';
		$link = substr( $link, 0, -1 );
		$link .= '>'. $matches[ 2 ] .'</a>';
		$link = apply_filters('wpel_external_link', $link, $matches[ 0 ], $matches[ 2 ], $attrs);
		return $link;
	}

	function add_attr_value( &$attrs, $attr_name, $value, $default = NULL ) {
		if ( key_exists( $attr_name, $attrs ) )
			$old_value = $attrs[ $attr_name ];

		if ( empty( $old_value ) )
			$old_value = '';

		$split = split( ' ', strtolower( $old_value ) );

		if ( in_array( $value, $split ) ) {
			$value = $old_value;
		} else {
			$value = ( empty( $old_value ) )
								? $value
								: $old_value .' '. $value;
		}

		if ( empty( $value ) AND $default === NULL ) {
			unset( $attrs[ $attr_name ] );
		} else {
			$attrs[ $attr_name ] = $value;
		}
		return $value;
	}
}
$bbit_outlink = new bbit_outlink();

/*-----------------------------------------------------------------------------------*/
/* Option Comment
/*-----------------------------------------------------------------------------------*/
function bbit_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
<article id="comment-<?php comment_ID() ?>">
	<span class="icon_comment"><strong><?php echo get_comment_author_link() ?></strong></span>
	<small><?php printf(__('%1$s lúc %2$s'), get_comment_date(),  get_comment_time()) ?></small>
	<?php if ($comment->comment_approved == '0') : ?><em>Bình luận chờ duyệt!</em><br><?php endif; ?>
	<?php comment_text() ?>
</article>
<?php }