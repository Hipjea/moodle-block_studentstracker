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
 * Studentstracker block plugin's external functions file.
 *
 * @package    block_studentstracker
 * @copyright  2026 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_studentstracker;

use core_external\external_api;
use core_external\external_description;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * External API class.
 *
 * @package    block_studentstracker
 * @copyright  2026 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {
    /**
     * Describes the parameters for send_message_parameters.
     *
     * @return external_function_parameters
     */
    public static function send_message_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'The course ID of the block instance'),
            'message' => new external_value(PARAM_TEXT, 'The message body'),
        ]);
    }

    /**
     * Send a message to flagged users.
     *
     * @param int $courseid The course ID.
     * @param string $message The message body.
     * @return bool success.
     */
    public static function send_message($courseid, $message) {
        require_once(__DIR__ . '/../locallib.php');

        $params = self::validate_parameters(
            self::send_message_parameters(),
            [
                'courseid' => $courseid,
                'message' => $message,
            ]
        );

        $context = \context_course::instance($params['courseid']);
        self::validate_context($context);

        global $DB, $USER;

        $blockinstance = $DB->get_record('block_instances', [
            'blockname' => 'studentstracker',
            'parentcontextid' => \context_course::instance($params['courseid'])->id,
        ], '*', MUST_EXIST);

        $config = block_instance('studentstracker', $blockinstance)->config;
        $users = (new \studentstracker($config))
            ->init_users($courseid, true)
            ->get_users();

        // Send message to the users.
        $task = new \block_studentstracker\task\send_message();
        $task->set_custom_data([
            'userfrom' => $USER->id,
            'users' => array_map(fn($u) => $u->id, $users),
            'courseid' => $params['courseid'],
            'message' => $params['message'],
        ]);
        \core\task\manager::queue_adhoc_task($task, true);

        return ['success' => true];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function send_message_returns() {
        return new external_single_structure(
            ['success' => new external_value(PARAM_INT, 'Message sent or not')]
        );
    }
}
