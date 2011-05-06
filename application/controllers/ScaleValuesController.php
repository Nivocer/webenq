<?php
/**
 * Controller class
 *
 * @category    Webenq
 * @package        Controllers
 * @author        Bart Huttinga <b.huttinga@nivocer.com>
 */
class ScaleValuesController extends Zend_Controller_Action
{
    /**
     * Current language
     */
    protected $_language;

    /**
     * Initialisation
     *
     * @return void
     */
    public function init()
    {
        $this->_language = Zend_Registry::get('language');
    }


    /**
     * Renders the overview of export options
     */
    public function indexAction()
    {
        /* get model, and query for defined scale labels */
        $scale = new Webenq_Model_DbTable_ScaleValues();

        try {
            $this->view->scaleValues = $scale->fetchAll($scale->select()
                ->order("question_type")
                ->order("value")
                ->where("language = ?", $this->_language));
        }
        catch (Zend_Db_Statement_Exception $e) {
               throw $e;
        }
    }


    public function addAction()
    {
        $this->view->form = $form = new Webenq_Form_ScaleValues_Add();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                if (true === $this->_processAdd()) {
                    $this->_redirect("/scale-values/index/langauge/" . $this->_language);
                }
            }
        }
    }

    protected function _processAdd()
    {
        $data = $this->getRequest()->getpost();
        $scaleValues = new Webenq_Model_DbTable_ScaleValues();

        try {
            $scaleValues->insert(array(
                'label'            => $data['label'],
                'value'            => $data['value'],
                'question_type'    => $data['question_type'],
                'language'        => $this->_language,
            ));
        }
        catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function editAction()
    {
        $this->view->form = $form = new Webenq_Form_ScaleValues_Add();
        $id = $this->getRequest()->getParam('id');

        $scaleValues = new Webenq_Model_DbTable_ScaleValues();
        $form->populate($scaleValues->find($id)->current()->toArray());

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                if (true === $this->_processEdit($id)) {
                    $this->_redirect("/scale-values/index/langauge/" . $this->_language);
                }
            }
        }
    }

    protected function _processEdit($id)
    {
        $data = $this->getRequest()->getpost();
        $scaleValues = new Webenq_Model_DbTable_ScaleValues();

        try {
            $scaleValues->update(array(
                'label'            => $data['label'],
                'value'            => $data['value'],
                'question_type'    => $data['question_type'],
                'language'        => $this->_language,
            ), "id = $id");
        }
        catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function delAction()
    {
        $this->view->form = $form = new Webenq_Form_ScaleValues_Del();
        $id = $this->getRequest()->getParam('id');

        $scaleValues = new Webenq_Model_DbTable_ScaleValues();
        $this->view->label = $scaleValues->find($id)->current()->label;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_processDel($id);
                $this->_redirect("/scale-values/index/language/" . $this->_language);
            }
        }
    }

    protected function _processDel($id)
    {
        $scaleValues = new Webenq_Model_DbTable_ScaleValues();
        $scaleValues->delete("id = $id");
    }
}