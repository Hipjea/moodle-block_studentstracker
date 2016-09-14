<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Classes to enforce the various access rules that can apply to a quiz.
 *
 * @package    block_studentstracker
 * @copyright  2015 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/blocks/studentstracker/locallib.php");

class block_studentstracker_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $CFG;

        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('blocktitle', $this->block->blockname));
        $mform->setDefault('config_title', 'Students tracker');
        $mform->setType('config_title', PARAM_TEXT);

        $mform->addElement('text', 'config_days', get_string('block_studentstracker_days', $this->block->blockname));
        $mform->setDefault('config_days', 3);
        $mform->setType('config_days', PARAM_INT);

        $mform->addElement('text', 'config_days_critical',
         get_string('block_studentstracker_days_critical', $this->block->blockname));
        $mform->setDefault('config_days_critical', 6);
        $mform->setType('config_days_critical', PARAM_INT);

        $mform->addElement('text', 'config_color_days', get_string('block_studentstracker_color_days', $this->block->blockname));
        $mform->setDefault('config_color_days', '#FFD9BA');
        $mform->setType('config_color_days', PARAM_RAW);

        $mform->addElement('text', 'config_color_days_critical',
         get_string('block_studentstracker_color_days_critical', $this->block->blockname));
        $mform->setDefault('config_color_days_critical', '#FECFCF');
        $mform->setType('config_color_days_critical', PARAM_RAW);

        $mform->addElement('text', 'config_color_never', get_string('block_studentstracker_color_never', $this->block->blockname));
        $mform->setDefault('config_color_never', '#D0D0D0');
        $mform->setType('config_color_never', PARAM_RAW);

        $mform->addElement('text', 'config_text_header', get_string('block_studentstracker_text_header', $this->block->blockname));
        $mform->setDefault('config_text_header', 'users absent');
        $mform->setType('config_text_header', PARAM_TEXT);

        $mform->addElement('text', 'config_text_header_fine',
         get_string('block_studentstracker_text_header_fine', $this->block->blockname));
        $mform->setDefault('config_text_header_fine', 'Everything is fine!');
        $mform->setType('config_text_header_fine', PARAM_TEXT);

        $mform->addElement('text', 'config_text_never', get_string('block_studentstracker_text_never', $this->block->blockname));
        $mform->setDefault('config_text_never', 'no access');
        $mform->setType('config_text_never', PARAM_TEXT);

        $mform->addElement('text', 'config_text_footer', get_string('block_studentstracker_text_footer', $this->block->blockname));
        $mform->setDefault('config_text_footer', 'Contact them !');
        $mform->setType('config_text_footer', PARAM_TEXT);

        $values = array(get_string('no'), get_string('yes'));
        $mform->addElement('select', 'config_role', get_string('block_studentstracker_role', $this->block->blockname), $values);

        $roles = studentstracker::get_roles();
        $roles_array = array();
        foreach($roles as $role) {
            $roles_array[$role->id] = $role->shortname;
        }
        $select = $mform->addElement('select', 'config_roles', get_string('block_studentstracker_roles'), $roles_array, null);
        $select->setMultiple(true);
    }
}
