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

//        Doctrine_Core::generateMigrationsFromDiff(
//            $config['migrations_path'],
//            $config['schema_path'] . '/4.yml',
//            $config['schema_path'] . '/5.yml');
//        die('Success!');

//        try {
//            $migration->migrate(5);
//        }
//        catch(Doctrine_Migration_Exception $exception) {
//            die('<pre>' . $exception->getMessage() . '</pre>');
//        }
//        die('Success!');

        $form = new Zend_Form();

        if ($current === $latest) {
            $count = Doctrine_Query::create()->from('Webenq_Model_AnswerPossibility')->count();
            if ($count === 0) {
                $form->addElement($form->createElement('submit', 'fixtures', array(
                	'label' => 'Load test data')));
            }
        } else {
            $form->addElement($form->createElement('submit', 'migrate', array(
                'label' => 'Migrate to latest version')));
        }

        if ($this->_request->isPost()) {
            if ($this->_request->migrate) {
                try {
                    $migration->migrate($latest);
                }
                catch(Doctrine_Migration_Exception $exception) {
                    die('<pre>' . $exception->getMessage() . '</pre>');
                }
                $this->_redirect($this->_request->getPathInfo());
            } elseif ($this->_request->fixtures) {
                try {
                    Doctrine_Core::loadData($config['fixtures_path'], true);
                }
                catch(Doctrine_Exception $exception) {
                    die('<pre>' . $exception->getMessage() . '</pre>');
                }
                $this->_redirect($this->_request->getPathInfo());
            }
        }

        $this->view->form = $form;
        $this->view->current = $current;
        $this->view->latest = $latest;
    }
}