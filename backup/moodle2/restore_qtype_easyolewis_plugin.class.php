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

/**
 * @package    moodlecore
 * @subpackage backup-moodle2
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

class restore_qtype_easyolewis_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure() {

        $paths = array();

        // This qtype uses question_answers, add them.
        $this->add_question_question_answers($paths);

        // Add own qtype stuff.
        $elename = 'easyolewis';
        // We used get_recommended_name() so this works.
        $elepath = $this->get_pathfor('/easyolewis');
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths.
    }

    /**
     * Process the qtype/easyolewis element
     */
    public function process_easyolewis($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its...
        // question_easyolewis too.
        if ($questioncreated) {
            // Adjust some columns.
            $data->question = $newquestionid;
            // Map sequence of question_answer ids.
            $answersarr = explode(',', $data->answers);
            foreach ($answersarr as $key => $answer) {
                $answersarr[$key] = $this->get_mappingid('question_answer', $answer);
            }
            $data->answers = implode(',', $answersarr);
            // Insert record.
            $newitemid = $DB->insert_record('question_easyolewis', $data);
            // Create mapping.
            $this->set_mapping('question_easyolewis', $oldid, $newitemid);
        }
    }
}
