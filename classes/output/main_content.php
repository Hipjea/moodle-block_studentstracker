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
 * Competencies to review renderable.
 *
 * @package    block_studentstracker
 * @copyright  2025 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_studentstracker\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;
use moodle_url;
use core\output\help_icon;

/**
 * Main content renderable.
 */
class main_content implements renderable, templatable {
    /**
     * @var int The number of users.
     */
    public $usercount;
    /**
     * @var array The users to display.
     */
    public $users;
    /**
     * @var bool Whether to truncate the text.
     */
    public $truncate;
    /**
     * @var string The text to display in the header.
     */
    public $textheader;
    /**
     * @var string The text to display in the footer.
     */
    public $textfooter;

    /**
     * Constructor.
     *
     * @param int $usercount
     * @param array $users
     * @param bool $truncate
     * @param string $textheader
     * @param string $textfooter
     */
    public function __construct($usercount, $users, $truncate, $textheader, $textfooter) {
        $this->usercount = $usercount;
        $this->users = $users;
        $this->truncate = $truncate;
        $this->textheader = $textheader;
        $this->textfooter = $textfooter;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = [
            'usercount' => $this->usercount,
            'users' => $this->users,
            'truncate' => $this->truncate,
            'textheader' => $this->textheader,
            'textfooter' => $this->textfooter,
            'pluginbaseurl' => (new moodle_url('/blocks/studenstracker'))->out(false),
        ];

        return $data;
    }
}
