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

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Shadowsocks View class for the Users
 */
class ShadowsocksViewUsers extends JViewLegacy
{
	/**
	 * Users view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			ShadowsocksHelper::addSubmenu('users');
		}

		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = JFactory::getUser();
		$this->listOrder = $this->escape($this->state->get('list.ordering'));
		$this->listDirn = $this->escape($this->state->get('list.direction'));
		$this->saveOrder = $this->listOrder == 'ordering';
		// get global action permissions
		$this->canDo = ShadowsocksHelper::getActions('user');
		$this->canEdit = $this->canDo->get('user.edit');
		$this->canState = $this->canDo->get('user.edit.state');
		$this->canCreate = $this->canDo->get('user.create');
		$this->canDelete = $this->canDo->get('user.delete');
		$this->canBatch = $this->canDo->get('core.batch');

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
			// load the batch html
			if ($this->canCreate && $this->canEdit && $this->canState)
			{
				$this->batchDisplay = JHtmlBatch_::render();
			}
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_SHADOWSOCKS_USERS'), 'users');
		JHtmlSidebar::setAction('index.php?option=com_shadowsocks&view=users');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('user.add');
		}

		// Only load if there are items
		if (ShadowsocksHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('user.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('users.publish');
				JToolBarHelper::unpublishList('users.unpublish');
				JToolBarHelper::archiveList('users.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('users.checkin');
				}
			}

			// Add a batch button
			if ($this->canBatch && $this->canCreate && $this->canEdit && $this->canState)
			{
				// Get the toolbar object instance
				$bar = JToolBar::getInstance('toolbar');
				// set the batch button name
				$title = JText::_('JTOOLBAR_BATCH');
				// Instantiate a new JLayoutFile instance and render the batch button
				$layout = new JLayoutFile('joomla.toolbar.batch');
				// add the button to the page
				$dhtml = $layout->render(array('title' => $title));
				$bar->appendButton('Custom', $dhtml, 'batch');
			} 

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				JToolbarHelper::deleteList('', 'users.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('users.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('user.export'))
			{
				JToolBarHelper::custom('users.exportData', 'download', '', 'COM_SHADOWSOCKS_EXPORT_DATA', true);
			}
		} 

		if ($this->canDo->get('core.import') && $this->canDo->get('user.import'))
		{
			JToolBarHelper::custom('users.importData', 'upload', '', 'COM_SHADOWSOCKS_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$help_url = ShadowsocksHelper::getHelpUrl('users');
		if (ShadowsocksHelper::checkString($help_url))
		{
				JToolbarHelper::help('COM_SHADOWSOCKS_HELP_MANAGER', false, $help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_shadowsocks');
		}

		if ($this->canState)
		{
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
			);
			// only load if batch allowed
			if ($this->canBatch)
			{
				JHtmlBatch_::addListSelection(
					JText::_('COM_SHADOWSOCKS_KEEP_ORIGINAL_STATE'),
					'batch[published]',
					JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text', '', true)
				);
			}
		}

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);

		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_SHADOWSOCKS_KEEP_ORIGINAL_ACCESS'),
				'batch[access]',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text')
			);
		} 

		// Set Ss User Package Ss Package Name Selection
		$this->ss_user_packageSs_package_nameOptions = JFormHelper::loadFieldType('Userpackages')->getOptions();
		if ($this->ss_user_packageSs_package_nameOptions)
		{
			// Ss User Package Ss Package Name Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_SHADOWSOCKS_USER_SS_USER_PACKAGE_LABEL').' -',
				'filter_ss_user_package',
				JHtml::_('select.options', $this->ss_user_packageSs_package_nameOptions, 'value', 'text', $this->state->get('filter.ss_user_package'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Ss User Package Ss Package Name Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_SHADOWSOCKS_USER_SS_USER_PACKAGE_LABEL').' -',
					'batch[ss_user_package]',
					JHtml::_('select.options', $this->ss_user_packageSs_package_nameOptions, 'value', 'text')
				);
			}
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		if (!isset($this->document))
		{
			$this->document = JFactory::getDocument();
		}
		$this->document->setTitle(JText::_('COM_SHADOWSOCKS_USERS'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_shadowsocks/assets/css/users.css", (ShadowsocksHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if(strlen($var) > 50)
		{
			// use the helper htmlEscape method instead and shorten the string
			return ShadowsocksHelper::htmlEscape($var, $this->_charset, true);
		}
		// use the helper htmlEscape method instead.
		return ShadowsocksHelper::htmlEscape($var, $this->_charset);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.sorting' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.assign_to' => JText::_('COM_SHADOWSOCKS_USER_ASSIGN_TO_LABEL'),
			'g.ss_package_name' => JText::_('COM_SHADOWSOCKS_USER_SS_USER_PACKAGE_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
