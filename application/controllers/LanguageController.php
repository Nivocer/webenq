<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class LanguageController extends Zend_Controller_Action
{
    /**
     * Sets the current language and redirects to referer page
     *
     * @return void
     */
    public function selectAction()
    {
        $session = new Zend_Session_Namespace();
        $session->language = $this->_request->language;
        $this->_redirect($_SERVER['HTTP_REFERER']);
    }
}
