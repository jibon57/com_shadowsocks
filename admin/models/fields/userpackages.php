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

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Userpackages Form Field class for the Shadowsocks component
 */
class JFormFieldUserpackages extends JFormFieldList
{
	/**
	 * The userpackages field type.
	 *
	 * @var		string
	 */
	public $type = 'userpackages';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	public function getOptions()
	{
		$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select($db->quoteName(array('a.id','a.ss_package_name'),array('id','ss_user_package_ss_package_name')));
$query->from($db->quoteName('#__shadowsocks_package', 'a'));
$query->where($db->quoteName('a.published') . ' = 1');
$query->order('a.ss_package_name ASC');
$db->setQuery((string)$query);
$items = $db->loadObjectList();
$options = array();
if ($items)
{
	$options[] = JHtml::_('select.option', '', 'Select an option');
	foreach($items as $item)
	{
		$options[] = JHtml::_('select.option', $item->id, $item->ss_user_package_ss_package_name);
	}
}

return $options;
	}
}
