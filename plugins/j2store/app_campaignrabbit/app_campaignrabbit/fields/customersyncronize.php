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
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework');
require_once JPATH_ADMINISTRATOR.'/components/com_j2store/helpers/j2html.php';

class JFormFieldCustomersyncronize extends JFormFieldList
{

    protected $type = 'customersyncronize';
    protected $_element = 'app_campaignrabbit';

    public function getInput()
    {
        F0FModel::addIncludePath(JPATH_SITE.'/plugins/j2store/'.$this->_element.'/'.$this->_element.'/models');
        $model = F0FModel::getTmpInstance('AppCampaignRabbits', 'J2StoreModel');
        $plugin = $model->getPlugin();
        $plugin_params = $model->getPluginParams();
        $customer_url =  JUri::base()."index.php?option=com_j2store&view=apps&task=view&id=".$plugin->extension_id."&appTask=usersyn";

        $patch = $plugin_params->get('patch_count',20);
        $total = $model->getCustomerListCount();
        $start = 0;
        $done = 0;
?>
        <?php
        echo "<a class='btn btn-success' id='customer_patch'>".JText::_('J2STORE_CAMPAIGN_CUSTOMER_SYN')."</a>";
        ?>
        <script>
            (function ($) {
                $('#customer_patch').on('click', function () {
                    var total = '<?php echo $total;?>';
                    var patch = '<?php echo $patch;?>';
                    var start = '<?php echo $start;?>';
                    var done = '<?php echo $done;?>';
                    doCustomPatchRequest(total, patch, start, done);
                });
            })(jQuery);
                function doCustomPatchRequest(count,limit,st,done) {
                    (function ($) {
                        var data = {
                            total: count,
                            limit: limit,
                            start: st,
                            done: done
                        };
                        $.ajax({
                            url : '<?php echo $customer_url;?>',
                            type : 'post',
                            cache : false,
                            data : data,
                            dataType : 'json',
                            beforeSend: function() {
                                $('#customer_patch').after('<span class="wait"><img src="/media/j2store/images/loader.gif" alt="" /></span>');
                                $('#customer_patch').attr('disabled',true);
                            },
                            success : function(json) {
                                console.log(json);
                                if(json['success']){
                                    $('.wait').remove();
                                    $('#customer_patch').attr('disabled',false);
                                    window.location = json['redirect'];
                                }

                                if(json['dopatch']){
                                    doCustomPatchRequest(json['total'],limit,json['start'],json['done']);
                                }
                            }

                        });
                    })(jQuery);
                }

        </script>
        <?php

    }
}