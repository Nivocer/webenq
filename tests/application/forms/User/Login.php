<?php
class Webenq_Test_Form_User_LoginTest extends Webenq_Test_Form
{
    public function testFormOnlyValidatesWhenBothUsernameAndPasswordAreProvided()
    {
        $form = new Webenq_Form_User_Login();

        $values = array('username' => 'test', 'password' => '');
        $this->assertFalse($form->isValid($values));

        $values = array('username' => '', 'password' => 'test');
        $this->assertFalse($form->isValid($values));

        $values = array('username' => 'test', 'password' => 'test');
        $this->assertTrue($form->isValid($values));
    }
}