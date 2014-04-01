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
 * easyolewis Molecular Editor question definition class.
 *
 * @package    qtype
 * @subpackage easyolewis
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/shortanswer/question.php');


/**
 * Represents a easyolewis question.
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_easyolewis_question extends qtype_shortanswer_question {
	// all comparisons in easyolewis are case sensitive
	public function compare_response_with_answer(array $response, question_answer $answer) {
	global $DB;
//	$question = $qa->get_question();
//	$DB->get_field('question_answer', $return, array $conditions, $strictness=IGNORE_MISSING)
//	echo "answer_id=".$answer->id;
        $result = $DB->get_records('question_answers',array('id'=>$answer->id));
//	$questiondata = question_bank::load_question($questionid);
//	$id = optional_param('id', 0, PARAM_INT);
//	echo "id=".$id;
	
//	print_r($result);
	$question=$result[$answer->id]->question;
//	echo "question=$question";
	$qid=$result[$answer->id]->id;
	$chargeorlonepairs=$DB->get_field('question_easyolewis', 'chargeorlonepairs', array('question'=>$question), $strictness=IGNORE_MISSING);
//	 $result2 = $DB->get_records('question_easyolewis',array('question'=>$question));
//	print_r($result2);
	
//	$chargeorlonepairs=$result2[]->chargeorlonepairs;
//	echo "corlp=".$chargeorlonepairs;

//	echo "chargeorlonepairs".$question->chargeorlonepairs; 
        
	if($chargeorlonepairs==0){
//	echo "here";
	$attribute="formalCharge";}
	else{
	$attribute="lonePair";}	
	

        return self::compare_string_with_wildcard(self::get_xmlattribute($response['answer'], $attribute), self::get_xmlattribute($answer->answer, $attribute), false);
//        return self::compare_string_with_wildcard($arrowsusrall, $arrowsansall, false);



    }
	
	public function get_expected_data() {

        return array('answer' => PARAM_RAW, 'easyolewis' => PARAM_RAW, 'mol' => PARAM_RAW);
    }



 	public function get_xmlattribute($string, $attribute){
	

	$xml = simplexml_load_string($string);
//	echo $xmlstring."nextto";
	return $xml->MDocument[0]->MChemicalStruct[0]->molecule->atomArray[0][$attribute];
//	echo "here";
//	return $xml->saveXML();

	}














}
