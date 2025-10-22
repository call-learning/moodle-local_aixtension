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
use core\exception\moodle_exception;
use core_ai\aiactions\responses\response_base;
use stored_file;

/**
 * Generate audio from text
 *
 * @package    local_aixtension
 * @copyright  Laurent David <laurent@call-learning.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class convert_speech_to_text extends \core_ai\aiactions\base {
    /** @var array $inputmeta Minimal metadata we keep about the input file. */
    protected array $inputmeta = [];

    /**
     * Create a new instance of the text_to_speech action.
     *
     * It’s responsible for performing any setup tasks,
     * such as getting additional data from the database etc.
     *
     * @param int $contextid The context id the action was created in.
     * @param int $userid The user id making the request.
     * @param stored_file $audiofile
     * @param string|null $language
     * @param string|null $model
     * @param array $additionaloptions
     */
    public function __construct(
        int $contextid,
        /** @var int The user id requesting the action. */
        protected int $userid,
        /** @var stored_file The audio file to transcribe */
        protected stored_file $audiofile,
        /** @var string|null BCP-47 language code (e.g., 'fr-FR') */
        protected ?string $language = null,
        /** @var string|null $model Provider/model hint (not required) */
        protected ?string $model = null,
        /** @var array $additionaloptions Additional provider-specific options */
        protected array $additionaloptions = []
    ) {
        parent::__construct($contextid);
        $this->inputmeta = $this->probe_audio($audiofile);
        $this->validate();
    }

    #[\Override]
    public function store(response_base $response): int {
        global $DB;

        $responsearr = $response->get_response_data();
        $text = (string)($responsearr['text'] ?? '');
        if ($text === '') {
            throw new moodle_exception('invalidresponse', 'error', '', 'Missing transcribed text.');
        }
        $record = new \stdClass();
        $record->userid = $this->userid;
        $record->storedfileid = $this->audiofile->get_id();
        $record->text = $text;
        $record->stats = json_encode($responsearr['stats'] ?? []);
        $record->detectedlanguage = $responsearr['detectedlanguage'] ?? '';
        $record->language = $this->language ?? '';
        $record->model = $this->model ?? '';
        $record->additionaloptions = json_encode($this->additionaloptions);
        return $DB->insert_record($this->get_tablename(), $record);
    }

    #[\Override]
    public function get_tablename(): string {
        return 'local_aixtension_action_stt';
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
        return \local_aixtension\aiactions\responses\response_convert_speech_to_text::class;
    }

    /**
     * Validate the action parameters.
     *
     * @return void
     * @throws moodle_exception
     */
    protected function validate(): void {
        // MIME allowlist: keep it short and universal.
        $allowed = [
            'audio/wav', 'audio/x-wav', 'audio/mpeg', 'audio/mp3',
            'audio/mp4', 'audio/webm', 'audio/ogg', 'audio/opus', 'audio/flac',
        ];
        if (!in_array($this->inputmeta['mime'], $allowed, true)) {
            throw new moodle_exception('invalidfiletype', 'error', '', 'Unsupported audio type: ' . $this->inputmeta['mime']);
        }
    }

    /**
     * Probe the audio file to extract metadata.
     *
     * @param stored_file $f The audio file to probe.
     * @return array The extracted metadata.
     * @throws \moodle_exception If the file is invalid.
     */
    protected function probe_audio(stored_file $f): array {
        $size = $f->get_filesize();
        if ($size <= 0) {
            throw new moodle_exception('invalidfile', 'error', '', 'Empty audio file.');
        }
        return [
            'mime'  => $f->get_mimetype(),
            'bytes' => $size,
        ];
    }
}
