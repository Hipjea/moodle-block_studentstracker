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
 * Settings file
 *
 * @package    block_studentstracker
 * @copyright  2021 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once("$CFG->dirroot/blocks/studentstracker/locallib.php");

if ($ADMIN->fulltree) {
    $roles = studentstracker::get_roles();
    $rolesarray = array();
    foreach ($roles as $role) {
        $rolesarray[$role->id] = $role->shortname;
    }

    $settings->add(new admin_setting_configtext(
        'studentstracker/trackingdays',
        get_string('days', 'block_studentstracker'),
        get_string('days_desc', 'block_studentstracker'), '3'));

    $settings->add(new admin_setting_configtext(
        'studentstracker/trackingdayscritical',
        get_string('days_critical', 'block_studentstracker'),
        get_string('days_critical_desc', 'block_studentstracker'),
        '6'));

    $settings->add(new admin_setting_configcolourpicker(
        'studentstracker/colordays',
        get_string('color_days', 'block_studentstracker'),
        get_string('color_days_desc', 'block_studentstracker'),
        '#FFD9BA', null));

    $settings->add(new admin_setting_configcolourpicker(
        'studentstracker/colordayscritical',
        get_string('color_days_critical', 'block_studentstracker'),
        get_string('color_days_critical_desc', 'block_studentstracker'),
        '#FECFCF', null));

    $settings->add(new admin_setting_configcolourpicker(
        'studentstracker/colordaysnever',
        get_string('color_never', 'block_studentstracker'),
        get_string('color_never_desc', 'block_studentstracker'),
        '#e0e0e0', null));

    $settings->add(new admin_setting_configmultiselect(
        'studentstracker/roletrack',
        get_string('roletrack', 'block_studentstracker'),
        get_string('roletrack_desc', 'block_studentstracker'),
        array('5'),
        $rolesarray));

    $settings->add(new admin_setting_configmultiselect(
        'studentstracker/roletrack',
        get_string('roletrack', 'block_studentstracker'),
        get_string('roletrack_desc', 'block_studentstracker'),
        array('5'),
        $rolesarray));

    $settings->add(new admin_setting_configtext(
        'studentstracker/truncate',
        get_string('truncate', 'block_studentstracker'),
        get_string('truncate_desc', 'block_studentstracker'), '10'));

    $settings->add(new admin_setting_configtext(
        'studentstracker/excludeolder',
        get_string('excludeolder', 'block_studentstracker'),
        get_string('excludeolder_desc', 'block_studentstracker'), ''));

    $default = 'd/m/Y H:i';
    $choices['d/m/Y H:i'] = 'd/m/Y H:i';
    $choices['m/d/Y H:i'] = 'm/d/Y H:i';
    $choices['d/m/Y'] = 'd/m/Y';
    $choices['m/d/Y'] = 'm/d/Y';
    $settings->add(new admin_setting_configselect(
        'studentstracker/dateformat',
        get_string('dateformat', 'block_studentstracker'),
        get_string('dateformat_desc', 'block_studentstracker'),
        $default,
        $choices));

    unset($default);
    unset($choices);

    $default = 'date_desc';
    $choices['id'] = 'id';
    $choices['lastname'] = get_string('lastname', 'core');
    $choices['firstname'] = get_string('firstname', 'core');
    $choices['email'] = get_string('email', 'core');
    $choices['date_asc'] = get_string('date_asc', 'block_studentstracker');
    $choices['date_desc'] = get_string('date_desc', 'block_studentstracker');
    $settings->add(new admin_setting_configselect(
        'studentstracker/sorting',
        get_string('sorting', 'block_studentstracker'),
        get_string('sorting_desc', 'block_studentstracker'),
        $default,
        $choices));
}