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
 * Generate audio from text (TTS) response class.
 *
 * Any method that processes an action must return an instance of this class.
 *
 * @package    core_ai
 * @copyright  Laurent David <laurent@call-learning.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class response_convert_text_to_speech extends response_base {
    /** @var \stored_file|null The URL of the generated audio file. */
    private ?\stored_file $draftfile = null;

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
            actionname: 'convert_text_to_speech',
            errorcode: $errorcode,
            errormessage: $errormessage,
        );
    }

    #[\Override]
    public function set_response_data(array $response): void {
        $fs = get_file_storage();
        if (!empty($response['fileid']) && is_numeric($response['fileid'])) {
            $this->draftfile = $fs->get_file_by_id($response['fileid']);
        } else {
            $this->draftfile = null;
        }
    }

    #[\Override]
    public function get_response_data(): array {
        return [
            'draftfile' => $this->draftfile,
            'filename' => $this->draftfile ? $this->draftfile->get_filename() : '',
            'mimetype' => $this->draftfile ? $this->draftfile->get_mimetype() : '',
            'filesize' => $this->draftfile ? $this->draftfile->get_filesize() : 0,
        ];
    }
}
