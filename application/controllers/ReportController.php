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
        $report = $this->view->report = Doctrine_Core::getTable('Webenq_Model_Report')
            ->find($this->_request->id);
    }

    /**
     * Action for editing a report
     */
    public function editAction()
    {
        $report = $this->view->report = Doctrine_Core::getTable('Webenq_Model_Report')
            ->find($this->_request->id);

        $form = $this->view->form = new Webenq_Form_Report_Edit();
        $form->setAction($this->_request->getRequestUri());
        $form->populate($report->toArray());

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {

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
}