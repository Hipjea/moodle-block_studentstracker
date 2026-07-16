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
 * Studentstracker block upgrade code.
 *
 * @package    block_studentstracker
 * @copyright  2025 Pierre Duverneix
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade code for installation of block_studentstracker.
 *
 * @param int $oldversion The version we are upgrading form.
 * @return bool Returns true on success.
 * @throws coding_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_block_studentstracker_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025051200) {
        $table = new xmldb_table('block_studentstracker_notes');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('note', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('authorid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);
        $table->add_index('authorid', XMLDB_INDEX_NOTUNIQUE, ['authorid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025051200, 'block', 'studentstracker');
        unset($table);
    }

    if ($oldversion < 2026071600) {
        $table = new xmldb_table('block_studentstracker_messagetemplate');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('message', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2026071600, 'block', 'studentstracker');
        unset($table);
    }

    if ($oldversion < 2026071700) {
        // Drop block_studentstracker_notes indexes and add proper foreign keys.
        $table = new xmldb_table('block_studentstracker_notes');

        $index = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $index = new xmldb_index('authorid', XMLDB_INDEX_NOTUNIQUE, ['authorid']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $dbman->add_key(
            $table,
            new xmldb_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id'])
        );

        $dbman->add_key(
            $table,
            new xmldb_key('authorid', XMLDB_KEY_FOREIGN, ['authorid'], 'user', ['id'])
        );

        upgrade_plugin_savepoint(true, 2026071700, 'block', 'studentstracker');
    }

    return true;
}
