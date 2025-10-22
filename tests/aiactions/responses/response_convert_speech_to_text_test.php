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

namespace local_aixtension\aiactions\responses;


/**
 * Test response_convert_speech_to_text action methods.
 *
 * @package    local_aixtension
 * @copyright  2025 Laurent David <laurent@call-learning.fr>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_aixtension\aiactions\responses\response_convert_speech_to_text
 */
final class response_convert_speech_to_text_test extends \advanced_testcase {
    /**
     * Test get_basename.
     */
    public function test_get_success(): void {
        $actionresponse = new response_convert_speech_to_text(
            success: true,
        );

        $this->assertTrue($actionresponse->get_success());
        $this->assertEquals('convert_speech_to_text', $actionresponse->get_actionname());
    }

    /**
     * Test constructor with error.
     */
    public function test_construct_error(): void {
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Error code and message must exist in an error response.');
        new response_convert_speech_to_text(
            success: false,
        );
    }

    /**
     * Test set_response_data.
     */
    public function test_set_response_data(): void {
        $this->resetAfterTest();
        $body = [
            'task' => 'transcribe',
            'duration' => 42.7,
            'language' => 'english',
            'text' => "Agent: Thanks for calling OpenAI support.\nCustomer: Hi, I need help with diarization.",
            'segments' => [
                [
                    'type' => 'transcript.text.segment',
                    'id' => 'seg_001',
                    'start' => 0.0,
                    'end' => 5.2,
                    'text' => 'Thanks for calling OpenAI support.',
                    'speaker' => 'agent',
                    "avg_logprob" => -0.2860786020755768,
                ],
                [
                    'type' => 'transcript.text.segment',
                    'id' => 'seg_002',
                    'start' => 5.2,
                    'end' => 12.8,
                    'text' => 'Hi, I need help with diarization.',
                    'speaker' => 'A',
                ],
            ],
            'usage' => [
                'type' => 'duration',
                'seconds' => 43,
            ],
            'logprobs' =>
                [
                    [
                        'bytes' => [
                            4, 8, 5, 6, 7,
                        ],
                        'token' => 'Thanks',
                        'logprob' => -0.280123,
                    ],
                    [
                        'bytes' => [
                            4, 8, 5, 6, 7,
                        ],
                        'token' => 'for',
                        'logprob' => -0.5,
                    ],
                ],
        ];
        $actionresponse = new response_convert_speech_to_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $this->assertEquals(
            [
                [
                    'start' => 0.0,
                    'end' => 5.2,
                    'text' => 'Thanks for calling OpenAI support.',
                    "confidence" => 71,
                ],
                [
                    'start' => 5.2,
                    'end' => 12.8,
                    'text' => 'Hi, I need help with diarization.',
                    'confidence' => 0,
                ],
            ],
            $actionresponse->get_response_data()['segments']
        );
        $this->assertEquals(
            "Agent: Thanks for calling OpenAI support.\nCustomer: Hi, I need help with diarization.",
            $actionresponse->get_response_data()['text']
        );
        $this->assertEquals(42.7, $actionresponse->get_response_data()['duration']);
        $this->assertEquals(61, $actionresponse->get_response_data()['confidence']);
        $this->assertEquals(
            [
                [
                    'token' => 'Thanks',
                    'confidence' => 72,
                ],
                [
                    'token' => 'for',
                    'confidence' => 50,
                ],
            ],
            $actionresponse->get_response_data()['confidence_stats']
        );
        $this->assertEquals('english', $actionresponse->get_response_data()['detected_language']);
    }
}
