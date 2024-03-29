<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */

// No direct access
defined('_JEXEC') or die;

?>
<div class="j2store-product-general">


			<div class="control-group form-inline">
					<?php echo J2Html::label(JText::_('J2STORE_PRODUCT_VISIBILITY'), 'visibility', array('class'=>'control-label')); ?>
					<?php echo J2Html::radio($this->form_prefix.'[visibility]', $this->item->visibility ); ?>
				</div>
				<div class="control-group">
					<?php echo J2Html::label(JText::_('J2STORE_PRODUCT_SKU'), 'sku',array('class'=>'control-label')); ?>
					<?php echo J2Html::text($this->form_prefix.'[sku]', $this->variant->sku,array('class'=>'input-small ')); ?>
				</div>
				<div class="control-group">
					<?php echo J2Html::label(JText::_('J2STORE_PRODUCT_UPC'), 'upc',array('class'=>'control-label')); ?>
					<?php echo J2Html::text($this->form_prefix.'[upc]', (isset($this->variant->upc))?$this->variant->upc:'',array('class'=>'input-small ')); ?>
 				</div>
 				<div class="control-group">
					<?php echo J2Html::label(JText::_('Product Base ID'), 'lumiseid',array('class'=>'control-label')); ?>
					<?php echo J2Html::text($this->form_prefix.'[lumiseid]', (isset($this->variant->lumiseid))?$this->variant->lumiseid:'',array('class'=>'input-small ')); ?>
 				</div>
				<div class="control-group">
					<?php echo J2Html::label(JText::_('J2STORE_PRODUCT_MANUFACTURER'), 'manufacturer',array('class'=>'control-label')); ?>
					<?php echo $this->manufacturers;?>
				</div>
				<?php if(J2Store::isPro()): ?>
				<div class="control-group">
					<?php echo J2Html::label(JText::_('J2STORE_PRODUCT_VENDOR'), 'vendor',array('class'=>'control-label')); ?>

						<?php echo $this->vendors;?>
				</div>
				<?php endif;?>

				<div class="control-group">
					<?php echo J2Html::label(JText::_('J2STORE_PRODUCT_TAX_PROFILE'), 'tax_profile',array('class'=>'control-label')); ?>
					<?php echo $this->taxprofiles;?>
				</div>

                <div class="control-group form-inline">
                    <?php echo J2Html::label(JText::_('J2STORE_PRODUCT_MAIN_TAG'), 'main_tag', array('class'=>'control-label')); ?>
                    <?php echo $this->tag_lists;?>
                </div>

				<div class="control-group">
					<?php echo J2Html::label(JText::_('J2STORE_PRODUCT_CART_TEXT'), 'addtocart_text',array('class'=>'control-label')); ?>
					<?php echo J2Html::text($this->form_prefix.'[addtocart_text]', JText::_($this->item->addtocart_text), array('class'=>'input-large ')); ?>
				</div>

				<div class="control-group">
					<?php echo J2Html::label(JText::_('J2STORE_PRODUCT_CUSTOM_CSS_CLASS'), 'custom_css_class',array('class'=>'control-label')); ?>
					<?php echo J2Html::text($this->form_prefix.'[params][product_css_class]', $this->item->params->get('product_css_class',''), array('class'=>'input-large ')); ?>
				</div>

</div>
