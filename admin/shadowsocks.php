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

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_shadowsocks'))
{
	return JError::raiseWaring(404, JText::_('JERROR_ALERTNOAUTHOR'));
};

// Load cms libraries
JLoader::registerPrefix('J', JPATH_PLATFORM . '/cms');
// Load joomla libraries without overwrite
JLoader::registerPrefix('J', JPATH_PLATFORM . '/joomla',false);

// Add CSS file for all pages
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_shadowsocks/assets/css/admin.css');
$document->addScript('components/com_shadowsocks/assets/js/admin.js');

// require helper files
JLoader::register('ShadowsocksHelper', dirname(__FILE__) . '/helpers/shadowsocks.php'); 
JLoader::register('JHtmlBatch_', dirname(__FILE__) . '/helpers/html/batch_.php'); 

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by Shadowsocks
$controller = JControllerLegacy::getInstance('Shadowsocks');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
