<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class ImportController extends Zend_Controller_Action
{
    /**
     * Handles the importing of files
     *
     * @return void
     */
    public function indexAction()
    {
        $session = new Zend_Session_Namespace();
        $supportedFormats = Webenq_Import_Adapter_Abstract::$supportedFormats;

        $form = new Webenq_Form_Import($supportedFormats);
        $form->language->setValue($session->language);
        $errors = array();

        if ($this->_helper->form->isPostedAndValid($form)) {

            // make sure enough resources are assigned
            try {
                //Webenq::setMemoryLimit('512M');
                Webenq::setMaxExecutionTime(0);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }

            // get form data
            $data = $form->getValues();
            $session->language = $data['language'];

            // get uploaded file
            if ($form->file->receive()) {
                $filename = $form->file->getFileName();
            } else {
                $errors[] = 'Error receiving the file';
            }

            if (empty($errors)) {
                $adapter = Webenq_Import_Adapter_Abstract::factory($filename);
                $importer = Webenq_Import_Abstract::factory($data['type'], $adapter, $data['language']);
                $importer->import();
                $this->_redirect('/');
            }
        }

        $this->view->errors = $errors;
        $this->view->form = $form;
    }
}
