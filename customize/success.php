<?php 
	
require('autoload.php');
global $lumise, $lumise_helper;

$order = $lumise->connector->get_session('lumise_justcheckout');
$order_id = $order['id'];
$user = $order['user'];

if(isset($_POST['txn_id'])){
    //update order data
    $db = $lumise->get_db();
	$db->where ('id', $order_id)->update ('orders', array(
        'txn_id' => $_POST['txn_id'],
    ));
	
	$db->where ('id', $order_id);
	$db->where ('status', 'pending')->update ('orders', array(
        'status' => 'processing'
    ));
    $order['status'] = 'processing';
}

$page_title = $lumise->lang('Thank you');

include(theme('header.php'));

?>
        <div class="container">
            <div id="confirm" class="thankyou">
                <div class="col-md-12 pt-3">
                    <h3><?php echo $lumise->lang('Thank you. Your order has been received.'); ?></h3>
                    <div class="lumise-order-sumary">
                        <p>
	                        <?php echo $lumise->lang('Order ID'); ?>: 
	                        <strong>#<?php echo $order_id;?></strong>
	                    </p>
                        <p>
	                        <?php echo $lumise->lang('Date'); ?>: 
	                        <strong><?php echo $order['created'];?></strong></p>
						<p>
							<?php echo $lumise->lang('Status'); ?>: 
							<strong><?php echo $lumise->connector->order_status_name($order['status']);?></strong>
						</p>
                        <p>
	                        <?php echo $lumise->lang('Total'); ?>: 
	                        <strong><?php echo $lumise->lib->price($order['total']);?></strong>
	                    </p>
                        <p class="lumise-payment-name">
	                        <?php echo $lumise->lang('Payment Method'); ?>: <strong><?php echo $order['payment'];?></strong>
	                    </p>
                    </div>
                    
                    <h4><?php echo $lumise->lang('Order details'); ?></h4>
                    <div class="wrap-table">
                        <table class="lumise-table">
                            <thead>
                                <tr>
                                    <th><?php echo $lumise->lang('Product Name'); ?></th>
                                    <th><?php echo $lumise->lang('Total'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $items = $order['items'];
                                $total = 0;
                                foreach($items as $item):
									$cart_data = $lumise->lib->get_cart_item_file($item['file']);
									$item = array_merge($item, $cart_data);
									?>
                                <tr>
                                    <td>
	                                    <span class="product-title"><?php echo $item['product_name'];?></span> 
	                                    x <?php echo $item['qty'];?>
	                                </td>                                    
                                    <td>
	                                    <?php echo $lumise->lib->price($item['price']['total']); ?>
	                                    <?php $total += $item['price']['total']; ?>
	                                </td>
                                </tr>
                                <?php endforeach;?>
                                <tr>
                                    <td><strong><?php echo $lumise->lang('Grand Total'); ?></strong></td>
                                    <td><?php $grand_total = $total?><?php echo $lumise->lib->price($grand_total);?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
					<div class="mt-30"></div>
                    <h4><?php echo $lumise->lang('Customer details'); ?></h4>
                    <div class="wrap-table">
                        <table class="lumise-table">
                            
                            <tbody>
                                <tr>
                                    <td><strong><?php echo $lumise->lang('Email'); ?></strong></td>
                                    <td><?php echo $user['email'];?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo $lumise->lang('Phone'); ?></strong></td>
                                    <td><?php echo $user['phone'];?></td>
                                </tr>
                            </tbody>
                        </table>
						<div class="mt-30"></div>
                        <div class="lumise-billing-details">
							<h4><?php echo $lumise->lang('Billing Address'); ?></h4>
                            <p><?php echo $user['name'];?></p>
                            <p><?php echo $user['address'];?></p>
                            <p><?php echo $user['city'];?></p>
                            <p><?php echo $user['country'];?></p>
                        </div>
                    </div>
					<div class="mt-30"></div>
                    <div class="form-actions">
                        <a href="<?php echo $lumise->cfg->url;?>" class="btn btn-large btn-primary">
	                        <?php echo $lumise->lang('Continue Shopping'); ?>
	                    </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>   
<?php
	
if(isset($_GET['order_id']) && isset($order['id']) && $order['id'] == $_GET['order_id']){
	echo "<script>localStorage.setItem('LUMISE-CART-DATA', '');</script> ";
}
	
include(theme('footer.php'));
