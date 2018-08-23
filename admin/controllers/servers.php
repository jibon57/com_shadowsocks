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

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * Servers Controller
 */
class ShadowsocksControllerServers extends JControllerAdmin
{
	protected $text_prefix = 'COM_SHADOWSOCKS_SERVERS';
	/**
	 * Proxy for getModel.
	 * @since	2.5
	 */
	public function getModel($name = 'Server', $prefix = 'ShadowsocksModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		
		return $model;
	}

	public function exportData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('server.export', 'com_shadowsocks') && $user->authorise('core.export', 'com_shadowsocks'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			JArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Servers');
			// get the data to export
			$data = $model->getExportData($pks);
			if (ShadowsocksHelper::checkArray($data))
			{
				// now set the data to the spreadsheet
				$date = JFactory::getDate();
				ShadowsocksHelper::xls($data,'Servers_'.$date->format('jS_F_Y'),'Servers exported ('.$date->format('jS F, Y').')','servers');
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_SHADOWSOCKS_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_shadowsocks&view=servers', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('server.import', 'com_shadowsocks') && $user->authorise('core.import', 'com_shadowsocks'))
		{
			// Get the import model
			$model = $this->getModel('Servers');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (ShadowsocksHelper::checkObject($headers))
			{
				// Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('server_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'servers');
				$session->set('dataType_VDM_IMPORTINTO', 'server');
				// Redirect to import view.
				$message = JText::_('COM_SHADOWSOCKS_IMPORT_SELECT_FILE_FOR_SERVERS');
				$this->setRedirect(JRoute::_('index.php?option=com_shadowsocks&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_SHADOWSOCKS_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_shadowsocks&view=servers', false), $message, 'error');
		return;
	}  
}
