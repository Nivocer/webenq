<?php
/**
 * WebEnq4 Library
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
 * @category   Webenq
 * @package    Webenq_Controllers
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

class Webenq_Test_ControllerTestCase_CategoryControllerTest extends Webenq_Test_Case_Controller
{

    public $setupDatabase=true;

    public function testCorrectControllerIsUsed()
    {
        $this->dispatch('/category');
        $this->assertController('category');
    }
    public function testCorrectActionIsUsedIndex()
    {
        $this->dispatch('/category/index');
        $this->assertAction("index");
    }
    public function testCorrectActionIsUsedAdd()
    {
        $this->dispatch('/category/add');
        $this->assertAction("add");
    }
     public function testCorrectActionIsUsedDelete()
    {
        $this->loadDatabase();
        //$category=new Webenq_Model_Category();
        //var_dump($category->getCategories(1)->getFirst()->toArray());
        $this->dispatch('/category/delete/id/2');
        $this->assertAction("delete");
    }
    public function testDeleteCategoryWithQuestionnaire()
    {
        //if we have questionnaires in a category, it should get the category id 1 before deleting the category
    }
    public function testDeleteCategoryDontDeleteCategoryOne()
    {
        //it is not allowed to delete category with id=1,
        //because when deleting categories, questionnaires in that category are put in this category
    }
    public function testCategoryOneExist(){
        //We need to have category with id=1, because when deleting categories, questionnaires are put in this questionnaire
    }
    public function testCorrectActionIsUsedEdit()
    {
        $this->dispatch('/category/edit/id/2');
        $this->assertAction("edit");
    }
    public function testCorrectActionIsUsedOrder()
    {
        $this->dispatch('/category/order');
        $this->assertAction("order");
    }


}