<?php
/**
 * Helper class for rendering a report element
 */
class Zend_View_Helper_ReportElement extends Zend_View_Helper_Abstract
{
    protected $_data = array();

    public function reportElement(Webenq_Model_ReportElement $element)
    {
        $this->_data = unserialize($element->data);

        $html = '<div class="report-element-wrapper">
        	<div class="actions">
            	<span class="type">' . t($this->_data['type']) . '</span>
        		<a class="ajax icon edit" title="' . t('edit') . '" href="' . $this->view->baseUrl('report-element/edit/id/' . $element->id) . '">&nbsp;</a>
            	<a class="ajax icon delete" title="' . t('delete') . '" href="' . $this->view->baseUrl('report-element/delete/id/' . $element->id) . '">&nbsp;</a>
            </div>
            <div class="report-element">';

        switch ($this->_data['type']) {
            case 'text':
                $html .= $this->_renderTextElement();
                break;
            case 'open question':
                $html .= $this->_renderOpenQuestionElement();
                break;
            case 'percentages table':
                $html .= $this->_renderPercentageTableElement();
                break;
            case 'mean table':
                $html .= $this->_renderMeanTableElement();
                break;
            case 'barchart and mean':
                $html .= $this->_renderBarchartAndMeanElement();
                break;
            case 'response':
                $html .= $this->_renderResponseElement();
                break;
            default:
                throw new Exception('Unknown element type ' . $this->_data['type']);

        }

        $html .= '</div></div>';

        return $html;
    }

    protected function _renderTextElement()
    {
        if (isset($this->_data['text'])) {
            return '<strong>' . $this->_data['text'] . '</strong>';
        }
    }

    protected function _renderOpenQuestionElement()
    {
        if (isset($this->_data['report_qq_id'])) {
            $qq = Doctrine_Core::getTable('Webenq_Model_QuestionnaireQuestion')
                ->find($this->_data['report_qq_id']);
            return '<strong>' . $qq->Question->getQuestionText()->text . '</strong>';
        }
    }

    protected function _renderPercentageTableElement()
    {
        $html = '';

        if (isset($this->_data['report_qq_id'])) {
            $rqq = Doctrine_Core::getTable('Webenq_Model_QuestionnaireQuestion')
                ->find($this->_data['report_qq_id']);
            $html .= '<strong>' . $rqq->Question->getQuestionText()->text . '</strong><br/>';
        }

        if (isset($this->_data['group_qq_id']) && !empty($this->_data['group_qq_id'])) {
            $gqq = Doctrine_Core::getTable('Webenq_Model_QuestionnaireQuestion')
                ->find($this->_data['group_qq_id']);
            $html .= t('grouped by')
            . ' <strong>' . $gqq->Question->getQuestionText()->text . '</strong>';
        }

        return $html;
    }

    protected function _renderMeanTableElement()
    {
        $html = '';

        if (isset($this->_data['header_qq_id'])) {
            $hqq = Doctrine_Core::getTable('Webenq_Model_QuestionnaireQuestion')
                ->find($this->_data['header_qq_id']);
            $html .= '<strong>' . $hqq->Question->getQuestionText()->text . '</strong><br/>';
        }

        if (isset($this->_data['report_qq_ids'])) {
            foreach ($this->_data['report_qq_ids'] as $id) {
                $rqq = Doctrine_Core::getTable('Webenq_Model_QuestionnaireQuestion')->find($id);
                $html .= ' - <strong>' . $rqq->Question->getQuestionText()->text . '</strong><br/>';
            }
        }

        if (isset($this->_data['group_qq_id']) && !empty($this->_data['group_qq_id'])) {
            $gqq = Doctrine_Core::getTable('Webenq_Model_QuestionnaireQuestion')
                ->find($this->_data['group_qq_id']);
            $html .= t('grouped by')
                . ' <strong>' . $gqq->Question->getQuestionText()->text . '</strong><br/>';
        }

        if (isset($this->_data['color_schema'])) {
            $html .= t('with color schema') . ' <strong>'
                . t($this->_data['color_schema']) . '</strong>';
        }

        return $html;
    }

    protected function _renderBarchartAndMeanElement()
    {
        $html = '';

        if (isset($this->_data['header_qq_id'])) {
            $hqq = Doctrine_Core::getTable('Webenq_Model_QuestionnaireQuestion')
                ->find($this->_data['header_qq_id']);
            $html .= '<strong>' . $hqq->Question->getQuestionText()->text . '</strong><br/>';
        }

        if (isset($this->_data['report_qq_ids'])) {
            foreach ($this->_data['report_qq_ids'] as $id) {
                $rqq = Doctrine_Core::getTable('Webenq_Model_QuestionnaireQuestion')->find($id);
                $html .= ' - <strong>' . $rqq->Question->getQuestionText()->text . '</strong><br/>';
            }
        }

        if (isset($this->_data['color_schema'])) {
            $html .= t('with color schema') . ' <strong>'
                . t($this->_data['color_schema']) . '</strong>';
        }

        return $html;
    }
    protected function _renderResponseElement()
    {
        $html = '';

        if (isset($this->_data['report_qq_id'])) {
            $rqq = Doctrine_Core::getTable('Webenq_Model_QuestionnaireQuestion')
                ->find($this->_data['report_qq_id']);
            $html .= '<strong>' . $rqq->Question->getQuestionText()->text . '</strong><br/>';
        }

        if (isset($this->_data['group_qq_id']) && !empty($this->_data['group_qq_id'])) {
            $gqq = Doctrine_Core::getTable('Webenq_Model_QuestionnaireQuestion')
                ->find($this->_data['group_qq_id']);
            $html .= t('grouped by')
            . ' <strong>' . $gqq->Question->getQuestionText()->text . '</strong>';
        }

        return $html;
    }
    
}