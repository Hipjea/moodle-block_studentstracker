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
 * Studentstracker block
 *
 * @package    block_studentstracker
 * @copyright  2015 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/blocks/studentstracker/locallib.php");

class block_studentstracker extends block_base {
    public function init() {
        global $COURSE;
        $this->blockname = get_class($this);
        $this->title = get_string('pluginname', 'block_studentstracker');
        $this->courseid = $COURSE->id;
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function has_config() {
        return true;
    }

    public function instance_allow_config() {
        return true;
    }

    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle', 'block_studentstracker');
            } else {
                $this->title = $this->config->title;
            }
        }
    }

    public function get_content() {
        global $CFG, $COURSE, $USER, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $PAGE->requires->js_call_amd('block_studentstracker/ui', 'init', array());

        $context = context_course::instance($COURSE->id);
        $roles = !empty($this->config->roles) ? $this->config->roles : array(1);
        $isgranted = studentstracker::has_role($roles, $context->id, $USER->id);

        if ($isgranted == false && !is_siteadmin($USER->id)) {
            return $this->content;
        } else {
            $usercount = 0;

            $this->content = new stdClass();
            $this->content->items = array();

            $days = !empty($this->config->days) ? '-'.$this->config->days.' day' : '-3 day';
            $dayscritical = !empty($this->config->days_critical) ? '-'.$this->config->days_critical.' day' : '-6 day';
            $colordays = !empty($this->config->color_days) ? $this->config->color_days : '#FFD9BA';
            $colordayscritical = !empty($this->config->color_days_critical) ? $this->config->color_days_critical : '#FECFCF';
            $colornever = !empty($this->config->color_never) ? $this->config->color_never : '#D0D0D0';
            $trackedroles = !empty($this->config->role) ? $this->config->role : array(5);
            $trackedgroups = !empty($this->config->groups) ? $this->config->groups : array();
            $truncate = !empty($this->config->truncate) ? $this->config->truncate : 0;

            if (!empty($this->config->text_header)) {
                $this->text_header = $this->config->text_header;
            } else {
                $this->text_header = get_string('text_header', 'block_studentstracker');
            }
            if (!empty($this->config->text_header_fine)) {
                $this->text_header_fine = $this->config->text_header_fine;
            } else {
                $this->text_header_fine = get_string('text_header_fine', 'block_studentstracker');
            }
            if (!empty($this->config->text_never_content)) {
                $this->text_never_content = $this->config->text_never_content;
            } else {
                $this->text_never_content = get_string('text_never_content', 'block_studentstracker');
            }
            if (!empty($this->config->text_footer)) {
                $this->text_footer = $this->config->text_footer;
            } else {
                $this->text_footer = get_string('text_footer', 'block_studentstracker');
            }

            $enrols = get_enrolled_users($context);
            foreach ($enrols as $enrol) {
                $hasrole = studentstracker::has_role($trackedroles, $context->id, $enrol->id);
                if ((in_array("0", $trackedgroups) == false) && (count($trackedgroups) > 0)) {
                    if (!(studentstracker::is_in_groups($trackedgroups, $COURSE->id, $enrol->id))) {
                        continue;
                    }
                }

                if ($hasrole == true) {
                    if ($enrol->lastaccess != 0) {
                        if ( (intval($enrol->lastaccess) < strtotime($days, time()))
                         && (intval($enrol->lastaccess) >= strtotime($dayscritical, time())) ) {
                            $lastaccess = date('d/m/Y H:i', $enrol->lastaccess);
                            $output = "<li class='studentstracker-first' style='background:".$colordays."'>";
                            $output .= $this->messaging($enrol)."<span> - $lastaccess</span></li>";
                            array_push($this->content->items, $output);
                            $usercount++;
                            unset($output);
                        } else if (intval($enrol->lastaccess) < strtotime($days, time())) {
                            $lastaccess = date('d/m/Y H:i', $enrol->lastaccess);
                            $output = "<li class='studentstracker-critical' style='background:".$colordayscritical."'>";
                            $output .= $this->messaging($enrol)."<span> - $lastaccess</span></li>";
                            array_push($this->content->items, $output);
                            $usercount++;
                            unset($output);
                        }
                    } else {
                        $output = "<li class='studentstracker-never' style='background:".$colornever."'>";
                        $output .= $this->messaging($enrol)."<span> - $this->text_never_content</span></li>";
                        array_push($this->content->items, $output);
                        $usercount++;
                        unset($output);
                    }
                }
            }

            if ($usercount > 0) {
                $headertext = '<div class="studentstracker_header"><span class="badge badge-warning">'.$usercount.'</span>';
                $headertext .= $this->text_header.'</div>';
                $footertext = '<div class="studentstracker_footer">'.$this->text_footer.'</div>';
            } else {
                $headertext = '<div class="studentstracker_header">'.$this->text_header_fine.'</div>';
                $footertext = '';
            }

            $this->content->text = $headertext;
            $this->content->text .= "<ul id='studentstracker-list' data-show=". $truncate .">";
            foreach ($this->content->items as $item) {
                $this->content->text .= $item;
            }
            $this->content->text .= "</ul>";
            $this->content->text .= "<center><div id=\"tracker_showmore\"></div>\n<div id=\"tracker_showless\"></div></center>";
            $this->content->text .= $footertext;

            return $this->content;
        }
    }

    private function messaging($user) {
        global $DB;
        $userid = optional_param('user2', $user->id, PARAM_INT);
        $url = new moodle_url('/message/index.php');
        if ($user->id) {
            $url->param('id', $userid);
        }
        return html_writer::link($url, "<img src=\"../pix/t/message.png\"> $user->firstname $user->lastname", array());
    }

    public function applicable_formats() {
        return array('all' => false, 'course' => true, 'course-index' => false);
    }
}
