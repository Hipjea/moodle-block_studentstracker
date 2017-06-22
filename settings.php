<?php

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
        get_string('days_desc', 'block_studentstracker'),
        '3'));

 $settings->add(new admin_setting_configtext(
        'studentstracker/trackingdayscritical',
        get_string('days_critical', 'block_studentstracker'),
        get_string('days_critical_desc', 'block_studentstracker'),
        '6'));

 $settings->add(new admin_setting_configcolourpicker(
        'studentstracker/colordays',
        get_string('color_days', 'block_studentstracker'),
        get_string('color_days_desc', 'block_studentstracker'),
        '#FFD9BA',null));

 $settings->add(new admin_setting_configcolourpicker(
        'studentstracker/colordayscritical',
        get_string('color_days_critical', 'block_studentstracker'),
        get_string('color_days_critical_desc', 'block_studentstracker'),
        '#FECFCF',null));

 $settings->add(new admin_setting_configcolourpicker(
        'studentstracker/colordaysnever',
        get_string('color_never', 'block_studentstracker'),
        get_string('color_never_desc', 'block_studentstracker'),
        '#D0D0D0',null));

 $settings->add(new admin_setting_configmultiselect(
        'studentstracker/roletrack',
        get_string('roletrack', 'block_studentstracker'),
        get_string('roletrack_desc', 'block_studentstracker'),
        array('5'),
        $rolesarray));

}


