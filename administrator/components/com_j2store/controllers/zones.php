<?php
/*------------------------------------------------------------------------
 # com_j2store - J2Store
# ------------------------------------------------------------------------
# author    Sasi varna kumar - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2014 - 19 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://j2store.org
# Technical Support:  Forum - http://j2store.org/forum/index.html
-------------------------------------------------------------------------*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');

class J2StoreControllerZones extends F0FController
{
    public function __construct($config) {
        parent::__construct($config);
        $this->registerTask('apply', 'save');
    }

	function getZoneList(){
		$app = JFactory::getApplication();
		$post = $app->input->getArray($_POST);
		if($post['country_id']) {
			$model = F0FModel::getTmpInstance('Zones', 'J2storeModel');
			$model->setState('country_id', $post['country_id']);
			$zones = $model->getList(true);
		}
		$model->clearState();
		$options = array();
		if (!empty($zones))
		{
			foreach ($zones as $zone)
			{
				$options[] = JHtml::_('select.option',
						$zone->j2store_zone_id, $zone->zone_name);
			}
		}
		$default = $post['zone_id'];
		echo JHtmlSelect::genericlist($options, $post['field_name'], '', 'value', 'text', $default, $post['field_id']);
		$app->close();
	}


	public function getCountry(){
		$app = JFactory::getApplication();
		$country_id = $this->input->getInt('country_id');
		$zone_id = $this->input->getInt('zone_id');
		$json = array();
		if($country_id) {
			$zones = F0FModel::getTmpInstance('Zones', 'J2storeModel')->country_id($country_id)->getList();
			$json['zone'] = $zones ;
		}
		echo json_encode($json);
		$app->close();
	}

	/**
	 * Method
	 * @return boolean
	 */
	function elements(){
		$geozone_id = $this->input->getInt('geozone_id');
		$model = F0FModel::getTmpInstance('Zones','J2StoreModel');
		$filter =array();
		$filter['limit'] = $this->input->getInt('limit',20);
		$filter['limitstart'] = $this->input->getInt('limitstart');
		$filter['search'] = $this->input->getString('search','');
		$filter['country_id'] = $this->input->getInt('country_id',1);
		foreach($filter as $key => $value){
			$model->setState($key,$value);
		}
		if(isset($geozone_id) && $geozone_id){
			$view = $this->getThisView();
			$view->setModel($this->getThisModel(),true);
			$zones =$model->enabled(1)->getItemList();
			$view->assign('zones',$zones);
			$view->assign('pagination',$model->getPagination());
			$view->assign('geozone_id',$geozone_id);
			$view->assign('state',$model->getState());
			$view->setLayout('modal');
			$view->display();
		}else{
			return false;
		}
	}

    /**
     * Save zone
     *
     * @return bool|void
     * @throws Exception
     */
    function save(){
        $app = JFactory::getApplication();
        $post = $app->input->getArray($_REQUEST);
        $zone_id = isset($post['j2store_zone_id']) && !empty($post['j2store_zone_id']) ? $post['j2store_zone_id']: 0;
        $zone_table = F0FTable::getAnInstance('Zone' ,'J2StoreTable');
        try{
            $zone_table->load($zone_id);
            $zone_table->bind($post);
            $zone_table->store();
            $zone_id = $zone_table->j2store_zone_id;
            $msg = JText::_('J2STORE_SAVE_SUCCESS');
            $type = '';
        }catch (Exception $e){
            $msg = $e->getMessage();
            $type = 'error';
        }
        if(isset($post['task']) && $post['task'] == 'apply'){
            $url = 'index.php?option=com_j2store&view=zone&id='.$zone_id;
        }else{
            $url = 'index.php?option=com_j2store&view=zones';
        }
        $app->redirect($url,$msg,$type);
    }
}
