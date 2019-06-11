<?php
/**
 * --------------------------------------------------------------------------------
 * APP - Campaign Rabbit
 * --------------------------------------------------------------------------------
 * @package     Joomla  3.x
 * @subpackage  J2 Store
 * @author      Alagesan, J2Store <support@j2store.org>
 * @copyright   Copyright (c) 2018 J2Store . All rights reserved.
 * @license     GNU/GPL license: v3 or later
 * @link        http://j2store.org
 * --------------------------------------------------------------------------------
 *
 * */
defined('_JEXEC') or die('Restricted access');
?>
<div class="queue-configuration">
    <h3><?php echo JText::_('J2STORE_CAMPAIGNRABBIT_ORDER_TITLE');?></h3>
    <br>
    <a class="btn btn-primary" id="add_to_queue_button" onclick="addToQueue()"><?php echo $vars->button_text;?></a>
</div>
<br>
<script>
    if(typeof(j2store) == 'undefined') {
        var j2store = {};
    }
    if(typeof(j2store.jQuery) == 'undefined') {
        j2store.jQuery = jQuery.noConflict();
    }

    function addToQueue(){
        (function ($) {
            $.ajax({
                url : 'index.php?option=com_j2store&view=app&task=view&id=<?php echo $vars->id;?>&appTask=add_to_queue&order_id=<?php echo $vars->order_id;?>',
                type : 'post',
                dataType : 'json',
                beforeSend: function() {
                    $('#add_to_queue_button').after('<span class="wait"><img src="/media/j2store/images/loader.gif" alt="" /></span>');
                    $('#add_to_queue_button').attr('disabled',true);
                },
                complete: function() {
                    $('.wait').remove();
                },
                success : function(json) {
                    $('#add_to_queue_button').attr('disabled',false);
                    if(json['success']){
                        window.location.reload();
                    }
                    if(json['error']){
                        // do nothing
                    }
                }

            });
        })(j2store.jQuery);
    }
</script>
