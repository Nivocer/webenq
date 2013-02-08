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
class Webenq_Test_ControllerTestCase_CategoryControllerTest extends Webenq_Test_Case_Controller
{

    public $setupDatabase=true;

    public function mockup(){
        $c=new Webenq_Model_Category();
        $c->save();
        $testString='id=1, should not get deleted';
        $c->addCategoryText('en',$testString);

        $c2=new Webenq_Model_Category();
        $c2->save();
        $testString2='id=2,has questionnaire';
        $c2->addCategoryText('en',$testString2);
        $c3=new Webenq_Model_Category();
        $c3->save();
        $testString3='id=3 has no questionnire';
        $c3->addCategoryText('en',$testString3);
        $q=new Webenq_Model_Questionnaire();
        $q->save();
        $q->category_id=2;
        $q->save();
    }
    public function testMockup()
    {
        $this->mockup();
        $c=new Webenq_Model_Category();
        $this->assertEquals(3,$c->getCategories()->count(), "we should have 3 categories");
        $this->assertEquals(1,$c->getCategories(3)->count(),"we should have category with id=3");
        $this->assertNotEquals(1,$c->getCategories(4)->count(),"we should not have category with id=4"); //

        $q=new Webenq_Model_Questionnaire();
        $this->assertEquals(0,$q->getQuestionnaires(1)->count(), "category 1 should have zero questionnaires");
        $this->assertEquals(1,$q->getQuestionnaires(2)->count(), "category 2 should have one questionnaires");
        $this->assertEquals(0,$q->getQuestionnaires(3)->count(), "category 3 should have zero questionnaires");
    }

    public function testCorrectControllerIsUsed()
    {
        $this->dispatch('/category');
        $this->assertController('category');
    }

    /* tests for index action */

    public function testCorrectActionIsUsedIndex()
    {
        $this->dispatch('/category/index');
        $this->assertAction("index");
    }
/* tests for add categories */
    public function testCorrectActionIsUsedAdd()
    {
        $this->dispatch('/category/add');
        $this->assertAction("add");
    }

/* tests for delete categories */

     public function testCorrectActionIsUsedDelete()
    {
        $this->mockup();
        $this->dispatch('/category/delete/id/1');
        $this->assertAction("delete");
    }
    public function testDeleteCategoryDefaultIdIsOne()
    {
        $controller=new CategoryController(new Zend_Controller_Request_Simple(), new Zend_Controller_Response_Http());
        $this->assertEquals(1,$controller->defaultCategoryId,"default action should be 1");
    }
    /* flashmessager problem
    public function testDeleteCategoryWithoutQuestionnaire()
    {
        //if we have questionnaires in a category, it should get the category id 1 before deleting the category
        $this->mockup();
        $c=new Webenq_Model_Category();
        $this->assertEquals(1,$c->getCategories(3)->count(),"testing precondition: we should have 1 category with id=3");
        $this->request->setMethod('POST')
            ->setPost(
                array(
                    'id' => '3',
                    'yes' => 'yes',
                )
            );
        $this->dispatch('/category/delete/');
        $this->assertAction("delete");
        $this->assertEquals(0,$c->getCategories(3)->count(), "we should have zero categories with id=3");
    }
    */
    public function testDeleteCategoryDontDeleteDefaultCategory()
    {
        //it is not allowed to delete category with id=1,
        //because when deleting categories, questionnaires in that category are put in this category
        $this->mockup();
        $this->request->setMethod('POST')
        ->setPost(
                array(
                        'id' => '1',
                        'yes' => 'yes',
                )
        );
        $c=new Webenq_Model_Category();
        $this->dispatch('/category/delete/');
        $this->assertAction("delete");
        $this->assertEquals(1,$c->getCategories(1)->count(), "we should not be able to delete category with id=1");
    }

    /* flashmessager problem
      public function testDeleteCategoryWithQuestionnaire()
    {
        //if we have questionnaires in a category, it should get the category id 1 before deleting the category
        $this->mockup();
        $c=new Webenq_Model_Category();
        $q=new Webenq_Model_Questionnaire();
        $this->assertEquals(0,$q->getQuestionnaires(1)->count(), "testing precondition: we should have zero questionnaire in category 1");
        $this->assertEquals(1,$q->getQuestionnaires(2)->count(), "testing precondition: we should have one questionnaire in category 2");
        $this->assertEquals(1,$c->getCategories(2)->count(), "testing precondition: we should have category with id=1");
        $this->request->setMethod('POST')
        ->setPost(
                array(
                        'id' => '2',
                        'yes' => 'yes',
                )
        );
        $this->dispatch('/category/delete/');
        $this->assertAction("delete");
        $this->assertEquals(1,$q->getQuestionnaires(1)->count(), "we should have one questionnaire in category 1");
        $this->assertEquals(0,$q->getQuestionnaires(2)->count(), "we should have zero questionnaire in category 2");
        $this->assertEquals(0,$c->getCategories(2)->count(), "not able to delete category with questionnaires");

    }
    */
    public function testCategoryOneExist(){
        //We need to have category with id=1, because when deleting categories, questionnaires are put in this questionnaire
        //test with real database, to test if fixture (or database) is correct
        $this->loadDatabase();
        $c=new Webenq_Model_Category();
        $this->assertEquals(1,$c->getCategories(1)->count(), "no category with id = 1 exist in fixture/database");
    }

/* tests for edit categories */

    public function testCorrectActionIsUsedEdit()
    {
        $this->dispatch('/category/edit/id/2');
        $this->assertAction("edit");
    }

/* tests for order categories */

    public function testCorrectActionIsUsedOrder()
    {
        $this->dispatch('/category/order');
        $this->assertAction("order");
    }
}