<?php
/**
 * Webenq
 *
 *  LICENSE
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    Webenq_Tests
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * @package    Webenq_Tests
 */
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