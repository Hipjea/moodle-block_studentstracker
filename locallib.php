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
 * @copyright  2025 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Main studentstracker class.
 *
 * @author Pierre Duverneix
 */
class studentstracker {
    /** @var int */
    private $days;

    /** @var string */
    private $dateformat;

    /** @var string */
    private $colordays;

    /** @var string */
    private $colordayscritical;

    /** @var string */
    private $colornever;

    /** @var string */
    private $textnever;

    /** @var array */
    private $trackedroles;

    /** @var array */
    private $trackedgroups;

    /** @var string */
    private $sorting;

    /** @var int */
    private $truncate;

    /** @var int */
    private $excludeolder;

    /** @var int */
    private $initialsonly;

    /** @var string */
    private $textheader;

    /** @var string */
    private $textfooter;

    /** @var int */
    private $dayscritical;

    /** @var int */
    private $usercount;

    /** @var array */
    private $users;

    /**
     * Constructor.
     *
     * @param stdClass $config
     * @param ?string $textheader
     * @param ?string $textfooter
     * @throws coding_exception
     * @throws dml_exception
     */
    public function __construct(\stdClass $config, ?string $textheader = '', ?string $textfooter = '') {
        $this->colordays = $config->color_days ?? get_config('studentstracker', 'colordays');
        $this->colordayscritical = $config->color_days_critical ?? get_config('studentstracker', 'colordayscritical');
        $this->colornever = $config->color_never ?? get_config('studentstracker', 'colordaysnever');
        $this->dateformat = $config->dateformat ?? 'd/m/Y';
        $this->days = $config->days ?? get_config('studentstracker', 'trackingdays');
        $this->dayscritical = get_config('studentstracker', 'trackingdayscritical');
        $this->excludeolder = $config->excludeolder ?? 0;
        $this->initialsonly = isset($config->initialsonly) ? intval($config->initialsonly) : get_config('studentstracker', 'initialsonly');
        $this->sorting = $config->sorting ?? 'date_desc';
        $this->textnever = $config->textnevercontent ?? get_string('text_never_content', 'block_studentstracker');
        $this->trackedroles = $config->role ?? explode(",", get_config('studentstracker', 'roletrack'));
        $this->trackedgroups = $config->groups ?? [];
        $this->truncate = $config->truncate ?? get_config('studentstracker', 'truncate');
        $this->textheader = $textheader;
        $this->textfooter = $textfooter;
        $this->usercount = 0;
        $this->users = [];
    }

    /**
     * Get the value of usercount.
     *
     * @return int
     */
    public function getusercount(): int {
        return $this->usercount;
    }

    /**
     * Set the value of usercount.
     *
     * @param int $usercount
     * @return $this
     * @throws coding_exception
     */
    public function setusercount(int $usercount): self {
        if (!is_int($usercount)) {
            throw new coding_exception('usercount must be an integer.');
        }

        $this->usercount = $usercount;

        return $this;
    }

    /**
     * Get the value of textheader.
     *
     * @return string
     */
    public function gettextheader(): string {
        return $this->textheader;
    }

    /**
     * Set the value of textheader.
     *
     * @param ?string $textheader
     * @return $this
     * @throws coding_exception
     */
    public function settextheader(?string $textheader = ''): self {
        if (!is_string($textheader)) {
            throw new coding_exception('textheader must be a string.');
        }

        $this->textheader = $textheader;

        return $this;
    }

    /**
     * Set the value of users.
     *
     * @param array $users
     * @return $this
     * @throws coding_exception
     */
    public function setusers(array $users): self {
        if (!is_array($users)) {
            throw new coding_exception('users must be an array.');
        }

        $this->users = $users;

        return $this;
    }


    /**
     * Retrieves the list of the enrolled users of the courses and apply the logic.
     *
     * @param \stdClass $context The context object
     * @param int $courseid The course object id
     * @return $this
     */
    public function init_users($context, $courseid) {
        global $DB, $OUTPUT;

        $usercount = 0;
        $groupids = empty($this->trackedgroups) || isset($this->trackedgroups[0]) ? 0 : $this->trackedgroups;
        $users = get_enrolled_users($context, '', $groupids, 'u.*', null, 0, 0, true);
        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

        foreach ($users as $enrol) {
            if (groups_user_groups_visible($course, $enrol->id)) {
                $enrol->lastaccesstimestamp = $this->get_last_access($context->instanceid, $enrol->id);

                if ($this->excludeolder != 0 && $enrol->lastaccesstimestamp < strtotime("-$this->excludeolder day", time())) {
                    continue;
                }

                $enrol->hasrole = self::has_role($this->trackedroles, $context->id, $enrol->id);
                if ((in_array("0", $this->trackedgroups) == false) && (count($this->trackedgroups) > 0)) {
                    if (!($this->is_in_groups($courseid, $enrol->id))) {
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
                $enrol->picture = self::profile($enrol, $context, 15, $this->initialsonly, $OUTPUT);
                $enrol->popoverpicture = self::profile($enrol, $context, 25, $this->initialsonly, $OUTPUT);
                $enrol->lastaccess = date($this->dateformat, $enrol->lastaccess);

                if ($enrol->lastaccesstimestamp < 1) {
                    $enrol->rowcolor = $this->colornever;
                    $enrol->rowclass = 'studentstracker-never';
                } else if (
                    $enrol->lastaccesstimestamp > 1 && $enrol->lastaccesstimestamp < strtotime($this->days, time())
                    && ($enrol->lastaccesstimestamp < strtotime($this->dayscritical, time()))
                ) {
                    // Critical access level.
                    $enrol->rowcolor = $this->colordayscritical;
                    $enrol->rowclass = 'studentstracker-critical';
                } else if (($enrol->lastaccesstimestamp < strtotime($this->days, time()))
                    && ($enrol->lastaccesstimestamp >= strtotime($this->dayscritical, time()))
                ) {
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
        }

        if (isset($this->sorting)) {
            usort($users, self::sort_objects($this->sorting));
        }

        $this->usercount = $usercount;
        $this->users = $users;

        return $this;
    }

    /**
     * Check if the given user has the tracked roles.
     *
     * @param array $roleids The role ids
     * @param int $courseid The course id
     * @param int $userid The user id
     * @return bool
     */
    private static function has_role($roleids, $courseid, $userid) {
        global $DB;

        $params = [];

        foreach ($roleids as $role) {
            array_push($params, (int)$role);
        }

        array_push($params, $courseid, (int)$userid);

        $roles = join(',', array_fill(0, count($roleids), '?'));
        $r = $DB->count_records_sql(
            "SELECT COUNT(id) FROM {role_assignments} WHERE roleid IN($roles) AND contextid=? AND userid=?",
            $params
        );

        if ($r > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the given user is part of the tracked groups.
     *
     * @param int $courseid The course id
     * @param int $userid The user id
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
     * @param int $courseid The course id
     * @param int $userid The user id
     * @return $lastaccess string
     */
    private function get_last_access($courseid, $userid) {
        global $DB;

        $lastaccess = $DB->get_field('user_lastaccess', 'timeaccess', ['courseid' => $courseid, 'userid' => $userid]);
        if ($lastaccess < 1) {
            return 0;
        }

        return $lastaccess;
    }

    /**
     * Link to the private message page for a given user.
     *
     * @param \stdClass $user The user object
     */
    public static function messaging($user) {
        global $OUTPUT;

        $userid = optional_param('user2', $user->id, PARAM_INT);
        $url = new moodle_url('/message/index.php');
        if ($user->id) {
            $url->param('id', $userid);
        }

        return html_writer::link($url, '<img class="icon mr-0" src="' . $OUTPUT->image_url('t/message') . '">', []);
    }

    /**
     * Link to the user's profile.
     *
     * @param \stdClass $user The user object
     * @param \stdClass $context The context object
     * @param int $size The user picture size
     * @param int $initialsonly Setting to display only the users initials
     * @param \core_renderer $output The core_renderer to use when generating the output
     */
    public static function profile($user, $context, $size, $initialsonly, $output) {
        $url = new moodle_url('/user/view.php', ['id' => $user->id, 'course' => $context->instanceid]);

        // Get the users initials only, depending on the settings.
        if ($initialsonly) {
            $firstnames = explode(' ', $user->firstname);
            $initials = '';
            foreach ($firstnames as $name) {
                if (!empty($name)) {
                    $initials .= strtoupper($name[0]) . '. ';
                }
            }

            $initials = trim($initials);
        }

        $username = isset($initials) ? "$initials $user->lastname" : "$user->firstname $user->lastname";

        return html_writer::link(
            $url,
            $output->user_picture($user, ['size' => $size, 'alttext' => false, 'link' => false]) . $username,
            ['class' => 'block_studentstracker-userpicture']
        );
    }

    /**
     * Sorting function used for the sorting config option.
     *
     * @param string $key The key used for sorting the objects
     */
    public static function sort_objects($key) {
        if ($key == 'date_desc') {
            return function ($a, $b) {
                if (isset($a->lastaccesstimestamp) && isset($b->lastaccesstimestamp)) {
                    return strnatcmp($b->lastaccesstimestamp, $a->lastaccesstimestamp);
                }
            };
        } else if ($key == 'date_asc') {
            return function ($a, $b) {
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
            $this->textfooter
        );
    }
}
