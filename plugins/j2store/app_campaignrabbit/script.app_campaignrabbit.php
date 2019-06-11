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

class plgJ2StoreApp_campaignrabbitInstallerScript {

    function preflight( $type, $parent ) {

        jimport('joomla.filesystem.file');

        $db = JFactory::getDbo ();
        // get the table list
        $tables = $db->getTableList ();
        // get prefix
        $prefix = $db->getPrefix ();

        //campaign_addr_id
        if (in_array ( $prefix . 'j2store_addresses', $tables )) {
            $fields = $db->getTableColumns ( '#__j2store_addresses' );
            if (! array_key_exists ( 'campaign_addr_id', $fields )) {
                $query = "ALTER TABLE #__j2store_addresses ADD `campaign_addr_id` varchar(255) NOT NULL;";
                $this->_executeQuery ( $query );
            }
        }

        //campaign_variant_id
       /* if (in_array ( $prefix . 'j2store_variants', $tables )) {
            $fields = $db->getTableColumns ( '#__j2store_variants' );
            if (! array_key_exists ( 'campaign_variant_id', $fields )) {
                $query = "ALTER TABLE #__j2store_variants ADD `campaign_variant_id` varchar(255) NOT NULL;";
                $this->_executeQuery ( $query );
            }
        }*/

        //campaign_order_id
        if (in_array ( $prefix . 'j2store_orders', $tables )) {
            $fields = $db->getTableColumns ( '#__j2store_orders' );
            if (! array_key_exists ( 'campaign_order_id', $fields )) {
                $query = "ALTER TABLE #__j2store_orders ADD `campaign_order_id` varchar(255) NOT NULL;";
                $this->_executeQuery ( $query );
            }
        }
        //campaign_double_opt_in
        if (in_array ( $prefix . 'j2store_orders', $tables )) {
            $fields = $db->getTableColumns ( '#__j2store_orders' );
            if (! array_key_exists ( 'campaign_double_opt_in', $fields )) {
                $query = "ALTER TABLE #__j2store_orders ADD `campaign_double_opt_in` INT(3) NOT NULL;";
                $this->_executeQuery ( $query );
            }
        }
        return true;
    }

    private function _executeQuery($query) {
        $db = JFactory::getDbo ();
        $db->setQuery ( $query );
        try {
            $db->execute ();
        } catch ( Exception $e ) {
            // do nothing. we dont want to fail the install process.
        }
    }
}