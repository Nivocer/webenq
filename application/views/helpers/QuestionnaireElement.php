<?php
/**
 * WebEnq4
 *
 *  LICENSE
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    Webenq_Reports_Manage
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Helper class for rendering a report element
 * @package    Webenq_Questionnaires_Manage
 */

class Zend_View_Helper_QuestionnaireElement extends Zend_View_Helper_Abstract
{
    public $form;
    public $displayGroups=array();
    public function __construct(){
        $this->form=new Zend_Form_SubForm();
        $this->form->removeDecorator('HtmlTag');
        $this->form->removeDecorator('Fieldset');
        $this->form->removeDecorator('DtDdWrapper');
    }

    public function questionnaireElement(Webenq_Model_QuestionnaireNode $questionnaireNode, $format='preview', $output='form', $subFormId=null)
    {
        $elm=array();
        if ($questionnaireNode->getNode()->hasChildren()) {
            foreach ($questionnaireNode->getNode()->getChildren() as $node) {
                switch ($node->type) {
                    case 'QuestionnairePageNode':
                        //render (return subform)
                        $pageElement=$node->render($format);

                        //add decorator for page Group/remove unnecessary decorators
                        $pageElement=$this->_addDecoratorsPageGroup($node, $pageElement);
                        $pageElement->removeDecorator('DtDdWrapper');
                        $pageElement->removeDecorator('FieldSet');
                        $pageElement->removeDecorator('HtmlTag');
                        $this->form->addSubForm($pageElement, $node->id);
                        $subFormId=$node->id;

                        //process children
                        $childElements=$this->questionnaireElement($node,$format, 'element', $subFormId);


                    break;
                    case 'QuestionnaireLikertNode':
                    case 'QuestionnaireGroupNode':
                        $headerElement=$node->render($format);
                        $this->form->getSubForm($subFormId)->addElement($headerElement);
                        //get children
                        $groupElements=$this->questionnaireElement($node, $format, 'element', $subFormId);
                        $groupElements=array_merge(array($headerElement), $groupElements);
                        $elm=array_merge($elm,$groupElements);

                        //get names of elements to group using a display group
                        foreach ($groupElements as $element){
                            $elementNames[]=$element->getName();
                            $element->removeDecorator('adminOptions');
                        }

                        $this->form->getSubForm($subFormId)->addDisplayGroup($elementNames, $node->id);
                        $displayGroup=$this->form->getSubForm($subFormId)->getDisplayGroup($node->id);
                        $displayGroup=$this->_addDecoratorsGroup($node->id, $displayGroup);
                        $displayGroup->removeDecorator('DtDdWrapper');
                        $displayGroup->removeDecorator('FieldSet');
                        $displayGroup->removeDecorator('HtmlTag');

                        break;
                    case 'QuestionnaireQuestionNode':
                    case 'QuestionnaireTextNode':
                        $element=$node->render($format);
                        $element=$this->_addDecoratorsAdmin($node->id, $element);
                        $this->form->getSubForm($subFormId)->addElement($element);
                        $elm[]=$element;
                        //no children (at this moment)
                    break;
                    default:
                        throw new Exception(sprintf('Preview of %s is not implemented!', $node->type));

                }
            }
        }
        if ($output=='form'){
            return $this->form;
        } else {
            return $elm;
        }
    }

// add decorators
private function _addDecoratorsPageGroup($node, $elm)
    {
        if ($elm instanceof Zend_Form_SubForm) {
            // add decorators
            $elm->addDecorators(
                array(
                    array(
                        array(
                            'pageGroup' => 'Callback'
                        ),
                        array(
                            'callback' => array(
                                get_class($this),
                                'pageGroup'
                            ),
                            'placement' => '',
                            'node' => $node,
                            'view' =>$this->view,
                        )
                    ),
                )
            );
        }
    return $elm;
    }


private function _addDecoratorsGroup($nodeId, $elm)
    {
    if ($elm instanceof Zend_Form_DisplayGroup) {
        // add decorators
        $elm->addDecorators(
            array(
                array(
                    array(
                        'adminOptions' => 'Callback'
                    ),
                    array(
                        'callback' => array(
                            get_class($this),
                            'adminOptions'
                        ),
                        'placement' => Zend_Form_Decorator_Abstract::PREPEND,
                        'view' => $this->view,
                        'nodeId' => $nodeId,
                    )
                ),




                array(
                    array(
                        'group' => 'Callback'
                    ),
                    array(
                        'callback' => array(
                            get_class($this),
                            'group'
                        ),
                        'placement' => '',
                        'nodeId' => $nodeId,
                    )
                ),
            )
        );
    }
    return $elm;
    }


    /**
     * call adminOptions (move/edit/delete buttons)
     * call listItems (make it a li)
     *
     * @param unknown $node
     * @param unknown $elm
     * @return Zend_Form_Element
     */
    private function _addDecoratorsAdmin($nodeId, $elm)
    {

        if ($elm instanceof Zend_Form_Element) {
            // add decorators
            $elm->addDecorators(
                array(
                    array(
                        array(
                            'adminOptions' => 'Callback'
                        ),
                        array(
                            'callback' => array(
                                get_class($this),
                                'adminOptions'
                            ),
                            'placement' => Zend_Form_Decorator_Abstract::PREPEND,
                            'view' => $this->view,
                            'nodeId' => $nodeId,
                        )
                    ),
                    array(
                        array(
                            'listItem' => 'Callback'
                        ),
                        array(
                            'callback' => array(
                                get_class($this),
                                'listItem'
                            ),
                            'placement' => '',
                        )
                    ),
                )
            );
        }
        return $elm;

    }
/*
 * @todo make whole group draggable
 */
    public static function group($content, $element, $options) {
        $html='';
        $nodeId=$options['nodeId'];
        $html.='<li id="note' . $nodeId . '" class="question hoverable">';
        //replace first li by <ul><li>
        $html.=preg_replace("/<li/", '<ul class="subquestions"><li',$content, 1);

        $html.='</ul>';
        $html.='</li>';
        return $html;
    }

//callback functions for decorators
    public static function pageGroup($content, $element, $options) {
        $node=$options['node'];
        $view=$options['view'];
        $pageNumber=$node->QuestionnaireElement->getTranslation('text');
        $html="\n";
        $html.='<div id="pageId-'.$node->id.'">';
        $html.='<a href="/questionnaire/delete-page/id/'.$view->questionnaire->id.'/page_id/'.$node->id .'" class="delete-page link delete">'.t('Delete this page') .'</a>';
        //add question to page
        $html.='<a class="link add"  title="'. t('add a question').'" href="';
        $html.=$view->baseUrl('/questionnaire-question/add/questionnaire_id/' . $view->questionnaire->id.'/parent_id/'.$node->id);
        $html.= '">'.t('add a question to this page');
        $html.='</a>';

        //sortable/droppable
        $html.="\n";
        $html.= '<div class="questions"><ul id="sortable'.$pageNumber.'" class="questions-list connectedSortable ui-helper-reset">';
        $html.=$content;
        $html.="\n";
        $html.='</ul></div></div>';
        return $html;
    }
    public static function adminOptions($content, $element, $options)
    {
        $nodeId            = $options['nodeId'];
        $view          = $options['view'];
        //@todo check if subQuestion, if it is subquestion we don't have a move to page, but we want to change that behavior
        //$isSubQuestion = (bool) $qq->CollectionPresentation[0]->parent_id;
        $isSubQuestion=false;

        $html='';
        // add move handler
        $html .= '
            <div class="admin">
                <div class="handle" title="';
        $html.=t('move question to other position').'">';
        $html.=' </div>
            <div class="options">';

        // add edit/delete question button
        $html .= '<a class="icon edit" title="';
        $html.=t('edit');
        $html.= '" href="' .
            $view->baseUrl('/questionnaire-question/edit/id/' . $nodeId) . '">&nbsp;
            </a>';
        $html.= '<a class="icon delete" title="';
        $html.=t('delete');
        $html.='" href="' .
            $view->baseUrl('/questionnaire-question/delete/id/' . $nodeId) . '">&nbsp;
            </a>';
        //close option and admin divs
        $html.='</div>
            </div>';
        return $html;
    }

    static public function listItem($content, $element, $options)
    {
        return '<li id="' . $element->getName() . '" class="question hoverable">' . $content . '</li>';
    }
}