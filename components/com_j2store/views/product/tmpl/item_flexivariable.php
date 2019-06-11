<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */

// No direct access
defined('_JEXEC') or die;
$this->form_id = 'j2store-addtocart-form-'.$this->product->j2store_product_id;
?>

    <div class="product-sku">
        <span class="sku-text"><?php echo JText::_('J2STORE_SKU')?></span>
        <span itemprop="sku" class="sku"></span>
    </div>
<?php //echo $this->loadTemplate('sku'); ?>
<?php echo $this->loadTemplate('flexiprice'); ?>
<?php if($this->params->get('list_show_product_stock', 1) ) : ?>
    <span class="product-stock-container">
        <span class="instock"></span>
        <span class="outofstock"></span>
    </span>
    <span class="backorder-notification"></span>
<?php endif; ?>
<?php if( J2Store::product()->canShowCart($this->params) ): ?>
    <form action="<?php echo $this->product->cart_form_action; ?>"
          method="post" class="j2store-addtocart-form"
          id="<?php echo $this->form_id; ?>"
          name="j2store-addtocart-form-<?php echo $this->product->j2store_product_id; ?>"
          data-product_id="<?php echo $this->product->j2store_product_id; ?>"
          data-product_type="<?php echo $this->product->product_type; ?>"
        <?php if(isset($this->product->variant_json)): ?>
            data-product_variants="<?php echo $this->escape($this->product->variant_json);?>"
        <?php endif; ?>
          enctype="multipart/form-data">
        <?php if($this->product->has_options): ?>
            <?php echo $this->loadTemplate('flexivariableoptions'); ?>
        <?php endif; ?>

        <?php echo $this->loadTemplate('cart'); ?>
        <div class="j2store-notifications"></div>
        <input type="hidden" name="variant_id" value="" />
    </form>
<?php endif; ?>