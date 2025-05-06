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
 * @copyright  2025 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/blocks/studentstracker/locallib.php");

class block_studentstracker extends block_base {
    /** @var string The block name. */
    public $blockname;

    /** @var stdClass */
    public $config;

    /** @var stdClass|null */
    public $content;

    /** @var moodle_page */
    public $page;

    /** @var string The block title. */
    public $title;

    /** @var int The course ID. */
    public $courseid;

    /** @var string */
    private $text_header;

    /** @var string */
    private $text_header_fine;

    /** @var string */
    private $text_never_content;

    /** @var string */
    private $text_footer;

    /**
     * The block's init function.
     * 
     * @return void
     */
    public function init() {
        global $COURSE;

        $this->blockname = get_class($this);
        $this->title = get_string('pluginname', 'block_studentstracker');
        $this->courseid = $COURSE->id;
    }

    /**
     * All multiple instances of the block.
     * 
     * @return bool
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Global configurability of the block.
     *
     * @return bool
     */
    public function has_config() {
        return true;
    }

    /**
     * Local configurability of the block.
     *
     * @return bool
     */
    public function instance_allow_config() {
        return true;
    }

    /**
     * Instance specialisations if instance_allow_config is true.
     *
     * @return void
     */
    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle', 'block_studentstracker');
            } else {
                $this->title = $this->config->title;
            }
        }
    }

    /**
     * Defines where the block can be added.
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
            'all' => false,
            'course' => true,
            'course-index' => false
        );
    }

    /**
     * The block's main content.
     *
     * @return string|stdClass
     */
    public function get_content() {
        global $COURSE, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $isgranted = false;

        $context = context_course::instance($COURSE->id);
        if (has_capability('block/studentstracker:view', $context)) {
            $isgranted = true;
        }

        if ($isgranted == false && !is_siteadmin($USER->id)) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();

        // Define and set the needed properties.
        $properties = [
            'text_header' => 'text_header',
            'text_header_fine' => 'text_header_fine',
            'text_never_content' => 'text_never_content',
            'text_footer' => ''
        ];

        foreach ($properties as $property => $langstr) {
            if (!empty($this->config->{$property})) {
                $this->{$property} = $this->config->{$property};
            } else {
                $this->{$property} = $langstr ? get_string($langstr, 'block_studentstracker') : '';
            }
        }

        // Instantiate the studentstracker class from locallib.php.
        $st = new \studentstracker(
            $this->config ?? new stdClass(),
            $this->text_header_fine,
            $this->text_footer,
        );

        $st->init_users($context, $COURSE->id);

        // If the usercount is greater than 0, display the warning text.
        if ($st->getUsercount() > 0) {
            $st->setTextHeader($this->text_header);
        }

        $content = $st->generate_content();
        $renderer = $this->page->get_renderer('block_studentstracker');
        $this->content->text = $renderer->render($content);

        return $this->content;
    }
}
