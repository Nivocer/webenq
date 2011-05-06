<?php
/**
 * Class representing the form for user login
 * 
 * @category    Webenq
 * @package        Webenq_Form
 * @subpackage    User
 * @author        Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_User_Login extends Zend_Form
{
    /**
     * Builds the form.
     * 
     * Adds elements, validators and filters to the form.
     */
    public function init()
    {
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->setAction("$baseUrl/user/login");
        
        $username= new Zend_Form_Element_Text('username');
        $username->setLabel('Gebruikersnaam:')
            ->setRequired(true)
            ->addValidator('NotEmpty');
            
        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Wachtwoord:')
            ->setRequired(true)
            ->addValidator('NotEmpty');
            
        $redirect = new Zend_Form_Element_Hidden('redirect');
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('inloggen');
        
        $this->addElements(array($username, $password, $redirect, $submit));
    }
}
