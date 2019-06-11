<?php
/**
 * @package     J2Store
 * @author      Alagesan, J2Store <support@j2store.org>
 * @copyright   Copyright (c)2018 Ramesh Elamathi / J2Store.org
 * @license     GNU GPL v3 or later
 * */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$current_page = $this->state->get('current_page','popular');
$page_url = 'index.php?option=com_j2store&view=appstores';
$sidebar = JHtmlSidebar::render();
?>
<div class="row-fluid">
    <?php if(!empty( $sidebar )): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $sidebar ; ?>
    </div>
    <div id="j-main-container" class="span10">
        <?php else : ?>
        <div id="j-main-container">
            <?php endif;?>
    <ul class="nav nav-tabs">
        <li class="<?php echo ($current_page == 'popular') ? 'active' : '';?>"><a  href="<?php echo $page_url;?>&page=popular"><?php echo JText::_('J2STORE_PLUGIN_POPULAR');?></a></li>
        <li class="<?php echo ($current_page == 'free') ? 'active' : '';?>"><a  href="<?php echo $page_url;?>&page=free"><?php echo JText::_('J2STORE_PLUGIN_FREE');?></a></li>
        <li class="<?php echo ($current_page == 'installed') ? 'active' : '';?>"><a  href="<?php echo $page_url;?>&page=installed"><?php echo JText::_('J2STORE_PLUGIN_INSTALLED');?></a></li>
        <li class="<?php echo ($current_page == 'all') ? 'active' : '';?>"><a  href="<?php echo $page_url;?>&page=all"><?php echo JText::_('J2STORE_PLUGIN_ALL');?></a></li>
    </ul>
    <div class="tab-content">
        <div id="home" class="tab-pane fade in active">
            <?php echo $this->loadTemplate('item');?>
        </div>
    </div>
</div>
