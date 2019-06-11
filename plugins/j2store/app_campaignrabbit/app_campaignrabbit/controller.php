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
require_once(JPATH_ADMINISTRATOR.'/components/com_j2store/library/appcontroller.php');
class J2StoreControllerAppCampaignRabbit extends J2StoreAppController
{
    var $_element = 'app_campaignrabbit';

    /**
     * constructor
     */
    function __construct()
    {
        parent::__construct();
        F0FModel::addIncludePath(JPATH_SITE.'/plugins/j2store/'.$this->_element.'/'.$this->_element.'/models');
        F0FModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_j2store/models');
        F0FTable::addIncludePath(JPATH_SITE.'/plugins/j2store/'.$this->_element.'/'.$this->_element.'/tables');
        F0FTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_j2store/tables');
        JFactory::getLanguage()->load('plg_j2store_' . $this->_element, JPATH_ADMINISTRATOR);
    }

    public function checkToken(){
        $model = F0FModel::getTmpInstance('AppCampaignRabbits', 'J2StoreModel');
        $params = $model->getPluginParams();
        $app = JFactory::getApplication();
        $app_id = $app->input->get('app_id','');
        $api_token = $app->input->get('api_token','');
        $params->set('app_id',$app_id);
        $params->set('api_token',$api_token);
        try{
            $model->saveParams($params);
        }catch (Exception $e){

        }
        $out_response = $model->auth($params);

        $json = array();
        if(isset($out_response['body']->error) && !empty($out_response['body']->error)){
            $json['error'] = $out_response['body']->error;
            $params->set('is_verified',0);
        }elseif(isset($out_response['body']->success) && !empty($out_response['body']->success)){
            $json['success'] = $out_response['body']->message;
            //save params with yes
            $params->set('is_verified',1);
        }else{
            $params->set('is_verified',0);
            $json['error'] = JText::_('J2STORE_CAMPAIGNRABBIT_AUTH_RESPONSE_NOT_FOUND');
        }
        $model->saveParams($params);
        echo json_encode($json);
        $app->close();
    }

    public function add_to_queue(){
        $app = JFactory::getApplication();
        $order_id = $app->input->getString('order_id','');
        $order = F0FTable::getInstance('Order', 'J2StoreTable')->getClone();
        $order->load(array(
            'order_id' => $order_id
        ));
        $model = F0FModel::getTmpInstance('AppCampaignRabbits', 'J2StoreModel');
        //$params = $model->getPluginParams();
        $json = array();
        if( !empty($order_id) && $order_id == $order->order_id){
            $address = $this->getCustomerInfo($order->user_email,$order->user_id);
            if(isset($address->j2store_address_id) && !empty($address->j2store_address_id)){
                if(isset($address->campaign_addr_id) && !empty($address->campaign_addr_id)){
                    $task = 'update_customer';
                }else{
                    $task = 'create_customer';
                }

                $ship_address_id = $address->j2store_address_id;
                $queue_data = array(
                    'user_id' => $order->user_id,
                    'email' => $address->email,
                    'ship_address_id' => $ship_address_id,
                    'billing_address_id' => $address->j2store_address_id,
                    'task' => $task
                );
                $customer_queue_params = $model->getRegistryObject(json_encode($queue_data));

                $customer_status = $model->addCustomer($customer_queue_params);
                
                if(!$customer_status){
                    $tz = JFactory::getConfig()->get('offset');
                    $current_date = JFactory::getDate('now', $tz)->toSql(true);
                    $date = JFactory::getDate('now +7 day', $tz)->toSql(true);

                    $queue = array(
                        'queue_type' => $this->_element,
                        'relation_id' => 'user_'.$address->j2store_address_id,
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
                        $customer_note = JText::sprintf('J2STORE_APP_CAMPAIGNRABBIT_CUSTOMER_ADD_TO_QUEUE',$address->email);
                        $order->add_history($customer_note);
                    }catch (Exception $e){
                        // do nothing
                        $this->_log($e->getMessage(),'Backend Admin Customer Exception: ');
                        $json['error'] = $e->getMessage();
                        $customer_note_error = JText::sprintf('J2STORE_APP_CAMPAIGNRABBIT_CUSTOMER_ADD_TO_QUEUE_FAILED',json_encode($e->getMessage()));
                        $order->add_history($customer_note_error);
                    }
                }

            }
            if(empty($json)){

                if(!empty($order->campaign_order_id)){
                    $task = 'update_order';
                }else{
                    $task = 'create_order';
                }
                $queue_data = array(
                    'order_id' =>$order->order_id,
                    'task' => $task
                );

                $order_queue_params = $model->getRegistryObject(json_encode($queue_data));

                $order_status = $model->addSales($order_queue_params);

                if(!$order_status){
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
                        $json['success'] = 1;
                        $order_note = JText::sprintf('J2STORE_APP_CAMPAIGNRABBIT_ORDER_ADD_TO_QUEUE',$order->order_id);
                        $order->add_history($order_note);
                    }catch (Exception $e){
                        $this->_log($e->getMessage(),'Order task Exception: ');
                        $json['error'] = $e->getMessage();
                        $order_note_failed = JText::sprintf('J2STORE_APP_CAMPAIGNRABBIT_ORDER_ADD_TO_QUEUE_FAILED',json_encode($e->getMessage()));
                        $order->add_history($order_note_failed);
                    }
                }

            }
        }
        if(empty($json)){
            $json['success'] = 1;
        }
        echo json_encode($json);
        $app->close();
    }

    public function usersyn(){
        $app = JFactory::getApplication();
        $total = $app->input->get('total',0);
        $limit = $app->input->get('limit',0);
        $start = $app->input->get('start',0);
        $done = $app->input->get('done',0);
        $model = F0FModel::getTmpInstance('AppCampaignRabbits', 'J2StoreModel');
        $lists = $model->getCustomerList($limit,$start);

        if(!empty($lists)){
            foreach ($lists as $address){
                if(isset($address->campaign_addr_id) && !empty($address->campaign_addr_id)){
                    $task = 'update_customer';
                }else{
                    $task = 'create_customer';
                }

                $ship_address_id = $address->j2store_address_id;
                $queue_data = array(
                    'user_id' => $address->user_id,
                    'email' => $address->email,
                    'ship_address_id' => $ship_address_id,
                    'billing_address_id' => $address->j2store_address_id,
                    'task' => $task
                );

                $tz = JFactory::getConfig()->get('offset');
                $current_date = JFactory::getDate('now', $tz)->toSql(true);
                $date = JFactory::getDate('now +7 day', $tz)->toSql(true);

                $queue = array(
                    'queue_type' => $this->_element,
                    'relation_id' => 'user_'.$address->j2store_address_id,
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
                    // do nothing
                    $this->_log($e->getMessage(),'Backend Admin Customer Exception: ');
                    $json['error'] = $e->getMessage();
                }
            }
        }

        $json = array();

        if($total < $limit){
            $json['total'] = 0;
            $json['success'] = true;
            $plugin = $model->getPlugin();
            $json['redirect'] = JUri::base()."index.php?option=com_j2store&view=app&task=view&id=".$plugin->extension_id;
        }else{
            $json['total'] = $total - $limit;
            $json['dopatch'] = true;
            $json['done'] = $done+$limit;

        }
        $json['start'] = $start+$limit;

        echo json_encode($json);
        $app->close();
    }

    public function invoicesyn(){
        $app = JFactory::getApplication();
        $total = $app->input->get('total',0);
        $limit = $app->input->get('limit',0);
        $start = $app->input->get('start',0);
        $done = $app->input->get('done',0);
        $model = F0FModel::getTmpInstance('AppCampaignRabbits', 'J2StoreModel');
        $params = $model->getPluginParams();
        $lists = $model->getInvoiceList($limit,$start);
        if(!empty($lists)) {
            foreach ($lists as $order) {
                //check orderstatus for syncronize
                $order_status = $params->get('orderstatus',array('*'));
                if(!is_array($order_status)){
                    $order_status = array($order_status);
                }

                if(!in_array('*',$order_status)){
                    if(!in_array($order->order_state_id, $order_status)){
                        //remove from queue
                        continue;
                    }
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
        }

        $json = array();

        if($total < $limit){
            $json['total'] = 0;
            $json['success'] = true;
            $plugin = $model->getPlugin();
            $json['redirect'] = JUri::base()."index.php?option=com_j2store&view=app&task=view&id=".$plugin->extension_id;
        }else{
            $json['total'] = $total - $limit;
            $json['dopatch'] = true;
        }
        $json['start'] = $start+$limit;

        echo json_encode($json);
        $app->close();
    }

    public function getCustomerInfo($email,$user_id){

        if(!empty($email)){
            $address = F0FTable::getInstance('Address', 'J2StoreTable')->getClone();
            $address->load(array(
                'email' => $email,
                'user_id' => $user_id
            ));

            if($address->user_id > 0){
                return $address;
            }

        }
        return array();
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
        $model = F0FModel::getTmpInstance('AppZohocrms', 'J2StoreModel');
        $params = $model->getPluginParams();
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


    function manageQueue(){
        $app = JFactory::getApplication();
        $vars = new stdClass();
        $data = $app->input->getArray($_POST);
        $is_expired = $app->input->get('is_expired','no');
        $option = 'com_j2store';
        $ns = $option.'.app.'.$this->_element;
        //form
        $form = array();
        $form['action'] = "index.php?option=com_j2store&view=app&task=view&id={$data['id']}";
        $model_app = F0FModel::getTmpInstance('Apps','J2StoreModel');

        F0FModel::addIncludePath(JPATH_SITE.'/plugins/j2store/'.$this->_element.'/'.$this->_element.'/models');
        // get Queue list
        $model = F0FModel::getTmpInstance('AppCampaignRabbits', 'J2StoreModel');

        $model->setState('queue_type',$this->_element);

        $limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
        $limitstart	= $app->getUserStateFromRequest( $ns.'.limitstart', 'limitstart', 0, 'int' );
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $filter_order_Dir =  $app->getUserStateFromRequest( $ns.'filter_order_Dir',	'filter_order_Dir',	'',	'word' );
        $filter_order	= $app->getUserStateFromRequest( $ns.'filter_order',		'filter_order',		'tbl.user_id',	'cmd' );
        $search = $app->input->getString('search',  $model->getState('search', ''));
        $vars->is_expired = $is_expired;
        $model->setState('is_expired', $is_expired);
        $model->setState('limit', $limit);
        $model->setState('limitstart', $limitstart);
        $model->setState('filter_order_Dir', $filter_order_Dir);
        $model->setState('filter_order', $filter_order);
        $model->setState('search',$search);
        $vars->pagination = $model->getPagination();
        $vars->state =  $model->getState();
        $vars->queue = $model->getList();
        $view = $this->getView( 'Apps', 'html' );
        $view->setModel($model_app, true );
        $view->addTemplatePath(JPATH_SITE.'/plugins/j2store/'.$this->_element.'/'.$this->_element.'/tmpl');
        JToolBarHelper::back('PLG_J2STORE_BACK_TO_APPS', $form['action']);

        $vars->form2 =  $form;
        $vars->limit = $limit;
        $vars->limitstart = $limitstart;
        $vars->id = $data['id'];
        $view->set('vars',$vars);
        $view->setLayout('queue_manage');
        $view->display();
    }

    public function reQueue(){
        $app = JFactory::getApplication();
        $is_expired = $app->input->get('is_expired','no');
        $option = 'com_j2store';
        $ns = $option.'.app.'.$this->_element;
        F0FModel::addIncludePath(JPATH_SITE.'/plugins/j2store/'.$this->_element.'/'.$this->_element.'/models');
        // get Queue list
        $model = F0FModel::getTmpInstance('AppCampaignRabbits', 'J2StoreModel');

        $model->setState('queue_type',$this->_element);

        $limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
        $limitstart	= $app->getUserStateFromRequest( $ns.'.limitstart', 'limitstart', 0, 'int' );
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $filter_order_Dir =  $app->getUserStateFromRequest( $ns.'filter_order_Dir',	'filter_order_Dir',	'',	'word' );
        $filter_order	= $app->getUserStateFromRequest( $ns.'filter_order',		'filter_order',		'tbl.user_id',	'cmd' );
        $search = $app->input->getString('search',  $model->getState('search', ''));
        $model->setState('is_expired', $is_expired);
        $model->setState('limit', $limit);
        $model->setState('limitstart', $limitstart);
        $model->setState('filter_order_Dir', $filter_order_Dir);
        $model->setState('filter_order', $filter_order);
        $model->setState('search',$search);
        $lists = $model->getList();
        foreach ($lists as $list){
            $queue_table = F0FTable::getInstance('Queue', 'J2StoreTable')->getClone();
            $queue_table->load($list->j2store_queue_id);
            $new_table = clone $queue_table;

            //delete the current queue
            $queue_table->delete();

            $new_table->j2store_queue_id = '';
            $tz = JFactory::getConfig()->get('offset');
            $current_date = JFactory::getDate('now', $tz)->toSql(true);
            $date_string = 'now +7 day';
            $date = JFactory::getDate($date_string, $tz)->toSql(true);
            $new_table->status = 'Requeue';
            $new_table->expired = $date;
            $new_table->repeat_count = 0;
            $new_table->modified_on = $current_date;
            $new_table->store();
        }
        $id = $app->input->get('id',0);
        $url = "index.php?option=com_j2store&view=app&task=view&appTask=manageQueue&id=".$id;
        $app->redirect($url,JText::_('J2STORE_CAMPAIGN_RABBIT_QUEUE_RESET_COMPLETED'));
    }
}