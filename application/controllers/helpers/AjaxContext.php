<?php
class Webenq_Controller_Action_Helper_AjaxContext
    extends Zend_Controller_Action_Helper_AjaxContext
{
    public function preDispatch()
    {
        $this->initContext();
    }
}