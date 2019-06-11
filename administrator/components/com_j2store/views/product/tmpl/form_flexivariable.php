<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */

// No direct access
defined('_JEXEC') or die;
$is_Pro = J2Store::isPro();
?>
<div class="row-fluid">
    <div class="span12">

        <div class="alert alert-block alert-info">
            <h4><?php echo JText::_('J2STORE_QUICK_HELP'); ?></h4>
            <?php echo JText::_('J2STORE_FLEXIVARIANT_PRODUCT_HELP_TEXT'); ?>
        </div>

        <div class="tabbable tabs-left">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#generalTab" data-toggle="tab"><i class="fa fa-home"></i>
                        <?php echo JText::_('J2STORE_PRODUCT_TAB_GENERAL'); ?>
                    </a>
                </li>
                <li><a href="#imagesTab" data-toggle="tab"><i class="fa fa-file-image-o"></i> <?php echo JText::_('J2STORE_PRODUCT_TAB_IMAGES'); ?></a></li>
                <li><a href="#variantsTab" data-toggle="tab"><i class="fa fa-sitemap"></i> <?php echo JText::_('J2STORE_PRODUCT_TAB_VARIANTS'); ?></a></li>
                <li><a href="#filterTab" data-toggle="tab"><i class="fa fa-filter"></i> <?php echo JText::_('J2STORE_PRODUCT_TAB_FILTER'); ?></a></li>
                <li><a href="#relationsTab" data-toggle="tab"><i class="fa fa-group"></i> <?php echo JText::_('J2STORE_PRODUCT_TAB_RELATIONS'); ?></a></li>
                <li><a href="#appsTab" data-toggle="tab"><i class="fa fa-group"></i> <?php echo JText::_('J2STORE_PRODUCT_TAB_APPS'); ?></a></li>

            </ul>
            <!-- / Tab content starts -->
            <div class="tab-content">
                <div class="tab-pane active" id="generalTab">
                    <input type="hidden" name="<?php echo $this->form_prefix.'[j2store_variant_id]'; ?>" value="<?php echo $this->item->variant->j2store_variant_id; ?>" />
                    <?php if($is_Pro):?>
                        <?php echo $this->loadTemplate('flexivariable_general');?>
                    <?php else:?>
                        <?php echo J2Html::pro(); ?>
                    <?php endif; ?>
                </div>
                <div class="tab-pane" id="imagesTab">
                    <?php if($is_Pro):?>
                        <?php echo $this->loadTemplate('images');?>
                    <?php else:?>
                        <?php echo J2Html::pro(); ?>
                    <?php endif; ?>
                </div>

                <div class="tab-pane" id="variantsTab">
                    <?php if($is_Pro):?>
                        <?php echo $this->loadTemplate('flexivariable_options');?>
                        <?php echo $this->loadTemplate('flexivariablevariants');?>
                    <?php else:?>
                        <?php echo J2Html::pro(); ?>
                    <?php endif; ?>
                </div>
                <div class="tab-pane" id="filterTab">
                    <?php if($is_Pro):?>
                        <?php echo $this->loadTemplate('filters');?>
                    <?php else:?>
                        <?php echo J2Html::pro(); ?>
                    <?php endif; ?>
                </div>
                <div class="tab-pane" id="relationsTab">
                    <?php if($is_Pro):?>
                        <?php echo $this->loadTemplate('relations');?>
                    <?php else:?>
                        <?php echo J2Html::pro(); ?>
                    <?php endif; ?>
                </div>
                <div class="tab-pane" id="appsTab">
                    <?php if($is_Pro):?>
                        <?php echo $this->loadTemplate('apps');?>
                    <?php else:?>
                        <?php echo J2Html::pro(); ?>
                    <?php endif; ?>
                </div>
            </div>
            <!-- / Tab content Ends -->
        </div> <!-- /tabbable -->
    </div>
</div>
