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
        global $COURSE, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $context = context_course::instance($COURSE->id);
        if (has_capability('block/studentstracker:view', $context)) {
            $isgranted = true;
        }

        if ($isgranted == false && !is_siteadmin($USER->id)) {
            return $this->content;
        } else {
            $this->content = new stdClass();
            $this->content->items = array();

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
                $this->text_footer = '';
            }

            $st = new \studentstracker();
            $st->trackedroles = !empty($this->config->role) ? $this->config->role : explode(",", get_config(
                'studentstracker', 'roletrack'));
            $st->trackedgroups = !empty($this->config->groups) ? $this->config->groups : array();
            $st->dateformat = !empty($this->config->dateformat) ? $this->config->dateformat : 'd/m/Y';
            $st->days = !empty($this->config->days) ? '-'.$this->config->days.' day' : '-'.get_config(
                'studentstracker', 'trackingdays').' day';
            $st->dayscritical = !empty($this->config->days_critical) ? '-'.$this->config->days_critical.
                ' day' : '-'.get_config('studentstracker', 'trackingdays').' day';
            $st->colordays = !empty($this->config->color_days) ? $this->config->color_days : get_config(
                'studentstracker', 'colordays');
            $st->colordayscritical = !empty($this->config->color_days_critical) ?
                $this->config->color_days_critical : get_config('studentstracker', 'colordayscritical');
            $st->colornever = !empty($this->config->color_never) ? $this->config->color_never : get_config(
                'studentstracker', 'colordaysnever');
            $st->truncate = !empty($this->config->truncate) ? $this->config->truncate : 6;
            $st->sorting = !empty($this->config->sorting) ? $this->config->sorting : 'date_desc';
            $st->textheader = $this->text_header_fine;
            $st->textnever = !empty($this->config->text_never_content) ?
                $this->config->text_never_content : get_string('text_never_content', 'block_studentstracker');
            $st->textfooter = $this->text_footer;
            $st->excludeolder = !empty($this->config->excludeolder) ? $this->config->excludeolder : '';
            $st->get_enrolled_users($context, $COURSE->id);

            // If the usercount is greater than 0, display the warning text.
            if ($st->usercount > 0) {
                $st->textheader = $this->text_header;
            }

            $content = $st->generate_content();
            $renderer = $this->page->get_renderer('block_studentstracker');

            $this->content->text = $renderer->render($content);
            return $this->content;
        }
    }

    public function applicable_formats() {
        return array('all' => false, 'course' => true, 'course-index' => false);
    }
}