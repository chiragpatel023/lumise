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

class JFormFieldCampaignconnect extends JFormFieldList
{

    protected $type = 'campaignconnect';

    public function getInput()
    {
        F0FModel::addIncludePath(JPATH_SITE.'/plugins/j2store/'.$this->_element.'/'.$this->_element.'/models');
        $model = F0FModel::getTmpInstance('AppCampaignRabbits', 'J2StoreModel');
        $plugin = $model->getPlugin();
        $url = "index.php?option=com_j2store&view=app&task=view&layout=view&id=".$plugin->extension_id."&appTask=checkToken&tmpl=component";
        echo "<button id='campaign_connect_btn' class='btn btn-primary' type='button' onclick='connectCampaign()'>" . JText::_('J2STORE_CHECK') . "</button>";
        echo "<div class='campaign_error'></div>";
        ?>
        <script>
            function connectCampaign() {
                (function ($) {
                    $('.campaign_error').html('');
                    $.ajax({
                        url: '<?php echo $url;?>',
                        type: 'post',
                        cache: false,
                        data:{
                            app_id : $('#params_app_id').val(),
                            api_token: $('#params_api_token').val()
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            $('#campaign_connect_btn').after('<span class="wait"><img src="<?php echo trim(JUri::root(),'/');?>/media/j2store/images/loader.gif" alt="" /></span>');
                        },
                        complete: function() {
                            $('.wait').remove();
                        },
                        success: function (json) {
                            if (json['error']) {
                                $('.campaign_error').html('<span class="j2error">' + json['error'] + '</span>');
                            }
                            if (json['success']) {
                                $('.campaign_error').html('<span class="text-success">' + json['success'] + '</span>');
                                //window.location = json['redirect'];
                            }
                        }
                    });
                })(jQuery);
            }

        </script>

        <?php
    }
}