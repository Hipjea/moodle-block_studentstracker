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
 * Renderer.
 *
 * @package    block_studentstracker
 * @copyright  2025 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_studentstracker\output;

use plugin_renderer_base;
use renderable;

/**
 * Renderer for the main content of the block.
 */
class renderer extends plugin_renderer_base {
    /**
     * Defer to template.
     * @param \templatable $output
     * @return string
     */
    public function render_main_content(\templatable $output) {
        $data = $output->export_for_template($this);

        return parent::render_from_template('block_studentstracker/main_content', $data);
    }
}
