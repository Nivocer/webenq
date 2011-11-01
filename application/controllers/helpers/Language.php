<?php
class Webenq_Controller_Action_Helper_Language
    extends Zend_Controller_Action_Helper_Abstract
{
    public function direct()
    {
        return Zend_Registry::get('Zend_Locale')->getLanguage();
    }
}