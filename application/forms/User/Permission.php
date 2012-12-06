<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_User_Permission extends ZendX_JQuery_Form
{
    protected $_acl;

    protected $_resources;

    public function __construct(array $resources, $options = null)
    {
        $this->_resources = $resources;
        parent::__construct($options);
    }

    public function init()
    {
        $acl = $this->_acl = Zend_Registry::get('acl');
        $roles = $acl->getRoles();
        $resources = $this->_resources;

        $this->setAction($this->getView()->baseUrl('user/permission'));

        $this->setAttrib('id', 'mainForm')
        ->setDecorators(
            array(
                'FormElements',
                array('TabContainer', array('class' => 'tabs')),
                'Form',
            )
        );

        foreach ($roles as $role) {

            $subForm = new ZendX_JQuery_Form(
                array(
                    'decorators' => array(
                        'FormElements',
                         array(
                            'TabPane', array(
                               'jQueryParams' => array(
                                      'containerId' => 'mainForm',
                                      'title' => $role,
                            ))),
                        'Form',
                    ),
                )
            );

            foreach ($resources as $resource) {

                try {
                    $isAllowed = $acl->isAllowed($role, $resource);
                    $r = Doctrine_Core::getTable('Webenq_Model_Resource')
                    ->findOneByName($resource);
                } catch (Zend_Acl_Exception $e) {
                    $isAllowed = false;
                    try {
                        $r = new Webenq_Model_Resource();
                        $r->name = $resource;
                        $r->save();
                    }
                    catch (Exception $e) {
                    }
                }

                $element = $this->createElement(
                    'checkbox', base64_encode($resource), array(
                        'label' => $r->description ? $r->description : $r->name,
                        'value' => $isAllowed,
                        'belongsTo' => $role,
                    )
                );

                $element->getDecorator('Label')->setOption('placement', 'append');

                $subForm->addElement($element);
            }

            $subForm->addElement(
                $this->createElement(
                    'submit', 'submit', array(
                        'label' => 'save',
                    )
                )
            );

            $this->addSubForm($subForm, $role);
        }
    }

    public function store()
    {
        $acl = $this->_acl;

        foreach ($this->getValues() as $subformValues) {
            foreach ($subformValues as $roleName => $value) {
                foreach ($value as $key => $setting) {

                    $resourceName = base64_decode($key);
                    $resource = Doctrine_Core::getTable('Webenq_Model_Resource')
                    ->findOneByName($resourceName);
                    $role = Doctrine_Core::getTable('Webenq_Model_Role')
                    ->findOneByName($roleName);

                    /* check if settings has changed */
                    if ($setting == 0 && $acl->isAllowed($role->name, $resource->name)) {

                        /* turn off permission */
                        $roleResource = Doctrine_Core::getTable('Webenq_Model_RoleResource')
                        ->findOneByRoleIdAndResourceId($role->id, $resource->id);
                        if ($roleResource) $roleResource->delete();

                    } elseif ($setting == 1 && !$acl->isAllowed($role->name, $resource->name)) {

                        /* turn on permission */
                        $roleResource = new Webenq_Model_RoleResource();
                        $roleResource->role_id = $role->id;
                        $roleResource->resource_id = $resource->id;
                        try {
                            $roleResource->save();
                        }
                        catch (Exception $e) {
                        }
                    }
                }
            }
        }
    }
}