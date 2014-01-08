<?php
require_once 'app/Mage.php';
Mage::app('admin', 'store');

$root = 2;
$host = '127.0.0.1';
$user = 'root';
$pass = 'tooor';
$attributeSetId = 4;
$website_ids = array(1, 2);

$typeId = 'simple';
$base = Mage::getBaseDir();
$baseUrl = 'http://kidsplaza.vn/media/';
$tree = array();
$attributeBrand = null;
$attributeSetup = null;
$mediaGalleryBackendModel = null;
$brands = array();

$db = @mysql_connect($host, $user, $pass);
if (!$db) die("Error: db connection\n");
if (!@mysql_select_db('kid', $db)) die("Error: db select\n");
@mysql_query('set names latin1');

$query = '
    select m.id,m.catPath,m.url,m.name,m.summary,m.imgUrl,m.parentId,m.meta_title,m.meta_keyword,m.meta_description,c.static_html
    from idv_seller_category as m
    left join idv_seller_category_content as c on m.id=c.id';
$rs = query($query);
while ($row = mysql_fetch_assoc($rs)){
    $path = array();
    foreach (explode(':', $row['catPath']) as $node){
        if (!$node) continue;
        $path[] = $node;
    }
    $tree[$row['id']] = array(
        'id' => $row['id'],
        'name' => $row['name'],
        'url_key' => $row['url'],
        'meta_title' => $row['meta_title'],
        'meta_keywords' => $row['meta_keyword'],
        'meta_description' => $row['meta_description'],
        'summary' => $row['summary'],
        'description' => $row['static_html'],
        'image' => $row['imgUrl'],
        'path' => implode('/', array_reverse($path))
    );
}

$query = '
    select m.id,m.product_cat,m.storeSKU,m.proName,m.proSummary,m.url,m.meta_title,m.meta_keyword,m.meta_description,p.price,p.market_price,p.quantity,i.description,i.video_code,b.name as brand
    from idv_sell_product_store as m
    inner join idv_sell_product_price as p on m.id=p.id
    left join idv_sell_product_info as i on m.id=i.id
    left join idv_brand as b on b.id=m.brandId';
$rs = query($query);
$i = 0;
while ($row = mysql_fetch_assoc($rs)){
    echo "Import: ".$row['proName']."\n";
    $product = Mage::getModel('catalog/product');
    $sku = $row['storeSKU'] ? $row['storeSKU'] : 'KID-'.$row['id'];
    $productId = '';
    if ($sku){
        $productId = $product->getIdBySku($sku);
    }

    $data = array();
    if ($productId){
        $product->load($productId);
    }else{
        $product->setData('type_id', $typeId);
        $product->setData('attribute_set_id', $attributeSetId);
    }
    $product->setData('entity_id', $productId);
    $product->setData('name', $row['proName']);
    $product->setData('short_description', $row['proSummary']);
    $product->setData('description', $row['description']);
    $product->setData('sku', $sku);
    $product->setData('url_key', $row['url']);
    $product->setData('meta_title', $row['meta_title']);
    $product->setData('meta_keyword', $row['meta_keyword']);
    $product->setData('meta_description', $row['meta_description']);
    $product->setData('price', $row['market_price']  > $row['price'] ? $row['market_price'] : $row['price']);
    $product->setData('special_price', $row['market_price']  > $row['price'] ? $row['price'] : '');
    $product->setData('website_ids', $website_ids);
    $product->setData('weight', 1);
    $product->setData('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
    $product->setData('status', 1);
    $product->setData('tax_class_id', 0);
    $product->setData('category_ids', process_category($row));
    $product->setData('brand', process_brand($row));

    if ($stock = $product->getData('stock_item')){
        $stock->setData('qty', $row['quantity']);
        $stock->setData('is_in_stock', $row['quantity'] > 0 ? 1 : 0);
        $stock->save();
    }else{
        $product->setData('stock_data', array(
            'qty' => $row['quantity'],
            'is_in_stock' => $row['quantity'] > 0 ? 1 : 0
        ));
    }
    process_image($product, $row);
    $product->setIsMassupdate(true);
    $product->setExcludeUrlRewrite(true);

    $product->save();

    if (!$productId) echo 'Saved product: '.$product->getName()."\n\n";
    else echo 'Updated product: '.$product->getName()."\n\n";

    //if ($i++ == 1) break;
    $i++;
}
echo 'Save product(s): '.$i."\n";

function process_brand($data){
    global $attributeSetup, $attributeBrand, $brands;

    $out = '';
    if (isset($data['brand'])){
        if (!$attributeBrand && !$attributeSetup){
            $attributeBrand = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'brand');
            $attributeSetup = new Mage_Eav_Model_Entity_Setup('core_setup');
        }
        if ($attributeBrand && $attributeSetup){
            if (!count($brands)){
                $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                    ->setAttributeFilter($attributeBrand->getAttributeId())
                    ->setStoreFilter(0)
                    ->load()
                    ->toOptionArray();

                foreach ($collection as $option){
                    $brands[$option['value']] = $option['label'];
                }
            }
            foreach ($brands as $brandId => $brand){
                if ($brand == $data['brand']){
                    echo 'Exist brand: '.$data['brand']."\n";
                    $out = $brandId;
                    break;
                }
            }
            if (!$out){
                $attributeSetup->addAttributeOption(array(
                    'attribute_id' => $attributeBrand->getAttributeId(),
                    'value' => array(
                        array(0 => $data['brand'])
                    )
                ));
                echo "Add brand: " . $data['brand'] . "\n";
                $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                    ->setAttributeFilter($attributeBrand->getAttributeId())
                    ->setStoreFilter(0)
                    ->load()
                    ->toOptionArray();

                foreach ($collection as $option){
                    if ($option['label'] == $data['brand']){
                        $out = $option['value'];
                        $brands[$option['value']] = $option['label'];
                        break;
                    }
                }
            }
        }
    }
    return $out;
}

function process_image(&$product, $data){
    global $mediaGalleryBackendModel,$base,$baseUrl;

    $defaultImage = '';
    $arrayToMassAdd = array();
    $query = 'select * from idv_sell_product_image_name where proId='.$data['id'];
    $rs = query($query);
    $dir = $base.DS.'var'.DS.'tmp'.DS;
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    if (!$mediaGalleryBackendModel){
        $mediaGalleryBackendModel = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode('catalog_product', 'media_gallery')
            ->getBackend();
    }
    while ($row = mysql_fetch_assoc($rs)){
        if (!$row['imgName']) continue;
        $file = $row['imgName'] . '.jpg';
        if ($row['isMain'] == 1) $defaultImage = $file;
        try{
            if (file_exists($dir.$file)){
                echo "Exist product image: " . $file . "\n";
            }else{
                $url = $baseUrl.'product/'.$file;
                echo "Saving product image: " . $url;
                file_put_contents($dir.$file, file_get_contents($url));
                echo " OK\n";
            }

            if (!$mediaGalleryBackendModel->getImage($product, getImage($file))){
                $arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => null);
            }
        }catch (Exception $e){
            echo " FAILED\n";
        }
    }

    $addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes(
        $product,
        $arrayToMassAdd,
        $dir,
        false,
        false
    );

    if (count($addedFilesCorrespondence['alreadyAddedFilesNames'])){
        $defaultImage = $defaultImage ? $defaultImage : reset($arrayToMassAdd);
        if (in_array($defaultImage, $addedFilesCorrespondence['alreadyAddedFiles'])){
            $keyInAddedFiles = array_search($defaultImage, $addedFilesCorrespondence['alreadyAddedFiles'], true);
            $defaultImage = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFiles];
        }else{
            $defaultImage = $addedFilesCorrespondence['alreadyAddedFilesNames'][0];
        }
        $product->setData('image', $defaultImage);
        $product->setData('small_image', $defaultImage);
        $product->setData('thumbnail', $defaultImage);
    }

    return $product;
}

function getImage($file){
    if (!$file) return '';
    $pathinfo = pathinfo($file);
    $basename = $pathinfo['basename'];
    switch(strlen($basename)){
        case 0:
            return '/_/_/'.$file;
        case 1:
            return '/'.substr($file,0,1).'/_/'.$file;
        default:
            return '/'.substr($file,0,1).'/'.substr($file,1,1).'/'.$file;
    }
}

function process_category($data){
    global $tree, $root, $base, $baseUrl;

    $out = array();
    $categories = explode(',', $data['product_cat']);
    foreach ($categories as $category){
        if (!$category) continue;
        if (isset($tree[$category])){
            $path = explode('/', $tree[$category]['path']);
            $currentPath = [1, $root];
            $level = 1;
            foreach ($path as $node){
                if (!$node) return;
                if (isset($tree[$node])){
                    $data = $tree[$node];
                    $collection = Mage::getModel('catalog/category')->getCollection()
                        ->addAttributeToFilter('name', array('eq' => $data['name']))
                        ->addAttributeToFIlter('parent_id', array('eq' => end($currentPath)));

                    if (!$collection->getSize()){
                        $categoryModel = Mage::getModel('catalog/category');
                        $categoryModel->setData(array(
                            'name' => $data['name'],
                            'meta_title' => $data['meta_title'],
                            'meta_keywords' => $data['meta_keywords'],
                            'meta_description' => $data['meta_description'],
                            'summary' => $data['summary'],
                            'description' => $data['description'],
                            'image' => $data['image'],
                            'url_key' => $data['url_key'],
                            'is_active' => 1,
                            'is_anchor' => 1,
                            'store' => 0,
                            'path' => implode('/', $currentPath),
                            'custom_use_parent_settings' => 1
                        ));
                        try{
                            if ($data['image']){
                                $dir = $base.DS.'media'.DS.'catalog'.DS.'category'.DS;
                                if (!is_dir($dir)) mkdir($dir, 0777, true);
                                $file = $dir.$data['image'];
                                $url = $baseUrl.'category/'.$data['image'];
                                if (!file_exists($file)){
                                    echo 'Saving category image: '.$url;
                                    @file_put_contents($file, file_get_contents($url));
                                    echo " OK\n";
                                }else{
                                    echo "Exist catagory image: ".$data['image']."\n";
                                }
                            }
                            $categoryModel->save();
                            echo "Add category: " . $categoryModel->getName() . "\n";
                            $currentPath[] = $categoryModel->getId();
                        }catch (Exception $e){}
                        unset($categoryModel);
                    }else{
                        $categoryModel = $collection->getFirstItem();
                        $currentPath[] = $categoryModel->getId();
                        echo "Exist category lv $level: ".$data['name']."\n";
                        unset($categoryModel);
                    }
                    $level++;
                }
            }
            $out[] = end($currentPath);
        }
    }
    return implode(',', $out);
}

function query($query){
    global $db;

    if ($query){
        $rs = @mysql_query($query, $db);
        if (!$rs) die("Error: " . $query . "\n");
        return $rs;
    }else die("Error: empty query\n");
}