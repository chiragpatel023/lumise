<?php 
global $lumise;
include(theme('head.php'));
?>
<header class="header">
    <div class="container">
        <a href="<?php echo $lumise->cfg->url; ?>" class="logo">
            <img src="https://www.lumise.com/assets/images/logo_new.png" alt="Lumise Product Designer online tool">
        </a>
        <div class="menu">
            <a href="#" class="btn_menu"><i class="fa fa-bars" aria-hidden="true"></i></a>
            <ul class="main-menu">
                <li><a href="<?php echo $lumise->cfg->url; ?>"><?php echo $lumise->lang('Home'); ?></a></li>
                <li><a href="<?php echo $lumise->cfg->url.'products.php'; ?>"><?php echo $lumise->lang('Products'); ?></a></li>
                <li><a href="<?php echo $lumise->cfg->url.'cart.php'; ?>"><?php echo $lumise->lang('Cart'); ?></a></li>
                <li><a href="<?php echo $lumise->cfg->url.'checkout.php'; ?>"><?php echo $lumise->lang('Checkout'); ?></a></li>
            </ul>
        </div>
        <div class="overlay_menu"></div>
        <div class="menu_mobile">
            <a href="<?php echo $lumise->cfg->url; ?>" class="logo_mobile"><img src="https://www.lumise.com/assets/images/logo_new.png" alt=""></a>
            <a href="#" class="close_menu"><svg enable-background="new 0 0 32 32" height="32px" id="Слой_1" version="1.1" viewBox="0 0 32 32" width="32px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M17.459,16.014l8.239-8.194c0.395-0.391,0.395-1.024,0-1.414c-0.394-0.391-1.034-0.391-1.428,0  l-8.232,8.187L7.73,6.284c-0.394-0.395-1.034-0.395-1.428,0c-0.394,0.396-0.394,1.037,0,1.432l8.302,8.303l-8.332,8.286  c-0.394,0.391-0.394,1.024,0,1.414c0.394,0.391,1.034,0.391,1.428,0l8.325-8.279l8.275,8.276c0.394,0.395,1.034,0.395,1.428,0  c0.394-0.396,0.394-1.037,0-1.432L17.459,16.014z" fill="#777" id="Close"></path><g></g><g></g><g></g><g></g><g></g><g></g></svg></a>
            <ul class="main-menu">
                <li><a href="<?php echo $lumise->cfg->url; ?>"><?php echo $lumise->lang('Home'); ?></a></li>
                <li><a href="<?php echo $lumise->cfg->url.'products.php'; ?>"><?php echo $lumise->lang('Shop'); ?></a></li>
                <li><a href="<?php echo $lumise->cfg->url.'cart.php'; ?>"><?php echo $lumise->lang('Cart'); ?></a></li>
                <li><a href="<?php echo $lumise->cfg->url.'checkout.php'; ?>"><?php echo $lumise->lang('Checkout'); ?></a></li>
            </ul>
        </div>
    </div>
</header>
