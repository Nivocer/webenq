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
//            $config['schema_path'] . '/7.yml',
//            $config['schema_path'] . '/8.yml');
//        die('Success!');

//        try {
//            $migration->migrate(4);
//        }
//        catch(Doctrine_Migration_Exception $exception) {
//            die('<pre>' . $exception->getMessage() . '</pre>');
//        }
//        die('Success!');

        if ($current !== $latest) {

            $form = new Zend_Form();
            $form->addElement($form->createElement('submit', 'migrate', array(
            	'label' => 'Migrate to latest version')));

            if ($this->_request->isPost()) {
                if ($this->_request->migrate) {
                    try {
                        $migration->migrate($latest);
                    }
                    catch(Doctrine_Migration_Exception $exception) {
                        die('<pre>' . $exception->getMessage() . '</pre>');
                    }
                    $this->_redirect($this->_request->getPathInfo());
                }
            }
            $this->view->form = $form;
        }

        $this->view->current = $current;
        $this->view->latest = $latest;
    }
}