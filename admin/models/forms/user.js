/**
 * @package    Shadowsocks
 *
 * @created    14th August, 2018
 * @author     Jibon L. Costa <support@mynaparrot.com>
 * @website    https://www.mynaparrot.com
 * @copyright  Copyright (C) MynaParrot 2018. All Rights Reserved
 * @license    MIT
 */

// Some Global Values
jform_vvvvvvvvvv_required = false;

// Initial Script
jQuery(document).ready(function()
{
	var ss_user_enable_plugin_vvvvvvv = jQuery("#jform_ss_user_enable_plugin input[type='radio']:checked").val();
	vvvvvvv(ss_user_enable_plugin_vvvvvvv);
});

// the vvvvvvv function
function vvvvvvv(ss_user_enable_plugin_vvvvvvv)
{
	// set the function logic
	if (ss_user_enable_plugin_vvvvvvv == 1)
	{
		jQuery('#jform_ss_user_plugin_options').closest('.control-group').show();
		if (jform_vvvvvvvvvv_required)
		{
			updateFieldRequired('ss_user_plugin_options',0);
			jQuery('#jform_ss_user_plugin_options').prop('required','required');
			jQuery('#jform_ss_user_plugin_options').attr('aria-required',true);
			jQuery('#jform_ss_user_plugin_options').addClass('required');
			jform_vvvvvvvvvv_required = false;
		}

	}
	else
	{
		jQuery('#jform_ss_user_plugin_options').closest('.control-group').hide();
		if (!jform_vvvvvvvvvv_required)
		{
			updateFieldRequired('ss_user_plugin_options',1);
			jQuery('#jform_ss_user_plugin_options').removeAttr('required');
			jQuery('#jform_ss_user_plugin_options').removeAttr('aria-required');
			jQuery('#jform_ss_user_plugin_options').removeClass('required');
			jform_vvvvvvvvvv_required = true;
		}
	}
}

// update required fields
function updateFieldRequired(name,status)
{
	var not_required = jQuery('#jform_not_required').val();

	if(status == 1)
	{
		if (isSet(not_required) && not_required != 0)
		{
			not_required = not_required+','+name;
		}
		else
		{
			not_required = ','+name;
		}
	}
	else
	{
		if (isSet(not_required) && not_required != 0)
		{
			not_required = not_required.replace(','+name,'');
		}
	}

	jQuery('#jform_not_required').val(not_required);
}

// the isSet function
function isSet(val)
{
	if ((val != undefined) && (val != null) && 0 !== val.length){
		return true;
	}
	return false;
} 
