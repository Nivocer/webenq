<?php

/**
 * Webenq_Model_QuestionnaireNode
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    Webenq_Models
 * @subpackage ##SUBPACKAGE##
 * @author     Nivocer <webenq@nivocer.com>
 * @version    SVN: $Id: Builder.php,v 1.2 2011/07/12 13:39:03 bart Exp $
 */
class Webenq_Model_QuestionnaireNode extends Webenq_Model_Base_QuestionnaireNode
{
    /*
     * Save this node element
     *
     * Check the linked QuestionnaireElement: if it has changes and is used in
     * more than one QuestionnaireNode, make a copy of the object and update the
     * reference to it
     * @todo check function
     *
     */
    public function xsave(Doctrine_Connection $conn = null)
    {
        if ($this->QuestionnaireElement->isModified()) {
            if (1 < Doctrine_Query::create()
                ->select('COUNT(id)')
                ->from('Webenq_Model_QuestionnaireNode qn')
                ->where('qn.questionnaire_element_id = ?', $this->QuestionnaireElement->id)->count()) {
                $this->QuestionnaireElement = clone $this->QuestionnaireElement;
                $this->QuestionnaireElement->save($conn);
                $this->element_id = $this->QuestionnaireElement->id;
            } else {
                $this->QuestionnaireElement->save($conn);

            }
        }
        parent::save($conn);
    }

    // Reorder the children
    public function reorderChildren($ordering)
    {
        // query findAll where id in $ordering
        // foreach result
        //     moveAsLastChildOf($this->id)
        //
        // cases:
        // - not all children of this node were given
        // - more nodes were given
        // - given nodes were not children of this node
        //   example: drag question from one Likert table into another
    }

    // prepare the output (
    //return  Zend_Form_Element_*
    public function render($format)
    {
        switch ($format){
            case 'previewTab':
                $return='';
                if ($this->getNode()->hasChildren()){
                    foreach ($this->getNode()->getChildren() as $group){
                        $return.=$group->render($format);
                    }
                }
                return $return;
            break;
            default:
                $return=new Zend_Form_Element_Text('dummy-'.$this->id);
                $return->setLabel($this->QuestionnaireElement->name);
                return $return;
            break;
        }
    }
}