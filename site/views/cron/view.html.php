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
 * Shadowsocks View class for the Cron
 */
class ShadowsocksViewCron extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{		
		// get combined params of both component and menu
		$this->app = JFactory::getApplication();
		$this->params = $this->app->getParams();
		$this->menu = $this->app->getMenu()->getActive();
		// get the user object
		$this->user = JFactory::getUser();
		// Initialise variables.
		$this->items = $this->get('Items');
				if(!class_exists('ShadowsocksConnectionClass')){
					require_once JPATH_COMPONENT_ADMINISTRATOR."/helpers/shadowsocksConnection.php";
				}

		// Set the toolbar
		$this->addToolBar();

		// set the document
		$this->_prepareDocument();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		parent::display($tpl);
	}


	public function getUsersByPort($serverid, $port){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('a.*, b.ss_package_duration, b.ss_package_period, b.ss_package_traffic')
			->from($db->quoteName('#__shadowsocks_user', 'a'))
			->join('LEFT', $db->quoteName('#__shadowsocks_package', 'b') . ' ON (' . $db->quoteName('a.ss_user_package') . ' = ' . $db->quoteName('b.id') . ')');
			//->where($db->quoteName('a.ss_user_server') . ' = '. $db->quote($serverid) /*.' AND '. $db->quoteName('a.ss_user_port') . ' = '. $db->quote($port)*/);
		$db->setQuery($query);
		$results = $db->loadObjectList();
		
		$output = array();
		if($results){
			foreach($results as $data){
				$servers = json_decode($data->ss_user_server);
				foreach($servers as $server){
					if($server == $serverid){
						array_push($output, $data);
					}
				}
			}
			return $output;
		}
		return false;

	}
	
	public function findUserPortFromAllPorts($ports, $userport){
		
		if(is_array($ports)){
			foreach($ports as $port){
				if($port->port == $userport){
					return $port->traffic;
				}
			}
		}
		return false;
	}


	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{

		// always make sure jquery is loaded.
		JHtml::_('jquery.framework');
		// Load the header checker class.
		require_once( JPATH_COMPONENT_SITE.'/helpers/headercheck.php' );
		// Initialize the header checker.
		$HeaderCheck = new shadowsocksHeaderCheck;     
		// add the document default css file
		$this->document->addStyleSheet(JURI::root(true) .'/components/com_shadowsocks/assets/css/cron.css', (ShadowsocksHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css'); 
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		// adding the joomla toolbar to the front
		JLoader::register('JToolbarHelper', JPATH_ADMINISTRATOR.'/includes/toolbar.php');
		
		// set help url for this view if found
		$help_url = ShadowsocksHelper::getHelpUrl('cron');
		if (ShadowsocksHelper::checkString($help_url))
		{
			JToolbarHelper::help('COM_SHADOWSOCKS_HELP_MANAGER', false, $help_url);
		}
		// now initiate the toolbar
		$this->toolbar = JToolbar::getInstance();
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var, $sorten = false, $length = 40)
	{
		// use the helper htmlEscape method instead.
		return ShadowsocksHelper::htmlEscape($var, $this->_charset, $sorten, $length);
	}
}
