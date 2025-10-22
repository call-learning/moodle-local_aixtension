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

namespace local_aixtension\aiactions;

use local_aixtension\aiactions\responses\response_convert_text_to_speech;

/**
 * Test convert_text_to_speech action methods.
 *
 * @package    local_aixtension
 * @copyright  2025 Laurent David <laurent@call-learning.fr>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \local_aixtension\aiactions\convert_text_to_speech
 * @covers \local_aixtension\aiactions\convert_text_to_speech
 */
final class convert_text_to_speech_test extends \advanced_testcase {
    /**
     * Test constructor method.
     */
    public function test_constructor(): void {
        $contextid = 1;
        $userid = 1;
        $prompttext = 'This is a test prompt';
        $voice = 'voice1';
        $format = 'mp3';
        $action = new convert_text_to_speech(
            contextid: $contextid,
            userid: $userid,
            texttoread: $prompttext,
            voice: $voice,
            format: $format
        );

        $this->assertEquals($userid, $action->get_configuration('userid'));
        $this->assertEquals($prompttext, $action->get_configuration('texttoread'));
        $this->assertEquals($voice, $action->get_configuration('voice'));
        $this->assertEquals($format, $action->get_configuration('format'));
    }

    /**
     * Test store method.
     */
    public function test_store(): void {
        $this->resetAfterTest();
        global $DB;

        $contextid = 1;
        $userid = 1;
        $prompttext = 'This is a test prompt';
        $voice = 'voice1';
        $format = 'mp3';
        $action = new convert_text_to_speech(
            contextid: $contextid,
            userid: $userid,
            texttoread: $prompttext,
            voice: $voice,
            format: $format
        );

        $body = [];
        $actionresponse = new response_convert_text_to_speech(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $storeid = $action->store($actionresponse);

        // Check the stored record.
        $record = $DB->get_record('local_aixtension_action_tts', ['id' => $storeid]);
        $this->assertEquals($prompttext, $record->texttoread);
        $this->assertEquals($voice, $record->voice);
        $this->assertEquals($format, $record->format);
    }
}
