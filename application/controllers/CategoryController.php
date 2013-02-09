<?php /**
 * WebEnq4
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
 * @package    Webenq_Questionnaires_Manage
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * @package    Webenq_Questionnaires_Manage
 */
 class CategoryController extends Zend_Controller_Action
{
    public $defaultCategoryId=1;
    /**
     * Controller actions that are ajaxable
     *
     * @var array
     */
    public $ajaxable = array(
        //'add' => array('html'),
        //'edit' => array('html'),
    );

    /**
     * Renders the overview of categories
     *
     * @return void
    */
    public function indexAction()
    {
        $this->view->pageTitle = t("Categories");
        $this->view->categories = Webenq_Model_Category::getCategories();
    }
    public function addAction()
    {
        $form = new Webenq_Form_Category_Add();
        $form->setAction($this->_request->getRequestUri());

        if ($this->_helper->form->isPostedAndValid($form)) {
            $category = new Webenq_Model_Category();
            $category->fromArray($form->getValues());
            $category->save();
            //$this->_helper->json(array('reload' => true));
            $this->_helper->getHelper('FlashMessenger')
                ->setNamespace('success')
                ->addMessage('category added succesfully');

            $this->_redirect('category');
        }
        $this->view->pageTitle = t("Add category");
        $this->view->form = $form;
    }


    public function editAction()
    {

        $category = Webenq_Model_Category::getCategories($this->_request->id)->getFirst();
        if (!$category) {
            $this->_redirect('category');
            return; //extra return for phpunit tests
        }

        $form = new Webenq_Form_Category_Edit($category);

        if ($this->_helper->form->isPostedAndValid($form)) {
            $category->fromArray($form->getValues());
            $category->save();
            //$this->_redirect($this->_request->getPathInfo());
            $this->_helper->getHelper('FlashMessenger')
                ->setNamespace('success')
                ->addMessage('category saved succesfully');

            $this->_redirect('category');
        }

        $this->view->pageTitle = t("Edit category");
        $this->view->form = $form;
        $this->view->category = $category;
    }
    /**
     * Renders the confirmation form for deleting a category and perform the deleting.
     * If a categor has questionnaires, the questionnaires are moved to default category with id =1
     * So it is forbidden to delete category with id=1
     *
     * @return void
     */
    public function deleteAction()
    {
        $defaultCategoryId=$this->defaultCategoryId;

        //does category exist
        $category = Doctrine_Core::getTable('Webenq_Model_Category')
        ->find($this->_request->id);

        if (!$category){
            $this->_helper->getHelper('FlashMessenger')
                ->setNamespace('error')
                ->addMessage('category does not exist, or no category selected');
            $this->_redirect('/category');
            return; //extra return for phpunit
        }

        //display confirmation dialog
        //set confirmationText based on number of questionnaires
        $q=new Webenq_Model_Questionnaire();
        $questionnaires=$q->getQuestionnaires($this->_request->id);

        if ($questionnaires->count()>0) {
            //determin text of default category 1.
            $categoryDefault = Doctrine_Core::getTable('Webenq_Model_Category')->find($defaultCategoryId);
            $confirmationText = sprintf(
                t(
                        'Are you sure you want to delete the category: %s (the %d questionnaires in this category are moved to the category: %s)?'
                ),
                $category->getCategoryText()->text,
                $questionnaires->count(),
                $categoryDefault->getCategoryText()->text
            );
        } else {
            $confirmationText = sprintf(
                t(
                        'Are you sure you want to delete the category: %s?'
                ),
                $category->getCategoryText()->text
            );
        }

        $form = new Webenq_Form_Confirm($category->id, $confirmationText);
        $form->setAction($this->view->baseUrl('/category/delete/id/' . $this->_request->id));

        /* process posted data */
        //only delete if category not equal to $defaultCategory
        if ($this->_request->isPost() && $this->_request->id <> $defaultCategoryId) {
            if ($this->_request->yes ) {
                //if we have questionnaires in this category we should update questionnaire properties category_id->1
                if ($questionnaires->count()>0) {
                    foreach ($questionnaires as $questionnaire){
                        $questionnaire->setArray(array('category_id'=>$defaultCategoryId));
                        $questionnaire->save(); //moet dit nog gebeuren
                    }
                }
                if (Webenq_Model_Questionnaire::getQuestionnaires($this->_request->id)->count()==0){
                    $category->delete();
                } else {
                    throw new Exception("Cannot delete category, it has still questionnaires");
                }
            }
            if ($this->_request->isXmlHttpRequest()) {
                if ($this->_request->yes) {
                    $this->_helper->json(array('reload' => true));
                } else {
                    $this->_helper->json(array('reload' => false));
                }
            } else {
                $this->_helper->getHelper('FlashMessenger')
                ->setNamespace('success')
                ->addMessage('category deleted succesfully');
                $this->_redirect('/category');
                return;

            }
        }

        /* render view */
        $this->view->pageTitle = t("Delete category");
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->form = $form;
        $this->_response->setBody($this->view->render('confirm.phtml'));
    }

    public function orderAction()
    {
        /* disable view/layout rendering */
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_helper->hasHelper('layout')) {
            $this->_helper->layout->disableLayout();
        }
        if ($this->_request->category){
            $this->_orderCategories(Zend_Json::decode($this->_request->category));
        }

    }
    protected function _orderCategories(array $data) {
        if (count($data) === 0) {
            return;
        }

        $cIds = array();
        foreach ($data as $key => $id) {
            $id = (int) str_replace('c_', null, $id);
            $cIds[] = $id;
        }

        // reset categories
        Doctrine_Query::create()
            ->update('Webenq_Model_Category c')
            ->set('weight', '?', 1)
            ->whereIn('c.id', $cIds)
            ->execute();

        // get categories
        $cs = Doctrine_Query::create()
            ->from('Webenq_Model_Category c')
            ->whereIn('c.id', $cIds)
            ->execute();

        // set new weight
        foreach ($cs as $weight => $c) {
            $c->weight = array_search($c->id, $cIds);
            $c->save();
        }
    }
}
