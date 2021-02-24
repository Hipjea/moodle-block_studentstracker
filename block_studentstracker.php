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
 * @copyright  2021 Pierre Duverneix
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
        if (has_capability('block/studentstracker:view', $context)) {
            $isgranted = true;
        }

        if ($isgranted == false && !is_siteadmin($USER->id)) {
            return $this->content;
        } else {
            $usercount = 0;
            $dateformat = $this->config->dateformat;

            $this->content = new stdClass();
            $this->content->items = array();

            $days = !empty($this->config->days) ? '-'.$this->config->days.' day' : '-'.get_config(
                'studentstracker', 'trackingdays').' day';
            $dayscritical = !empty($this->config->days_critical) ? '-'.$this->config->days_critical.' day' : '-'.get_config(
                'studentstracker', 'trackingdays').' day';
            $colordays = !empty($this->config->color_days) ? $this->config->color_days : get_config(
                'studentstracker', 'colordays');
            $colordayscritical = !empty($this->config->color_days_critical) ? $this->config->color_days_critical : get_config(
                'studentstracker', 'colordayscritical');
            $colornever = !empty($this->config->color_never) ? $this->config->color_never : get_config(
                'studentstracker', 'colordaysnever');
            $trackedroles = !empty($this->config->role) ? $this->config->role : explode(",", get_config(
                'studentstracker', 'roletrack'));
            $trackedgroups = !empty($this->config->groups) ? $this->config->groups : array();
            $truncate = !empty($this->config->truncate) ? $this->config->truncate : 6;

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
            if (!empty($this->config->text_footer_content)) {
                $this->text_footer = $this->config->text_footer_content;
            } else {
                $this->text_footer = get_string('text_footer_content', 'block_studentstracker');
            }

            $enrols = get_enrolled_users($context, '', 0, 'u.*', null, 0, 0, true);
            foreach ($enrols as $enrol) {
                $enrol->hasrole = studentstracker::has_role($trackedroles, $context->id, $enrol->id);
                if ((in_array("0", $trackedgroups) == false) && (count($trackedgroups) > 0)) {
                    if (!(studentstracker::is_in_groups($trackedgroups, $COURSE->id, $enrol->id))) {
                        continue;
                    }
                }
                $enrol->lastaccesscourse = studentstracker::get_last_access($context->instanceid, $enrol->id);

                if ($enrol->hasrole == true) {
                    $output = true;
                        // Never access level.
                    if ($enrol->lastaccesscourse < 1) {
                        $rowcolor = $colornever;
                        $rowclass = 'studentstracker-never';
                        $lastaccess = $this->text_never_content;

                        // Critical access level.
                    } else if (intval($enrol->lastaccesscourse) > 1 && intval($enrol->lastaccesscourse) < strtotime($days, time())
                        && (intval($enrol->lastaccesscourse) < strtotime($dayscritical, time())) ) {
                        $rowcolor = $colordayscritical;
                        $rowclass = 'studentstracker-critical';
                        $lastaccess = date($dateformat, $enrol->lastaccesscourse);

                        // First access level.
                    } else if ( (intval($enrol->lastaccesscourse) < strtotime($days, time()))
                         && (intval($enrol->lastaccesscourse) >= strtotime($dayscritical, time())) ) {
                        $rowcolor = $colordays;
                        $rowclass = 'studentstracker-first';
                        $lastaccess = date($dateformat, $enrol->lastaccesscourse);
                    } else {
                        $output = false;
                    }

                    if ($output != false) {
                        $output = "<li class='$rowclass' style='background:".$rowcolor."'>";
                        $output .= '<span class="pull-left">';
                        $output .= studentstracker::messaging($enrol).studentstracker::output_info($enrol, $dateformat);
                        $output .= studentstracker::profile($enrol, $context).'</span>';
                        $output .= '<span class="text-right">'.$lastaccess.'</span></li>';
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

    public function applicable_formats() {
        return array('all' => false, 'course' => true, 'course-index' => false);
    }
}