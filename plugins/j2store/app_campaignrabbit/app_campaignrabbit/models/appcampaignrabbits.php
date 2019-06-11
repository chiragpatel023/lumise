<?php
/**
 * --------------------------------------------------------------------------------
 * APP - Campaign Rabbit
 * --------------------------------------------------------------------------------
 * @package     Joomla  3.x
 * @subpackage  J2 Store
 * @author      Alagesan, J2Store <support@j2store.org>
 * @copyright   Copyright (c) 2018 J2Store . All rights reserved.
 * @license     GNU/GPL license: v3 or later
 * @link        http://j2store.org
 * --------------------------------------------------------------------------------
 *
 * */
defined('_JEXEC') or die('Restricted access');
require_once (JPATH_ADMINISTRATOR . '/components/com_j2store/library/appmodel.php');
require_once(JPATH_SITE.'/plugins/j2store/app_campaignrabbit/library/campaignrabbit/vendor/autoload.php');
class J2StoreModelAppCampaignRabbits extends J2StoreAppModel
{
    public $_element = 'app_campaignrabbit';

    public function getPluginParams(){
        $plugin = JPluginHelper::getPlugin('j2store', $this->_element);
        $params = new JRegistry($plugin->params);
        return $params;
    }

    public function getPlugin(){
        $db = JFactory::getDBo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__extensions')->where('type='.$db->q('plugin'))->where('element='.$db->q($this->_element))->where('folder='.$db->q('j2store'));
        $db->setQuery($query);
        return $db->loadObject();
    }

    public function saveParams($params){
        $json = $params->toString();
        $db = JFactory::getDbo ();
        $query = $db->getQuery ( true )->update ( $db->qn ( '#__extensions' ) )->set ( $db->qn ( 'params' ) . ' = ' . $db->q ( $json ) )->where ( $db->qn ( 'element' ) . ' = ' . $db->q ( $this->_element ) )->where ( $db->qn ( 'folder' ) . ' = ' . $db->q ( 'j2store' ) )->where ( $db->qn ( 'type' ) . ' = ' . $db->q ( 'plugin' ) );
        $db->setQuery ( $query );
        $db->execute ();
    }

    public function getInvoiceQuery($is_count = false) {
        $db = JFactory::getDbo ();
        $query = $db->getQuery ( true );
        if($is_count) {
            $query->select('COUNT(#__j2store_orders.j2store_order_id)')->from('#__j2store_orders');
        }else {
            $query->select('#__j2store_orders.*')->from('#__j2store_orders');
        }

        $plugin_params = $this->getPluginParams();

        $query->where('#__j2store_orders.campaign_order_id = ""');


        $zero_order = $plugin_params->get('synch_zero_order',1);
        if(!$zero_order){
            $query->where('#__j2store_orders.order_total > 0');
        }
        $order_status = $plugin_params->get('orderstatus',array('*'));
        if(!is_array($order_status)){
            $order_status = array($order_status);
        }
        if(!in_array('*',$order_status)){
            $query->where('#__j2store_orders.order_state_id IN ('.implode(',', $order_status ).')');
        }
        $query->group('#__j2store_orders.j2store_order_id');
        return $query;
    }

    public function getInvoiceList($limit=0,$start=0){
        $db = JFactory::getDbo ();
        try{
            $query = $this->getInvoiceQuery();
            $db->setQuery($query,$start,$limit);
            $list = $db->loadObjectList();
        }catch ( Exception $e ){
            $list = array();
        }
        return $list;
    }

    public function getInvoiceListCount(){
        $db = JFactory::getDbo ();
        try{
            $query = $this->getInvoiceQuery(true);
            $db->setQuery($query);
            $total = $db->loadResult();
        }catch ( Exception $e ){
            $total = 0;
        }
        return $total;
    }

    public function getCustomerQuery($is_count = false){
        $db = JFactory::getDbo ();
        $query = $db->getQuery ( true );
        if($is_count) {
            $query->select('COUNT(#__j2store_addresses.j2store_address_id)')->from('#__j2store_addresses');
        }else {
            $query->select('#__j2store_addresses.*')->from('#__j2store_addresses');
        }
        $query->join('INNER', '#__j2store_orders AS o ON #__j2store_addresses.email = o.user_email');
        $query->where('#__j2store_addresses.campaign_addr_id = ""');
        $query->group('#__j2store_addresses.j2store_address_id');
        return $query;
    }

    public function getCustomerList($limit=0,$start=0){
        $db = JFactory::getDbo ();
        try{
            $query = $this->getCustomerQuery();
            $db->setQuery($query,$start,$limit);
            $list = $db->loadObjectList();
        }catch (Exception $e){
            $list = array();
        }
        return $list;
    }

    public function getCustomerListCount(){
        $db = JFactory::getDbo ();
        try{
            $query = $this->getCustomerQuery(true);
            $db->setQuery($query);
            $total = $db->loadResult();
        }catch ( Exception $e ){
            $total = 0;
        }
        return $total;
    }

    public function buildQuery($overrideLimits=false) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__j2store_queues');
        $this->_buildQueryWhere ( $query );
        return $query;
    }

    protected function _buildQueryWhere(&$query)
    {
        $db = JFactory::getDbo ();
        $state = $this->getQueueState();

        if(isset( $state->queue_type ) && !empty( $state->queue_type )){
            $query->where ( 'queue_type ='.$db->q($state->queue_type) );
        }

        if(isset( $state->search ) && !empty( $state->search )){
            $query->where('(relation_id LIKE '.$db->q ( '%'.$state->search.'%' ).' OR status LIKE '.$db->q('%'.$state->search.'%').')');
        }

        $repeat_count = J2Store::config()->get('queue_repeat_count',10);
        if(!empty( $repeat_count ) && isset($state->is_expired) && $state->is_expired == 'no'){
            $query->where ( 'repeat_count <= '.$db->q($repeat_count) );
        }
        if(!empty( $repeat_count ) && isset($state->is_expired) && $state->is_expired == 'yes'){
            $query->where ( 'repeat_count > '.$db->q($repeat_count) );
        }
    }

    function getQueueState(){
        $state = array(
            'queue_type' => $this->getState ('queue_type',''),
            'search' => $this->getState('search',''),
            'is_expired' => $this->getState('is_expired','no')
        );
        return (Object)$state;
    }

    /**
     * Campaign Authentication
     * @return array - response from Campaign Rabbit
     */
    public function auth($params = ''){

        if(empty($params)){
            $params = $this->getPluginParams();
        }
        try{
            $api_token = $params->get('api_token','');
            $app_id = $params->get('app_id','');
            $domain = trim(JUri::root());
            $campaign = new \CampaignRabbit\CampaignRabbit\Action\Request($api_token,$app_id,$domain);
            $response = $campaign->request('POST','user/store/auth','');
            $out_response = $campaign->parseResponse($response);
        }catch (Exception $e){
            $ex_body = $e->getBody()->getContents();
            $out_response = array(
                'message'=> $e->getReasonPhrase(),
                'code'=>$e->getStatusCode(),
                'body'=> isset($ex_body->data) ? $ex_body->data: $ex_body
            );
        }
        return $out_response;
    }

    /**
     * Get Customer from Campaign Rabbit
     * @param $email - customer email
     * @param array - Response
     */
    public function getCustomer($email){
        $params = $this->getPluginParams();
        try{
            $api_token = $params->get('api_token','');
            $app_id = $params->get('app_id','');
            $domain = trim(JUri::root());
            $customer = new \CampaignRabbit\CampaignRabbit\Action\Customer($api_token,$app_id,$domain);
            $out_response = $customer->getCustomer($email);
        }catch (Exception $e){
            $ex_body = $e->getBody()->getContents();
            $out_response = array(
                'message'=> $e->getReasonPhrase(),
                'code'=>$e->getStatusCode(),
                'body'=> isset($ex_body->data) ? $ex_body->data: $ex_body
            );
        }
        return $out_response;
    }

    /**
     * Update Customer to Campaign Rabbit
     * @param $email - customer email
     * @param $name - customer name
     * @param $metas - customer meta data
     * @param array - Response
     */
    public function updateCustomer($customer_params,$email){
        $params = $this->getPluginParams();
        try{
            $api_token = $params->get('api_token','');
            $app_id = $params->get('app_id','');
            $domain = trim(JUri::root());

            $customer = new \CampaignRabbit\CampaignRabbit\Action\Customer($api_token,$app_id,$domain);

            $out_response = $customer->updateCustomer($customer_params,$email);
        }catch (Exception $e){
            $ex_body = $e->getBody()->getContents();
            $out_response = array(
                'message'=> $e->getReasonPhrase(),
                'code'=>$e->getStatusCode(),
                'body'=> isset($ex_body->data) ? $ex_body->data: $ex_body
            );
        }
        return $out_response;
    }


    /**
     * Create Customer to Campaign Rabbit
     * @param $email - customer email
     * @param $name - customer name
     * @param $metas - customer meta data
     * @param array - Response
     */
    public function createCustomer($customer_params){
        $params = $this->getPluginParams();
        try{
            $api_token = $params->get('api_token','');
            $app_id = $params->get('app_id','');
            $domain = trim(JUri::root());
            $customer = new \CampaignRabbit\CampaignRabbit\Action\Customer($api_token,$app_id,$domain);
            //$campaign = new \CampaignRabbit\CampaignRabbit\Action\Request($api_token,$app_id,$domain);
            $out_response = $customer->createCustomer($customer_params);//$campaign->request('POST','customer',json_encode($where));
            //$out_response = $campaign->parseResponse($response);
        }catch (Exception $e){
            $ex_body = $e->getBody()->getContents();
            $out_response = array(
                'message'=> $e->getReasonPhrase(),
                'code'=>$e->getStatusCode(),
                'body'=> isset($ex_body->data) ? $ex_body->data: $ex_body
            );
        }
        return $out_response;
    }

    /**
     * Get Order from Campaign Rabbit
     * @param $order - Order Object
     * @param array - Response
     */
    public function getRabbitOrder($order){
        $params = $this->getPluginParams();
        try{
            $api_token = $params->get('api_token','');
            $app_id = $params->get('app_id','');
            $domain = trim(JUri::root());
            $rabbit_order = new \CampaignRabbit\CampaignRabbit\Action\Order($api_token,$app_id,$domain);
            $out_response = $rabbit_order->getOrderByRef($order->order_id);
        }catch (Exception $e){
            $ex_body = $e->getBody()->getContents();
            $out_response = array(
                'message'=> $e->getReasonPhrase(),
                'code'=>$e->getStatusCode(),
                'body'=> isset($ex_body->data) ? $ex_body->data: $ex_body
            );
        }
        return $out_response;
    }

    /**
     * Update Order to Campaign Rabbit
     * @param $order - Order Object
     * @param $order_params - Campaign order params
     * @param array - Response
     */
    public function updateRabbitOrder($order,$order_params){
        $params = $this->getPluginParams();
        try{
            $api_token = $params->get('api_token','');
            $app_id = $params->get('app_id','');
            $domain = trim(JUri::root());
            $rabbit_order = new \CampaignRabbit\CampaignRabbit\Action\Order($api_token,$app_id,$domain);
            $old_rabbit_order = $rabbit_order->getOrderByRef($order->order_id);
            if(isset($old_rabbit_order['body']->id)){
                $out_response = $rabbit_order->updateOrder($order_params,$old_rabbit_order['body']->id);
            }else{
                $ex_body = $old_rabbit_order->getBody()->getContents();
                $out_response = array(
                    'message'=> $old_rabbit_order->getReasonPhrase(),
                    'code'=> $old_rabbit_order->getStatusCode(),
                    'body'=> isset($ex_body->data) ? $ex_body->data: $ex_body//$old_rabbit_order->getBody()->getContents()
                );
            }

        }catch (Exception $e){
            $ex_body = $e->getBody()->getContents();
            $out_response = array(
                'message'=> $e->getReasonPhrase(),
                'code'=>$e->getStatusCode(),
                'body'=> isset($ex_body->data) ? $ex_body->data: $ex_body
            );
        }
        return $out_response;
    }

    /**
     * Create order to Campaign Rabbit
     * @param $order - Order Object
     * @param $order_params - Campaign order params
     * @param array - Response
     */
    public function createRabbitOrder($order,$order_params){
        $params = $this->getPluginParams();
        try{
            $api_token = $params->get('api_token','');
            $app_id = $params->get('app_id','');
            $domain = trim(JUri::root());
            $rabbit_order = new \CampaignRabbit\CampaignRabbit\Action\Order($api_token,$app_id,$domain);
            $out_response = $rabbit_order->createOrder($order_params);
        }catch (Exception $e){
            $ex_body = $e->getBody()->getContents();
            $out_response = array(
                'message'=> $e->getReasonPhrase(),
                'code'=>$e->getStatusCode(),
                'body'=> isset($ex_body->data) ? $ex_body->data: $ex_body
            );
        }
        return $out_response;
    }

    /**
     * Add/Update Product to Campaign Rabbit
     * @param $item - Order Item Object
     * @param $product_params - Campaign product params
     * @param $order - order object
     * @param array - Response
     */
    public function addOrUpdateProducts($item,$product_params,$order){
        if(!isset($product_params['sku'])){
            return '';
        }
        $params = $this->getPluginParams();
        try{
            $api_token = $params->get('api_token','');
            $app_id = $params->get('app_id','');
            $domain = trim(JUri::root());
            $rabbit_order = new \CampaignRabbit\CampaignRabbit\Action\Product($api_token,$app_id,$domain);
            $out_response = $rabbit_order->getProduct($product_params['sku']);

            $is_need_update = false;
            if(isset($out_response['body']->sku)){
                $is_need_update = true;
            }
            if($is_need_update){
                $product_response =  $rabbit_order->updateProduct($product_params,$product_params['sku']);
            }else{
                $product_response =  $rabbit_order->createProduct($product_params);
            }

            if(isset($product_response['body']->sku)){
                $this->_log(json_encode($product_response),'Product Create/Update: ');
                $order->add_history('Campaign Rabbit Product sku : '.$product_params['sku']);
            }
        }catch (Exception $e){
            $ex_body = $e->getBody()->getContents();
            $out_response = array(
                'message'=> $e->getReasonPhrase(),
                'code'=>$e->getStatusCode(),
                'body'=> isset($ex_body->data) ? $ex_body->data: $ex_body
            );
            $this->_log(json_encode($out_response),'Product Error: ');
            $order->add_history($product_params['sku']. ' - Campaign Rabbit Product Error: '.json_encode($out_response));
        }

        return $out_response;
    }

    /**
     * Syncronize to Campaign Rabbit
     */
    public function addCustomer($queue_data){

        $params = $this->getPluginParams();
        $token = $params->get('api_token','');
        if(empty($token)){
            return false;
        }

        $app_id = $params->get('app_id','');
        if(empty($app_id)){
            return false;
        }

        $email = $queue_data->get('email', '');
        $email = trim($email);
        if(empty($email)) return true;

        $address_id = $queue_data->get('billing_address_id','');
        $user_id = $queue_data->get('user_id',0);

        F0FTable::addIncludePath ( JPATH_ADMINISTRATOR . '/components/com_j2store/tables' );
        $address = F0FTable::getInstance('Address', 'J2StoreTable')->getClone();

        if(!$address->load($address_id)){
            $address->load(array(
                'user_id' => $user_id
            ));
        }
        $user = JFactory::getUser($user_id);
        if(empty($address->j2store_address_id)){
            $name = $user->username;
        }else{
            $name = $address->first_name.' '. $address->last_name;
        }


        $contact_status = false;
        try{
            // check customer exit
            //query-customer

            $campaign_customer = $this->getCustomer($email);

            $is_need_update = false;
            if(isset($campaign_customer['body']->id)){
                $is_need_update = true;
            }

            // customer params
            $metas = array();
            $metas[] = array(
                'meta_key' => 'CUSTOMER_GROUP',
                'meta_value' => $this->getUserGroups($user_id),
                'meta_options' => ''
            );

            foreach ($address as $key => $value){
                if($key == "country_id"){
                    $country_name = $this->getCountryById($address->country_id)->country_name;
                    $value = $country_name;
                }elseif($key == 'zone_id'){
                    $state = $this->getZoneById($address->zone_id)->zone_name;
                    $value = $state;
                }
                $meta = array();
                $meta['meta_key'] = $key;
                if(is_array($value)){
                    $value = json_encode($value);
                }
                $meta['meta_value'] = $value;
                $meta['meta_options'] = '';
                $metas[] = $meta;
            }
            //$name = $address->first_name.' '. $address->last_name;
            $customer_params = array(
                'email' => $email,
                'created_at' => $user->registerDate,
                'updated_at' => $user->registerDate,
                'name' => $name,
                'meta' => $metas,
            );

            if($is_need_update){
                // update customer
                $out_response = $this->updateCustomer($customer_params,$email);

            }else{
                // create customer
                $out_response = $this->createCustomer($customer_params);

            }

            if($out_response['body']->id){
                $this->_log(json_encode($out_response),'Customer Create/Update: ');
                $contact_status = true;
            }
        }catch (Exception $e){
            $this->_log($e->getMessage(),'Customer Exception: ');
            $contact_status = false;
        }
        return $contact_status;
    }

    /**
     * Syncronize to Sales Order
     */
    public function addSales($queue_data){
        $params = $this->getPluginParams();
        $token = $params->get('api_token','');
        if(empty($token)){
            return false;
        }

        $order_id = $queue_data->get('order_id','');
        if(empty($order_id)){
            return false;
        }

        $order = F0FTable::getInstance('Order', 'J2StoreTable')->getClone();
        $order->load(array(
            'order_id' => $order_id

        ));

        $zero_order = $params->get('synch_zero_order',1);
        if(!$zero_order && $order->order_total <=0){
            //remove from queue
            return true;
        }
        //check orderstatus for syncronize
        $order_status = $params->get('orderstatus',array('*'));
        if(!is_array($order_status)){
            $order_status = array($order_status);
        }
        if(!in_array('*',$order_status)){
            if(!in_array($order->order_state_id, $order_status)){
                //remove from queue
                return true;
            }
        }

        //$invoice_number = $order->getInvoiceNumber();
        $orderinfo = $order->getOrderInformation();
        //$order_status = false;

        //$model = F0FModel::getTmpInstance ( 'AppCampaignRabbits', 'J2StoreModel' );

        // customer params
        $metas = array();
        foreach ($order as $key => $value){
            $meta = array();
            $meta['meta_key'] = $key;
            if(is_array($value)){
                $value = json_encode($value);
            }
            $meta['meta_value'] = $value;
            $meta['meta_options'] = '';
            $metas[] = $meta;
        }

        $orderitems = $order->getItems();
        $items = array();
        $config = J2store::config();
        $tax_display_option = $config->get('checkout_price_display_options', 1);
        foreach ($orderitems as $order_item){
            $sku = str_replace(' ','_',$order_item->orderitem_sku);
            if(empty($sku)){
                $sku = $order_item->variant_id;
            }
            $item = array();
            $item['r_product_id'] = $order_item->variant_id;
            $item['sku'] = $sku;
            $item['product_name'] = $order_item->orderitem_name;

            if($tax_display_option) {
                $unit_price = $order_item->orderitem_finalprice_with_tax / $order_item->orderitem_quantity;
                $item_total = $order_item->orderitem_finalprice_with_tax;
            }else {
                $unit_price = $order_item->orderitem_finalprice_without_tax / $order_item->orderitem_quantity;
                $item_total = $order_item->orderitem_finalprice_without_tax;
            }

            $item['product_price'] = $unit_price;
            $item['item_total'] = $item_total;
            $item['item_qty'] = $order_item->orderitem_quantity;
            $item_meta = array();
            foreach ($order_item as $key => $value){
                $meta = array();
                $meta['meta_key'] = $key;
                if(is_array($value)){
                    $value = json_encode($value);
                }
                $meta['meta_value'] = $value;
                $meta['meta_options'] = '';
                $item_meta[] = $meta;
            }
            $item['meta'] = $item_meta;
            //$this->addOrUpdateProducts($order_item,$item,$order);

            $items[] = $item;
        }

        if($tax_display_option == 0 ) {
            if($order->order_tax > 0) {
                $item = array();
                $item['r_product_id'] = 0;
                $item['sku'] = "TAX";
                $item['product_name'] = JText::_('J2STORE_CART_TAX_INCLUDED');
                $item['product_price'] = $order->order_tax;
                $item['item_total'] = $order->order_tax;
                $item['item_qty'] = 1;
                $items[] = $item;
            }
        }

        //add discount
        if($tax_display_option){
            $discount_amount = $order->order_discount + $order->order_discount_tax;
        }else{
            $discount_amount = $order->order_discount;
        }

        if( $discount_amount > 0 ){
            $item = array();
            $item['r_product_id'] = 0;
            $item['sku'] = "DISCOUNT";
            $item['product_name'] = JText::_('J2STORE_CART_DISCOUNT');
            $item['product_price'] = -$discount_amount;
            $item['item_total'] = -$discount_amount;
            $item['item_qty'] = 1;
            $items[] = $item;
        }
//add fees as line item
        foreach($order->get_fees() as $fee) {
            if($tax_display_option) {
                $fee_amount = $fee->amount + $fee->tax;
            }else {
                $fee_amount = $fee->amount;
            }
            $item = array();
            $item['r_product_id'] = 0;
            $item['sku'] = "FEE".$fee->j2store_orderfee_id;
            $item['product_name'] = JText::_($fee->name);
            $item['product_price'] = $fee_amount;
            $item['item_total'] = $fee_amount;
            $item['item_qty'] = 1;
            $items[] = $item;
        }

        //add shipping as a line item too
        $handling_cost = $order->order_shipping + $order->order_shipping_tax + $order->order_surcharge;
        if($handling_cost){
            $item = array();
            $item['r_product_id'] = 0;
            $item['sku'] = 'SHIPPING';
            $item['product_name'] = JText::_('J2STORE_SHIPPING_AND_HANDLING');
            $item['product_price'] = $handling_cost;
            $item['item_total'] = $handling_cost;
            $item['item_qty'] = 1;
            $items[] = $item;
        }

        $bill_country_name = $this->getCountryById($orderinfo->billing_country_id)->country_name;
        $bill_state = $this->getZoneById($orderinfo->billing_zone_id)->zone_name;
        $ship_country_name = $this->getCountryById($orderinfo->shipping_country_id)->country_name;
        $ship_state = $this->getZoneById($orderinfo->shipping_zone_id)->zone_name;

        $billing_address = array(
            "first_name" => $orderinfo->billing_first_name,
            "company_name" => $orderinfo->billing_company,
            "email" => $order->user_email,
            "mobile" => $orderinfo->billing_phone_2,
            "address_1" => $orderinfo->billing_address_1,
            "address_2" => $orderinfo->billing_address_2,
            "city" => $orderinfo->billing_city,
            "state" => $bill_state,
            "country" => $bill_country_name,
            "zipcode" => $orderinfo->billing_zip
        );
        if(empty($orderinfo->shipping_first_name)){
            $shipping_address = $billing_address;
        }else{
            $shipping_address = array(
                "first_name" => $orderinfo->shipping_first_name,
                "company_name" => $orderinfo->shipping_company,
                "email" => $order->user_email,
                "mobile" => $orderinfo->shipping_phone_2,
                "address_1" => $orderinfo->shipping_address_1,
                "address_2" => $orderinfo->shipping_address_2,
                "city" => $orderinfo->shipping_city,
                "state" => $ship_state,
                "country" => $ship_country_name,
                "zipcode" => $orderinfo->shipping_zip
            );
        }

        $status = 'unpaid';
        if(in_array($order->order_state_id,array(1,2))){
            $status = 'paid';
        }elseif($order->order_state_id == 3){
            $status = 'failed';
        }elseif($order->order_state_id == 4){
            $status = 'pending';
        }elseif($order->order_state_id == 5){
            $status = 'unpaid';
        }elseif($order->order_state_id == 6){
            $status = 'cancelled';
        }
        //[‘unpaid’, ‘paid’, ‘pending’, ‘cancelled’, ‘failed’]
        $user = JFactory::getUser($order->user_id);
        $order_params = array(
            'r_order_id' => $order->order_id,
            'r_order_ref' => $order->j2store_order_id,
            'customer_email' => $order->user_email,
            'customer_name' => $orderinfo->billing_first_name.' '.$orderinfo->billing_last_name,
            'customer_created_at' => $user->registerDate,
            'customer_updated_at' => $user->registerDate,
            'status' => $status,
            'order_total' => $order->order_total,
            'meta' => $metas,
            'order_items' => $items,
            'shipping' => $shipping_address,
            'billing' => $billing_address,
            'created_at' => $order->created_on,
            'updated_at' => $order->modified_on
        );
        $order_status = false;

        try{

            $campaign_order = $this->getRabbitOrder($order);

            $is_need_update = false;
            if(isset($campaign_order['body']->id)){
                $is_need_update = true;
            }
            if($is_need_update){
                // update customer
                $out_response = $this->updateRabbitOrder($order,$order_params);
            }else{

                // create customer
                $out_response = $this->createRabbitOrder($order,$order_params);

            }

            if(isset($out_response['body']->id)){
                $this->_log(json_encode($out_response),'Invoice Create/Update: ');
                $order_status = true;
                $order->add_history('Campaign Rabbit Order id: '.$out_response['body']->id);
            }elseif(isset($out_response['body'])){
                $order->add_history('Campaign Rabbit Order Error: '.json_encode($out_response['body']));
            }

        }catch (Exception $e){
            $this->_log($e->getMessage(),'Order Exception: ');
            $order_status = false;
            $order->add_history('Order Exception:'.$e->getMessage());
        }
        return $order_status;

    }

    public function checkPHPVersion(){
        if (defined('PHP_VERSION'))
        {
            $version = PHP_VERSION;
        }
        elseif (function_exists('phpversion'))
        {
            $version = phpversion();
        }else{
            $version = '5.3.0';
        }
        $status = true;
        if (!version_compare($version, '5.5.0', 'ge'))
        {
            $status = false;
        }
        return $status;
    }

    /**
     * Add Order details to Queue Table
     */
    function orderSyn($order,$force = false){
        $status = $this->checkPHPVersion();
        $html = '';
        if(!$status){
            return $html;
        }
        $params = $this->getPluginParams();
        //check orderstatus for syncronize
        $order_status = $params->get('orderstatus',array('*'));
        if(!is_array($order_status)){
            $order_status = array($order_status);
        }

        if(!in_array('*',$order_status)){
            if(!in_array($order->order_state_id, $order_status)){
                //remove from queue
                return '';
            }
        }
        $order_params = $this->getRegistryObject($order->order_params);
        $order_campaign_status = $order_params->get('app_campainrabbit_order',0);
        $opt_in = $params->get('enable_opt_in',0);
        $enable_double_opt_in = $params->get('enable_double_opt_in',0);
        $check_opt_in_status = false;
        if(!$opt_in && !$enable_double_opt_in){ // check opt-in and double opt-in also disable
            $check_opt_in_status = true;
        }elseif(($opt_in || $enable_double_opt_in) && $order_campaign_status){ // any one opt-in enabled, user allow to synchronize order.
            $check_opt_in_status = true;
        }
        if(!$check_opt_in_status && $force){ // synchronize status failed but force synchronize
            $check_opt_in_status = true;
        }
        if(!$check_opt_in_status){ //return synchronize status failed
            return '';
        }

        if(!$enable_double_opt_in && $force){ // not enable double opt-in, when force - no need to allow any operation
            return '';
        }
        //check already double opt-in accepted if yes,then no need to send email again.
        if($enable_double_opt_in && !$force && !$this->checkDoubleOptInAccepted($order->user_email)){ // enabled double opt-in and not force synchronize - need to send email with order synchronize url.
            // send email
            $this->sendEmail($order);
            return '';
        }

        if(!empty($order->campaign_order_id)){
            $task = 'update_order';
        }else{
            $task = 'create_order';
        }
        $queue_data = array(
            'order_id' =>$order->order_id,
            'task' => $task
        );

        $order_queue_params = $this->getRegistryObject(json_encode($queue_data));
        $status = $this->addSales($order_queue_params);
        if(!$status){
            $tz = JFactory::getConfig()->get('offset');
            $current_date = JFactory::getDate('now', $tz)->toSql(true);
            $date = JFactory::getDate('now +7 day', $tz)->toSql(true);

            $queue = array(
                'queue_type' => $this->_element,
                'relation_id' => 'order_'.$order->order_id,
                'queue_data' => json_encode($queue_data),
                'params' => '{}',
                'priority' => 0,
                'status' => 'new',
                'expired' => $date,
                'modified_on' => $current_date
            );
            try{
                $queue_table = F0FTable::getInstance('Queue', 'J2StoreTable')->getClone();
                $queue_table->load(array(
                    'relation_id' => $queue['relation_id']
                ));
                if(empty($queue_table->created_on)){
                    $queue_table->created_on = $current_date;
                }
                $queue_table->bind($queue);
                $queue_table->store();
            }catch (Exception $e){
                $this->_log($e->getMessage(),'Order task Exception: ');
            }
        }
        if($enable_double_opt_in && $force){
            // save order table
            $order->campaign_double_opt_in = 1;
            $order->store();
        }
        return '';
    }

    public function checkDoubleOptInAccepted($user_email){
        if(empty($user_email)){
            return false;
        }

        $db = JFactory::getDBo();
        $query = $db->getQuery(true);
        $query->select("order_id")->from('#__j2store_orders')
            ->where('user_email='.$db->q($user_email))
            ->where('campaign_double_opt_in=1');
        $db->setQuery($query);
        $order_id = $db->loadResult();
        if(!empty($order_id)){
            return true;
        }
        return false;
    }

    public function sendEmail($order){
        //get the config class obj
        $config = JFactory::getConfig();
        //get the mailer class object
        $mailer = JFactory::getMailer();
        $mailfrom = $config->get('mailfrom');
        $fromname = $config->get('fromname');
        $sitename = $config->get('sitename');
        $mailer->addRecipient($order->user_email);
        $url = rtrim(JUri::base(false),'/').'/index.php?option=com_j2store&view=carts&command=campaign_double_opt&order_id='.$order->order_id;
        $subject = JText::sprintf('J2STORE_CAMPAIGN_RABBIT_SUBJECT',$sitename,$order->order_id);
        $body = JText::sprintf('J2STORE_CAMPAIGN_RABBIT_BODY',$url);
        $mailer->setSubject($subject );
        $mailer->setBody($body);
        $mailer->IsHTML(1);
        $mailer->setSender(array( $mailfrom, $fromname ));
        if(!$mailer->send()){
            $msg = JText::_('PLG_J2STORE_EMAILBASKET_SENDING_FAILED');
            $this->_log($msg);
            $order->add_history($msg);
        }else{
            $msg = JText::_('PLG_J2STORE_EMAILBASKET_SENDING_SUCCESS');
            $order->add_history($msg);
        }
    }

    function getUserGroups($id){
        $groups = JAccess::getGroupsByUser($id);
        $groupid_list      = '(' . implode(',', $groups) . ')';
        $db = JFactory::getDBo();
        $query  = $db->getQuery(true);
        $query->select('title');
        $query->from('#__usergroups');
        $query->where('id IN ' .$groupid_list);
        $db->setQuery($query);
        $rows   = $db->loadObjectList();
        $final_list = array();
        foreach ($rows as $row){
            $final_list[] = $row->title;
        }
        return implode('|',$final_list);
    }

    /**
     * Simple logger
     *
     * @param string $text
     * @param string $type
     * @return void
     */
    function _log($text, $type = 'message')
    {
        $params = $this->getPluginParams();
        $isLog = $params->get('debug',0);
        if ($isLog) {
            $file = JPATH_ROOT . "/cache/{$this->_element}.log";
            $date = JFactory::getDate();

            $f = fopen($file, 'a');
            fwrite($f, "\n\n" . $date->format('Y-m-d H:i:s'));
            fwrite($f, "\n" . $type . ': ' . $text);
            fclose($f);
        }
    }

    public function getRegistryObject($json){
        if(!$json instanceof JRegistry) {
            $params = new JRegistry();
            try {
                $params->loadString($json);

            }catch(Exception $e) {
                $params = new JRegistry('{}');
            }
        }else{
            $params = $json;
        }
        return $params;
    }

    public function getCountryById($country_id) {
        F0FTable::addIncludePath ( JPATH_ADMINISTRATOR . '/components/com_j2store/tables' );
        $country = F0FTable::getInstance('Country', 'J2StoreTable')->getClone();
        $country->load($country_id);
        return $country;
    }

    public function getZoneById($zone_id) {
        F0FTable::addIncludePath ( JPATH_ADMINISTRATOR . '/components/com_j2store/tables' );
        $zone = F0FTable::getInstance('Zone', 'J2StoreTable')->getClone();
        $zone->load($zone_id);
        return $zone;
    }
}