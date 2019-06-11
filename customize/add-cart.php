<?php
require('autoload.php');
global $lumise;

$product_id = $_REQUEST['product'];
$product = $lumise->lib->get_product();
//$lumise->connector->set_session('lumise_cart', array('items' => array()));
if(is_array($product)){
    $stages = $lumise->lib->dejson($product['stages']);
    $template = $template_price = array();
    $tmp_price = 0;
    $screenshorts = array($product['thumbnail_url']);
    
    foreach ($stages as $stage => $data) {
    
        if(
            isset($data->template) &&
            isset($data->template->id) && 
            $data->template->id > 0
        ){
            $template[$stage] = $data->template->id;
            $tmp = $lumise->lib->get_template($data->template->id);
            $template_price[$stage] = empty($tmp['price'])? 0: $tmp['price'];
            $tmp_price += empty($tmp['price'])? 0: $tmp['price'];
        }
    
    }
    
    $item = array(
        'id' => $product_id,
        'cart_id' => time(),
        'qty' => 1,
        'product_id' => $product_id,
        'product_cms' => $product_id,
        'product_name' => $product['name'],
        'price' => array(
            'total' => $product['price'] + $tmp_price,
            'attr' => 0,
            'template' => $template_price,
            'resource' => 0,
            'base' => $product['price'],
        ),
        'attributes' => array(),
        'printing' => 0,
        'resource' => array(),
        'uploads' => array(),
        'design' => array(),
        'template' => $lumise->lib->enjson($template),
        'screenshots' => $screenshorts
    );

    $lumise->lib->add_item_cart($item);
    $lumise->redirect($lumise->connector->editor_url.'cart.php');
}
