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

use core_ai\aiactions\responses\response_base;

/**
 * Convert speech to text (STT) response class.
 *
 * @package    local_aixtension
 * @copyright  Laurent David <laurent@call-learning.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class response_convert_speech_to_text extends response_base {
    /** @var \stored_file|null The URL of the generated audio file. */
    private ?\stored_file $draftfile = null;
    /**
     * @var int $confidence
     */
    private float $confidence;
    /**
     * @var string $detectedlanguage
     */
    private string $detectedlanguage;
    /**
     * @var float $duration
     */
    private float $duration;
    /**
     * @var string $text
     */
    private string $text;
    /**
     * @var array|object[]
     */
    private array $segments;
    /**
     * @var array|array[]
     */
    private array $confidencestats;

    /**
     * Constructor.
     *
     * @param bool $success The success status of the action.
     * @param int $errorcode Error code. Must exist if success is false.
     * @param string $errormessage Error message. Must exist if success is false
     */
    public function __construct(
        bool $success,
        int $errorcode = 0,
        string $errormessage = '',
    ) {
        parent::__construct(
            success: $success,
            actionname: 'convert_speech_to_text',
            errorcode: $errorcode,
            errormessage: $errormessage,
        );
    }

    #[\Override]
    public function set_response_data(array $response): void {
        $segments = [];
        if ($response['segments'] && is_array($response['segments'])) {
            // Make sure segments are objects with required properties.
            $segments = array_map(
                fn($segment) => [
                    'start' => $segment['start'] ?? 0.0,
                    'end' => $segment['end'] ?? 0.0,
                    'text' => $segment['text'] ?? '',
                    'confidence' => $this->log_percent_to_confidence(
                        floatval($segment['avg_logprob'] ?? -1.0)
                    ),
                ],
                $response['segments']
            );
        }
        $this->segments = $segments;
        $this->text = $response['text'] ?? '';
        $this->duration = $response['duration'] ?? 0.0;
        $this->detectedlanguage = $response['language'] ?? '';
        $stats = array_map(
            fn($log) => [
                'token' => $log['token'] ?? '',
                'confidence' => $this->log_percent_to_confidence(
                    floatval($log['logprob'] ?? -1.0)
                ),
            ],
            $response['logprobs'] ?? []
        );
        $this->confidencestats = $stats;

        if (!empty($stats)) {
            // Calculate average confidence.
            $this->confidence = round(
                array_sum(array_column($stats, 'confidence')) /
                max(count($stats), 1)
            );
        } else {
            $this->confidence = 0;
        }
    }

    #[\Override]
    public function get_response_data(): array {
        return [
            'segments' => $this->segments,
            'text' => $this->text,
            'duration' => $this->duration,
            'detected_language' => $this->detectedlanguage,
            'confidence' => $this->confidence,
            'confidence_stats' => $this->confidencestats,
        ];
    }

    /**
     * Convert log probability to confidence percentage.
     *
     * @param float $logprob The log probability from -1 to 0 (very confident 0, no confident -1).
     * @return int The confidence percentage.
     */
    private function log_percent_to_confidence(float $logprob): int {
        $logprobinvert = 1 + $logprob; // From 0 (no confident) to 1 (very confident).
        return intval(round($logprobinvert * 100));
    }
}
