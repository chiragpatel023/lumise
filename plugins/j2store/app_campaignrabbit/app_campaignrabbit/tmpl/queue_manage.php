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
unset ( $listOrder );
$listOrder = $this->vars->state->get ( 'filter_order', 'tbl.user_id' );
$listDirn = $this->vars->state->get ( 'filter_order_Dir' );
$form = $this->vars->form2;
$items = $this->vars->queue;
$j2_params = J2Store::config();
$tab1 = $this->vars->is_expired == 'no' ? 'active':'';
$tab2 = $this->vars->is_expired == 'yes' ? 'active':'';
$url = "index.php?option=com_j2store&view=app&task=view&appTask=manageQueue&id=".$this->vars->id;
?>
<ul class="nav nav-tabs">
    <li class="<?php echo $tab1;?>">
        <a  href="<?php echo $url.'&is_expired=no'?>"><?php echo JText::_('J2STORE_CAMPAIGNRABBIT_QUEUE_LIST');?></a>
    </li>
    <li class="<?php echo $tab2;?>"><a  href="<?php echo $url.'&is_expired=yes'?>"><?php echo JText::_('J2STORE_CAMPAIGNRABBIT_EXPIRED_QUEUE_LIST');?></a></li>
</ul>
<div class="tab-content">
    <div id="home" class="tab-pane fade in active">
        <div class="manage-quickbook-logs">
            <form class="form-horizontal" method="post" action="<?php echo $form['action'];?>" name="adminForm" id="adminForm" >
                <span class="pull-right">
		<?php echo $this->vars->pagination->getLimitBox();?>
		</span>
                <?php if($this->vars->is_expired == 'yes'):?>
                    <div>
                        <span class="pull-right"><a class="btn btn-primary" href="<?php echo 'index.php?option=com_j2store&view=app&task=view&appTask=reQueue&id='.$this->vars->id.'&is_expired=yes'?>"><?php echo JText::_('J2STORE_APP_CAMPAIGN_RABBIT_REQUEUE');?></a></span>
                    </div>
                <?php endif; ?>
                <br/>
                <br />
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo JText::_('J2STORE_CAMPAIGNRABBIT_RELATION_ID'); ?>
                        </th>
                        <th><?php echo JText::_('J2STORE_CAMPAIGNRABBIT_STATUS'); ?>
                        </th>
                        <th><?php echo JText::_('J2STORE_CAMPAIGNRABBITQUEUE_DATA'); ?>
                        </th>
                        <th><?php echo JText::_('J2STORE_CAMPAIGNRABBIT_PRIORITY'); ?>
                        </th>
                        <th><?php echo JText::_('J2STORE_CAMPAIGNRABBIT_EXPIRED'); ?>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($items)):
                        foreach ($items as $item):?>
                            <tr>
                                <td><?php echo $item->relation_id;?></td>
                                <td><?php echo $item->status;?></td>
                                <td><?php echo $item->queue_data;?></td>
                                <td><?php echo $item->priority;?></td>
                                <td><?php
                                    $tz = JFactory::getConfig()->get('offset');
                                    $date = JFactory::getDate($item->expired, $tz);
                                    echo $date->format($j2_params->get('date_format', JText::_('DATE_FORMAT_LC1')), true);
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <?php else:?>
                        <tr>
                            <td colspan="5"><?php echo JText::_('J2STORE_NO_ITEMS_FOUND');?></td>
                        </tr>
                    <?php endif;?>
                    </tbody>
                </table>

                <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                <input type="hidden" id="appTask" name="appTask" value="manageQueue" />
                <input type="hidden" name="task" value="view" />
                <?php echo $this->vars->pagination->getListFooter(); ?>
                <?php echo J2Html::hidden('boxchecked',0);?>
                <input type="hidden" name="id" value="<?php echo $this->vars->id; ?>" />
            </form>
        </div>
    </div>
</div>

