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
 * easyolewis question renderer class.
 *
 * @package    qtype
 * @subpackage easyolewis
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


class qtype_easyolewis_renderer extends qtype_renderer {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {
        global $CFG, $PAGE;

        $question = $qa->get_question();
        $questiontext = $question->format_questiontext($qa);
        $placeholder = false;
        /*  Hack to show user/correct answers!
        $myanswer_id = "my_answer".$qa->get_slot();
        $correctanswer_id = "correct_answer".$qa->get_slot();  */

        if (preg_match('/_____+/', $questiontext, $matches)) {
            $placeholder = $matches[0];
        }

        $result = '';
        /*     Hack to add buttons for display user/correct answers
        if ($options->readonly) {
        $name2 = 'EASYOLEWIS'.$qa->get_slot();
        $result .= html_writer::tag('input', '', array('type' => 'button','value' => 'Show My Response',
        'onClick' => 'var s = document.getElementById("'.$myanswer_id.'").value;
        document.getElementById("'.$name2.'").setMol(s, "mrv");'));
        $result .= html_writer::tag('input', '', array('type' => 'button','value' => 'Show Correct Answer',
        'onClick' => 'var s = document.getElementById("'.$correctanswer_id.'").value;
        document.getElementById("'.$name2.'").setMol(s, "mrv");'));
        $result .= html_writer::tag('BR', '', array());
        }  */

        $toreplaceid = 'applet'.$qa->get_slot();
        $toreplace = html_writer::tag('span',
                                      get_string('enablejavaandjavascript', 'qtype_easyolewis'),
                                      array('id' => $toreplaceid));

        if ($placeholder) {
            $toreplace = html_writer::tag('span',
                                      get_string('enablejavaandjavascript', 'qtype_easyolewis'),
                                      array('class' => 'ablock'));
            $questiontext = substr_replace($questiontext,
                                            $toreplace,
                                            strpos($questiontext, $placeholder),
                                            strlen($placeholder));
        }

        $result .= html_writer::tag('div', $questiontext, array('class' => 'qtext'));

        if (!$placeholder) {
            $answerlabel = html_writer::tag('span', get_string('answer', 'qtype_easyolewis', ''),
                                            array('class' => 'answerlabel'));
            $result .= html_writer::tag('div', $answerlabel.$toreplace, array('class' => 'ablock'));
        }

        if ($qa->get_state() == question_state::$invalid) {
            $lastresponse = $this->get_last_response($qa);
            $result .= html_writer::nonempty_tag('div',
                                                $question->get_validation_error($lastresponse),
                                                array('class' => 'validationerror'));
        }

        if (!$options->readonly) {
                $question = $qa->get_question();
                $answer = $question->get_matching_answer($question->get_correct_response());

            if ($question->chargeorlonepairs == 0) {
                $strippedxml  = $this->remove_xmlattribute($answer->answer, 'formalCharge');
            } else {
                $strippedxml  = $this->remove_xmlattribute($answer->answer, 'lonePair');
                $strippedxml  = $this->remove_xmlattribute($strippedxml , 'radical');
            }

            $strippedanswerid = "stripped_answer".$qa->get_slot();
            $result .= html_writer::tag('textarea', $strippedxml,
            array('id' => $strippedanswerid, 'style' => 'display:none;', 'name' => $strippedanswerid));
        }

        if ($options->readonly) {
            $currentanswer = $qa->get_last_qt_var('answer');
            $strippedanswerid = "stripped_answer".$qa->get_slot();
            $result .= html_writer::tag('textarea', $currentanswer,
            array('id' => $strippedanswerid, 'style' => 'display:none;', 'name' => $strippedanswerid));
            $strippedxml = $currentanswer;
            $answer = $question->get_matching_answer($question->get_correct_response());

            /* Buttons to show correct and user answers currently disabled!
            $result .= html_writer::tag('textarea', $qa->get_last_qt_var('answer'), array('id' => $myanswer_id, 'name' => $myanswer_id, 'style' => 'display:none;'));
            $result .= html_writer::tag('textarea', $answer->answer, array('id' => $correctanswer_id, 'name' => $correctanswer_id, 'style' => 'display:none;'));
            */
        }

        $result .= html_writer::tag('div',
                                    $this->hidden_fields($qa),
                                    array('class' => 'inputcontrol'));
        $this->require_js($toreplaceid, $qa, $options->readonly, $options->correctness,
        $CFG->qtype_easyolewis_options, $strippedxml );
        return $result;
    }

    protected function remove_xmlattribute($xmlstring, $attribute) {
        $xml = simplexml_load_string($xmlstring);
        unset($xml->MDocument[0]->MChemicalStruct[0]->molecule->atomArray[0][$attribute]);
        return $xml->saveXML();
    }


    protected function require_js($toreplaceid, question_attempt $qa, $readonly, $correctness, $appletoptions, $strippedxml ) {
        global $PAGE, $CFG;

        $marvinconfig = get_config('qtype_easyolewis_options');
        $marvinpath = $marvinconfig->path;

        $jsmodule = array(
            'name'     => 'qtype_easyolewis',
            'fullpath' => '/question/type/easyolewis/module.js',
            'requires' => array(),
            'strings' => array(
                array('enablejava', 'qtype_easyolewis')
            )
        );
        $topnode = 'div.que.easyolewis#q'.$qa->get_slot();
        $appleturl = new moodle_url('appletlaunch.jar');
        if ($correctness) {
            $feedbackimage = $this->feedback_image($this->fraction_for_last_response($qa));
        } else {
            $feedbackimage = '';
        }
        $name = 'EASYOLEWIS'.$qa->get_slot();
        $appletid = 'easyolewis'.$qa->get_slot();
        $strippedanswerid = "stripped_answer".$qa->get_slot();
        $PAGE->requires->js_init_call('M.qtype_easyolewis.insert_easyolewis_applet',
                                      array($toreplaceid,
                                            $name,
                                            $appletid,
                                            $topnode,
                                            $appleturl->out(),
                                            $feedbackimage,
                                            $readonly,
                                            $appletoptions,
                                            $strippedanswerid, $strippedxml, $CFG->wwwroot, $marvinpath),
                                      false,
                                      $jsmodule);
    }

    protected function fraction_for_last_response(question_attempt $qa) {
        $question = $qa->get_question();
        $lastresponse = $this->get_last_response($qa);
        $answer = $question->get_matching_answer($lastresponse);
        if ($answer) {
            $fraction = $answer->fraction;
        } else {
            $fraction = 0;
        }
        return $fraction;
    }


    protected function get_last_response(question_attempt $qa) {
        $question = $qa->get_question();
        $responsefields = array_keys($question->get_expected_data());
        $response = array();
        foreach ($responsefields as $responsefield) {
            $response[$responsefield] = $qa->get_last_qt_var($responsefield);
        }
        return $response;
    }

    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();

        $answer = $question->get_matching_answer($this->get_last_response($qa));
        if (!$answer) {
            return '';
        }

        $feedback = '';
        if ($answer->feedback) {
            $feedback .= $question->format_text($answer->feedback, $answer->feedbackformat,
                    $qa, 'question', 'answerfeedback', $answer->id);
        }
        return $feedback;
    }

    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();
        $answer = $question->get_matching_answer($question->get_correct_response());
        if (!$answer) {
            return '';
        }
    }

    protected function hidden_fields(question_attempt $qa) {
        $question = $qa->get_question();
        $hiddenfieldshtml = '';
        $inputids = new stdClass();
        $responsefields = array_keys($question->get_expected_data());
        foreach ($responsefields as $responsefield) {
            $hiddenfieldshtml .= $this->hidden_field_for_qt_var($qa, $responsefield);
        }
        return $hiddenfieldshtml;
    }
    protected function hidden_field_for_qt_var(question_attempt $qa, $varname) {
        $value = $qa->get_last_qt_var($varname, '');
        $fieldname = $qa->get_qt_field_name($varname);
        $attributes = array('type' => 'hidden',
                            'id' => str_replace(':', '_', $fieldname),
                            'class' => $varname,
                            'name' => $fieldname,
                            'value' => $value);
        return html_writer::empty_tag('input', $attributes);
    }
}
