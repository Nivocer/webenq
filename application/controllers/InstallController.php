<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class InstallController extends Zend_Controller_Action
{
    /**
     * Messages of success and failure to show after installation-test has run
     *
     * @var array
     */
    protected $_messages = array(
    	'success' => array(),
    	'failure' => array()
    );

    /**
     * Webenq thirdparty dependencies that should be in the include path
     *
     * @var array Class name as key and file name as value
     */
    protected static  $_dependencies = array(
    	'FPDF' => 'fpdf/fpdf.php',
    	'ODS' => 'ods-php/ods.php',
    	'PHPExcel' => 'PHPExcel/Classes/PHPExcel.php',
    );

    public function testAction()
    {
//        $this->_testMemoryLimit();
        $this->_testMaxExecutionTime();
        $this->_testMagicQuotesTurnedOff();
        $this->_testTempDir();
        $this->_testDependencies();
        $this->_testDatabaseSchema();
        $this->view->messages = $this->_messages;
    }

    protected function _testMemoryLimit()
    {
        try {
            $value = '512MB';
            Webenq::setMemoryLimit($value);
        } catch(Exception $e) {
            $this->_messages['failure'][] = "Memory limit could not be set to <strong>$value</strong>.";
            return;
        }
        $this->_messages['success'][] = "Memory limit succesfully set to <strong>$value</strong>.";
    }

    protected function _testMaxExecutionTime()
    {
        try {
            $value = '300';
            Webenq::setMaxExecutionTime($value);
        } catch(Exception $e) {
            $this->_messages['failure'][] = "Maximum execution time could not be set to <strong>$value</strong>.";
            return;
        }
        $this->_messages['success'][] = "Maximum execution time succesfully set to <strong>$value</strong>.";
    }

    protected function _testMagicQuotesTurnedOff()
    {
        if (ini_get('magic_quotes_runtime') == 0) {
            $this->_messages['success'][] = "Magic quotes are <strong>disabled</strong>";
        } else {
            $this->_messages['failure'][] = "Magic quotes are <strong>enabled</strong>, but should be disabled";
        }
    }

    protected function _testTempDir()
    {
        $cacheBackend = new Zend_Cache_Backend();
        try {
            $tmpDir = $cacheBackend->getTmpDir();
        } catch (Zend_Cache_Exception $e) {
            $this->_messages['failure'][] = "No temp. dir could be detected.";
            return;
        }
        $this->_messages['success'][] = "Temp. dir is set to <strong>$tmpDir</strong>.";
    }

    protected function _testDependencies()
    {
        foreach (self::$_dependencies as $class => $file) {
            @include_once $file;
            if (class_exists($class)) {
                $this->_messages['success'][] = "Found class <strong>$class</strong>.";
            } else {
                $this->_messages['failure'][] = "Could not find class <strong>$class</strong>. Make sure the file <strong>$file</strong> is in the include path.";
            }
        }
    }

    protected function _testDatabaseSchema()
    {
        // get current and latest db schema version
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('doctrine');
        $migration = new Doctrine_Migration($config['migrations_path']);
        $current = (int) $migration->getCurrentVersion();
        $latest = (int) $migration->getLatestVersion();
        if ($current === $latest) {
            $this->_messages['success'][] = "Database schema is up to date.";
        } else {
            $this->_messages['failure'][] = "Database schema is out of date: current version is $current, lates version is $latest.";
        }
    }

    /**
     * Renders the installer
     */
    public function indexAction()
    {
        // get doctrine config settings
        $config = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')->getOption('doctrine');

        // get current and latest db schema version
        $migration = new Doctrine_Migration($config['migrations_path']);
        $current = (int) $migration->getCurrentVersion();
        $latest = (int) $migration->getLatestVersion();

//        try {
//            Doctrine_Core::generateModelsFromYaml($config['yaml_schema_path'], $config['models_path']);
//        } catch(Exception $exception) {
//            die('<pre>' . $exception->getMessage() . '</pre>');
//        }
//        die('Success!');

//        try {
//            Doctrine_Core::generateMigrationsFromDiff(
//                $config['migrations_path'],
//                $config['yaml_schema_versions_path'] . '/2.yml',
//                $config['yaml_schema_versions_path'] . '/current/schema.yml');
//        } catch (Exception $e) {
//            die($e->getMessage());
//        }
//        die('Success!');

//        try {
//            $migration->migrate(6);
//        }
//        catch(Doctrine_Migration_Exception $exception) {
//            die('<pre>' . $exception->getMessage() . '</pre>');
//        }
//        die('Success!');

//        $form = new Zend_Form();
//        if ($current === $latest) {
//            $count = Doctrine_Query::create()->from('Webenq_Model_AnswerPossibility')->count();
//            if ($count === 0) {
//                $form->addElement($form->createElement('submit', 'fixtures', array(
//                	'label' => 'Load test data')));
//            }
//        } else {
//            $form->addElement($form->createElement('submit', 'migrate', array(
//                'label' => 'Migrate to latest version')));
//        }
//
//        if ($this->_request->isPost()) {
//            if ($this->_request->migrate) {
//                try {
//                    $migration->migrate($latest);
//                }
//                catch(Doctrine_Migration_Exception $exception) {
//                    die('<pre>' . $exception->getMessage() . '</pre>');
//                }
//                $this->_redirect($this->_request->getPathInfo());
//            } elseif ($this->_request->fixtures) {
//                try {
//                    Doctrine_Core::loadData($config['fixtures_path'], true);
//                }
//                catch(Doctrine_Exception $exception) {
//                    die('<pre>' . $exception->getMessage() . '</pre>');
//                }
//                $this->_redirect($this->_request->getPathInfo());
//            }
//        }
//
//        $this->view->form = $form;
        $this->view->current = $current;
        $this->view->latest = $latest;
    }

    /**
     * Renders the installer
     */
    public function migrateAction()
    {
        // get doctrine config settings
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $config = $bootstrap->getOption('doctrine');

//        Doctrine_Core::dropDatabases();
//        Doctrine_Core::createDatabases();

        // get current and latest db schema version
        $migration = new Doctrine_Migration($config['migrations_path']);
        $current = (int) $migration->getCurrentVersion();
        $latest = (int) $migration->getLatestVersion();

        // migrate database to latest known migration
        if ($latest > $current) {
            try {
                $migration->migrate();
            } catch (Exception $e) {
                die(nl2br($e->getMessage()));
            }
        }

        // calculate differences between current database structure
        // and YAML file with current schema
        $from = 'doctrine';
        $to = $config['yaml_schema_path'] . '/schema.yml';
        $diff = new Doctrine_Migration_Diff($from, $to, $migration);
        $changes = $diff->generateChanges();
        $hasChanges = false;
        foreach ($changes as $key => $values) {
            $oppositeKey = preg_match('/^created_/', $key)
                ? preg_replace('/^created_/', 'dropped_', $key)
                : preg_replace('/^dropped_/', 'created_', $key);
            if (count(array_diff_assoc($changes[$oppositeKey], $changes[$key])) > 0
                || count(array_diff_assoc($changes[$key], $changes[$oppositeKey])) > 0)
            {
                $hasChanges = true;
                break;
            }
        }

        if ($hasChanges) {
            // generate migration classes from database to current
            try {
                $diff->generateMigrationClasses();
            } catch (Exception $e) {
                die(nl2br($e->getMessage()));
            }

            // generate models from YAML file with current schema
            Doctrine_Core::generateModelsFromYaml(
                $config['yaml_schema_path'],
                $config['models_path'],
                $config['generate_models_options']);

            // perform migration
            try {
                $migration->migrate();
            } catch (Exception $e) {
                die(nl2br($e->getMessage()));
            }
        }

        $current = (int) $migration->getCurrentVersion();
        die('Mirgated to db schema version ' . $current);
    }
}
