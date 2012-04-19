<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>, Jaap-Andre de Hoop <j.dehoop@nivocer.com>
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
        foreach ($report->ReportTitle as $title) {
            $form->title->{$title->language}->setValue($title->text);
        }

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
        switch ($report->orientation) {
            case 'p':
                $this->view->pageWidth = 595;
                $this->view->pageHeight = 842;
                $this->view->pageOrientation = 'Portrait';
                break;
            case 'a':
            case 'l':
            default:
                $this->view->pageWidth = 842;
                $this->view->pageHeight = 595;
                $this->view->pageOrientation = 'Landscape';
                break;
        }
        $this->view->leftMargin =20;
        $this->view->rightMargin =20;
        $this->view->topMargin =20;
        $this->view->bottomMargin =20;
        $this->view->columnWidth=$this->view->pageWidth - $this->view->leftMargin - $this->view->rightMargin;

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

    /*
     * Generate the report
     * TODO use code from reportGeneratinoController.php for controlAction and generateAction
     *
     */
     /**
      * Let jasper report (via java) generate the reports and display the list of reports, reported back from jasper report
      *
      *
      */
     public function generateAction()
     {
     $cwd = getcwd();
     $output = array();
     $returnVar = 0;

    $this->_id = $this->getRequest()->getParam("id");
    if (!$this->_id) {
        throw new Exception("No id given!");
   	}
	/* create the report(s) */
        chdir(APPLICATION_PATH . "/../java");
        $baseUrl=$this->getRequest()->getScheme().'://'.$this->getRequest()->getHttpHost().'/';
        $reportControlUrl=$baseUrl.'report/control/id/'.$this->_id;
        $cmd = "java -cp .:./lib/* it.bisi.report.jasper.ExecuteReport $reportControlUrl";
        ob_start();
        passthru($cmd, $returnVar);
        $output = ob_get_contents();
        ob_end_clean();
        chdir($cwd);

        //command returned error value
        if ($returnVar > 0) {
            $this->view->output = $output;
            return;
        }else{
        	$generatedFiles=explode(',', $output);
       		//remove ../public/
       		$generatedFiles=str_replace('../public','',$generatedFiles);
       		$this->view->file = $generatedFiles;
       	}
        /*
        //if $output contains: 'error generating report(s)' some error occured
        $errorIndication='error generating report(s)';
        if (stripos($errorIndication,$output)){
        	  $this->view->output = $output;
            return;
        }else{
        //else $output contains commaseperated list with generated report, relative to java starting with ../public/report/
       		$generatedFiles=explode(',', $output);
       		//remove ../public/
       		$generatedFiles=str_replace('../public','',$generatedFiles);
       		$this->view->file = $generatedFiles;
       	}
       	*/
    }
}