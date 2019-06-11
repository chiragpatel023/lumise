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
require_once(JPATH_ADMINISTRATOR.'/components/com_j2store/library/plugins/app.php');
class plgJ2StoreApp_campaignrabbit extends J2StoreAppPlugin
{
    /**
     * @var $_element  string  Should always correspond with the plugin's filename,
     *                         forcing it to be unique
     */
    var $_element = 'app_campaignrabbit';

    function __construct ( &$subject, $config )
    {
        parent::__construct ( $subject, $config );
        JFactory::getLanguage ()->load ( 'plg_j2store_' . $this->_element, JPATH_ADMINISTRATOR );
    }

    /**
     * Overriding
     *
     * @param $options
     * @return unknown_type
     */
    function onJ2StoreGetAppView ( $row )
    {

        if ( !$this->_isMe ( $row ) ) {
            return null;
        }

        $html = $this->viewList ();


        return $html;
    }

    /**
     * Validates the data submitted based on the suffix provided
     * A controller for this plugin, you could say
     *
     * @param $task
     * @return html
     */
    function viewList ()
    {
        $app = JFactory::getApplication ();
        $vars = new stdClass();
        $id = $app->input->getInt ( 'id', 0 );
        $vars->id = $id;
        JToolBarHelper::title ( JText::_ ( 'J2STORE_APP' ) . '-' . JText::_ ( 'PLG_J2STORE_' . strtoupper ( $this->_element ) ), 'j2store-logo' );
        JToolBarHelper::apply ( 'apply' );
        JToolBarHelper::save ();
        JToolBarHelper::back ( 'PLG_J2STORE_BACK_TO_APPS', 'index.php?option=com_j2store&view=apps' );
        JToolBarHelper::back ( 'J2STORE_BACK_TO_DASHBOARD', 'index.php?option=com_j2store' );
        $bar =JToolBar::getInstance('toolbar');
        $bar->appendButton( 'Link', 'list', 'J2STORE_APP_CAMPAIGNRABBIT_MANAGE_QUEUE', 'index.php?option=com_j2store&view=apps&id='.$id.'&task=view&appTask=manageQueue' );
        $this->includeCustomModel ( 'AppCampaignRabbits' );
        $model = F0FModel::getTmpInstance ( 'AppCampaignRabbits', 'J2StoreModel' );

        $data = $this->params->toArray ();

        $newdata = array();
        $newdata[ 'params' ] = $data;
        $form = $model->getForm ( $newdata );
        $vars->form = $form;
        $vars->action = "index.php?option=com_j2store&view=app&task=view&id={$id}";
        $vars->status = $this->checkPHPVersion();
        $html = $this->_getLayout ( 'default', $vars );
        return $html;
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

    public function onJ2StoreAfterDisplayShippingPayment($order){
        $status = $this->checkPHPVersion();
        $html = '';
        if($status){
            $html = $this->displayOptIn('payment');
        }
        return $html;
    }

    public function displayOptIn($opt_in_type){
        $html = '';
        $opt_in_postion = $this->params->get('opt_in_position','payment');
        $enable_opt_in = $this->params->get('enable_opt_in',0);
        $enable_double_opt_in = $this->params->get('enable_double_opt_in',0);
        if(($opt_in_postion == $opt_in_type) && ($enable_opt_in || $enable_double_opt_in)){
            $vars = new JObject();
            $vars->params = $this->params;
            $html = $this->_getLayout('optin_check', $vars);
        }
        return $html;
    }

    public function onJ2StoreCheckoutValidateShippingPayment($values, $order){
        $status = $this->checkPHPVersion();
        $html = '';
        if(!$status){
            return $html;
        }
        $session = JFactory::getSession();
        $address_id = $session->get('billing_address_id','','j2store');
        $session->set('app_campainrabbit_order',0,'j2store');
        $check_opt_in_status = false;
        $opt_in = $this->params->get('enable_opt_in',0);
        $enable_double_opt_in = $this->params->get('enable_double_opt_in',0);
        if(!$opt_in && !$enable_double_opt_in){
            $check_opt_in_status = true;
        }elseif(($opt_in || $enable_double_opt_in) && isset($values['app_camp_rabbit_opt_in']) && $values['app_camp_rabbit_opt_in']){
            $check_opt_in_status = true;
        }
        if($address_id && $check_opt_in_status) {

            $session->set('app_campainrabbit_order',1,'j2store');
            $user = JFactory::getUser();
            $address = F0FTable::getInstance('Address', 'J2StoreTable')->getClone();
            $address->load($address_id);
            if(isset($address->campaign_addr_id) && !empty($address->campaign_addr_id)){
                $task = 'update_customer';
            }else{
                $task = 'create_customer';
            }

            $ship_address_id = $session->get('shipping_address_id','','j2store');
            $queue_data = array(
                'user_id' => $user->id,
                'email' => $address->email,
                'ship_address_id' => $ship_address_id,
                'billing_address_id' => $address_id,
                'task' => $task
            );
            $this->includeCustomModel ( 'AppCampaignRabbits' );
            $model = F0FModel::getTmpInstance ( 'AppCampaignRabbits', 'J2StoreModel' );
            $queue_params = $model->getRegistryObject(json_encode($queue_data));
            $status = $model->addCustomer($queue_params);
            if(!$status){
                $tz = JFactory::getConfig()->get('offset');
                $current_date = JFactory::getDate('now', $tz)->toSql(true);
                $date = JFactory::getDate('now +7 day', $tz)->toSql(true);

                $queue = array(
                    'queue_type' => $this->_element,
                    'relation_id' => 'user_'.$address_id,
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
                    $this->_log($e->getMessage(),'User Exception: ');
                }
            }
        }
    }

    public function onJ2StorePrePayment($orderpayment_type, $data){

        $status = $this->checkPHPVersion();
        $html = '';
        if(!$status){
            return $html;
        }
        if(isset($data['order_id']) && !empty($data['order_id'])){
            F0FTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_j2store/tables');
            $order = F0FTable::getInstance ( 'Order', 'J2StoreTable' )->getClone ();
            $order->load ( array (
                'order_id' => $data ['order_id']
            ) );
            $session = JFactory::getSession();
            $campaign_status = $session->get('app_campainrabbit_order',0,'j2store');
            $order_params = $this->getRegistryObject($order->order_params);
            if($campaign_status){
                $order_params->set('app_campainrabbit_order',1);
                $order->order_params = $order_params->toString();
                $order->store();
            }else{
                $order_params->set('app_campainrabbit_order',0);
                $order->order_params = $order_params->toString();
                $order->store();
            }

        }
    }

    /**
     *
     */
    function onJ2StoreAfterCreateNewOrder($order){
        F0FModel::addIncludePath(JPATH_SITE.'/plugins/j2store/app_campaignrabbit/app_campaignrabbit/models');
        $model = F0FModel::getTmpInstance('AppCampaignRabbits', 'J2StoreModel');
        return $model->orderSyn($order);
    }

    /**
     * Add Order details to Queue Table
     */
    function onJ2StoreAfterOrderstatusUpdate($order,$new_status){
        F0FModel::addIncludePath(JPATH_SITE.'/plugins/j2store/app_campaignrabbit/app_campaignrabbit/models');
        $model = F0FModel::getTmpInstance('AppCampaignRabbits', 'J2StoreModel');
        return $model->orderSyn($order);
    }



    function onJ2StoreCheckoutAfterRegister(){
        $status = $this->checkPHPVersion();
        $html = '';
        if(!$status){
            return $html;
        }
        $session = JFactory::getSession();
        $address_id = $session->get('billing_address_id', '' , 'j2store');
        $address = F0FTable::getInstance('Address', 'J2StoreTable')->getClone();
        if($address->load($address_id)){
            $task = 'create_customer';
            $queue_data = array(
                'user_id' => $address->user_id,
                'email' => $address->email,
                'ship_address_id' => $address_id,
                'billing_address_id' => $address_id,
                'task' => $task
            );
            $this->includeCustomModel ( 'AppCampaignRabbits' );
            $model = F0FModel::getTmpInstance ( 'AppCampaignRabbits', 'J2StoreModel' );
            $queue_params = $model->getRegistryObject(json_encode($queue_data));
            $status = $model->addCustomer($queue_params);
            if(!$status){
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
                    $this->_log($e->getMessage(),'Customer Checkout Register Exception: ');
                }
            }
        }
    }

    /**
     * Process Queue
     */
    public function onJ2StoreProcessQueue($list){

        $status = $this->checkPHPVersion();
        $html = '';
        if(!$status){
            return $html;
        }

        if(isset($list->queue_type) && $list->queue_type == $this->_element){
            if(isset($list->queue_data) && !empty($list->queue_data)){
                $queue_helper = J2Store::queue();
                $queue_data = new JRegistry;
                $queue_data->loadString($list->queue_data);
                $task = $queue_data->get('task','');
                $queue_status = false;
                $this->includeCustomModel ( 'AppCampaignRabbits' );
                $model = F0FModel::getTmpInstance ( 'AppCampaignRabbits', 'J2StoreModel' );
                if(!empty($task)){
                    switch ($task){
                        case 'create_customer':
                            $queue_status = $model->addCustomer($queue_data);
                            break;
                        case 'update_customer':
                            $queue_status = $model->addCustomer($queue_data);
                            break;
                        case 'create_order':
                            $queue_status = $model->addSales($queue_data);
                            break;
                        case 'update_order':
                            $queue_status = $model->addSales($queue_data);
                            break;
                        default:
                            $queue_status = false;
                            break;
                    }
                }

                if($queue_status){
                    $queue_helper->deleteQueue($list);
                }else{
                    $queue_helper->resetQueue($list);
                }
            }
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

    public function onJ2StoreAdminOrderAfterGeneralInformation($order_view){
        $status = $this->checkPHPVersion();
        $html = '';
        if(!$status){
            return $html;
        }
        $is_enable_manuval = $this->params->get('syn_manual',0);
        $order = $order_view->order;
        $html = '';
        //check orderstatus for syncronize
        $order_status = $this->params->get('orderstatus',array('*'));
        if(!is_array($order_status)){
            $order_status = array($order_status);
        }
        if(!in_array('*',$order_status)){
            if(!in_array($order->order_state_id, $order_status)){
                //remove from queue
                return $html;
            }
        }
        if($is_enable_manuval){
            $vars = new stdClass();
            //model should always be a plural
            $this->includeCustomModel ( 'AppCampaignRabbits' );
            $model = F0FModel::getTmpInstance('AppCampaignRabbits', 'J2StoreModel');
            $id = $model->getPlugin()->extension_id;
            $vars->id = $id;
            $vars->action = "index.php?option=com_j2store&view=app&task=view&id={$id}";
            $vars->button_text = JText::_('J2STORE_CAMPAIGN_SYNCRONIZE');
            $vars->order_id = $order->order_id;
            $html .= $this->_getLayout('order_queue', $vars);
        }
        return $html;
    }



    /*public function onJ2StoreProcessCron($command){
        if($command == 'campaign_double_opt'){
            $app = JFactory::getApplication();
            $order_id = $app->input->getString('order_id','');
            $order_table = F0FTable::getInstance('Order', 'J2StoreTable')->getClone();
            $order_table->load(array(
                'order_id' => $order_id
            ));

            if(!empty($order_table->order_id) && $order_table->order_id == $order_id ) {
                $this->orderSyn($order_table, true);
            }
        }
    }*/

    /**
     * Simple logger
     *
     * @param string $text
     * @param string $type
     * @return void
     */
    function _log($text, $type = 'message')
    {
        $isLog = $this->params->get('debug',0);
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
        try{
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
        }catch (Exception $e){
            $params = new JRegistry('{}');
        }
        return $params;
    }
}