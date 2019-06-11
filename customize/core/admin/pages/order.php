<?php
    
	$prefix = 'ops_';
    
    $order_id = isset($_REQUEST['order_id'])? $_REQUEST['order_id']: 0;
    // Search Form
	$data_search = '';
	if (isset($_POST['search_ops']) && !empty($_POST['search_ops'])) {
		
		$data_search = isset($_POST['search']) ? trim($_POST['search']) : '';

		if (empty($data_search)) {
			$errors = 'Search Product Name';
			$_SESSION[$prefix.'data_search'] = '';
		} else {
			$_SESSION[$prefix.'data_search'] = 	$data_search;
		}

	}

	if (!empty($_SESSION[$prefix.'data_search'])) {
		$data_search = '%'.$_SESSION[$prefix.'data_search'].'%';
	}
    
    $search_filter = array(
        'keyword' => $data_search,
        'fields' => 'ops.product_name'
    );
    
    $lumise->do_action('before_order_products', $order_id);
    
    // Sort Form
	if (!empty($_POST['sort'])) {

		$dt_sort = isset($_POST['sort']) ? $_POST['sort'] : '';
		$_SESSION[$prefix.'dt_order'] = $dt_sort;
		
		switch ($dt_sort) {

			case 'product_id_asc':
				$_SESSION[$prefix.'orderby'] = 'product_id';
				$_SESSION[$prefix.'ordering'] = 'asc';
				break;
			case 'product_id_desc':
				$_SESSION[$prefix.'orderby'] = 'product_id';
				$_SESSION[$prefix.'ordering'] = 'desc';
				break;
			case 'name_asc':
				$_SESSION[$prefix.'orderby'] = 'product_name';
				$_SESSION[$prefix.'ordering'] = 'asc';
				break;
			case 'name_desc':
				$_SESSION[$prefix.'orderby'] = 'product_name';
				$_SESSION[$prefix.'ordering'] = 'desc';
				break;
            
			default:
				break;

		}

	}
    
    if(
        $_SERVER['REQUEST_METHOD'] =='POST' &&
        (
            !empty($_POST['sort']) ||
            isset($_POST['do'])
        )
    ){
        $lumise->redirect($lumise_router->getURI().'lumise-page=order&order_id='.$order_id);
    }

	$orderby  = (isset($_SESSION[$prefix.'orderby']) && !empty($_SESSION[$prefix.'orderby'])) ? $_SESSION[$prefix.'orderby'] : 'product_id';
	$ordering = (isset($_SESSION[$prefix.'ordering']) && !empty($_SESSION[$prefix.'ordering'])) ? $_SESSION[$prefix.'ordering'] : 'asc';
	$dt_order = isset($_SESSION[$prefix.'dt_order']) ? $_SESSION[$prefix.'dt_order'] : 'product_id_desc';
    $items = $lumise->connector->products_order($order_id, $search_filter, $orderby, $ordering);

    $lumise_printings = $lumise->lib->get_prints();
    $printings = array();
    foreach( $lumise_printings as $p ) {
        $printings[ $p['id'] ] = $p;
    }
    
?><div class="lumise_wrapper">
	
	<div class="lumise_content">

		<div class="lumise_header">
			<h2>
				<a href="<?php echo $lumise_router->getURI(); ?>lumise-page=orders"><?php echo $lumise->lang('All Orders'); ?></a> 
				<i class="fa fa-angle-right"></i> 
				<?php printf($lumise->lang('Order %s'), '#'.$_REQUEST['order_id']) ?>
			</h2>
			<?php
				$lumise_page = isset($_GET['lumise-page']) ? $_GET['lumise-page'] : '';
				echo $lumise_helper->breadcrumb($lumise_page);
			?>
            <div class="lumise-order-details lumise_option">
                <div class="col-3">
                    <h4><?php echo $lumise->lang('General Details'); ?></h4>
                    <p>
                        <strong><?php echo $lumise->lang('Total Price:'); ?></strong>
                        <span><?php echo $lumise->lib->price($items['order']['total']);?></span>
                    </p>
                    <p>
                        <strong><?php echo $lumise->lang('Created At:'); ?></strong>
                        <span><?php echo $items['order']['created'];?></span>
                    </p>
                    <p>
                        <strong><?php echo $lumise->lang('Updated At:'); ?></strong>
                        <span><?php echo $items['order']['updated'];?></span>
                    </p>
                    <?php if(isset($items['order']['payment'])): ?>
                    <p>
                        <strong><?php echo $lumise->lang('Payment:'); ?></strong>
                        <span class="lumise-payment-method"><?php echo isset($items['order']['payment'])? $items['order']['payment']: '';?></span>
                    </p>
                    <?php endif; ?>
                    <div class="order_status">
                        <strong><?php echo $lumise->lang('Status:'); ?></strong>
                    
                        <form action="<?php echo $lumise_router->getURI();?>lumise-page=order&order_id=<?php echo $order_id;?>" method="post">
                            <?php $lumise->views->order_statuses($items['order']['status'], true);?>
                            <input type="hidden" name="do" value="action"/>
                        </form>
                    </div>
                </div>
                <?php if(isset($items['billing']) && count($items['billing'])>0):?>
                <div class="col-3">
                	<h4><?php echo $lumise->lang('Billing details'); ?></h4>
                    <p>
                        <strong><?php echo $lumise->lang('Name:'); ?></strong>
                        <span><?php echo isset($items['billing']['name'])? $items['billing']['name'] : '';?></span>
                    </p>
                	<p>
                		<strong><?php echo $lumise->lang('Address:'); ?></strong>
                		<span><?php echo isset($items['billing']['address'])? $items['billing']['address'] : '';?></span>
                	</p>
                	<p>
                		<strong><?php echo $lumise->lang('Email address:'); ?></strong>
                		<span><?php echo isset($items['billing']['email'])? $items['billing']['email'] : '';?></span>
                	</p>
                	<p>
                		<strong><?php echo $lumise->lang('Phone:'); ?></strong>
                		<span><?php echo isset($items['billing']['phone'])? $items['billing']['phone'] : '';?></span>
                	</p>
                	
                </div>
                <?php endif;?>
            </div>
            
		</div>

            <div class="lumise_option">
                <div class="left">
                    <form action="<?php echo $lumise_router->getURI();?>lumise-page=order&order_id=<?php echo $order_id;?>" method="post">
                        <?php $lumise->securityFrom();?>
                    </form>
                </div>
                <div class="right">
                    <form action="<?php echo $lumise_router->getURI();?>lumise-page=order&order_id=<?php echo $order_id;?>" method="post">
                        <input type="text" name="search" class="search" placeholder="<?php echo $lumise->lang('Search ...'); ?>" value="<?php if(isset($_SESSION[$prefix.'data_search'])) echo $_SESSION[$prefix.'data_search']; ?>">
                        <input  class="lumise_submit" type="submit" name="search_ops" value="<?php echo $lumise->lang('Search'); ?>">
                        <?php $lumise->securityFrom();?>

                    </form>
                </div>
            </div>
        
        <div class="lumise_wrap_table">
			<table class="lumise_table lumise_ops lumise_order_details">
				<thead>
					<tr>
						<th width="5%"><?php echo $lumise->lang('ID'); ?></th>
						<th width="5%"><?php echo $lumise->lang('Product ID'); ?></th>
						<th><?php echo $lumise->lang('Product Name'); ?></th>
						<th><?php echo $lumise->lang('Thumbnail'); ?></th>
						<th><?php echo $lumise->lang('Attributes'); ?></th>
                        <th width="5%"><?php echo $lumise->lang('Subtotal'); ?></th>
                        <th width="5%"><?php echo $lumise->lang('Print'); ?></th>
					</tr>
				</thead>
				<tbody>
	                <?php
	                
	                if (count($items['rows']) > 0) {
	                    foreach($items['rows'] as $item):
	                ?>
	                <tr>
						<td>#<?php echo $item['id'];?></td>
						<td><?php echo $item['product_id'];?></td>
						<td><?php echo $item['product_name'] . ' x ' .$item['qty'];?></td>
						<td>
                            <?php
                            $product = $lumise->lib->get_product($item['product_base']);
                            if(isset($item['screenshots']) && $item['screenshots'] != null){
                                $screenshots = json_decode($item['screenshots']);
                                foreach ($screenshots as $screenshot) {
                					echo '<img src="'.$lumise->cfg->upload_url.'orders/'.$screenshot.'" class="lumise-order-thumbnail" />';
                				}
                            }
                            if(isset($item['custom']) && !$item['custom']){
                                
                                if(isset($product['thumbnail_url']))
                                    echo '<img src="'.$product['thumbnail_url'].'" class="lumise-order-thumbnail" />';
                            }
                            ?>
                        </td>
                        <td><?php
	                        
                            $data_obj = $lumise->lib->dejson($item['data']);
                            
                            if (isset($data_obj->attributes)) {
	                            
                                foreach($data_obj->attributes as $id => $attr){
	                                if (is_object($attr) && isset($attr->name)) {
		                                if (isset($attr->value)) {
		                                    echo "<strong>{$attr->name}:</strong> ";
		                                    if ($attr->type == 'color' || $attr->type == 'product_color') {
			                                    $vals = explode("\n", $attr->values);
			                                    $col = htmlentities($attr->value);
			                                    foreach ($vals as $val) {
				                                    $val = explode("|", $val);
				                                    if ($attr->value == $val[0] && isset($val[1]) && !empty($val[1])) {
					                                    $col = htmlentities($val[1]);
				                                    }
			                                    }
												echo '<span title="'.htmlentities($attr->value).'" style="background: '.$attr->value.';padding: 2px 5px;border-radius: 2px;">'.$col.'</span>';
		                                    } else echo htmlentities($attr->value);
		                                    
		                                    echo "<br>";
		                                }
                                    } else {
	                                    echo "<strong>$id:</strong>";
	                                    if (is_array($values)){
	                                        foreach($values as $att_val){
	                                            echo "<dt>$attr</dt>";
	                                        }
	                                    } 
                                    }
	                            }
	                          
                            }
                            
                            if( 
                                isset($data_obj->printing) 
                                && is_array($printings) 
                                && isset($printings[ $data_obj->printing]) 
                            ){
                                $pmethod = $printings[ $data_obj->printing];
                                echo "<strong>".$lumise->lang('Printing Type').":</strong>";
                                echo "<dt>".$pmethod['title']."</dt>";
                            }
                            
                            if( isset($data_obj->color) ){
                                echo "<strong>".$lumise->lang('Color').":</strong>";
                                echo "<dt>".(($data_obj->color != $data_obj->color_name)? $data_obj->color . ' - '. $data_obj->color_name : $data_obj->color)."</dt>";
                            }
                        ?></td>
                        <td><?php echo $lumise->lib->price($item['product_price']);?></td>
                        <td>
	                        <a target="_blank" class="btn btn-print-design" href="<?php
		                        
                                $is_query = explode('?', $lumise->cfg->tool_url);
	                                
                                $url = $lumise->cfg->tool_url.(isset($is_query[1])? '&':'?');
                                $url .= 'product='.$item['product_base'];
                                $url .= (($item['custom'] == 1)? '&design_print='.str_replace('.lumi', '', $item['design']) : '');
                                $url .= '&order_print='.$order_id;
                                $url .= ($lumise->connector->platform != 'php' ? '&product_cms='.$item['product_id'] : '');
	                                        
                                echo str_replace('?&', '?', $url);
	                                
			                ?>">
		                        <?php echo $lumise->lang('Download Design'); ?> &rarr;
		                    </a>
		                </td>
					</tr>
	                    <?php
	                    endforeach;
	                }
	                else {
	                ?>
	                <tr>
	                    <td colspan="6">
	                        <p class="no-data"><?php echo $lumise->lang('Apologies, but no results were found'); ?></p>
	                    </td>
	                </tr>
	                    
	                    
	                <?php
	                }
	                ?>
				</tbody>
                <tfoot class="no-border">
                    <tr>
                        <td colspan="3"></td>
                        <td></td>
                        <td colspan="2">
                            <strong style="float: right;"><?php echo $lumise->lang('Order Total:'); ?></strong>
                        </td>
                        <td>
                            <?php echo $lumise->lib->price($items['order']['total']); ?>
                        </td>
                    </tr>
                </tfoot>
			</table>
        </div>
		
	</div>

</div>
