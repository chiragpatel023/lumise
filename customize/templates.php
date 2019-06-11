<?php
require('autoload.php');
global $lumise;

$dt_order = isset($_REQUEST['order']) && !empty($_REQUEST['order']) ? $_REQUEST['order'] : 'name_asc';
$cate_id = isset($_REQUEST['category']) && !empty($_REQUEST['category']) ? $_REQUEST['category'] : 0;

$order_cfg = explode('_', $dt_order);

$orderby = "`{$order_cfg[0]}`";
$ordering = "{$order_cfg[1]}";

$current_page = isset($_GET['tpage']) ? $_GET['tpage'] : 1;

$search_filter = array(
    'keyword' => ''
,    'fields' => 'name'
);

$per_page = 16;
$start = ( $current_page - 1 ) * $per_page;

if (isset($cate_id) && $cate_id > 0)
    $data = $lumise->lib->get_xitems_by_category($cate_id, $search_filter, $orderby, $ordering, $per_page, $start, 'templates');
else
    $data = $lumise->lib->get_rows('templates', $search_filter, $orderby, $ordering, $per_page, $start, null, '');

$data['total_page'] = ceil($data['total_count'] / $per_page);
$data['per_page'] = $per_page;
$config = array(
    'current_page'  => $current_page,
    'total_record'  => $data['total_count'],
    'total_page'    => $data['total_page'],
    'limit'         => $per_page,
    'link_full'     => $lumise->cfg->url.'templates.php?tpage={page}'.(!empty($cate_id) ? '&category='.$cate_id : '').(!empty($dt_order) ? '&order='.$dt_order : ''),
    'link_first'    => $lumise->cfg->url.'templates.php'.(!empty($cate_id) ? '?category='.$cate_id : '').(!empty($dt_order) ? '&order='.$dt_order : ''),
);
$lumise_pagination = new lumise_pagination();
$lumise_pagination->init($config);

$page_title = 'Templates';

$categories = $lumise->lib->get_categories('templates');
$cat_options = array('' => '-- Categories --');
foreach($categories as $cat){
    $cat_options[$cat['id']] = $cat['name'];
}

$filters = array(
    'category' => array(
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

include(theme('header.php'));

?>
        <div class="lumise-bread">
            <div class="container">
                <h1><?php echo $lumise->lang('Lumise Shop'); ?></h1>
            </div>
        </div>
        <div class="lumise-products">
            <div class="lumise-list lumise-templates">
                <div class="container">
                    <form class="" action="templates.php" method="get">
                        
                        <?php
                        LumiseView::filter($data, $filters);
                        LumiseView::templates($data['rows']);
                        ?>
                    </form>
                    
                </div>
            </div>
            <div class="container">
	            <div class="lumise_pagination">
		            <?php echo $lumise_pagination->pagination_html(); ?>
		        </div>
		    </div>
        </div>
<?php include(theme('footer.php'));?>
