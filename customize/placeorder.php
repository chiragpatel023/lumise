<?php 
	
require('autoload.php');
global $lumise, $lumise_helper;

if(isset($_POST['action'])){
    
    $order_id = $lumise->connector->save_order();
	    
    if($order_id){
		//add action before redirect for process payment.
		$lumise_helper->redirect($lumise->cfg->url. 'placeorder.php?order_id='. $order_id);
    }
}

$order = $lumise->connector->get_session('lumise_justcheckout');
$order_id = $_GET['order_id'];
$user = $order['user'];

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
	                        <strong><?php echo $order['created'];?></strong>
	                    </p>
                        <p>
	                        <?php echo $lumise->lang('Total'); ?>: 
	                        <strong><?php echo $lumise->lib->price($order['total']);?></strong>
	                    </p>
                        <p>
	                        <?php echo $lumise->lang('Payment Method'); ?>: 
	                        <strong>COD</strong>
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
                                foreach($items as $item):?>
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
                    <h4>Customer details</h4>
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
include(theme('footer.php'));
