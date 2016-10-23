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
 * Classes to enforce the various access rules that can apply to a quiz.
 *
 * @package    block_studentstracker
 * @copyright  2015 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['studentstracker:addinstance'] = 'Add a new block Students tracker';
$string['pluginname'] = 'Students tracker';
$string['blocktitle'] = 'Block title';
$string['block_studentstracker_days'] = 'Number of days to start tracking';
$string['block_studentstracker_days_critical'] = 'Critical limit (in days)';
$string['block_studentstracker_color_days'] = 'Days color';
$string['block_studentstracker_color_days_critical'] = 'Critical days color';
$string['block_studentstracker_color_never'] = 'No access color';
$string['block_studentstracker_header'] = 'Text near the users counter';
$string['block_studentstracker_text_header'] = 'users absent.';
$string['block_studentstracker_header_fine'] = 'Text when everything\'s fine';
$string['block_studentstracker_text_header_fine'] = 'Everything is fine!';
$string['block_studentstracker_text_never'] = 'Text when no access at all';
$string['block_studentstracker_footer'] = 'Footer text';
$string['block_studentstracker_text_footer'] = 'Contact them!';
$string['block_studentstracker_role'] = 'Track every roles ?';
$string['block_studentstracker_roles'] = 'Roles allowed to see toe block';
$string['block_studentstracker_groups'] = 'Groups to track';