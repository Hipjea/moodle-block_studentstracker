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
 * Studentstracker block plugin's task class.
 *
 * @package    block_studentstracker
 * @copyright  2026 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_studentstracker\task;

defined('MOODLE_INTERNAL') || die();

class send_message extends \core\task\adhoc_task {
    /**
     * Execute the task.
     */
    public function execute() {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/message/lib.php');

        $data = $this->get_custom_data();
        $userfrom = $DB->get_record('user', ['id' => $data->userfrom], '*', MUST_EXIST);

        foreach ($data->users as $userid) {
            $userto = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

            $message = new \core\message\message();

            $message->component = 'moodle';
            $message->name = 'instantmessage';
            $message->courseid = $data->courseid;
            $message->userfrom = $userfrom;
            $message->userto = $userto;
            $message->subject = '';
            $message->fullmessage = $data->message;
            $message->fullmessageformat = FORMAT_PLAIN;
            $message->fullmessagehtml = '';
            $message->smallmessage = shorten_text($data->message, 50);
            $message->notification = 0;

            message_send($message);
        }
    }
}
