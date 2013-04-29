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
    protected static $_totalPages;
    public function __construct(){
        $this->form=new Zend_Form_SubForm();
        $this->form->removeDecorator('HtmlTag');
        $this->form->removeDecorator('Fieldset');
        $this->form->removeDecorator('DtDdWrapper');
    }

    public function questionnaireElement(Webenq_Model_QuestionnaireNode $questionnaireNode, $format='preview', $output='form')
    {
        $elm=array();
        //@todo get Total number of pages/rootgroups
        self::$_totalPages = 2;
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

                        //process children
                        $pageElement->addElements($this->questionnaireElement($node,$format, 'element'));

                        //add displaygroups (we need elements first)
                        foreach ($this->displayGroups as $nodeId=>$GroupElementNames){
                            $pageElement->addDisplayGroup($GroupElementNames, $nodeId);
                            $displayGroup=$pageElement->getDisplayGroup($nodeId);
                            $displayGroup=$this->_addDecoratorsGroup($nodeId, $displayGroup);
                            $displayGroup->removeDecorator('DtDdWrapper');
                            $displayGroup->removeDecorator('FieldSet');
                            $displayGroup->removeDecorator('HtmlTag');
                            //unset displaygroup
                            unset ($this->displayGroups[$nodeId]);
                        }
                    break;
                    case 'QuestionnaireGroupNode':
                        $groupElement=$node->render($format);
                        //get children
                        $elements=$this->questionnaireElement($node, $format, 'element');
                        $elm=array_merge($elm,$elements);
                        //get names of elements to group using a display group (defined in questionnairePageNode)
                        foreach ($elements as $element){
                            $elementNames[]=$element->getName();
                        }
                        //add this group to $this->displayGroup, so we can add it per page/form
                        $this->displayGroups[$node->id]=$elementNames;
                        break;
                    case 'QuestionnaireQuestionNode':
                    case 'QuestionnaireTextNode':
                        $element=$node->render($format);
                        $element=$this->_addDecoratorsAdmin($node, $element);
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


    private function _addDecoratorsAdmin($node, $elm)
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
                            'node' => $node,
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
        $nodeId=$options['nodeId'];
        $html='';
        $html.='Group:';
        $html.=$content;
        $html.='/group';
        //$html.='</li>';
        return $html;
    }

//callback functions for decorators
    public static function pageGroup($content, $element, $options) {
        $node=$options['node'];
        $view=$options['view'];

        $html='<div id="pageId-'.$node->id.'">';
        $html.='<a href="#" class="delete-page link delete">'.t('Delete this page') .'</a>';
        //add question to page
        $html.='<a class="link add"  title="'. t('add a question').'" href="';
        $html.=$view->baseUrl('/questionnaire-question/add/questionnaire_id/' . $view->questionnaire->id.'/parent_id/'.$node->id);
        $html.= '">'.t('add a question to this page');
        $html.='</a>';

        //sortable
        $html.= '<div class="questions"><ul class="questions-list sortable droppable">';
        $html.=$content;
        $html.='</ul></div></div>';
        return $html;
    }
    public static function adminOptions($content, $element, $options)
    {
        $node            = $options['node'];
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

        // add move to page pulldown
        if (!$isSubQuestion) {
            $pages = array();
            for ($page = 1; $page <=  self::$_totalPages; $page++) {
                $pages[$page] = $page;
            }
            //@todo determin current page (if we keep this method)
            $currentPage = isset($qq['CollectionPresentation'][0]['page'])
                ? $qq['CollectionPresentation'][0]['page'] : 1;

            $pageSelect = $view->formSelect(
                'to-page',
                $currentPage,
                array(
                    'id' => 'page-select-qq-' . $node->id
                ),
                $pages
            );
        $html .= t('move to page') . $pageSelect;
        }

        // add edit/delete question button
        $html .= '<a class="icon edit" title="';
        $html.=t('edit');
        $html.= '" href="' .
            $view->baseUrl('/questionnaire-question/edit/id/' . $node->id) . '">&nbsp;
            </a>';
        $html.= '<a class="icon delete" title="';
        $html.=t('delete');
        $html.='" href="' .
            $view->baseUrl('/questionnaire-question/delete/id/' . $node->id) . '">&nbsp;
            </a>';
        //close option and admin divs
        $html.='</div>
            </div>';
        return $html;
    }

    static public function listItem($content, $element, $options)
    {
        return '<li id="' . $element->getName() . '" class="question droppable hoverable">' . $content . '</li>';
    }
}