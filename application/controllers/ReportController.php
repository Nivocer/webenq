<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class ReportController extends Zend_Controller_Action
{
    /**
     * Controller actions that are ajaxable
     *
     * @var array
     */
    public $ajaxable = array(
        'add' => array('html'),
        'edit' => array('html'),
        'delete' => array('html'),
    );

    /**
     * Renders the overview of report definitions
     */
    public function indexAction()
    {
        $this->view->reports = Webenq_Model_Report::getReports($this->_request->id);
    }

    /**
     * Action for adding a report
     */
    public function addAction()
    {
        $form = $this->view->form = new Webenq_Form_Report_Add();
        $form->setAction($this->_request->getRequestUri());

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {

            $report = new Webenq_Model_Report();
            $report->fromArray($form->getValues());
            $report->save();

            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->json(array('reload' => true));
            } else {
                $this->_redirect('report');
            }
        }
    }

    /**
     * Action for viewing a report
     */
    public function viewAction()
    {
        $reports = Doctrine_Query::create()
            ->from('Webenq_Model_Report r')
            ->leftJoin('r.ReportElement e')
            ->where('r.id = ?', $this->_request->id)
            ->orderBY('e.sort ASC, e.id DESC')
            ->execute();

        $this->view->report = $reports->getFirst();
    }

    /**
     * Action for editing a report
     */
    public function editAction()
    {
        $report = $this->view->report = Doctrine_Core::getTable('Webenq_Model_Report')
            ->find($this->_request->id);

        $splitQuestionMultiOptions = array('' => '');
        foreach ($report->Questionnaire->QuestionnaireQuestion as $qq) {
            $splitQuestionMultiOptions[$qq->id] = $qq->Question->getQuestionText()->text;
        }

        $form = $this->view->form = new Webenq_Form_Report_Edit();
        $form->setAction($this->_request->getRequestUri());
        $form->getElement('split_qq_id')->setMultiOptions($splitQuestionMultiOptions);
        $form->populate($report->toArray());

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {

            $report->fromArray($form->getValues());
            if (empty($report->split_qq_id)) $report->split_qq_id = null;
            $report->save();

            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->json(array('reload' => true));
            } else {
                $this->_redirect('report');
            }
        }
    }

    /**
     * Action for deleting a report
     */
    public function deleteAction()
    {
        $report = Doctrine_Core::getTable('Webenq_Model_Report')
            ->find($this->_request->id);

        $form = $this->view->form = new Webenq_Form_Confirm(
            $report->id, t('Are you sure you want to delete this report?'));
        $form->setAction($this->_request->getRequestUri());

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {

            if ($this->_request->getPost('yes')) {
                $report->delete();
                $reload = true;
            } else {
                $reload = false;
            }

            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->json(array('reload' => $reload));
            } else {
                $this->_redirect('report');
            }
        }
    }

    /**
     * Saves the current state of the given report
     */
    public function saveStateAction()
    {
        // disable view/layout rendering
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        // save new order of elements
        foreach ($this->_request->getPost('re') as $sort => $reportElementId) {
            $reportElement = Doctrine_Core::getTable('Webenq_Model_ReportElement')
                ->find($reportElementId);
            $reportElement->sort = $sort;
            $reportElement->save();
        }
    }

    public function jrxmlAction()
    {
        $reports = Doctrine_Query::create()
            ->from('Webenq_Model_Report r')
            ->leftJoin('r.ReportElement e')
            ->where('r.id = ?', $this->_request->id)
            ->orderBY('e.sort ASC, e.id DESC')
            ->execute();

        $report = $this->view->report = $reports->getFirst();

        $filename = Webenq::filename(implode('-', array(
            $report->id,
            $report->getReportTitle()->text,
            date('YmdHis')))) . '.jrxml';

        // config settings
        // @todo get this from database
        $this->view->pageWidth = 595;
        $this->view->pageHeight = 842;

        $this->render();

        // create new dom document
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        // append rendered xml to dom
        $fragment = $dom->createDocumentFragment();
        $fragment->appendXML($this->_response->getBody());
        $dom->appendChild($fragment);

        // output
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_response
            ->setHeader('Content-Type', 'text/xml; charset=utf-8')
            ->setHeader('Content-Transfer-Encoding', 'binary')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dom->saveXML());
    }

    public function controlAction()
    {
        $reports = Doctrine_Query::create()
            ->from('Webenq_Model_Report r')
            ->leftJoin('r.ReportElement e')
            ->leftJoin('r.QuestionnaireQuestion qq')
            ->where('r.id = ?', $this->_request->id)
            ->orderBY('e.sort ASC, e.id DESC')
            ->execute();

        $report = $this->view->report = $reports->getFirst();

        $filename = Webenq::filename(implode('-', array(
            $report->id,
            $report->getReportTitle()->text,
            date('YmdHis')))) . '.xml';

        $this->render();

        // create new dom document
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        // append rendered xml to dom
        $fragment = $dom->createDocumentFragment();
        $fragment->appendXML($this->_response->getBody());
        $dom->appendChild($fragment);

        // output
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
         $this->_response
            ->setHeader('Content-Type', 'text/xml; charset=utf-8')
            ->setHeader('Content-Transfer-Encoding', 'binary')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dom->saveXML());
        
        /*$this->_response
            ->setHeader('Content-Type', 'text/xml; charset=utf-8')
            ->setBody($dom->saveXML());
            */
    }
}