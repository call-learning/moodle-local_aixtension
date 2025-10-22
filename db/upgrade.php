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

    if ($oldversion < 2025102200) {
        // Define table local_aixtension_action_stt to be created.
        $table = new xmldb_table('local_aixtension_action_stt');

        // Adding fields to table local_aixtension_action_stt.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('storedfileid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('language', XMLDB_TYPE_CHAR, '254', null, null, null, null);
        $table->add_field('timestamps', XMLDB_TYPE_INTEGER, '3', null, null, null, null);
        $table->add_field('diarisation', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('format', XMLDB_TYPE_INTEGER, '2', null, null, null, '0');
        $table->add_field('model', XMLDB_TYPE_CHAR, '1024', null, null, null, null);
        $table->add_field('additionaloptions', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table local_aixtension_action_stt.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for local_aixtension_action_stt.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Aixtension savepoint reached.
        upgrade_plugin_savepoint(true, 2025102200, 'local', 'aixtension');
    }
    if ($oldversion < 2025102201) {
        // Define field text to be added to local_aixtension_action_stt.
        $table = new xmldb_table('local_aixtension_action_stt');
        $field = new xmldb_field('text', XMLDB_TYPE_TEXT, null, null, null, null, null, 'storedfileid');

        // Conditionally launch add field text.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Aixtension savepoint reached.
        upgrade_plugin_savepoint(true, 2025102201, 'local', 'aixtension');
    }

    if ($oldversion < 2025102202) {
        // Define field textsegments to be added to local_aixtension_action_stt.
        $table = new xmldb_table('local_aixtension_action_stt');
        $field = new xmldb_field('textsegments', XMLDB_TYPE_TEXT, null, null, null, null, null, 'text');

        // Conditionally launch add field textsegments.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('detectedlanguage', XMLDB_TYPE_CHAR, '254', null, null, null, null, 'textsegments');

        // Conditionally launch add field detectedlanguage.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field confidence to be added to local_aixtension_action_stt.
        $table = new xmldb_table('local_aixtension_action_stt');
        $field = new xmldb_field('confidence', XMLDB_TYPE_INTEGER, '4', null, null, null, null, 'detectedlanguage');

        // Conditionally launch add field confidence.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Aixtension savepoint reached.
        upgrade_plugin_savepoint(true, 2025102202, 'local', 'aixtension');
    }
    if ($oldversion < 2025102203) {
        // Define field duration to be added to local_aixtension_action_stt.
        $table = new xmldb_table('local_aixtension_action_stt');
        $field = new xmldb_field('duration', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, 'text');

        // Conditionally launch add field duration.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Aixtension savepoint reached.
        upgrade_plugin_savepoint(true, 2025102203, 'local', 'aixtension');
    }

    if ($oldversion < 2025102204) {
        // Define field confidencestats to be added to local_aixtension_action_stt.
        $table = new xmldb_table('local_aixtension_action_stt');
        $field = new xmldb_field('confidencestats', XMLDB_TYPE_TEXT, null, null, null, null, null, 'confidence');

        // Conditionally launch add field confidencestats.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Aixtension savepoint reached.
        upgrade_plugin_savepoint(true, 2025102204, 'local', 'aixtension');
    }
    return true;
}
