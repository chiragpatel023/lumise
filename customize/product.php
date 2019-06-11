<?php

require('autoload.php');
global $lumise;

$id = isset($_GET['product_id']) ? $_GET['product_id'] : '1';
$value = $lumise->lib->get_row_id($id, 'products');

$orderby  = 'name';
$ordering = 'desc';
$dt_order = 'name_asc';
$current_page = isset($_GET['tpage']) ? $_GET['tpage'] : 1;

$search_filter = array(
    'keyword' => '',
    'fields' => 'name'
);

$default_filter = array(
    'type' => '',
);
$per_page = 4;
$start = ( $current_page - 1 ) * $per_page;
$data = $lumise->lib->get_rows('products', $search_filter, $orderby, $ordering, $per_page, $start, array('active'=> 1), '');
$thumbnail_url = 'https://demo.lumise.com/assets/images/not-found.jpg';
if(!empty($value['thumbnail_url']))
    $thumbnail_url = $value['thumbnail_url'];
    
$page_title = isset($value['name']) ? $value['name'] : 'Product Details';

$has_template = $lumise_helper->has_template($value);

include(theme('header.php'));
?>
        <div class="lumise-product">
            <div class="container">
                <div class="row">
                    <div class="col-md-5">
                        <div class="product-img">
                            <figure><img src="<?php echo $thumbnail_url;?>" alt=""></figure>    
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="product-detail">
                            <?php if(isset($value['name'])) echo '<h1>'.$value['name'].'</h1>'; ?>
                            <?php if(isset($value['price'])) echo '<p class="price">'.$lumise->lib->price($value['price']).'</p>'; ?>
                            <?php if(isset($value['description']) && !empty($value['description'])) 
	                            	echo '<p class="desc">'.$value['description'].'</p>';
	                        ?>
                            <form>
                                <input type="number" step="1" min="1" max="" name="quantity" value="1" inputmode="numeric">
                                <?php if($has_template): ?>
                                    <a href="<?php echo $lumise->cfg->url.'add-cart.php?product='.$value['id']; ?>" class="lumise-add">
	                                    <?php echo $lumise->lang('Add to cart'); ?>
	                                </a>
                                <?php endif; ?>
                                <a href="<?php echo $lumise->cfg->tool_url.'?product='.$value['id']; ?>" class="lumise-custom">
	                                <?php echo $lumise->lang('Customize'); ?>
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="lumise-list lumise-related">
                    <h2> <?php echo $lumise->lang('More products'); ?></h2>
                    <?php LumiseView::products($data['rows']); ?>
                </div>
            </div>
        </div>
<?php include(theme('footer.php')); ?>
