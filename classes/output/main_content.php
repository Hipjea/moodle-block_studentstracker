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
 * @copyright  2026 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_studentstracker\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;

/**
 * Main content renderable.
 */
class main_content implements renderable, templatable {
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
     * @param array $users
     * @param bool $truncate
     * @param string $textheader
     * @param string $textfooter
     */
    public function __construct($users, $truncate, $textheader, $textfooter) {
        $this->users = $users;
        $this->truncate = $truncate;
        $this->textheader = $textheader;
        $this->textfooter = $textfooter;
    }

    /** 
     * Builds the list of users to render.
     *
     * Only users with the required role are included.
     * The function ensures that the truncate limit is
     * respected even when some users are filtered out.
     *
     * @return array List of users to render.
     */
    private function build_users(): array {
        $users = [];
        $visibleusersindex = 0;

        foreach ($this->users as $user) {
            if (!$user->hasrole) {
                continue;
            }

            $user->hidden = $this->truncate && $visibleusersindex >= $this->truncate;
            $users[] = $user;
            $visibleusersindex++;
        }

        return $users;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $users = $this->build_users();
        $hastoggle = $this->truncate > 0 && count($users) > $this->truncate;

        $data = new stdClass();
        $data->usercount = count($users);
        $data->users = $users;
        $data->truncate = $this->truncate;
        $data->hastoggle = $hastoggle;
        $data->textheader = $this->textheader;
        $data->textfooter = $this->textfooter;
        $data->pluginbaseurl = (new \moodle_url('/blocks/studenstracker'))->out(false);

        return $data;
    }
}
