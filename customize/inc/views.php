<?php
/**
 * 
 */
class LumiseView {
    
    function __construct(){
    }
    
    public static function products($data){
        global $lumise, $lumise_helper;
    ?>
    <ul>
        <?php foreach ($data as $key => $value) {
            $thumbnail_url = $lumise->cfg->url.'assets/images/not-found.jpg';
            if(!empty($value['thumbnail_url']))
                $thumbnail_url = $value['thumbnail_url'];
            
            $has_template = $lumise_helper->has_template($value);
            ?>
            <li>
                <a href="<?php echo $lumise->cfg->url.'product.php?product_id='.$value['id']; ?>">
                    <figure><img src="<?php echo $thumbnail_url;?>" alt=""><div class="overlay"></div></figure>
                    <div class="content">
                        <?php if(isset($value['name'])) echo '<h3>'.$value['name'].'</h3>';?>
                        <?php if(isset($value['name'])) echo '<span class="lumise-price">'.$lumise->lib->price($value['price']).'</span>';?>
                    </div>
                </a>
                <div class="lumise-action">
                    <?php if($has_template):?>
                        <a href="<?php echo $lumise->cfg->url.'add-cart.php?product='.$value['id']; ?>" class="lumise-add">
	                        <?php echo $lumise->lang('Add to cart'); ?>
	                    </a>
                    <?php endif;?>
                    <a href="<?php echo $lumise->cfg->tool_url.'?product='.$value['id']; ?>" class="lumise-custom">
	                    <?php echo $lumise->lang('Customize'); ?>
	                </a>
                </div>
            </li>
        <?php } ?>
    </ul>
    <?php
    }

    public static function templates($data){
        global $lumise;
    ?>
    <ul>
        <?php foreach ($data as $key => $value) {
            $thumbnail_url = $lumise->cfg->url.'assets/images/not-found.jpg';
            if(!empty($value['screenshot']))
                $thumbnail_url = $value['screenshot'];
            ?>
            <li>
                <figure><img src="<?php echo $thumbnail_url;?>" alt=""><div class="overlay"></div></figure>
                <div class="content">
                    <?php if(isset($value['name'])) echo '<h3>'.$value['name'].'</h3>';?>
                    <?php if(isset($value['name'])) echo '<span class="lumise-price">'.$lumise->lib->price($value['price']).'</span>';?>
                </div>
                <?php /*
                <a href="<?php echo $lumise->cfg->editor_url.'?product_id='.$value['id']; ?>">
                    <figure><img src="<?php echo $thumbnail_url;?>" alt=""><div class="overlay"></div></figure>
                    <div class="content">
                        <?php if(isset($value['name'])) echo '<h3>'.$value['name'].'</h3>';?>
                        <?php if(isset($value['name'])) echo '<span class="lumise-price">'.$lumise->lib->price($value['price']).'</span>';?>
                    </div>
                </a>
                <div class="lumise-action">
                    <a href="<?php echo $lumise->cfg->tool_url.'?product='.$value['id']; ?>" class="lumise-custom"> 
	                    <?php echo $lumise->lang('Customize 2'); ?>
	                </a>
                </div>
                */?>
            </li>
        <?php } ?>
    </ul>
    <?php
    }
    
    public static function categories($limit = 3){
        global $lumise;
        $categories = $lumise->lib->get_categories('products', 0, '`order` DESC');
        $count = 0;
        if(count($categories)>0):
        ?>
        <div class="lumise-categories">
            <div class="container">
                <h2><?php echo $lumise->lang('Categories'); ?></h2>
                <div class="row">
            <?php
                foreach ($categories as $data) {
                    
                    $thumbnail_url = $lumise->cfg->url . 'assets/images/not-found.jpg';
                    if(!empty($data['thumbnail']))
                        $thumbnail_url = $data['thumbnail'];
                    ?>
                    <div class="col-md-4 col-sm-4">
                        <a href="<?php echo $lumise->cfg->url.'products.php?category_id='.$data['id']; ?>" class="lumise-banner">
                            <div class="text-content">
                                <h3><?php if(isset($data['name'])) echo $data['name']?></h3>
                            </div>
                            <img src="<?php echo $thumbnail_url;?>" alt="">
                            <div class="overlay"></div>
                        </a>
                    </div>
                <?php
                
                    $count++;
                    
                    if($limit == $count) break;
                }
            ?>
                </div>
            </div>
        </div>
        <?php
        endif;
    }
    
    public function message(){
        
        $lumise_msg = $this->get_session('lumise_msg');

		if (isset($lumise_msg['status']) && $lumise_msg['status'] == 'error') { ?>

			<div class="tsd_message err">
				<?php foreach ($lumise_msg['errors'] as $val) {
					echo '<em class="tsd_err"><i class="fa fa-times"></i>' . $val . '.</em>';
				} ?>
			</div>

		<?php }

		if (isset($lumise_msg['status']) && $lumise_msg['status'] == 'success') { ?>

			<div class="tsd_message">
				<?php
					echo '<em class="tsd_suc"><i class="fa fa-check"></i>'.(isset($lumise_msg['msg'])? $lumise_msg['msg'] : $lumise->lang('Your data has been successfully saved') ).'</em>';
					
				?>
			</div>

		<?php }
		
		$lumise_msg = array('status' => '');
		$this->set_session('lumise_msg', $lumise_msg);
        
    }
    public static function filter($data, $filters){
        
        
    ?>
    <div class="lumise-filter">
        <?php 
        foreach ($filters as $name => $cfg) {
            ?>
            <div class="lumise-filter-<?php echo $name;?>">
                <?php 
                switch ($cfg['type']) {
                    case 'dropdown':
                        ?>
                        <select name="<?php echo $name;?>" class="lumise-filter-dropdown">
                            <?php foreach($cfg['options'] as $key => $val):?>
                                <option value="<?php echo $key;?>"<?php echo (isset($cfg['val']) && $cfg['val'] == $key)? ' selected="selected"':''?>><?php echo $val;?></option>
                            <?php endforeach;?>
                        </select>
                        <?php
                        break;
                    
                    default:
                        # code...
                        break;
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
    }
}
