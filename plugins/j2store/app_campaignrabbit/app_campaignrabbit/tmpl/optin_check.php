<?php
/**
 * --------------------------------------------------------------------------------
 * APP - campignrabbit
 * --------------------------------------------------------------------------------
 * @package     Joomla  3.x
 * @subpackage  J2 Store
 * @author      Alagesan, J2Store <support@j2store.org>
 * @copyright   Copyright (c) 2018 J2Store . All rights reserved.
 * @license     GNU/GPL license: http://www.gnu.org/licenses/gpl-2.0.html
 * @link        https://www.j2store.org
 * --------------------------------------------------------------------------------
 *
 * */
defined('_JEXEC') or die('Restricted access');
$tos =  $vars->params->get('termsid', null);
?>
<!--  Then check the type of condition to show  -->
<div class="" id="j2store_app_campignrabbit_cancellation">
    <label for="j2store_app_tos_campignrabbitterms_check">
        <input type="checkbox"  name="app_camp_rabbit_opt_in"  />
        <?php echo JText::_($this->params->get('opt_in_text','J2STORE_CAMPAIGN_OPT_IN_TEXT'));?>
    </label>
</div>