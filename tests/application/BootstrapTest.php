<?php
class Webenq_Test_Bootstrap_BootstrapTest extends Webenq_Test_Case_Bootstrap
{
    public function testSimpleConfigurationOverride()
    {
        Webenq_Application::$defaultConfig = new Zend_Config(array(
            'env1' => array('test' => 'defaultValue'),
        ), true);

        Webenq_Application::$overrideConfig = new Zend_Config(array(
        	'env1' => array('test' => 'overrideValue'),
        ));

        $application = new Webenq_Application('env1');
        $this->assertEquals('overrideValue', $application->getOption('test'));
    }

    public function testNewSectionCreatedInOverrideConfig()
    {
        Webenq_Application::$defaultConfig = new Zend_Config(array(
            'env1' => array('test' => 'defaultValue'),
        ), true);

        Webenq_Application::$overrideConfig = new Zend_Config(array(
        	'env2' => array('test' => 'overrideValue'),
        ));

        $application = new Webenq_Application('env2');
        $this->assertEquals('overrideValue', $application->getOption('test'));
    }

    public function testExtendedSectionOverrideConfig()
    {
        $xml = '<?xml version="1.0"?>
            <configdata>
                <env1>
                	<test1>defaultValueEnv1Test1</test1>
                	<test2>defaultValueEnv1Test2</test2>
                </env1>
                <env2 extends="env1"></env2>
            </configdata>';

        Webenq_Application::$defaultConfig = new Zend_Config_Xml($xml, null, array(
        	'allowModifications' => true));

        $xml = '<?xml version="1.0"?>
            <configdata>
                <env1>
                	<test2>overrideValueEnv1Test2</test2>
                </env1>
                <env2 extends="env1">
                	<test2>overrideValueEnv2Test2</test2>
                </env2>
            </configdata>';

        Webenq_Application::$overrideConfig = new Zend_Config_Xml($xml);

        $application = new Webenq_Application('env1');
        $this->assertEquals('defaultValueEnv1Test1', $application->getOption('test1'));
        $this->assertEquals('overrideValueEnv1Test2', $application->getOption('test2'));

        $application = new Webenq_Application('env2');
        $this->assertEquals('defaultValueEnv1Test1', $application->getOption('test1'));
        $this->assertEquals('overrideValueEnv2Test2', $application->getOption('test2'));
    }
}