<?php

namespace block_studentstracker\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use renderable;

class renderer extends plugin_renderer_base {
    /**
     * Defer to template.
     * @param renderable $page
     * @return string
     */
    public function render_main_content(\templatable $output) {
        $data = $output->export_for_template($this);
        return parent::render_from_template('block_studentstracker/main_content', $data);
    }
}