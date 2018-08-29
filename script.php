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

JHTML::_('behavior.modal');
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');

/**
 * Script File of Shadowsocks Component
 */
class com_shadowsocksInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{

	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent)
	{
		// little notice as after service, in case of bad experience with component.
		echo '<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:support@mynaparrot.com">support@mynaparrot.com</a>.
		<br />We at MynaParrot are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="https://www.mynaparrot.com" target="_blank">https://www.mynaparrot.com</a> today!</p>';
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent)
	{
		
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// is redundant ...hmmm
		if ($type == 'uninstall')
		{
			return true;
		}
		// the default for both install and update
		$jversion = new JVersion();
		if (!$jversion->isCompatible('3.6.0'))
		{
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.6.0 before continuing!', 'error');
			return false;
		}
		// do any updates needed
		if ($type == 'update')
		{
		}
		// do any install needed
		if ($type == 'install')
		{
		}
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// set the default component settings
		if ($type == 'install')
		{
			// Install the global extenstion params.
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			// Field to update.
			$fields = array(
				$db->quoteName('params') . ' = ' . $db->quote('{"autorName":"Jibon L. Costa","autorEmail":"support@mynaparrot.com"}'),
			);
			// Condition.
			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('com_shadowsocks')
			);
			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$allDone = $db->execute();

			echo '<a target="_blank" href="https://www.mynaparrot.com" title="Shadowsocks">
				<img src="components/com_shadowsocks/assets/images/vdm-component.png"/>
				</a>';
		}
		// do any updates needed
		if ($type == 'update')
		{
			echo '<a target="_blank" href="https://www.mynaparrot.com" title="Shadowsocks">
				<img src="components/com_shadowsocks/assets/images/vdm-component.png"/>
				</a>
				<h3>Upgrade to Version 1.0.1 Was Successful! Let us know if anything is not working as expected.</h3>';
		}
	}
}
