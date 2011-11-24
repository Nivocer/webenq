<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class ToolController extends Zend_Controller_Action
{
    public function hvaAction()
    {
        $form = new Webenq_Form_Tool_Hva(array('xls', 'xlsx'));
        $errors = array();

        if ($this->_helper->form->isPostedAndValid($form)) {

            // make sure enough resources are assigned
            try {
                Webenq::setMemoryLimit('512M');
                Webenq::setMaxExecutionTime(0);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }

            // receive the file
            if ($form->file->receive()) {
                $filename = $form->file->getFileName();
            } else {
                $errors[] = 'Error receiving the file';
            }

            if (empty($errors)) {

                // process data
                $tool = new Webenq_Tool_Hva($filename);
                $tool->process();

                // disabled layout and viewRenderer
                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender();

                // return file for download
                $download = $tool->getDownload();
                $download->send($this->_response);
            }
        }

        $this->view->errors = $errors;
        $this->view->form = $form;
    }
}