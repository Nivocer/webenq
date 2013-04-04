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
    protected static $_totalPages;
    public function questionnaireElement(Webenq_Model_QuestionnaireNode $questionnaireNode, $format='preview')
    {
        //@todo get Total number of pages/rootgroups
        self::$_totalPages = 2;
        $form='';
        $elm='';
        if ($questionnaireNode->getNode()->hasChildren()) {
            foreach ($questionnaireNode->getNode()->getChildren() as $node) {
                switch ($node->type) {
                    case 'QuestionnairePageNode':
                        //try to use decorator and subform to display page, not succesfull yet
                        /*$elm=$node->render($format);
                        $elm=$this->_addDecoratorsPageGroup($node,$elm);
                        var_dump(__LINE__, __FILE__,  $this->questionnaireElement($node,'formElement'));
                        $elm->addElement($this->questionnaireElement($node, 'formElement'), $node->QuestionnaireElement->name);
                        //
                        $form.=$elm;
                        */
                        //@todo move to decorator
                        $form.=$this->_renderPageElementPre($node);
                        $form.=$this->questionnaireElement($node, $format);
                        // @todo move to decorator
                        //$html.=$this->_renderPageElementPost($node);
                        $form.='</ul></div></div>';
                    break;

                    default:
                        //get form element from model
                        $elm=$node->render($format);
                        //add decorators to form element
                        $elm=$this->_addDecoratorsAdmin($node,$elm);
                        //add form element to form
                        $form.=$elm;
                        //process children
                        $form.= $this->questionnaireElement($node, $format);
                    break;
                }
            }
        }
        if ($format=='formElement') {
            return $elm;
        }else {
            return $form;
        }
        return false;

    }

// add decorators
private function _addDecoratorsPageGroup($node, $elm)
    {

        if ($elm instanceof Zend_Form_Element) {
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

//callback functions for decorators
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
//        $html .= '  <a class="ajax icon edit" title="';
        $html .= '  <a class="icon edit" title="';
        $html.=t('edit');
        $html.= '" href="' .
            $view->baseUrl('/questionnaire-question/edit/id/' . $node->id) . '">&nbsp;</a>
                    <a class="ajax icon delete" title="verwijderen" href="' .
                        $view->baseUrl('/questionnaire-question/delete/id/' . $node->id) . '">&nbsp;
                    </a>
                </div>
            </div>';
        return $html;
    }

    static public function listItem($content, $element, $options)
    {
        return '<li id="' . $element->getName() . '" class="question droppable hoverable">' . $content . '</li>';
    }


    // @todo move this to decorator, but not working yet
    private function _renderPageElementPre($node)
    {
        $html='<div id="group-'.$node->id.'">';
        $html.='<a href="#" class="delete-page link delete">'.t('Delete this page') .'</a>';
        $html.=  '<div class="questions"><ul class="questions-list sortable droppable">';
        return $html;
    }
}