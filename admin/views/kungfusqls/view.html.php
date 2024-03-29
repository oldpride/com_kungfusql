<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_kungfusql
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * KungFuSqls View
 *
 * @since  0.0.1
 */
class KungFuSqlViewKungFuSqls extends JViewLegacy
{
	/**
	 * Display the Kung Fu Sql view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

                // Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);
	}

        /**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar()
	{
		JToolbarHelper::title(JText::_('COM_KUNGFUSQL_MANAGER_KUNGFUSQLS'));
		JToolbarHelper::addNew('kungfusql.add');
		JToolbarHelper::editList('kungfusql.edit');
		JToolbarHelper::deleteList('', 'kungfusqls.delete');
	}
}


