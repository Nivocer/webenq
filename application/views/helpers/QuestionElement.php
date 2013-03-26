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
 * @package    Webenq_Questionnaires_Manage
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * @package    Webenq_Questionnaires_Manage
 */
class Zend_View_Helper_QuestionElement extends Zend_View_Helper_Abstract
{
    protected static $_totalPages;

    /**
     * Helper for rendering form elements
     *
     * @param Webenq_Model_QuestionnaireQuestion $qq Questionnaire-question
     * @param int $totalPages Total number of pages
     * @param bool $deep Indicating if childs elements should be rendered as well
     * @return Zend_Form_Element
     * @todo make recursive
     */
    public function questionElement(Webenq_Model_QuestionnaireQuestion $qq, $totalPages, $deep = true)
    {
        self::$_totalPages = $totalPages;

        // get form element
        $elm = $qq->getFormElement();

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
                            'qq' => $qq,
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
        } elseif ($elm instanceof Zend_Form_SubForm) {

            $elm->getDecorator('HtmlTag')
                ->setOption('class', 'sub-questions sortable droppable ui-sortable ui-droppable');

            // add decorators to subform
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
                            'qq' => $qq,
                        )
                    ),
                    array(
                        array(
                            'listItem' => 'Callback'),
                            array(
                            'callback' => array(
                                get_class($this), 'listItem'
                            ),
                            'placement' => '',
                        )
                    ),
                )
            );

            $i = 0;
            $subQqs = Webenq_Model_QuestionnaireQuestion::getSubQuestions($qq);
            foreach ($elm->getElements() as $subFormElm) {
                // add decorators to subform elements
                $subFormElm->addDecorators(
                    array(
                        array(
                            array(
                                'adminOptions' => 'Callback'
                            ),
                            array(
                                'callback' => array(
                                    get_class($this), 'adminOptions'
                                ),
                                'placement' => Zend_Form_Decorator_Abstract::PREPEND,
                                'qq' => $subQqs[$i],
                                'view' => $this->view,
                            )
                        ),
                        array(
                            array(
                                'listItem' => 'Callback'
                            ),
                            array(
                                'callback' => array(
                                    get_class($this), 'listItem'
                                ),
                                'placement' => '',
                            )
                        ),
                    )
                );
                $i++;
            }
        } else {
            throw new Exception('Unexpected element type!');
        }

        return $elm;
    }

    public static function adminOptions($content, $element, $options)
    {
        $qq            = $options['qq'];
        $view          = $options['view'];
        $isSubQuestion = (bool) $qq->CollectionPresentation[0]->parent_id;

        if (!$isSubQuestion) {
            $pages = array();
            for ($page = 1; $page <= self::$_totalPages; $page++) {
                $pages[$page] = $page;
            }
            $currentPage = isset($qq['CollectionPresentation'][0]['page'])
                ? $qq['CollectionPresentation'][0]['page'] : 1;
            $pageSelect = $view->formSelect(
                'to-page',
                $currentPage,
                array(
                    'id' => 'page-select-qq-' . $qq['id']
                ),
                $pages
            );
        }

        $html = '
            <div class="admin">
                <div class="handle" title="'.t('Move question to other position or page').'"></div>
                <div class="options">';

        if (!$isSubQuestion) $html .= t('move to page') . $pageSelect;

        $html .= '  <a class="ajax icon edit" title="'.t('edit').'" href="' .
            $view->baseUrl('/questionnaire-question/edit/id/' . $qq->id) . '">&nbsp;</a>
                    <a class="ajax icon delete" title="'.t('delete').'" href="' .
                        $view->baseUrl('/questionnaire-question/delete/id/' . $qq->id) . '">&nbsp;
                    </a>
                </div>
            </div>';

        return $html;
    }

    static public function listItem($content, $element, $options)
    {
        return '<li id="' . $element->getName() . '" class="question droppable hoverable">' . $content . '</li>';
    }

//    protected function _getMultiElementTable(array $elements)
//    {
//        $html = $elements[0]->getLabel();
//        $html .= '<table><tbody>';
//
//        /* do not render root element */
//        unset($elements[0]);
//
//        foreach ($elements as $row) {
//            $html .= '<tr>';
//            foreach ($row as $i => $col) {
//                if ($i == 0) {
//                    $html .= '<td>' . $col->getLabel() . '</td>';
//                } else {
//                    /* is this element equal to the previous one? */
//                    if ($i > 1 && $this->_equalElementTypes($col, $row[$i-1])) {
//                        $html .= '<td>' . $col->render() . '</td>';
//                    } else {
//                        $html .= '<td>' . $col->render() . '</td>';
//                    }
//                }
//            }
//            $html .= '</tr>';
//        }
//        $html .= '</tbody></table>';
//
//        return $html;
//    }
//
//    protected function _equalElementTypes(Zend_Form_Element $elm1, Zend_Form_Element $elm2)
//    {
//        if (get_class($elm1) == get_class($elm2)) {
//            if ($elm1->getLabel() == $elm2->getLabel()) {
//                if ($elm1 instanceof Zend_Form_Element_Multi) {
//                    if ($elm1->getMultiOptions() == $elm2->getMultiOptions()) {
//                        return true;
//                    }
//                } else {
//                    return true;
//                }
//            }
//        }
//        return false;
//    }
//
//    protected function _getTableWidth($elements)
//    {
//        $max = 0;
//        foreach ($elements as $element) {
//            if (count($element) > $max) $max = count($element);
//        }
//        return $max;
//    }
}