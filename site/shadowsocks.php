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
JHtml::_('behavior.tabstate');

// Set the component css/js
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_shadowsocks/assets/css/site.css');
$document->addScript('components/com_shadowsocks/assets/js/site.js');

// Require helper files
JLoader::register('ShadowsocksHelper', __DIR__ . '/helpers/shadowsocks.php'); 
JLoader::register('ShadowsocksHelperRoute', __DIR__ . '/helpers/route.php'); 

// Get an instance of the controller prefixed by Shadowsocks
$controller = JControllerLegacy::getInstance('Shadowsocks');

// Perform the request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
