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
JHtml::_('behavior.framework');
JHtml::_('behavior.modal');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

JHtml::_('script', 'media/j2store/js/j2store.js', false, false);
?>
<?php if($vars->status): ?>
<style>
  .j2campaignrabbit-app{
    margin-top: 30px;
  }
  .j2campaignrabbit-app #configurationTabs{
    margin-bottom: 30px;
  }
  .j2campaignrabbit-app #configurationTabs li a{
    padding: 20px 30px;
    color: #999999;
    font-size: 15px;
    text-transform: capitalize;
    font-weight: 400;
    line-height: 1.6;
    -webkit-transition: color 0.2s;
    transition: color 0.2s;
    margin: 0;
    border-radius: 0;
  }
  .j2campaignrabbit-app #configurationTabs li.active a{
    box-shadow: inset 0 3px 0 #6772e5;
    color: #6772e5;
  }
  .j2campaignrabbit-app #configurationTabs li a:hover, .j2campaignrabbit-app #configurationTabs li a:focus{
    color: #6772e5;
  }
  .j2campaignrabbit-app #configurationContent .tab-pane .control-group .control-label label{
    text-transform: capitalize;
    font-weight: 500;
    margin-bottom: 10px;
  }
  .j2campaignrabbit-app #configurationContent .tab-pane .control-group .controls{
    margin-bottom: 10px;
  }
  .j2campaignrabbit-app #configurationContent .tab-pane .control-group .controls .muted {
    color: #999;
    font-size: 12px;
    margin-top: 10px;
    display: block;
  }
  .j2campaignrabbit-app #configurationContent .tab-pane .control-group .controls input[type="text"]{
    height: 32px;
    width: 100%;
    max-width: 500px;
    font-size: 14px;
    font-weight: 500;
    text-indent: 10px;
  }
  .j2campaignrabbit-app #configurationContent .tab-pane .control-group .controls .radio{
    position: relative;
  }
  @media (max-width: 40em) {
    .j2campaignrabbit-app #configurationContent .tab-pane .control-group .controls .radio{
      margin-top: -1.5em;
    }
  }
  .j2campaignrabbit-app #configurationContent .tab-pane .control-group .controls .radio label{
    display: inline-block;
    padding: 12px 20px;
    line-height: 1.6;
    cursor: pointer;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2), inset 0 -3px 0 rgba(0, 0, 0, 0.22);
    transition: 0.3s;
  }
  .j2campaignrabbit-app #configurationContent .tab-pane .control-group .controls .radio{
    padding-left: 0;
  }
  .j2campaignrabbit-app #configurationContent .tab-pane .control-group .controls .radio input[type="radio"]{
    float: none;
    margin: auto;
    position: absolute;
    opacity: 0;
  }
  .j2campaignrabbit-app .btn{
    padding: 12px 20px 14px;
    line-height: 1.6;
    font-weight: 500;
    text-transform: capitalize;
    border: none;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2), inset 0 -3px 0 rgba(0, 0, 0, 0.22);
    transition: 0.3s;
  }
  .j2campaignrabbit-app .btn-lg{
    padding: 12px 40px 14px;
    line-height: 2;
    font-size: 16px;
  }
  .j2campaignrabbit-app .btn:hover{
    opacity: 0.85;
  }
  .j2campaignrabbit-app .controls .btn-group > .btn {
    margin-left: 0;
  }
  .j2campaignrabbit-app .btn-primary{
    background: #6772e5;
    border-color: #6772e5;
  }
  .j2campaignrabbit-app .btn-success{
    background: #2ECC71;
    border-color: #2ECC71;
  }
  .j2campaignrabbit-app .btn-info{
    background: #4183D7;
    border-color: #4183D7;
  }
  .j2campaignrabbit-app .btn-danger{
    background: #D91E18;
    border-color: #D91E18;
  }
  .j2campaignrabbit-app .alert-success{
    background: #F4FAEE;
    border-color: #F4FAEE;
    padding: 15px 20px;
    color: #2ECC71;
    display: inline-block;
    font-size: 14px;
    font-weight: 600;
  }
  .j2campaignrabbit-app .chzn-container-multi .chzn-choices li.search-choice{
    background: #4183D7;
    padding: 3px 10px 4px;
  }
  .j2campaignrabbit-app .border-left{
    border-left: 1px solid #eee;
  }
  @media(max-width: 768px){
    .j2campaignrabbit-app .border-left{
      border-left: none;
      border-top: 1px solid #eee;
    }
  }
  .j2campaignrabbit-app .j2campaignrabbit-create-account {
      padding: 30px 40px;
      max-width: 500px;
      margin: 20px auto;
      background: #EFF4FB;
      border-radius: 4px;
  }

  .j2campaignrabbit-app .j2campaignrabbit-create-account h3{
    font-size: 26px;
    line-height: 1.33;
  }
  .j2campaignrabbit-app .j2campaignrabbit-create-account h4{
    font-size: 19px;
    line-height: 1.33;
    margin-bottom: 20px;
    color: #555;
  }
  .j2campaignrabbit-app .j2campaignrabbit-create-account p{
    font-size: 17px;
    margin-bottom: 20px;
    line-height: 1.6;
    font-weight: 400;
    color: #777;
  }
</style>

<script type="text/javascript">
    Joomla.submitbutton = function(pressbutton) {
        if(pressbutton == 'save' || pressbutton == 'apply') {
            document.adminForm.task ='view';
            document.getElementById('appTask').value = pressbutton;
        }

        if(pressbutton == 'cancel') {
            Joomla.submitform('cancel');
        }

        var atask = jQuery('#appTask').val();

        Joomla.submitform('view');
    }
</script>

<div class="j2store-configuration j2campaignrabbit-app">
    <form action="<?php echo $vars->action; ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal form-validate">
        <?php echo J2Html::hidden('option','com_j2store');?>
        <?php echo J2Html::hidden('view','apps');?>
        <?php echo J2Html::hidden('app_id',$vars->id);?>
        <?php echo J2Html::hidden('appTask', '', array('id'=>'appTask'));?>
        <?php echo J2Html::hidden('task', 'view', array('id'=>'task'));?>

        <?php echo JHtml::_('form.token'); ?>
        <?php
        $fieldsets = $vars->form->getFieldsets();
        $shortcode = $vars->form->getValue('text');
        $tab_count = 0;

        foreach ($fieldsets as $key => $attr)
        {

            if ( $tab_count == 0 )
            {
                echo JHtml::_('bootstrap.startTabSet', 'configuration', array('active' => $attr->name));
            }
            echo JHtml::_('bootstrap.addTab', 'configuration', $attr->name, JText::_($attr->label, true));
            ?>
            <?php  if(J2Store::isPro() != 1 && isset($attr->ispro) && $attr->ispro ==1 ) : ?>
            <?php echo J2Html::pro(); ?>
        <?php else: ?>

            <div class="row-fluid">
                <div class="span6">
                    <?php
                    $layout = '';
                    $style = '';
                    $fields = $vars->form->getFieldset($attr->name);
                    foreach ($fields as $key => $field)
                    {
                        $pro = $field->getAttribute('pro');
                        ?>
                        <div class="control-group <?php echo $layout; ?>" <?php echo $style; ?>>
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <?php if(J2Store::isPro() != 1 && $pro ==1 ): ?>
                                <?php echo J2Html::pro(); ?>
                            <?php else: ?>
                            <div class="controls">
                                <?php echo $field->input; ?>
                                <br />
                                <small class="muted"><?php echo JText::_($field->description); ?></small>
                                <?php endif; ?>

                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="span6 border-left">
                  <div class="j2campaignrabbit-create-account">
                    <h3>Don't have an account?</h3>
                    <h4>
                      Sell more by sending targetted, data-driven emails to your customers
                    </h4>
                    <p>
                      Say goodbye to generic, boring newsletters. Use the data to create personalized, effective ecommerce marketing campaigns and drive your sales
                    </p>
                    <p>
                      <a href="https://app.campaignrabbit.com/register?utm_campaign=integration&utm_source=j2store&utm_content=register&utm_medium=web" target="_blank" class="btn btn-primary btn-lg">Get Stated for free</a>
                    </p>
                  </div>
                </div>

            </div>
        <?php endif; ?>
            <?php
            echo JHtml::_('bootstrap.endTab');
            $tab_count++;

        }
        ?>
    </form>
</div>
<?php else: ?>
    <div class="alert alert-danger">
        <div>Message:</div>
        <div class="alert-message">
            <?php echo JText::_('J2STORE_PHP_VERSION_NOT_SUPPORT');?>
        </div>
    </div>
<?php endif; ?>