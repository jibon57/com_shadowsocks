<?php
/**
 * @package    Shadowsocks
 *
 * @created    14th August, 2018
 * @author     Jibon L. Costa <support@mynaparrot.com>
 * @website    https://www.mynaparrot.com
 * @copyright  Copyright (C) MynaParrot 2018. All Rights Reserved
 * @license    MIT
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
$componentParams = JComponentHelper::getParams('com_shadowsocks');
?>
<script type="text/javascript">
	// waiting spinner
	var outerDiv = jQuery('body');
	jQuery('<div id="loading"></div>')
		.css("background", "rgba(255, 255, 255, .8) url('components/com_shadowsocks/assets/images/import.gif') 50% 15% no-repeat")
		.css("top", outerDiv.position().top - jQuery(window).scrollTop())
		.css("left", outerDiv.position().left - jQuery(window).scrollLeft())
		.css("width", outerDiv.width())
		.css("height", outerDiv.height())
		.css("position", "fixed")
		.css("opacity", "0.80")
		.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
		.css("filter", "alpha(opacity = 80)")
		.css("display", "none")
		.appendTo(outerDiv);
	jQuery('#loading').show();
	// when page is ready remove and show
	jQuery(window).load(function() {
		jQuery('#shadowsocks_loader').fadeIn('fast');
		jQuery('#loading').hide();
	});
</script>
<div id="shadowsocks_loader" style="display: none;">
<form action="<?php echo JRoute::_('index.php?option=com_shadowsocks&layout=edit&id='. (int) $this->item->id . $this->referral); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

<div class="form-horizontal">

	<?php echo JHtml::_('bootstrap.startTabSet', 'userTab', array('active' => 'details')); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'userTab', 'details', JText::_('COM_SHADOWSOCKS_USER_DETAILS', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('user.details_left', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php $this->ignore_fieldsets = array('details','metadata','vdmmetadata','accesscontrol'); ?>
	<?php $this->tab_name = 'userTab'; ?>
	<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

	<?php if ($this->canDo->get('user.delete') || $this->canDo->get('user.edit.created_by') || $this->canDo->get('user.edit.state') || $this->canDo->get('user.edit.created')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'userTab', 'publishing', JText::_('COM_SHADOWSOCKS_USER_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('user.publishing', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('user.publlshing', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php if ($this->canDo->get('core.admin')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'userTab', 'permissions', JText::_('COM_SHADOWSOCKS_USER_PERMISSION', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<fieldset class="adminform">
					<div class="adminformlist">
					<?php foreach ($this->form->getFieldset('accesscontrol') as $field): ?>
						<div>
							<?php echo $field->label; echo $field->input;?>
						</div>
						<div class="clearfix"></div>
					<?php endforeach; ?>
					</div>
				</fieldset>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	<div>
		<input type="hidden" name="task" value="user.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	</div>
</div>
</form>
</div>

<script type="text/javascript">

// #jform_ss_user_enable_plugin listeners for ss_user_enable_plugin_vvvvvvv function
jQuery('#jform_ss_user_enable_plugin').on('keyup',function()
{
	var ss_user_enable_plugin_vvvvvvv = jQuery("#jform_ss_user_enable_plugin input[type='radio']:checked").val();
	vvvvvvv(ss_user_enable_plugin_vvvvvvv);

});
jQuery('#adminForm').on('change', '#jform_ss_user_enable_plugin',function (e)
{
	e.preventDefault();
	var ss_user_enable_plugin_vvvvvvv = jQuery("#jform_ss_user_enable_plugin input[type='radio']:checked").val();
	vvvvvvv(ss_user_enable_plugin_vvvvvvv);

});

</script>
