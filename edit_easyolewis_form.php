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
 * Defines the editing form for the easyolewis question type.
 *
 * @package    qtype
 * @subpackage easyolewis
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/shortanswer/edit_shortanswer_form.php');

class qtype_easyolewis_edit_form extends qtype_shortanswer_edit_form {

    protected function definition_inner($mform) {
        global $PAGE, $CFG;

        $PAGE->requires->css('/question/type/easyolewis/easyolewis_styles.css');
        $mform->addElement('hidden', 'usecase', 1);
        $mform->setType('usecase', PARAM_INT);

        $mform->addElement('static', 'answersinstruct',
        get_string('correctanswers', 'qtype_easyolewis'),
        get_string('filloutoneanswer', 'qtype_easyolewis'));
        $mform->closeHeaderBefore('answersinstruct');

        $menu = array(
            get_string('casecharge', 'qtype_easyolewis'),
            get_string('caselonepairs', 'qtype_easyolewis')
        );
        $mform->addElement('select', 'chargeorlonepairs',
                get_string('casechargeorlonepairs', 'qtype_easyolewis'), $menu);

        $mform->addElement('html', html_writer::start_tag('div', array('style' => 'width:650px;', 'id' => 'appletdiv')));
        $mform->addElement('html', html_writer::start_tag('div', array('style' => 'float: right;font-style: italic ;')));
        $mform->addElement('html', html_writer::start_tag('small'));
        $easyolewishomeurl = 'http://www.chemaxon.com';
        $mform->addElement('html', html_writer::link($easyolewishomeurl,
            get_string('easyolewiseditor', 'qtype_easyolewis')));
        $mform->addElement('html', html_writer::empty_tag('br'));
        $mform->addElement('html', html_writer::tag('span', get_string('author', 'qtype_easyolewis'),
            array('class' => 'easyolewisauthor')));
        $mform->addElement('html', html_writer::end_tag('small'));
        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', html_writer::empty_tag('br'));

        $marvinconfig = get_config('qtype_easyolewis_options');
        $marvinpath = $marvinconfig->path;

        // Add applet to page!
/*        $jsmodule = array(
            'name'     => 'qtype_easyolewis',
            'fullpath' => '/question/type/easyolewis/easyolewis_script.js',
            'requires' => array(),
            'strings' => array(
                array('enablejava', 'qtype_easyolewis')
            )
        ); */

        $PAGE->requires->js_init_call('M.qtype_easyolewis.insert_applet',
                                      array($CFG->wwwroot, $marvinpath),
                                      true);

        // Add structure to applet.
       /* $jsmodule = array(
            'name'     => 'qtype_easyolewis',
            'fullpath' => '/question/type/easyolewis/easyolewis_script.js',
            'requires' => array(),
            'strings' => array(
                array('enablejava', 'qtype_easyolewis')
            )
        );*/

        $PAGE->requires->js_init_call('M.qtype_easyolewis.insert_structure_into_applet',
                                      array(),
                                      true);

        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_easyolewis', '{no}'),
                question_bank::fraction_options());
        $this->add_interactive_settings();
        $PAGE->requires->js_init_call('M.qtype_easyolewis.init_getanswerstring', array($CFG->version));
    }

    protected function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption) {

        $repeated = parent::get_per_answer_fields($mform, $label, $gradeoptions,
        $repeatedoptions, $answersoption);
        $scriptattrs = 'class = id_insert';
        $insertbutton = $mform->createElement('button', 'insert', get_string('insertfromeditor', 'qtype_easyolewis'), $scriptattrs);
        array_splice($repeated, 2, 0, array($insertbutton));
        return $repeated;
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        return $question;
    }

    public function qtype() {
        return 'easyolewis';
    }
}
