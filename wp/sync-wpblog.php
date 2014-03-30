<?php
define( 'ABSPATH', dirname(__FILE__) . '/' );
define( 'WPINC', 'wp-includes' );
require_once(ABSPATH.'/wp-load.php');
require_once(ABSPATH.WPINC.'/post.php');
global $wpdb;

$tree = array();

$host = '127.0.0.1';
$user = 'root';
$pass = 'mysql';
$baseUrl = 'http://kidsplaza.vn/media/';

$db = @mysql_connect($host, $user, $pass);
if (!$db) die("Error: db connection\n");
if (!@mysql_select_db('kidsplog', $db)) die("Error: db select\n");
@mysql_query('set names latin1');

function query($query){
    global $db;

    if ($query){
        $rs = @mysql_query($query, $db);
        if (!$rs) die("Error: " . $query . "\n");
        return $rs;
    }else die("Error: empty query\n");
}


$query = '
    select c.id,c.catPath,c.url,c.name,c.summary,c.parentId,c.meta_keyword,c.meta_description
    from idv_seller_news_category as c';
$rs = query($query);
while ($row = mysql_fetch_assoc($rs)){
    $path = explode(':', $row['catPath']);
    $newPath = array();
    foreach ($path as $c){
        if (!$c) continue;
        $newPath[] = $c;
    }

    $tree[$row['id']] = array(
        'title' => $row['name'],
        'identifier' => $row['url'],
        'stores' => array(0),
        'meta_keywords' => $row['meta_keyword'],
        'meta_description' => $row['meta_description'],
        'path' => array_reverse($newPath)
    );
}
$query = '
    select b.id,b.url,b.catId,b.title,b.thumnail,b.summary,b.meta_keywords,b.meta_description,b.createDate,b.lastUpdate,bc.content
    from idv_seller_news as b
    inner join idv_seller_news_content as bc on bc.id=b.id';
$rs = query($query);
/* @var $helper Mage_Catalog_Model_Product_Url */
$i=0;
while ($row = mysql_fetch_assoc($rs)){
    if ($i++ > 10) break;
    if($row['summary']==''){
        $content = $row['content'];
    }else{
        $content = $row['summary'].$row['content'] ? '<!--more-->'.$row['content'] : '';
    }
    $data = array(
        'id' => $row['id'],
        'post_author' => '1',
        'post_date' => $row['createDate'],
        'post_content' => $content,
        'post_title' => $row['title'],
        'post_status' => 'publish',
        'comment_status' => 'open',
        'ping_status' => 'open',
        'post_modified' => $row['lastUpdate'],
        'post_modified_gmt' => $row['lastUpdate'],
        'post_parent' => '0',
        'guid' => '',
        'post_type' => 'post'
    );
    $catid = process_category($row['catId']);

    $id = wp_insert_post($data);
    wp_set_object_terms( $id, $catid["term_id"], 'category', true);
    if($row['thumnail']){
        process_thumb($row['thumnail'],$id,$row['title']);
    }
}

function process_category($id){
    global $tree;

    if (!$id) return array();
    $path = isset($tree[$id]) ? $tree[$id]['path'] : array();
    $currentPath = [0];
    foreach ($path as $cid){
        if (!isset($tree[$cid])) break;
        $catid = wp_insert_term(
            $tree[$cid]['title'],
            'category',
            array(
                'description'	=> '',
                'slug' 		=> $tree[$cid]['identifier'],
                'parent'=> $currentPath
            )
        );
        $currentPath[] = $catid;
    }
    return end($currentPath);
}

function process_thumb($uri,$post_id,$myTitle)
{
    global $baseUrl;
    $fileUrl = $baseUrl . 'news/' . $uri . '.jpg';
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($fileUrl);
    $filename = basename($fileUrl);
    if(wp_mkdir_p($upload_dir['path']))
        $file = $upload_dir['path'] . '/' . $filename;
    else
        $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_excerpt' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    require_once(ABSPATH . '/wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    wp_update_attachment_metadata( $attach_id, $attach_data );

    update_post_meta($attach_id, '_wp_attachment_image_alt', $myTitle);

    set_post_thumbnail( $post_id, $attach_id );
}