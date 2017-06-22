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
 * Plugin capabilities
 *
 * @package    block_studentstracker
 * @author     Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class studentstracker {
    public static function get_roles() {
        global $DB;
        return $DB->get_records_sql('SELECT id,shortname FROM {role} ORDER BY id');
    }

    public static function has_role($rids, $courseid, $uid) {
        global $DB;
        $params = array();
        foreach ($rids as $role) {
            array_push($params, (int)$role);
        }
        array_push($params, $courseid, (int)$uid);
        $roles = join(',', array_fill(0, count($rids), '?'));
        $r = $DB->count_records_sql("SELECT COUNT(id) FROM {role_assignments} WHERE roleid IN($roles) AND contextid=? AND userid=?",
        $params);
        if ($r > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function is_in_groups($trackedgroups, $courseid, $enrolid) {
        $usergroups = groups_get_user_groups($courseid, $userid = $enrolid);
        foreach ($trackedgroups as $group) {
            if (in_array(intval($group), $usergroups[0], true)) {
                return true;
            }
        }
        return false;
    }
}