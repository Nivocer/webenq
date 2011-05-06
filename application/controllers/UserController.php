<?php
/**
 * Controller class
 *
 * @category    Webenq
 * @package        Controllers
 * @author        Bart Huttinga <b.huttinga@nivocer.com>
 */
class UserController extends Zend_Controller_Action
{
    /**
     * Renders the login page
     *
     * @return void
     */
    public function loginAction()
    {
        $form = new Webenq_Form_User_Login();
        $form->redirect->setValue($this->_request->redirect);

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            if (User::login($form->username->getValue(), $form->password->getValue())) {
                if ($this->_request->redirect) {
                    $this->_redirect(base64_decode($this->_request->redirect));
                }
                $this->_redirect('/');
            }
        }

        $this->view->form = $form;
    }

    /**
     * Ends the session of the current user
     *
     * @return void
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }

    /**
     * Renders a page for editing users
     *
     * @return void
     */
    public function userAction()
    {
        $addForm = new Webenq_Form_User_User_Add();
        $this->view->addForm = $addForm;

        if ($this->_request->isPost()) {
            if ($this->_request->id) {
                $user = Doctrine_Core::getTable('User')->find($this->_request->id);
                if ($this->_request->yes || $this->_request->no) {
                    if ($this->_request->yes) {
                        $user->delete();
                    } else {
                        $this->_redirect('/user/user');
                    }
                } else {
                    $editForm = $this->view->editForm = new Webenq_Form_User_User_Edit($user);
                    if ($editForm->isValid($this->_request->getPost())) {
                        if ($editForm->store()) {
                            $this->_redirect('/user/user');
                        }
                    }
                }
            } else {
                if ($addForm->isValid($this->_request->getPost())) {
                    if ($addForm->store()) {
                        $this->_redirect('/user/user');
                    }
                }
            }
        } else {
            if ($this->_request->edit) {
                $user = Doctrine_Core::getTable('User')->find($this->_request->edit);
                $editForm = new Webenq_Form_User_User_Edit($user);
                $this->view->editForm = $editForm;
            } elseif ($this->_request->delete) {
                $user = Doctrine_Core::getTable('User')->find($this->_request->delete);
                $deleteForm = new Webenq_Form_Confirm($user->id,
                    "Weet u zeker dat u gebruiker '$user->fullname' wilt verwijderen?");
                $this->view->deleteForm = $deleteForm;
            }
        }

        $this->view->users = Doctrine_Core::getTable('User')->findAll();
    }

    /**
     * Renders a page for editing roles
     *
     * @return void
     */
    public function roleAction()
    {
        $addForm = new Webenq_Form_User_Role_Add();
        $this->view->addForm = $addForm;

        if ($this->_request->isPost()) {
            if ($this->_request->id) {
                $role = Doctrine_Core::getTable('Role')->find($this->_request->id);
                if ($this->_request->yes || $this->_request->no) {
                    if ($this->_request->yes) {
                        $role->delete();
                    } else {
                        $this->_redirect('/user/role');
                    }
                } else {
                    $editForm = $this->view->editForm = new Webenq_Form_User_Role_Edit($role);
                    if ($editForm->isValid($this->_request->getPost())) {
                        if ($editForm->store()) {
                            $this->_redirect('/user/role');
                        }
                    }
                }
            } else {
                if ($addForm->isValid($this->_request->getPost())) {
                    if ($addForm->store()) {
                        $this->_redirect('/user/role');
                    }
                }
            }
        } else {
            if ($this->_request->edit) {
                $role = Doctrine_Core::getTable('Role')->find($this->_request->edit);
                $editForm = new Webenq_Form_User_Role_Edit($role);
                $this->view->editForm = $editForm;
            } elseif ($this->_request->delete) {
                $role = Doctrine_Core::getTable('Role')->find($this->_request->delete);
                $deleteForm = new Webenq_Form_Confirm($role->id,
                    "Weet u zeker dat u rol '$role->name' wilt verwijderen?");
                $this->view->deleteForm = $deleteForm;
            }
        }

        $this->view->roles = Doctrine_Core::getTable('Role')->findAll();
    }

    /**
     * Renders the form for editing role's permissions
     *
     * @return void
     */
    public function permissionAction()
    {
        $resources = $this->_getResources();
        $form = new Webenq_Form_User_Permission($resources);

        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $form->store();
                $this->_redirect('/user/permission');
            }
        }

        $this->view->form = $form;
    }

    protected function _getResources()
    {
        $front = $this->getFrontController();
        $controllerDirectories = $front->getControllerDirectory();
        $controllerDirectory = $controllerDirectories['default'];
        $resources = array();

        foreach (scandir($controllerDirectory) as $file) {
            if (preg_match('/Controller\.php$/', $file)) {
                include_once $controllerDirectory . DIRECTORY_SEPARATOR . $file;
            }
        }

        foreach (get_declared_classes() as $class) {
            if (preg_match('/^(.*)Controller$/', $class, $matches)) {
                $controller = strtolower(substr(preg_replace('/([A-Z]{1})/', '-$1', $matches[1]), 1));
                foreach (get_class_methods($class) as $action) {
                    if (preg_match('/^(.*)Action$/', $action, $matches)) {
                        $action = strtolower(preg_replace('/([A-Z]{1})/', '-$1', $matches[1]));
                        $resources[] = "$controller/$action";
                    }
                }
            }
        }

        return $resources;
    }
}
