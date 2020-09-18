<?php
define('DEBUG', false); 
define('PS_SHOP_PATH', 'https://yourwebsite');
define('PS_WS_AUTH_KEY', 'yourauthkey');
require_once('../PSWebServiceLibrary.php');

try {

    $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
    $opt = array('resource' => 'products');
    $xml = $webService->get(array('url' => PS_SHOP_PATH . '/api/products?schema=blank'));
    $resource_product = $xml->children()->children();

    unset($resource_product->id);
    unset($resource_product->position_in_category);
    unset($resource_product->manufacturer_name);
    unset($resource_product->id_default_combination);
    unset($resource_product->associations);

    $resource_product->id_shop = 1;
    $resource_product->minimal_quantity = 1;
    $resource_product->available_for_order = 1;
    $resource_product->show_price = 1;
    
    $resource_product->id_category_default = 2;
    $resource_product->price = 12.23;
    $resource_product->active = 1;
    $resource_product->visibility = 'both';
    $resource_product->name->language[0] = "blablabla";
    $resource_product->description->language[0] = "blablabla";
    $resource_product->state = 1;

    $resource_product->addChild('associations')->addChild('categories')->addChild('category')->addChild('id', 6);
    $resource_product->addChild('associations')->addChild('categories')->addChild('category')->addChild('id', '7');
    $resource_product->addChild('associations')->addChild('categories')->addChild('category')->addChild('id', "8");

    $opt = array('resource' => 'products');
    $opt['postXml'] = $xml->asXML();
    $xml = $webService->add($opt);
    $id = $xml->product->id;
    echo $id;

    $new_product_categories = array(29, 30, 31); 
    $xml = $webService->get(array('resource' => 'products', 'id' => $id));
    $product = $xml->children()->children();


    unset($product->manufacturer_name);
    unset($product->quantity);

    unset($product->associations->categories);
    $categories = $product->associations->addChild('categories');

    foreach ($new_product_categories as $id_category) {
        $category = $categories->addChild('category');
        $category->addChild('id', $id_category);
    }

    $xml_response = $webService->edit(array('resource' => 'products', 'id' => $id, 'putXml' => $xml->asXML()));

    echo "$xml_response->product->id";


} catch (PrestaShopWebserviceException $e) {
     $trace = $e->getTrace();
    if ($trace[0]['args'][0] == 404) echo 'Bad ID';
    else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
    else echo $e->getMessage();
}