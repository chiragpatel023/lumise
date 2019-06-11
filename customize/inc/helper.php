<?php
class lumise_helper{
    
    function __construct(){
        
    }
    
    public function process_cart(){
        
        global $lumise;
        
        if(isset($_REQUEST['action'])){
            switch ($_REQUEST['action']) {
                case 'remove':
                
                    $lumise->lib->remove_cart_item($_REQUEST['item']);
                    $this->sys_msg('Product item was removed.', true);
                    
                    $this->redirect($_SERVER['HTTP_REFERER']);
                    
                    break;
                
                default:
                    # code...
                break;
            }
        }
    }
    
    public function has_template($product_data){
        global $lumise;
        
        $has_template = false;
        $stages = isset($product_data['stages'])? $lumise->lib->dejson($product_data['stages']) : array();
        
        foreach ($stages as $stage => $data) {

            if(
                isset($data->template) &&
                isset($data->template->id) && 
                $data->template->id > 0
            ){
                $has_template = true;
                break;
            }
        }
        return $has_template;
    }
    
    public function qty_extract($options){
        foreach($options as $option)
            if(strpos($option, '-') === true){
                $tmp = explode('-', $option);
                $option = $tmp[0] . ' ('.$tmp[1].')';
            }
        return $options;
    }
    
    public function redirect($url){
        
        if (empty($url))
            return;

        @header("location: " . $url);
        exit();
    }
    
    public function sys_msg($data = '', $clear = false){
        
        if(empty($data) && isset($_SESSION['lumise_sys_msg'])){
            $msg =  implode('<br/>', $_SESSION['lumise_sys_msg']);
            $_SESSION['lumise_sys_msg'] = array();
            
            return $msg;
        }
        if($clear) $_SESSION['lumise_sys_msg'] = array();
        
        $_SESSION['lumise_sys_msg'][] = $data;

    }

    public function show_sys_message(){
        if(isset($_SESSION['lumise_sys_msg']) && count($_SESSION['lumise_sys_msg'])){
            ?>
           <div class="lumise-sys-message lumise_message">
            <?php
            foreach($_SESSION['lumise_sys_msg'] as $msg) echo $msg;
            ?>
            </div>
            <?php
            $_SESSION['lumise_sys_msg'] = array();
        }
    }
    
    public function process_payment($order_data){
        global $lumise;
        
        if($order_data['payment'] == 'paypal'){
            
            if($lumise->cfg->settings['sanbox_mode']){
                $paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
            } else $paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
            
            ?>
            <center>
            <p><?php echo $lumise->lang('Your order placed, now you will be redirect to paypal in 5 seconds to progress payment.'); ?></p>
            <form action="<?php echo $paypal_url; ?>" method="post" id="cartCheckout">
                <input type="hidden" name="business" value="<?php echo $lumise->cfg->settings['merchant_id']; ?>">
                
                <input type="hidden" name="cmd" value="_xclick">
                
                <input type="hidden" name="item_name" value="Order #<?php echo $order_data['id']; ?>">
                <input type="hidden" name="item_number" value="<?php echo $order_data['id']; ?>">
                <input type="hidden" name="amount" value="<?php echo $order_data['total']; ?>">
                <input type="hidden" name="currency_code" value="<?php echo $lumise->cfg->settings['currency_code'];?>">
                <input type='hidden' name='cancel_return' value='<?php echo $lumise->cfg->url . PAYPAL_CANCEL;?>'>
                <input type='hidden' name='return' value='<?php echo $lumise->cfg->url . PAYPAL_RETURN;?>'>
                <input type='hidden' name='notify_url' value='<?php echo $lumise->cfg->url . 'paypal_ipn.php';?>'>
                <input type="hidden" name="rm" value="2" />
                <input type="image" name="submit" border="0"
                src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif" alt="PayPal - The safer, easier way to pay online">
                <img alt="" border="0" width="1" height="1" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" >
            </form>
            </center>
            <script>
            window.onload= function(){ 
                setTimeout(function (){
                    document.getElementById('cartCheckout').submit();
                }, 5000);
            };
            </script>
            <?php
            die();
        }
    }
}

global $lumise_helper;
$lumise_helper = new lumise_helper();
