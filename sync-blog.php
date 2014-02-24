<?php
require_once 'app/Mage.php';
Mage::app('admin', 'store');

$tree = array();

$host = '127.0.0.1';
$user = 'root';
$pass = 'tooor';
$baseDir = Mage::getBaseDir();
$baseUrl = 'http://kidsplaza.vn/media/';

$db = @mysql_connect($host, $user, $pass);
if (!$db) die("Error: db connection\n");
if (!@mysql_select_db('kid', $db)) die("Error: db select\n");
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
    select p.url,p.title,p.summary,p.status,pc.content
    from idv_seller_page as p
    inner join idv_seller_page_content as pc on pc.id=p.id';
$rs = query($query);
while ($row = mysql_fetch_assoc($rs)){
    $page = Mage::getModel('cms/page');
    /* @var $page Mage_Cms_Model_Page */
    $pageId = $page->checkIdentifier($row['url'], 0);
    if ($pageId) $page->load($pageId);

    $page->setData(array(
        'page_id' => $pageId,
        'title' => $row['title'] ? $row['title'] : $row['url'],
        'identifier' => $row['url'],
        'stores' => array(0),
        'is_active' => $row['status'],
        'content' => $row['content']
    ));
    try{
        $page->save();
        if ($pageId){
            printf("Update page '%s' OK\n", $row['url']);
        }else{
            printf("Save page '%s' OK\n", $row['url']);
        }
    }catch (Exception $e){
        printf("Save page '%s' FAILED\n", $row['url']);
    }
    unset($page);
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
    select b.url,b.catId,b.title,b.thumnail,b.summary,b.meta_keywords,b.meta_description,b.createDate,b.lastUpdate,bc.content
    from idv_seller_news as b
    inner join idv_seller_news_content as bc on bc.id=b.id';
$rs = query($query);
$helper = Mage::getModel('catalog/product_url');
/* @var $helper Mage_Catalog_Model_Product_Url */
$i=0;
while ($row = mysql_fetch_assoc($rs)){
    //if ($i++ > 10) break;
    $blog = Mage::getModel('blog/post');
    /* @var $blog AW_Blog_Model_Post */
    $url = $row['url'] ? $row['url'] : $helper->formatUrlKey($row['title']);
    $blog->loadByIdentifier($url);
    $bid = $blog->getId();
    $blog->setData(array(
        'post_id' => $bid,
        'title' => $row['title'],
        'identifier' => $url,
        'stores' => array(0),
        'status' => 1,
        'comments' => 1,
        'thumb' => process_thumb($row['thumnail']),
        'short_content' => $row['summary'],
        'post_content' => $row['content'],
        'meta_keywords' => $row['meta_keywords'],
        'meta_description' => $row['meta_description'],
        'cats' => process_category($row['catId']),
        'created_time' => $row['createDate'],
        'update_time' => $row['lastUpdate']
    ));
    try{
        $blog->save();
        if ($bid){
            printf("Update blog: %s OK\n\n", $blog->getTitle());
        }else{
            printf("Save blog: %s OK\n\n", $blog->getTitle());
        }
    }catch (Exception $e){
        printf("Error save blog: %s\n\n", $row['title']);
    }
    unset($blog);
}

function process_category($id){
    global $tree;

    if (!$id) return array();
    $path = isset($tree[$id]) ? $tree[$id]['path'] : array();
    $currentPath = [0];
    foreach ($path as $cid){
        if (!isset($tree[$cid])) break;
        $collection = Mage::getModel('blog/cat')->getCollection()
            ->addFieldToFilter('identifier', array('eq' => $tree[$cid]['identifier']))
            ->addFieldToFilter('parent', array('eq' => end($currentPath)));

        if ($collection->getSize()){
            printf("Exist category: %s\n", $tree[$cid]['title']);
            $model = $collection->getFirstItem();
            $currentPath[] = $model->getId();
            unset($model);
        }else{
            $model = Mage::getModel('blog/cat');
            $model->setData($tree[$cid]);
            $model->setData('parent', end($currentPath));
            try{
                $model->save();
                $currentPath[] = $model->getId();
                printf("Save category: %s OK\n", $model->getTitle());
            }catch (Exception $e){
                printf("Error save category: %s\n", $tree[$cid]['title']);
            }
            unset($model);
        }
        unset($collection);
    }
    return end($currentPath);
}

function process_thumb($uri){
    global $baseDir, $baseUrl;

    if (!$uri) return '';
    $dir = $baseDir .DS. 'media' .DS. 'wysiwyg' .DS. 'news' .DS;
    if (!is_dir($dir)) @mkdir($dir, 0777, true);
    $filePath = $dir . $uri . '.jpg';
    if (file_exists($filePath)){
        printf("Exist post thumb: %s\n", $filePath);
        return "wysiwyg" .DS. 'news' .DS. $uri. '.jpg';
    }else{
        $fileUrl = $baseUrl . 'news/' . $uri . '.jpg';
        echo "Saving post thumb: {$fileUrl} ";
        if (file_put_contents($dir.$uri.'.jpg', file_get_contents($fileUrl))){
            echo "OK\n";
            return "wysiwyg" .DS. 'news' .DS. $uri. '.jpg';
        }else{
            echo "FAILED\n";
            return '';
        }
    }
}