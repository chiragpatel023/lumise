<?php
/**
 * @package J2Store
 * @author  Alagesan, J2Store <support@j2store.org>
 * @copyright Copyright (c)2018 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */
/** ensure this file is being included by a parent file */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport('joomla.html.parameter');

// Make sure FOF is loaded, otherwise do not run
if (!defined('F0F_INCLUDED'))
{
    include_once JPATH_LIBRARIES . '/f0f/include.php';
}

if (!defined('F0F_INCLUDED') || !class_exists('F0FLess', true))
{
    return;
}

// Do not run if Akeeba Subscriptions is not enabled
JLoader::import('joomla.application.component.helper');

if (!JComponentHelper::isEnabled('com_j2store', true))
{
    return;
}


class plgSystemCampaignrabbit extends JPlugin {

    function getPluginParams(){
        try{
            $plugin_data = JPluginHelper::getPlugin('j2store', 'app_campaignrabbit');
            $params = new \JRegistry;
            $params->loadString($plugin_data->params);
        }catch (Exception $e){
            $params = new \JRegistry('{}');
        }
        return $params;
    }

    public function canRun(){
        $app = JFactory::getApplication();
        $run_status = false;
        //chk app campaign enabled
        if(JPluginHelper::isEnabled('j2store', 'app_campaignrabbit') && $app->isSite()) {
            $params = $this->getPluginParams();
            $app_id = $params->get('app_id', '');
            $is_verified = $params->get('is_verified', 0);
            if(!empty($app_id) && $is_verified){
                $run_status = true;
            }
        }
        return $run_status;
    }
    function onAfterRoute() {
        if($this->canRun()){
            $document = JFactory::getDocument();
            $params = $this->getPluginParams();
            $app_id = $params->get('app_id', '');
            $script_content = 'window.app_id = "'.$app_id.'";
                !function(e,t,n,p,o,a,i,s,c){e[o]||(i=e[o]=function(){i.process?i.process.apply(i,arguments):i.queue.push(arguments)},i.queue=[],i.t=1*new Date,s=t.createElement(n),s.async=1,s.src=p+"?t="+Math.ceil(new Date/a)*a,c=t.getElementsByTagName(n)[0],c.parentNode.insertBefore(s,c))}(window,document,"script","https://cdn.campaignrabbit.com/campaignrabbit.analytics.js","rabbit",1),rabbit("init",window.app_id),rabbit("event","pageload");';
            $document->addScriptDeclaration($script_content);
        }
        $app = JFactory::getApplication();
        $option = $app->input->get('option','');
        $command = $app->input->get('command','');
        if($option == 'com_j2store' && $command == 'campaign_double_opt'){
            $order_id = $app->input->getString('order_id','');
            F0FTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_j2store/tables');
            $order_table = F0FTable::getInstance('Order', 'J2StoreTable')->getClone();
            $order_table->load(array(
                'order_id' => $order_id
            ));

            F0FModel::addIncludePath(JPATH_SITE.'/plugins/j2store/app_campaignrabbit/app_campaignrabbit/models');
            $model = F0FModel::getTmpInstance('AppCampaignRabbits', 'J2StoreModel');
            if(!empty($order_table->order_id) && $order_table->order_id == $order_id ) {
                $model->orderSyn($order_table, true);
            }
        }
    }

    function onUserAfterSave($user,$isnew,$success,$msg){
        if($isnew && $this->canRun()){
            $task = 'create_customer';
            $queue_data = array(
                'user_id' => $user['id'],
                'email' => $user['email'],
                'ship_address_id' => 0,
                'billing_address_id' => 0,
                'task' => $task
            );
            F0FModel::addIncludePath(JPATH_SITE.'/plugins/j2store/app_campaignrabbit/app_campaignrabbit/models');
            $model = F0FModel::getTmpInstance('AppCampaignRabbits', 'J2StoreModel');
            $customer_queue_params = $model->getRegistryObject(json_encode($queue_data));
            $customer_status = $model->addCustomer($customer_queue_params);
            if(!$customer_status){
                $tz = JFactory::getConfig()->get('offset');
                $current_date = JFactory::getDate('now', $tz)->toSql(true);
                $date = JFactory::getDate('now +7 day', $tz)->toSql(true);

                $queue = array(
                    'queue_type' => 'app_campaignrabbit',
                    'relation_id' => 'user_reg_'.$user['id'],
                    'queue_data' => json_encode($queue_data),
                    'params' => '{}',
                    'priority' => 0,
                    'status' => 'new',
                    'expired' => $date,
                    'modified_on' => $current_date
                );

                try{
                    F0FTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_j2store/tables');
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
                    $this->_log($e->getMessage(),'User Register Queue Exception: ');
                }
            }
        }
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
        $plugin_data = JPluginHelper::getPlugin('j2store', 'app_campaignrabbit');
        $params = new \JRegistry;
        $params->loadString($plugin_data->params);
        $isLog = $params->get('debug',0);
        if ($isLog) {
            $file = JPATH_ROOT . "/cache/app_campaignrabbit.log";
            $date = JFactory::getDate();

            $f = fopen($file, 'a');
            fwrite($f, "\n\n" . $date->format('Y-m-d H:i:s'));
            fwrite($f, "\n" . $type . ': ' . $text);
            fclose($f);
        }
    }
}