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

defined('MOODLE_INTERNAL') || die();

/**
 * Classes to enforce the various access rules that can apply to a quiz.
 *
 * @package    block_studentstracker
 * @copyright  2021 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/blocks/studentstracker/locallib.php");

class block_studentstracker_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $CFG, $COURSE, $USER;
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));
        $mform->addElement('text', 'config_title', get_string('blocktitle', 'block_studentstracker'));
        $mform->setDefault('config_title', get_string('pluginname', 'block_studentstracker'));
        $mform->setType('config_title', PARAM_TEXT);

        $mform->addElement('text', 'config_color_days', get_string('color_days', 'block_studentstracker'));
        $mform->setDefault('config_color_days', get_config('studentstracker', 'colordays'));
        $mform->setType('config_color_days', PARAM_RAW);

        $mform->addElement('text', 'config_color_days_critical',
         get_string('color_days_critical', 'block_studentstracker'));
        $mform->setDefault('config_color_days_critical',  get_config('studentstracker', 'colordayscritical'));
        $mform->setType('config_color_days_critical', PARAM_RAW);

        $mform->addElement('text', 'config_color_never', get_string('color_never', 'block_studentstracker'));
        $mform->setDefault('config_color_never', get_config('studentstracker', 'colordaysnever'));
        $mform->setType('config_color_never', PARAM_RAW);

        if (has_capability('block/studentstracker:editadvance', context_course::instance($COURSE->id)) or is_siteadmin($USER->id)) {
            $mform->addElement('text', 'config_days', get_string('days', 'block_studentstracker'));
            $mform->setDefault('config_days', get_config('studentstracker', 'trackingdays'));
            $mform->setType('config_days', PARAM_INT);

            $mform->addElement('text', 'config_days_critical', get_string('days_critical', 'block_studentstracker'));
            $mform->setDefault('config_days_critical', get_config('studentstracker', 'trackingdayscritical'));
            $mform->setType('config_days_critical', PARAM_INT);

            $mform->addElement('text', 'config_text_header', get_string('text_header', 'block_studentstracker'));
            $mform->setDefault('config_text_header', get_string('text_header', 'block_studentstracker'));
            $mform->setType('config_text_header', PARAM_TEXT);

            $mform->addElement('text', 'config_text_header_fine', get_string('text_header_fine', 'block_studentstracker'));
            $mform->setDefault('config_text_header_fine', get_string('text_header_fine', 'block_studentstracker'));
            $mform->setType('config_text_header_fine', PARAM_TEXT);

            $mform->addElement('text', 'config_text_never', get_string('text_never', 'block_studentstracker'));
            $mform->setDefault('config_text_never', get_string('text_never_content', 'block_studentstracker'));
            $mform->setType('config_text_never', PARAM_TEXT);

            $mform->addElement('text', 'config_text_footer', get_string('text_footer', 'block_studentstracker'));
            $mform->setDefault('config_text_footer', get_string('text_footer_content', 'block_studentstracker'));
            $mform->setType('config_text_footer', PARAM_TEXT);

            $roles = studentstracker::get_roles();
            $rolesarray = array();
            foreach ($roles as $role) {
                $rolesarray[$role->id] = $role->shortname;
            }

            $select = $mform->addElement('select', 'config_role', get_string('role', 'block_studentstracker'), $rolesarray, null);
            $select->setMultiple(true);
            $mform->setDefault('config_role', get_config('studentstracker', 'roletrack'));

            $groups = groups_get_all_groups($this->block->courseid, $userid = 0, $groupingid = 0, $fields = 'g.*');
            $groupsarray = array();
            $groupsarray[0] = get_string('nogroups', 'block_studentstracker');
            foreach ($groups as $group) {
                $groupsarray[$group->id] = $group->name;
            }

            $select = $mform->addElement('select', 'config_groups', get_string('groups', 'block_studentstracker'),
                $groupsarray, null);
            $select->setMultiple(true);
            $mform->setDefault('config_groups', array());

            $mform->addElement('text', 'config_truncate', get_string('truncate', 'block_studentstracker'));
            $mform->setDefault('config_truncate', 6);
            $mform->setType('config_truncate', PARAM_INT);

            $mform->addElement('text', 'config_excludeolder', get_string('excludeolder', 'block_studentstracker'));
            $mform->setDefault('config_excludeolder', '');
            $mform->setType('config_excludeolder', PARAM_INT);

            $choices['d/m/Y H:i'] = 'd/m/Y H:i';
            $choices['m/d/Y H:i'] = 'm/d/Y H:i';
            $choices['d/m/Y'] = 'd/m/Y';
            $choices['m/d/Y'] = 'm/d/Y';
            $select = $mform->addElement('select', 'config_dateformat',
                get_string('dateformat', 'block_studentstracker'), $choices, null);
            $select->setMultiple(false);
            $mform->setDefault('config_dateformat', get_config('studentstracker', 'dateformat'));

            unset($choices);

            $default = 'date_desc';
            $choices['id'] = 'id';
            $choices['lastname'] = get_string('lastname', 'core');
            $choices['firstname'] = get_string('firstname', 'core');
            $choices['email'] = get_string('email', 'core');
            $choices['date_asc'] = get_string('date_asc', 'block_studentstracker');
            $choices['date_desc'] = get_string('date_desc', 'block_studentstracker');
            $select = $mform->addElement('select', 'config_sorting',
                get_string('sorting', 'block_studentstracker'), $choices, null);
            $select->setMultiple(false);
            $mform->setDefault('config_sorting', get_config('studentstracker', 'sorting'));
        }
    }
}
