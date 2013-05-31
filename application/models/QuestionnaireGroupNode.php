<?php

/**
 * Webenq_Model_QuestionnaireGroupNode
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    Webenq_Models
 * @subpackage ##SUBPACKAGE##
 * @author     Nivocer <webenq@nivocer.com>
 * @version    SVN: $Id: Builder.php,v 1.2 2011/07/12 13:39:03 bart Exp $
 */
class Webenq_Model_QuestionnaireGroupNode extends Webenq_Model_Base_QuestionnaireGroupNode
{
public function render($format)
    {
        switch ($format){
            case 'previewTab':
                // no render
                return '';
            break;
            default:
                return '';
            break;
        }
    }
    public function toArray($deep=true, $prefixKey=false){
        $return= parent::toArray($deep, $prefixKey);
//all children in one level
        foreach ($this->getNode()->getDescendants() as $key=>$descendant) {
            //hack add questionnaireElement & answerDomain to $descendant
            $temp=$descendant->QuestionnaireElement;
            $temp=$descendant->QuestionnaireElement->Translation;
            $temp=$descendant->QuestionnaireElement->AnswerDomain;
            $temp=$descendant->QuestionnaireElement->AnswerDomain->Translation;
            $return['descendants'][]=$descendant->QuestionnaireElement->toArray();
        }
    return $return;
    }
}