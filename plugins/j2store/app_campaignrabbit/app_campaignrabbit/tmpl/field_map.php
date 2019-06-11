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
?>
<table class="table table-striped table-bordered">
    <tr>
        <th><?php echo JText::_('J2STORE_APP_ZOHOCRM_FIELD_NAME');?></th>
        <th><?php echo JText::_('J2STORE_APP_ZOHOCRM_J2STORE_FIELD_NAME');?></th>
        <th><?php echo JText::_('J2STORE_APP_ZOHOCRM_DEFAULT_VALUE');?></th>
    </tr>
    <?php if(!empty($vars->fields)):?>
        <?php foreach ($vars->fields as $key=>$field):?>
            <tr>
                <td>
                    <?php if($field->required):?>
                        <strong><?php echo $field->zoho_field;?></strong>
                    <?php else: ?>
                        <?php echo $field->zoho_field;?>
                    <?php endif; ?>

                    <input type="hidden" name="<?php echo $vars->name;?>[<?php echo $field->zoho_field;?>][zoho_field]" value="<?php echo $field->zoho_field;?>">
                </td>
                <td>
                    <select name="<?php echo $vars->name;?>[<?php echo $field->zoho_field;?>][j2store_field]">
                        <option value=""><?php echo JText::_('J2STORE_OPTION_SELECT');?></option>
                        <?php foreach ($vars->j2store_fields as $j2_field):?>
                            <?php if(isset($this->value[$field->zoho_field]['j2store_field']) && $j2_field->field_namekey == $this->value[$field->zoho_field]['j2store_field']): ?>
                                <option value="<?php echo $j2_field->field_namekey;?>" selected="selected"><?php echo JText::_($j2_field->field_name)?></option>
                            <?php else: ?>
                                <option value="<?php echo $j2_field->field_namekey;?>"><?php echo JText::_($j2_field->field_name)?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>

                </td>
                <td><input type="text" name="<?php echo $vars->name;?>[<?php echo $field->zoho_field;?>][default_value]" value="<?php echo isset($this->value[$field->zoho_field]['default_value']) ? $this->value[$field->zoho_field]['default_value']: '';?>"/></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>

