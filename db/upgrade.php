<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Upgrade script for the AI Extension plugin.
 *
 * @param int $oldversion The old version of the plugin.
 * @return bool True on success.
 * @throws ddl_exception
 * @package    local_aixtension
 * @copyright  2025 Laurent David <laurent@call-learning.fr>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function xmldb_local_aixtension_upgrade(int $oldversion): bool {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025102900) {
        // Define table local_aixtension_action_stt to be created.
        $table = new xmldb_table('local_aixtension_action_stt');

        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('storedfileid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('text', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('stats', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('detectedlanguage', XMLDB_TYPE_CHAR, '254', null, null, null, null);
        $table->add_field('language', XMLDB_TYPE_CHAR, '254', null, null, null, null);
        $table->add_field('model', XMLDB_TYPE_CHAR, '1024', null, null, null, null);
        $table->add_field('additionaloptions', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table local_aixtension_action_stt.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for local_aixtension_action_stt.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Aixtension savepoint reached.
        upgrade_plugin_savepoint(true, 2025102900, 'local', 'aixtension');
    }
    return true;
}
