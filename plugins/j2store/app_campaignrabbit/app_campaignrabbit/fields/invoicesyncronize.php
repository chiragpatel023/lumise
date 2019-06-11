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
/* class JFormFieldFieldtypes extends JFormField */

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
require_once JPATH_ADMINISTRATOR.'/components/com_j2store/helpers/j2html.php';
class JFormFieldInvoicesyncronize extends JFormFieldList
{

    protected $type = 'invoicesyncronize';
    protected $_element = 'app_campaignrabbit';

    public function getInput()
    {
        F0FModel::addIncludePath(JPATH_SITE.'/plugins/j2store/'.$this->_element.'/'.$this->_element.'/models');
        $model = F0FModel::getTmpInstance('AppCampaignRabbits', 'J2StoreModel');
        $plugin = $model->getPlugin();
        $plugin_params = $model->getPluginParams();
        $invoice_url = JUri::base()."index.php?option=com_j2store&view=app&task=view&id=".$plugin->extension_id."&appTask=invoicesyn";

        $patch = $plugin_params->get('patch_count',20);
        $total = $model->getInvoiceListCount();
        $start = 0;
        echo "<a class='btn btn-success' id='invoice_patch'>".JText::_('J2STORE_CAMPAIGN_INVOICE_SYN')."</a>";
        // do script to patch process
        ?>
        <script>
            (function ($) {
                $('#invoice_patch').on('click', function () {
                    var total = '<?php echo $total;?>';
                    var patch = '<?php echo $patch;?>';
                    var start = '<?php echo $start;?>';
                    doInvoicePatchRequest(total, patch, start);
                });
            })(jQuery);
            function doInvoicePatchRequest(count,limit,st) {
                (function ($) {
                    var data = {
                        total: count,
                        limit: limit,
                        start: st
                    };
                    $('#invoice_patch').attr('disabled',true);
                    $.ajax({
                        url : '<?php echo $invoice_url;?>',
                        type : 'post',
                        cache : false,
                        data : data,
                        dataType : 'json',
                        beforeSend: function() {
                            $('#invoice_patch').after('<span class="wait"><img src="/media/j2store/images/loader.gif" alt="" /></span>');
                            $('#invoice_patch').attr('disabled',true);
                        },
                        success : function(json) {
                            if(json['success']){
                                $('.wait').remove();
                                $('#invoice_patch').attr('disabled',false);
                                window.location = json['redirect'];
                            }
                            if(json['dopatch']){
                                doInvoicePatchRequest(json['total'],limit,json['start']);
                            }
                        }

                    });
                })(jQuery);
            }

        </script>
<?php
    }
}