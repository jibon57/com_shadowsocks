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
 * Users Controller
 */
class ShadowsocksControllerUsers extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_SHADOWSOCKS_USERS';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelLegacy  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'User', $prefix = 'ShadowsocksModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('user.export', 'com_shadowsocks') && $user->authorise('core.export', 'com_shadowsocks'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			JArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Users');
			// get the data to export
			$data = $model->getExportData($pks);
			if (ShadowsocksHelper::checkArray($data))
			{
				// now set the data to the spreadsheet
				$date = JFactory::getDate();
				ShadowsocksHelper::xls($data,'Users_'.$date->format('jS_F_Y'),'Users exported ('.$date->format('jS F, Y').')','users');
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_SHADOWSOCKS_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_shadowsocks&view=users', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('user.import', 'com_shadowsocks') && $user->authorise('core.import', 'com_shadowsocks'))
		{
			// Get the import model
			$model = $this->getModel('Users');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (ShadowsocksHelper::checkObject($headers))
			{
				// Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('user_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'users');
				$session->set('dataType_VDM_IMPORTINTO', 'user');
				// Redirect to import view.
				$message = JText::_('COM_SHADOWSOCKS_IMPORT_SELECT_FILE_FOR_USERS');
				$this->setRedirect(JRoute::_('index.php?option=com_shadowsocks&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_SHADOWSOCKS_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_shadowsocks&view=users', false), $message, 'error');
		return;
	}  

	public function generateQRCode($userid){

	}
}
