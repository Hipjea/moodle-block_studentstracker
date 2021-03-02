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
 * @copyright  2021 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['studentstracker:addinstance'] = 'Add a new block Students tracker';
$string['pluginname'] = 'Students tracker';
$string['blocktitle'] = 'Block title';
$string['days'] = 'Number of days to start tracking';
$string['days_desc'] = 'Number of days to start tracking the users that did not access the course.';
$string['days_critical'] = 'Critical limit (in days)';
$string['days_critical_desc'] = '';
$string['color_days'] = 'Days color';
$string['color_days_critical'] = 'Critical days color';
$string['color_never'] = 'No access color';
$string['header'] = 'Text near the users counter';
$string['text_header'] = 'absent users';
$string['header_fine'] = 'Text if everything\'s fine';
$string['text_header_fine'] = 'Everything is fine';
$string['text_never'] = 'Text if the user never accessed the course at all';
$string['text_never_content'] = 'no access';
$string['footer'] = 'Footer text';
$string['text_footer'] = 'Contact invitation message';
$string['text_footer_content'] = 'Contact them';
$string['role'] = 'Roles to track';
$string['roles'] = 'Roles allowed to see the block';
$string['groups'] = 'Groups to track';
$string['nogroups'] = 'None (all users)';
$string['truncate'] = 'Show only n results';
$string['truncate_desc'] = 'Truncate the results to show only a particular amount of results, the others being toggleable.';
$string['roleview'] = "Roles allowed to see block";
$string['roleview_desc'] = 'Select the roles that are allowed to see the block.';
$string['roletrack'] = "Roles to track";
$string['roletrack_desc'] = 'Select the roles that are tracked.';
$string['color_days_desc'] = 'Color of the first level of warning (few days without loggin).';
$string['color_days_critical_desc'] = 'Color of the critical level of warning.';
$string['color_never_desc'] = 'Color of the users that never accessed the course.';
$string['studentstracker:view'] = 'View Students tracker results';
$string['studentstracker:editadvance'] = 'Advanced editing within block';
$string['lastaccess_date'] = 'Last access date';
$string['dateformat'] = 'Date format';
$string['dateformat_desc'] = 'Change the date format used in the block\'s display';
$string['date_asc'] = 'Date ascending';
$string['date_desc'] = 'Date descending';
$string['sorting'] = 'Sorting criteria';
$string['sorting_desc'] = 'Sorting criteria to list the users.';
$string['excludeolder'] = 'Exclude results older than n days';
$string['excludeolder_desc'] = 'Exclude results older than n days from the display. Leave it empty to not exclude any result.';