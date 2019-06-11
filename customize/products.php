<?php
require('autoload.php');
global $lumise;

$dt_order = isset($_REQUEST['order']) && !empty($_REQUEST['order']) ? $_REQUEST['order'] : 'name_asc';
$current_page = isset($_GET['tpage']) ? $_GET['tpage'] : 1;
$cate_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';

$order_cfg = explode('_', $dt_order);
$orderby = "`{$order_cfg[0]}`";
$ordering = "{$order_cfg[1]}";

$search_filter = array(
    'keyword' => ''
,    'fields' => 'name'
);

$default_filter = array(
    'type' => '',
);
$per_page = 8;
$start = ( $current_page - 1 ) * $per_page;

if (isset($cate_id) && !empty($cate_id))
    $data = $lumise->lib->get_by_category($cate_id, $orderby, $ordering, $per_page, $start, 'products', array('active'=> 1));
else
    $data = $lumise->lib->get_rows('products', $search_filter, $orderby, $ordering, $per_page, $start, array('active'=> 1), '');

$data['total_page'] = ceil($data['total_count'] / $per_page);
$config = array(
    'current_page'  => $current_page,
    'total_record'  => $data['total_count'],
    'total_page'    => $data['total_page'],
    'limit'         => $per_page,
    'link_full'     => $lumise->cfg->url.'products.php?tpage={page}'.(!empty($cate_id) ? '&category_id='.$cate_id : ''),
    'link_first'    => $lumise->cfg->url.'products.php'.(!empty($cate_id) ? '?category_id='.$cate_id : ''),
);
$lumise_pagination = new lumise_pagination();
$lumise_pagination->init($config);

$page_title = 'Products';
include(theme('header.php'));

$categories = $lumise->lib->get_categories('products');
$cat_options = array('' => '-- Categories --');
foreach($categories as $cat){
    $cat_options[$cat['id']] = $cat['name'];
}

$filters = array(
    'category_id' => array(
        'type' => 'dropdown',
        'options' => $cat_options,
        'default' => '',
        'val' => $cate_id
    ),
    'order' => array(
        'type' => 'dropdown',
        'options' => array(
            '' => '-- Sortby --',
            'name_asc' => 'Name Asc',
            'name_desc' => 'Name Desc',
            'order_asc' => 'Order Asc',
            'order_desc' => 'Order Desc',
        ),
        'default' => '',
        'val' => $dt_order
    )
);


?>
        <div class="lumise-bread">
            <div class="container">
                <h1><?php echo $lumise->lang('Lumise Shop'); ?></h1>
            </div>
        </div>
        <div class="lumise-products">
            <div class="lumise-list">
                <div class="container">
                    <form class="" action="products.php" method="get">
                    <?php
                    LumiseView::filter($data, $filters);
                    LumiseView::products($data['rows']);
                    ?>
                    </form>
                </div>
            </div>
            <div class="container">
	            <div class="lumise_pagination"><?php echo $lumise_pagination->pagination_html(); ?></div>
	        </div>
        </div>
<?php include(theme('footer.php'));?>
