<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */
// No direct access to this file
defined ( '_JEXEC' ) or die ();
$order = $this->order;
$items = $this->order->getItems();
$currency = J2Store::currency();

?>
	<h3><?php echo JText::_('J2STORE_ORDER_SUMMARY')?></h3>
	<table class="j2store-cart-table table table-bordered">
		<thead>
			<tr>
				<th><?php echo JText::_('J2STORE_CART_LINE_ITEM'); ?></th>
				<th><?php echo JText::_('J2STORE_CART_LINE_ITEM_QUANTITY'); ?></th>
				<th><?php echo JText::_('J2STORE_CART_LINE_ITEM_TOTAL'); ?></th>
			</tr>
			</thead>
			<tbody>

				<?php foreach ($items as $item): ?>
				<?php
					$registry = new JRegistry;
					$registry->loadString($item->orderitem_params);
					
					$db = JFactory::getDbo();

// Create a new query object.
$query = $db->getQuery(true);
					$query->select($db->quoteName(array('front_image', 'back_image')))
					->from($db->quoteName('#__j2store_cartitems'))
					->where($db->quoteName('j2store_cartitem_id') . ' = '. $db->quote($item->cartitem_id));
$db->setQuery($query);
$results = $db->loadAssoc();
					//echo '<pre>';print_r($results); exit;
					$item->params = $registry;
					//$thumb_image = $item->params->get('thumb_image', '');
					if($results['front_image'] != ''){
						$thumb_image = $results['front_image'];
					}else{
						$thumb_image = $item->params->get('thumb_image', '');
						$thumb_image = JURI::root(true).JPath::clean('/'.$thumb_image);
					}
					
                    $back_order_text = $item->params->get('back_order_item', '');
				?>
				<tr>
					<td>
						<?php if($this->params->get('show_thumb_cart', 1) && !empty($thumb_image)): ?>
							<span class="cart-thumb-image">
								<img alt="<?php echo $item->orderitem_name; ?>" src="<?php echo $thumb_image; ?>" >
								<?php if($results['back_image']){ ?>
									<img alt="<?php echo $item->orderitem_name; ?>" src="<?php echo $results['back_image']; ?>" >
								<?php } ?>
							</span>
						<?php endif; ?>

						<?php echo $this->order->get_formatted_lineitem_name($item);?>

						<?php if($this->params->get('show_price_field', 1)): ?>

							<span class="cart-product-unit-price">
								<span class="cart-item-title"><?php echo JText::_('J2STORE_CART_LINE_ITEM_UNIT_PRICE'); ?></span>								
								<span class="cart-item-value">
									<?php echo $currency->format($this->order->get_formatted_order_lineitem_price($item, $this->params->get('checkout_price_display_options', 1)), $this->order->currency_code, $this->order->currency_value);?>
								</span>
							</span>
						<?php endif; ?>

						<?php if(!empty($item->orderitem_sku)): ?>
						<br />
							<span class="cart-product-sku">
								<span class="cart-item-title"><?php echo JText::_('J2STORE_CART_LINE_ITEM_SKU'); ?></span>
								<span class="cart-item-value"><?php echo $item->orderitem_sku; ?></span>
							</span>

						<?php endif; ?>
                        <?php if($back_order_text):?>
                            <br />
                            <span class="label label-inverse"><?php echo JText::_($back_order_text);?></span>
                        <?php endif;?>
						<?php echo J2Store::plugin()->eventWithHtml('AfterDisplayLineItemTitleInOrder', array($item, $this->order, $this->params));?>
					</td>
					<td><?php echo $item->orderitem_quantity; ?></td>
					<td class="cart-line-subtotal">
						<?php echo $currency->format($this->order->get_formatted_lineitem_total($item, $this->params->get('checkout_price_display_options', 1)), $this->order->currency_code, $this->order->currency_value ); ?>					
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			
			<tfoot class="cart-footer">
				<?php if($totals = $this->order->get_formatted_order_totals()): ?>
					<?php foreach($totals as $total): ?>
						<tr>
							<th scope="row" colspan="2"> <?php echo $total['label']; ?></th>
							<td><?php echo $total['value']; ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tfoot>	
		</table>

