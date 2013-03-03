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
abstract class Webenq_Test_Case_Controller extends Zend_Test_PHPUnit_ControllerTestCase
{
    // as in Webenq_Test_Case_Fixture
    public $setupDatabase = false;
    private $_databaseCreated = false;
    private $_databaseFilled = false;

    // as in Webenq_Test_Case_Fixture
    public function createDatabase() {
        $this->application = new Webenq_Application(APPLICATION_ENV);
        $this->doctrineConfig = $this->application->getBootstrap()->getOption('doctrine');

        // set up database for testing
        $this->dropDatabase();
        Doctrine_Core::createDatabases();
        Doctrine_Core::createTablesFromModels($this->doctrineConfig['models_path']);
        $this->_databaseCreated = true;
        $this->_databaseFilled = false;
    }

    public function dropDatabase() {
        try {
            Doctrine_Core::dropDatabases();
            $this->_databaseCreated = false;
            $this->_databaseFilled = false;
        } catch (Exception $e) {
        }
    }

    // as in Webenq_Test_Case_Fixture
    public function loadDatabase() {
        $this->createDatabase();
        Doctrine_Core::loadData($this->doctrineConfig['data_fixtures_path'], false);
        $this->_databaseFilled = true;
    }

    protected function setUp()
    {
        parent::setUp();

        // as in Webenq_Test_Case_Fixture
        if ($this->setupDatabase) {
            $this->createDatabase();
        }

        $this->getFrontController()->setControllerDirectory(APPLICATION_PATH . '/controllers');
    }

    protected function tearDown()
    {
        $this->dropDatabase();
        parent::tearDown();
    }
}
