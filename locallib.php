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

    private $usercount;
    private $users;
    private $dateformat;
    private $colordays;
    private $colordayscritical;
    private $colornever;
    private $textnever;
    private $trackedroles;
    private $trackedgroups;
    private $textheader;
    private $textfooter;
    private $sorting;

    /**
     * Constructor.
     */
    public function __construct() {
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }

    public function get_enrolled_users($context) {
        global $OUTPUT;

        $usercount = 0;
        $users = get_enrolled_users($context, '', 0, 'u.*', null, 0, 0, true);

        foreach ($users as $enrol) {
            $enrol->hasrole = self::has_role($this->trackedroles, $context->id, $enrol->id);
            if ((in_array("0", $this->trackedgroups) == false) && (count($this->trackedgroups) > 0)) {
                if (!(self::is_in_groups($this->trackedgroups, $COURSE->id, $enrol->id))) {
                    continue;
                }
            }
            $enrol->lastaccesscourse = self::get_last_access($context->instanceid, $enrol->id);
            $enrol->messaging = self::messaging($enrol);
            $enrol->date_lastaccess = date($this->dateformat, $enrol->lastaccess);
            $enrol->picture = self::profile($enrol, $context, $OUTPUT);

            if ($enrol->lastaccesscourse < 1) {
                $enrol->rowcolor = $this->colornever;
                $enrol->rowclass = 'studentstracker-never';
                $enrol->lastaccess = $this->textnever;
            }
            // Critical access level.
            else if (intval($enrol->lastaccesscourse) > 1 && intval($enrol->lastaccesscourse) < strtotime($this->days, time())
                && (intval($enrol->lastaccesscourse) < strtotime($this->dayscritical, time())) ) {
                $enrol->rowcolor = $this->colordayscritical;
                $enrol->rowclass = 'studentstracker-critical';
                $enrol->lastaccess = date($this->dateformat, $enrol->lastaccesscourse);
            }
            // First access level.
            else if ( (intval($enrol->lastaccesscourse) < strtotime($this->days, time()))
                && (intval($enrol->lastaccesscourse) >= strtotime($this->dayscritical, time())) ) {
                $enrol->rowcolor = $this->colordays;
                $enrol->rowclass = 'studentstracker-first';
                $enrol->lastaccess = date($this->dateformat, $enrol->lastaccesscourse);
            } else {
                $output = false;
            }

            if ($enrol->hasrole) {
                $usercount++;
            }
        }

        if (isset($this->sorting)) {
            usort($users, self::sort_objects($this->sorting));
        }

        $this->usercount = $usercount;
        $this->users = $users;

        return $this;
    }

    public function generate_content() {
        return new \block_studentstracker\output\main_content($this->usercount,
            $this->users,
            1,
            $this->textheader,
            $this->textfooter);
    }

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

    public static function is_in_groups($courseid, $enrolid) {
        $usergroups = groups_get_user_groups($courseid, $userid = $enrolid);
        foreach ($this->trackedgroups as $group) {
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