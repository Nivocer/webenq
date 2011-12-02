<?php
/**
 * Helper class for rendering a report element
 */
class Zend_View_Helper_ReportElement extends Zend_View_Helper_Abstract
{
    protected $_data = array();

    public function reportElement(Webenq_Model_ReportElement $element)
    {
        $html = '<div class="report-element-wrapper">
        	<div class="actions">
            	<a class="ajax icon edit" title="' . t('edit') . '" href="' . $this->view->baseUrl('report-element/edit/id/' . $element->id) . '">&nbsp;</a>
            	<a class="ajax icon delete" title="' . t('delete') . '" href="' . $this->view->baseUrl('report-element/delete/id/' . $element->id) . '">&nbsp;</a>
            </div>
            <div class="report-element">';

        $this->_data = unserialize($element->data);
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
            default:
                throw new Exception('Unknown element type ' . $this->_data['type']);

        }

        $html .= '</div></div>';

        return $html;
    }

    protected function _renderTextElement()
    {
        $html = '';
        foreach ($this->_data as $key => $value)
            $html .= "$key => $value<br/>";
        return $html;
    }

    protected function _renderOpenQuestionElement()
    {
        $html = '';
        foreach ($this->_data as $key => $value)
            $html .= "$key => $value<br/>";
        return $html;
    }

    protected function _renderPercentageTableElement()
    {
        $html = '';
        foreach ($this->_data as $key => $value)
            $html .= "$key => $value<br/>";
        return $html;
    }

    protected function _renderMeanTableElement()
    {
        $html = '';
        foreach ($this->_data as $key => $value)
            $html .= "$key => $value<br/>";
        return $html;
    }

    protected function _renderBarchartAndMeanElement()
    {
        $html = '';
        foreach ($this->_data as $key => $value)
            $html .= "$key => $value<br/>";
        return $html;
    }
}