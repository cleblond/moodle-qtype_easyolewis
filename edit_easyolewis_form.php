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
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * easyolewis question editing form definition.
 *
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/question/type/shortanswer/edit_shortanswer_form.php');


/**
 * Calculated question type editing form definition.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_easyolewis_edit_form extends qtype_shortanswer_edit_form {

    protected function definition_inner($mform) {
		global $PAGE, $CFG;
		
		$PAGE->requires->js('/question/type/easyolewis/easyolewis_script.js');
		$PAGE->requires->css('/question/type/easyolewis/easyolewis_styles.css');
        $mform->addElement('hidden', 'usecase', 1);



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



       
		
//		$appleturl = new moodle_url('/question/type/easyolewis/easyolewis/easyolewis.jar');


		//get the html in the easyolewislib.php to build the applet
//	    $easyolewisbuildstring = "\n<applet code=\"easyolewis.class\" name=\"easyolewis\" id=\"easyolewis\" archive =\"$appleturl\" width=\"460\" height=\"335\">" .
//	  "\n<param name=\"options\" value=\"" . $CFG->qtype_easyolewis_options . "\" />" .
//      "\n" . get_string('javaneeded', 'qtype_easyolewis', '<a href="http://www.java.com">Java.com</a>') .
//	  "\n</applet>";


	    $easyolewisbuildstring = "\n<script LANGUAGE=\"JavaScript1.1\" SRC=\"../../marvin/marvin.js\"></script>".

"<script LANGUAGE=\"JavaScript1.1\">



msketch_name = \"MSketch\";
msketch_begin(\"../../marvin\", 460, 335);
msketch_param(\"menuconfig\", \"../eolms/question/type/easyolewis/customization_mech_instructor.xml\");
msketch_param(\"implicitH\", \"off\");
msketch_param(\"lonePairsAutoCalc\", \"false\");
msketch_param(\"lonePairsVisible\", \"true\");
msketch_param(\"valenceCheckEnabled\", \"false\");
msketch_param(\"valencePropertyVisible\", \"false\");
msketch_param(\"rendering\", \"wireframe\");
msketch_param(\"java_arguments\", \"-Djnlp.packEnabled=true\");
msketch_param(\"background\", \"#ffffff\");
msketch_param(\"molbg\", \"#ffffff\");
msketch_end();
</script> ";






        //output the marvin applet
        $mform->addElement('html', html_writer::start_tag('div', array('style'=>'width:650px;')));
		$mform->addElement('html', html_writer::start_tag('div', array('style'=>'float: right;font-style: italic ;')));
		$mform->addElement('html', html_writer::start_tag('small'));
		$easyolewishomeurl = 'http://www.chemaxon.com';
		$mform->addElement('html', html_writer::link($easyolewishomeurl, get_string('easyolewiseditor', 'qtype_easyolewis')));
		$mform->addElement('html', html_writer::empty_tag('br'));
		$mform->addElement('html', html_writer::tag('span', get_string('author', 'qtype_easyolewis'), array('class'=>'easyolewisauthor')));
		$mform->addElement('html', html_writer::end_tag('small'));
		$mform->addElement('html', html_writer::end_tag('div'));
		$mform->addElement('html',$easyolewisbuildstring);
		$mform->addElement('html', html_writer::end_tag('div'));


//		$mform->addElement('html', html_writer::tag('h1', 'Do you want students to determine the charge or add the correct # of lone pairs?'));

		$mform->addElement('html', html_writer::empty_tag('br'));

		

      ///add structure to applet
	$jsmodule = array(
            'name'     => 'qtype_easyolewis',
            'fullpath' => '/question/type/easyolewis/easyolewis_script.js',
            'requires' => array(),
            'strings' => array(
                array('enablejava', 'qtype_easyolewis')
            )
        );


	$PAGE->requires->js_init_call('M.qtype_easyolewis.insert_structure_into_applet',
                                      array(),		
                                      true,
                                      $jsmodule);






        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_easyolewis', '{no}'),
                question_bank::fraction_options());

        $this->add_interactive_settings();
    }
	
	protected function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption) {
		
        $repeated = parent::get_per_answer_fields($mform, $label, $gradeoptions,
                $repeatedoptions, $answersoption);
		
		//construct the insert button
//crl mrv		$scriptattrs = 'onClick = "getSmilesEdit(this.name, \'cxsmiles:u\')"';
		$scriptattrs = 'onClick = "getSmilesEdit(this.name, \'mrv\')"';


        $insert_button = $mform->createElement('button','insert',get_string('insertfromeditor', 'qtype_easyolewis'),$scriptattrs);
        array_splice($repeated, 2, 0, array($insert_button));

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
