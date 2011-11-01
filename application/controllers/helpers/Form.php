<?php
class Webenq_Controller_Action_Helper_Form
    extends Zend_Controller_Action_Helper_Abstract
{
    public function isPostedAndValid(Zend_Form $form)
    {
        $request = $this->getRequest();
        return ($request->isPost() && $form->isValid($request->getPost()));
    }

    public function render(Zend_Form $form)
    {
        $controller = $this->getActionController();
        $controller->getHelper('viewRenderer')->setNoRender(true);
        $controller->getResponse()->setBody($form->render());
    }
}