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

use local_aixtension\aiactions\responses\response_convert_speech_to_text;
use stored_file;

/**
 * Test convert_speech_to_text action methods.
 *
 * @package    local_aixtension
 * @copyright  2025 Laurent David <laurent@call-learning.fr>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \local_aixtension\aiactions\convert_speech_to_text
 * @covers \local_aixtension\aiactions\convert_speech_to_text
 */
final class convert_speech_to_text_test extends \advanced_testcase {
    /**
     * Test constructor method.
     */
    public function test_constructor(): void {
        global $CFG;
        $this->resetAfterTest();
        $file = $this->setup_file($CFG->dirroot . '/local/aixtension/tests/fixtures/hello_world.mp3');
        $action = new convert_speech_to_text(
            contextid: \context_system::instance()->id,
            userid: 1,
            audiofile: $file,
            language: 'en-US',
        );

        $this->assertEquals(1, $action->get_configuration('userid'));
        $this->assertEquals($file->get_id(), $action->get_configuration('audiofile')->get_id());
        $this->assertEquals('en-US', $action->get_configuration('language'));
    }

    /**
     * Test store method.
     */
    public function test_store(): void {
        global $DB, $CFG;
        $this->resetAfterTest();

        $file = $this->setup_file($CFG->dirroot . '/local/aixtension/tests/fixtures/hello_world.mp3');
        $action = new convert_speech_to_text(
            contextid: \context_system::instance()->id,
            userid: 1,
            audiofile: $file,
            language: 'en-US',
        );

        $body = [
            'detectedlanguage' => 'english',
            'text' => 'Agent: Thanks for calling OpenAI support.\nCustomer: Hi, I need help with diarization.',
            'stats' => [
                [
                    'start' => 0.0,
                    'end' => 5.2,
                    'text' => 'Thanks for calling OpenAI support.',
                    "confidence" => 0.5,
                ],
                [
                    'start' => 5.2,
                    'end' => 12.8,
                    'text' => 'Hi, I need help with diarization.',
                    "confidence" => 0.5,
                ],
            ],
        ];
        $actionresponse = new response_convert_speech_to_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $storeid = $action->store($actionresponse);

        // Check the stored record.
        $record = $DB->get_record('local_aixtension_action_stt', ['id' => $storeid]);
        $this->assertEquals($file->get_id(), $record->storedfileid);
        $this->assertEquals('en-US', $record->language);
        $this->assertEquals(
            'Agent: Thanks for calling OpenAI support.\nCustomer: Hi, I need help with diarization.',
            $record->text
        );
        $this->assertEquals('english', $record->detectedlanguage);
    }

    /**
     * Helper to create a stored_file instance for testing.
     *
     * @return stored_file
     */
    private function setup_file(string $path): stored_file {
        $fs = get_file_storage();
        $filerecord = new \stdClass();
        $filerecord->contextid = 1;
        $filerecord->component = 'core_ai';
        $filerecord->filearea = 'draft';
        $filerecord->itemid = 0;
        $filerecord->filepath = '/';
        $filerecord->filename = 'hello_world.mp3';
        $file = $fs->create_file_from_pathname($filerecord, $path);
        return $file;
    }
}
