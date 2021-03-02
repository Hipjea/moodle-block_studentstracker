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

/**
 * Main studentstracker class
 *
 * @author     Pierre Duverneix
 */
class studentstracker {
    /**
     * @var int
     */
    private $usercount;

    /**
     * @var array
     */
    private $users;

    /**
     * @var string
     */
    private $dateformat;

    /**
     * @var int
     */
    private $days;

    /**
     * @var int
     */
    private $dayscritical;

    /**
     * @var string
     */
    private $colordays;

    /**
     * @var string
     */
    private $colordayscritical;

    /**
     * @var string
     */
    private $colornever;

    /**
     * @var string
     */
    private $textnever;

    /**
     * @var array
     */
    private $trackedroles;

    /**
     * @var array
     */
    private $trackedgroups;

    /**
     * @var string
     */
    private $textheader;

    /**
     * @var string
     */
    private $textfooter;

    /**
     * @var string
     */
    private $sorting;

    /**
     * @var int
     */
    private $truncate;

    /**
     * @var int
     */
    private $excludeolder;

    /**
     * Constructor.
     */
    public function __construct() {
    }

    /**
     * Getter.
     *
     * @param $property
     * @throws coding_exception
     */
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        throw new coding_exception('Invalid property requested.');
    }

    /**
     * Setter.
     *
     * @param $property
     * @throws coding_exception
     */
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
            return $this;
        } else {
            throw new coding_exception('Invalid property requested.');
        }
    }

    /**
     * Retrieves the list of the enrolled users of the courses and apply the logic.
     *
     * @param $context The context object
     * @param $courseid The course object id
     * @return $this
     */
    public function get_enrolled_users($context, $courseid) {
        global $OUTPUT;

        $usercount = 0;
        $users = get_enrolled_users($context, '', 0, 'u.*', null, 0, 0, true);

        foreach ($users as $enrol) {
            $enrol->lastaccesstimestamp = $this->get_last_access($context->instanceid, $enrol->id);

            if ($this->excludeolder != '' && $enrol->lastaccesstimestamp < strtotime("-$this->excludeolder day", time())) {
                continue;
            }

            $enrol->hasrole = self::has_role($this->trackedroles, $context->id, $enrol->id);
            if ((in_array("0", $this->trackedgroups) == false) && (count($this->trackedgroups) > 0)) {
                if (!($this->is_in_groups($this->trackedgroups, $courseid, $enrol->id))) {
                    continue;
                }
            }

            if ($enrol->lastaccesstimestamp > 0) {
                $enrol->lastaccesscourse = date($this->dateformat, $enrol->lastaccesstimestamp);
            } else {
                $enrol->lastaccesscourse = $this->textnever;
            }

            $enrol->messaging = self::messaging($enrol);
            $enrol->datelastaccess = date($this->dateformat, $enrol->lastaccess);
            $enrol->picture = self::profile($enrol, $context, $OUTPUT);
            $enrol->lastaccess = date($this->dateformat, $enrol->lastaccess);

            if ($enrol->lastaccesstimestamp < 1) {
                $enrol->rowcolor = $this->colornever;
                $enrol->rowclass = 'studentstracker-never';
            } else if ($enrol->lastaccesstimestamp > 1 && $enrol->lastaccesstimestamp < strtotime($this->days, time())
                && ($enrol->lastaccesstimestamp < strtotime($this->dayscritical, time())) ) {
                // Critical access level.
                $enrol->rowcolor = $this->colordayscritical;
                $enrol->rowclass = 'studentstracker-critical';
            } else if ( ($enrol->lastaccesstimestamp < strtotime($this->days, time()))
                && ($enrol->lastaccesstimestamp >= strtotime($this->dayscritical, time())) ) {
                // First access level.
                $enrol->rowcolor = $this->colordays;
                $enrol->rowclass = 'studentstracker-first';
            } else {
                continue;
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

    /**
     * Get the site roles.
     */
    public static function get_roles() {
        global $DB;
        return $DB->get_records_sql('SELECT id,shortname FROM {role} ORDER BY id');
    }

    /**
     * Check if the given user has the tracked roles.
     *
     * @param $roleids The role ids
     * @param $courseid
     * @param $userid
     * @return bool
     */
    private static function has_role($roleids, $courseid, $userid) {
        global $DB;
        $params = array();
        foreach ($roleids as $role) {
            array_push($params, (int)$role);
        }
        array_push($params, $courseid, (int)$userid);
        $roles = join(',', array_fill(0, count($roleids), '?'));
        $r = $DB->count_records_sql("SELECT COUNT(id) FROM {role_assignments} WHERE roleid IN($roles) AND contextid=? AND userid=?",
        $params);
        if ($r > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the given user is part of the tracked groups.
     *
     * @param $courseid
     * @param $userid
     * @return bool
     */
    private function is_in_groups($courseid, $userid) {
        $usergroups = groups_get_user_groups($courseid, $userid);
        foreach ($this->trackedgroups as $group) {
            if (in_array(intval($group), $usergroups[0], true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the user's last access in a course.
     *
     * @param $courseid
     * @param $userid
     * @return $lastaccess string
     */
    private function get_last_access($courseid, $userid) {
        global $DB;
        $lastaccess = $DB->get_field('user_lastaccess', 'timeaccess', array('courseid' => $courseid, 'userid' => $userid));
        if ($lastaccess < 1) {
            return 0;
        }
        return $lastaccess;
    }

    /**
     * Link to the private message page for a given user.
     *
     * @param $user The user object
     */
    public static function messaging($user) {
        global $OUTPUT;

        $userid = optional_param('user2', $user->id, PARAM_INT);
        $url = new moodle_url('/message/index.php');
        if ($user->id) {
            $url->param('id', $userid);
        }
        return html_writer::link($url, '<img src="'.$OUTPUT->image_url('t/message').'">', array());
    }

    /**
     * Link to the user's profile.
     *
     * @param $user The user object
     * @param $context The context object
     * @param $output The core_renderer to use when generating the output.
     */
    public static function profile($user, $context, $output) {
        $url = new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $context->instanceid));
        return html_writer::link($url, $output->user_picture($user, array('size' => 15, 'alttext' => false, 'link' => false)) .
                                "$user->firstname $user->lastname", array());
    }

    /**
     * Sorting function used for the sorting config option.
     *
     * @param $key The key used for sorting the objects
     */
    public static function sort_objects($key) {
        if ($key == 'date_desc') {
            return function ($a, $b) use ($key) {
                if (isset($a->lastaccesstimestamp) && isset($b->lastaccesstimestamp)) {
                    return strnatcmp($b->lastaccesstimestamp, $a->lastaccesstimestamp);
                }
            };
        } else if ($key == 'date_asc') {
            return function ($a, $b) use ($key) {
                if (isset($a->lastaccesstimestamp) && isset($b->lastaccesstimestamp)) {
                    return strnatcmp($a->lastaccesstimestamp, $b->lastaccesstimestamp);
                }
            };
        }
        return function ($a, $b) use ($key) {
            return strnatcmp($a->{$key}, $b->{$key});
        };
    }

    /**
     * Call the plugin renderer with the data.
     *
     * @return \block_studentstracker\output\main_content Studentstracker main_content renderer
     */
    public function generate_content() {
        return new \block_studentstracker\output\main_content(
            $this->usercount,
            $this->users,
            $this->truncate,
            $this->textheader,
            $this->textfooter);
    }
}