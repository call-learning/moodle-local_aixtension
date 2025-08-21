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
use core_ai\aiactions\responses\response_base;

/**
 * Generate audio from text
 *
 * @package    core_ai
 * @copyright  Laurent David <laurent@call-learning.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class convert_text_to_speech extends \core_ai\aiactions\base {
    /**
     * Create a new instance of the text_to_speech action.
     *
     * It’s responsible for performing any setup tasks,
     * such as getting additional data from the database etc.
     *
     * @param int $contextid The context id the action was created in.
     * @param int $userid The user id making the request.
     * @param string $texttoread The text to synthesize.
     * @param string $voice Optional voice name used for generation.
     * @param string $format Optional output format (e.g. "mp3").
     */
    public function __construct(
        int $contextid,
        /** @var int The user id requesting the action. */
        protected int $userid,
        /** @var string The prompt text used to generate the image */
        protected string $texttoread,
        /** @param string|null $voice Optional voice name used for generation. */
        protected ?string $voice = null,
        /** @param string|null $format Optional output format (e.g. "mp3"). */
        protected ?string $format = null,
    ) {
        parent::__construct($contextid);
    }

    #[\Override]
    public function store(response_base $response): int {
        global $DB;

        $responsearr = $response->get_response_data();
        $record = new \stdClass();
        $record->userid = $this->userid;
        $record->texttoread = $this->texttoread;
        $record->voice = $this->voice;
        $record->format = $this->format;
        return $DB->insert_record($this->get_tablename(), $record);
    }

    #[\Override]
    public function get_tablename(): string {
        return 'local_aixtension_action_tts';
    }

    #[\Override]
    public static function get_name(): string {
        $stringid = 'action_' . self::get_basename();
        return get_string($stringid, 'local_aixtension');
    }

    #[\Override]
    public static function get_description(): string {
        $stringid = 'action_' . self::get_basename() . '_desc';
        return get_string($stringid, 'local_aixtension');
    }

    #[\Override]
    public static function get_system_instruction(): string {
        $stringid = 'action_' . self::get_basename() . '_instruction';

        // If the string doesn't exist, return an empty string.
        if (!get_string_manager()->string_exists($stringid, 'local_aixtension')) {
            return '';
        }
        return get_string($stringid, 'local_aixtension');
    }

    #[\Override]
    public static function get_response_classname(): string {
        return \local_aixtension\aiactions\responses\response_convert_text_to_speech::class;
    }
}
