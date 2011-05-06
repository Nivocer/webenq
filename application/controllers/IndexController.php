<?php
/**
 * Controller class
 *
 * @category    Webenq
 * @package        Controllers
 * @author        Bart Huttinga <b.huttinga@nivocer.com>
 */
class IndexController extends Zend_Controller_Action
{
    /**
     * Renders the dashboard
     */
    public function indexAction()
    {
        $this->_helper->actionStack('index', 'questionnaire');
        $this->_helper->actionStack('index', 'import');
        $this->_helper->viewRenderer->setNoRender();
    }
}
