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
defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormRule;
use Joomla\Registry\Registry;

/**
 * Form Rule (Ipaddress) class for the Joomla Platform.
 */
class JFormRuleIpaddress extends FormRule
{
	/**
	 * The regular expression to use in testing a form field value.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $regex = '^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$';

	/**
	 * The regular expression modifiers to use when testing a form field value.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $modifiers = 'i';
}
