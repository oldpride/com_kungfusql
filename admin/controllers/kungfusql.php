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
 * KungFuSql Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_kungfusql
 * @since       0.0.9
 */

// https://docs.joomla.org/API17:JControllerForm
class KungFuSqlControllerKungFuSql extends JControllerForm
{

        public function add() {
               //$msg = "redirecting from add";
               $msg = "";

               // this goes to admin/views/kungfusql/tmpl/edit.php
               //              admin/models/forms/kungfusql.xml
               // id=0 means add. id>0 means edit
               $this->setRedirect(JRoute::_('index.php?option=com_kungfusql&view=kungfusql&layout=edit&id=0', false), $msg);
               // then goes to save() in admin/models/kungfusql.php 
        }

        public function edit() {
                $input = JFactory::getApplication()->input;
                $id = $input->get('id', 0, 'int');
                if ($id == 0) {
                        $ids = $input->get('cid', array(), 'array');
                        $id  = $ids[0];
                }
                // $msg = "redirecting from edit";
                $msg = "";
                $this->setRedirect(JRoute::_("index.php?option=com_kungfusql&view=kungfusql&layout=edit&id=$id", false), $msg);
        }
}
