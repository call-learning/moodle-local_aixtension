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

namespace local_aixtension;

/**
 * Enumeration for speech-to-text output formats.
 *
 * @package    local_aixtension
 * @copyright  2025 Laurent David <laurent@call-learning.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
enum stt_output_format: int {
    case JSON = 0;
    case TEXT = 1;
    case SRT = 2;
    case VTT = 3;

    /**
     * Create an stt_output_format from a string.
     *
     * @param string $format The format string.
     * @return stt_output_format|null The corresponding stt_output_format or null if not found.
     */
    public static function from_string(string $format): ?stt_output_format {
        return match (strtolower($format)) {
            'json' => stt_output_format::JSON,
            'text' => stt_output_format::TEXT,
            'srt'  => stt_output_format::SRT,
            'vtt'  => stt_output_format::VTT,
            default => null,
        };
    }
    /**
     * Convert the stt_output_format to a string.
     *
     * @return string The format string.
     */
    public function to_string(): string {
        return match ($this) {
            stt_output_format::JSON => 'json',
            stt_output_format::TEXT => 'text',
            stt_output_format::SRT  => 'srt',
            stt_output_format::VTT  => 'vtt',
        };
    }
}
