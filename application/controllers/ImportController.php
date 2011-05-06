<?php
/**
 * Controller class
 *
 * @category    Webenq
 * @package        Controllers
 * @author        Bart Huttinga <b.huttinga@nivocer.com>
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
        $supportedFormats = Webenq_Import_Adapter_Abstract::$supportedFormats;

        $form = new Webenq_Form_Import($supportedFormats);
        $errors = array();

        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if ($form->isValid($data)) {

                if ($form->file->receive()) {
                    $filename = $form->file->getFileName();
                } else {
                    $errors[] = 'Error receiving the file';
                }

                if (!$errors) {

                    /* set memory_limit */
                    $key = 'memory_limit';
                    $value = '256M';
                    @ini_set($key, $value);
                    if (!ini_get($key) == $value) {
                        throw new Exception("PHP-settings $key could not be set to $value!");
                    }

                    $adapter = Webenq_Import_Adapter_Abstract::factory($filename);
                    $importer = Webenq_Import_Abstract::factory($data['type'], $adapter);
                    $importer->import();
                    $this->_redirect('/');
                }
            }
        }

        $this->view->errors = $errors;
        $this->view->form = $form;
    }
}