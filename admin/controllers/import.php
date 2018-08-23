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

/**
 * Shadowsocks Import Controller
 */
class ShadowsocksControllerImport extends JControllerLegacy
{
	/**
	 * Import an spreadsheet.
	 *
	 * @return  void
	 */
	public function import()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('import');
		if ($model->import())
		{
			$cache = JFactory::getCache('mod_menu');
			$cache->clean();
			// TODO: Reset the users acl here as well to kill off any missing bits
		}

		$app = JFactory::getApplication();
		$redirect_url = $app->getUserState('com_shadowsocks.redirect_url');
		if (empty($redirect_url))
		{
			$redirect_url = JRoute::_('index.php?option=com_shadowsocks&view=import', false);
		}
		else
		{
			// wipe out the user state when we're going to redirect
			$app->setUserState('com_shadowsocks.redirect_url', '');
			$app->setUserState('com_shadowsocks.message', '');
			$app->setUserState('com_shadowsocks.extension_message', '');
		}
		$this->setRedirect($redirect_url);
	}
}
