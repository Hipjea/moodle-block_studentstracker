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
 * @copyright  2021 Pierre Duverneix
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

    public static function get_last_access($courseid, $userid) {
        global $DB;
        $lastaccess = $DB->get_field('user_lastaccess', 'timeaccess', array('courseid' => $courseid, 'userid' => $userid));
        if ($lastaccess < 1) {
            return 0;
        }
        return $lastaccess;
    }

    public static function messaging($user) {
        global $DB;
        $userid = optional_param('user2', $user->id, PARAM_INT);
        $url = new moodle_url('/message/index.php');
        if ($user->id) {
            $url->param('id', $userid);
        }
        return html_writer::link($url, "<img src=\"../pix/t/message.png\">", array());
    }

    public static function profile($user, $context, $output) {
        global $DB;
        $url = new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $context->instanceid));
        return html_writer::link($url, $output->user_picture($user, array('size'=>15, 'alttext'=>false, 'link'=>false)) .
                                "$user->firstname $user->lastname", array());
    }

    public static function output_info($user, $dateformat) {
        if (isset($user->lastaccess)) {
            $out = '<a class="btn btn-link p-0 pl-1" role="button" data-container="body" ';
            $out .= 'data-toggle="popover" data-placement="right"';
            $out .= ' data-content="<div class=&quot;no-overflow&quot;>';
            $out .= '<a href=&quot;mailto:'.$user->email.'&quot;>'.$user->email.'</a></p>';
            $out .= '<p class=&quot;mb-0&quot;>'. get_string('lastaccess', 'core') . ' : ';
            $out .= date($dateformat, $user->lastaccess).'</p></div>"';
            $out .= ' data-html="true" tabindex="0" data-trigger="focus" data-original-title="" title="">';
            $out .= '<i class="icon fa fa-question-circle text-info fa-fw" title="" aria-label=""></i></a>';
        } else {
            $out = '<a class="btn btn-link p-0 pl-1" role="button" data-container="body" ';
            $out .= 'data-toggle="popover" data-placement="right"';
            $out .= ' data-content="<div class=&quot;no-overflow&quot;>';
            $out .= '<p class=&quot;mb-0&quot;><a href=&quot;mailto:'.$user->email.'&quot;>'.$user->email.'</a></p></div>"';
            $out .= ' data-html="true" tabindex="0" data-trigger="focus" data-original-title="" title="">';
            $out .= '<i class="icon fa fa-question-circle text-info fa-fw" title="" aria-label=""></i></a>';
        }
        return $out;
    }

    public static function sort_objects($key) {
        if ($key == 'date_desc') {
            return function ($a, $b) use ($key) {
                return strnatcmp($b->lastaccesscourse, $a->lastaccesscourse);
            };
        } else if ($key == 'date_asc') {
            return function ($a, $b) use ($key) {
                return strnatcmp($a->lastaccesscourse, $b->lastaccesscourse);
            };
        }
        return function ($a, $b) use ($key) {
            return strnatcmp($a->{$key}, $b->{$key});
        };
    }
}