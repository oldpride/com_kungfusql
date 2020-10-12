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

// https://stackoverflow.com/questions/30704394/joomla-backend-component-file-upload-for-custom-component
jimport('joomla.filesystem.file');

/**
 * KungFuSql Model
 *
 * @since  0.0.1
 */
class KungFuSqlModelKungFuSql extends JModelAdmin
{
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'KungFuSql', $prefix = 'KungFuSqlTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed    A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_kungfusql.kungfusql',
			'kungfusql',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
                        print 'empty form';
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState(
			'com_kungfusql.edit.kungfusql.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

        // https://stackoverflow.com/questions/21704490/what-are-the-differences-between-joomla-model-types
        // https://docs.joomla.org/API17:JModelAdmin
        // https://forum.joomla.org/viewtopic.php?t=978528
        function save($data)
        {
                $app = JFactory::getApplication();  // redirect call need this.

                // https://stackoverflow.com/questions/139474/how-can-i-capture-the-result-of-var-dump-to-a-string
                // uncomment to turn on debug message 
                //$app->enqueueMessage("data = ". json_encode($data));
                // data = {"id":0,"filename":"example1.sql","tags":null}
                // data is a list of ids to be deleted

                $id = $data['id'];
                $filename = $data['filename'];

                $db    = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('id,filename');
                $query->from('#__kungfusql');
                $db->setQuery((string) $query);

                // https://docs.joomla.org/Selecting_data_using_JDatabase
                // turn the table into an Associated Array, indexed by 'filename' column, values from 'id'
                $id_by_filename = $db->loadAssocList('filename', 'id');
                //$app->enqueueMessage("id_by_filename = ". json_encode($id_by_filename));

                if (array_key_exists($filename, $id_by_filename)) {
                        $existing_id = $id_by_filename[$filename];
                        $msg = "filename = $filename already exists in database, id=$existing_id";
                        $app->enqueueMessage($msg, 'error');

                        // 'false' is to stop html convert & to &amp;
		        $app->redirect(JRoute::_("index.php?option=com_kungfusql&view=kungfusql&layout=edit&id=$id", false));
                }

                // https://docs.joomla.org/Constants
                $dest = JPATH_ADMINISTRATOR . "/uploads/com_kungfusql/$filename";
                // decided not to store under JPATH_COMPONENT because uninstallation may remove it
                // $dest = JPATH_COMPONENT . "/uploads/$filename";
                // eitherway, we need manually create a folder


                // uncomment to turn on debug message 
                // JFactory::getApplication()->enqueueMessage("dest = $dest");
                // dest = /home/livin80/public_html/LCA2/administrator/components/com_kungfusql/uploads/filename

                $msg = '';

                // verify that the file has the right extension. We need sql only.
                if (! ( strtolower(JFile::getExt($filename)) == 'sql') ) {
                        $msg = "filename = $filename has wrong suffix";

                        // JText::_($msg) cannot control color. the color is a css rule, depending on message type.
                        // https://forum.joomla.org/viewtopic.php?t=466842
                        // to print red color, uese 'error'
                        $app->enqueueMessage($msg, 'error');

                        // 'false' is to stop html convert & to &amp;
		        $app->redirect(JRoute::_("index.php?option=com_kungfusql&view=kungfusql&layout=edit&id=$id", false));
		        // $app->redirect(JRoute::_("index.php?option=com_kungfusql&view=kungfusql&layout=edit&id=$id", false), JText::_($msg));

                        //redirect is like return or exit call. the control flow will go out of this file.
                }

                // verify that the file exists
                if (! file_exists($dest)) {
                        // Redirect and notify user file does not have right extension.
                        // JFactory::getApplication()->enqueueMessage("file = $dest is not founnd");
                        $msg = "$dest is not founnd in filesystem. please manually upload it first.";
                        $app->enqueueMessage($msg, 'error');

		        $app->redirect(JRoute::_("index.php?option=com_kungfusql&view=kungfusql&layout=edit&id=$id", false));
                }

                if (parent::save($data)) {
                        $msg = "$filename is added into database";
                        $app->enqueueMessage($msg, 'info');
		} else {
        	        $msg = "failed to add $filename into database";
                        $app->enqueueMessage($msg, 'error');
                }

                // we need the redirect
		$app->redirect(JRoute::_('index.php?option=com_kungfusql'));
        }

        function delete($data)
        {
	        $app = JFactory::getApplication();
                // $data contains the list of ids selected to be deleted
                // uncomment to turn on debug message 
                // $app->enqueueMessage("data = ". json_encode($data));
                // data = [3,7]

                $db    = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('id,filename');
                $query->from('#__kungfusql');
                $db->setQuery((string) $query);

                // https://docs.joomla.org/Selecting_data_using_JDatabase
                // turn the table into a Associated Array - indexed by 'id' column - of Associative Arrays
                $rows = $db->loadAssocList('id');
                //$app->enqueueMessage("rows = ". json_encode($rows));

	        if (parent::delete($data)) {
	           $msg = "database deletion successful";
	           $app->enqueueMessage($msg);
	        } else {
	           $msg = "database deletion failed";
	           $app->enqueueMessage($msg, 'error');
	        }

                foreach ($data as $id) {
                        // note to use $rows["$id"] not $rows[$id], because $rows
	                $filename = $rows["$id"]['filename'];
	                $dest = JPATH_ADMINISTRATOR . "/uploads/com_kungfusql/$filename";
	
	                // uncomment to turn on debug message 
	                // $app->enqueueMessage("dest = $dest");
	
	                if ( file_exists($dest) ) {
	                            if ( ! unlink($dest) ) {
	                                $msg = "$dest has been deleted from file system.\n";
	                                $app->enqueueMessage($msg, 'info');
	                            } else {
	                                $msg = "failed to delete $dest from file system.\n";
	                                $app->enqueueMessage($msg, 'error');
	                            }
	                } else {
	                            $msg = "$dest had been deleted from file system already.\n";
	                            $app->enqueueMessage($msg, 'info');
	                }
                }

                // we need the redirect
		$app->redirect(JRoute::_('index.php?option=com_kungfusql'));
        }

        // don't override add()/edit() here. do it in controllers
}
