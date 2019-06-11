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
// No direct access to this file
defined('_JEXEC') or die;
/* class JFormFieldFieldtypes extends JFormField */

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
require_once JPATH_ADMINISTRATOR.'/components/com_j2store/helpers/j2html.php';
class JFormFieldCronurl extends JFormFieldList
{

    protected $type = 'cronurl';

    public function getInput()
    {
        //$plugin = JPluginHelper::getPlugin('j2store', 'app_campaignrabbit');
        //$params = new JRegistry($plugin->params);
        $config = J2Store::config();
        $queue_key = $config->get('queue_key','');
        $url = trim(JUri::root(),'/').'/index.php?option=com_j2store&view=queues&task=processQueue&queue_key='.$queue_key.'&queue_type=app_campaignrabbit';
        return '<span class="alert alert-success">'.$url.'</span>';
    }
}