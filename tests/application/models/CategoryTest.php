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
 * @package    Webenq
 * @subpackage Models
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

class Webenq_Test_Model_CategoryTest extends Webenq_Test_Case_Model
{
    public $setupDatabase = true;

    public function testAddCategoryTextIsSuccesfull(){
        $category=new Webenq_Model_Category();
        $category->save();
        $testString='add category text is succesfull';
        $category->addCategoryText('en',$testString);
        $this->assertEquals($testString, $category->getCategoryText('en')->text);
        $category->delete();

        //can we add empty string
        $category=new Webenq_Model_Category();
        $category->save();
        $emptyString='';
        $category->addCategoryText('nl',$emptyString);
        $this->assertEquals($emptyString, $category->getCategoryText('nl')->text);
        $category->delete();

    }
    public function testUpdateCategoryTextIsSuccesfull(){
        //setup
        $category=new Webenq_Model_Category();
        $category->save();
        $oldString='update category text is succesfull old string';
        $category->addCategoryText('en',$oldString);
        $testString='update category text is succesfull new string';
        $category->addCategoryText('en',$testString);
        $this->assertEquals($testString, $category->getCategoryText('en')->text);
        //delete text if empty string
        $emptyString='';
        $category->addCategoryText('en',$emptyString);
        $this->assertEquals($emptyString, $category->getCategoryText('en')->text);
        $category->delete();

    }
    public function testaddMultipleCategoryTextsIsCorrect(){
        $category=new Webenq_Model_Category();
        $category->save();

        $testString1='add multiple category Text:en';
        $testString2='add multiple category Text:nl';
        $values = array('en' => $testString1, 'nl'=>$testString2);
        $category->addCategoryTexts($values);
        $this->assertEquals($testString1, $category->getCategoryText('en')->text);
        $this->assertEquals($testString2, $category->getCategoryText('nl')->text);
        $category->delete();
    }
    public function testgetCategoryTextIsCorrect(){
        $category=new Webenq_Model_Category();
        $category->save();
        $testString='get Category Text is correct:en';
        $category->addCategoryText('en',$testString);
        $this->assertEquals($testString, $category->getCategoryText('en')->text);

        //if we question not existent language, we get string of first prefered language
        $this->assertEquals($testString, $category->getCategoryText('nl')->text);

        //language not in prefered language
        $this->assertEquals($testString, $category->getCategoryText('xx')->text);
        $category->delete();
    }
    /*
     * test last part of webenq_model_category, but there seems no route to get to that code,
     * it is triggered if no questionText is available for a category, but this fails the database validity
     */
/*     public function testGetCategoryTextIsCorrectNoTranslationFound()
    {
        //no entry in categoryText?
        $category=new Webenq_Model_Category();
        $category->save();
        $testString="getCategoryTextIsCorrectNoTranslationFound";
        $this->assertEquals($testString, $category->getCategoryText('en')->text);
    } */
    /*
     * get all categories from the database fixture, currently we have 3 categories,
     * the first one (by weight) is 11
     */
    public function testGetAllCategoriesIsCorrect(){
        $this->loadDatabase();
        $category=new Webenq_Model_Category();
        $this->assertEquals(3, $category->getCategories()->count());
        $this->assertEquals(11, $category->getCategories()->getFirst()->id);
        $category->delete();

    }
    public function testGetCategorieByIdIsCorrect(){
        $this->loadDatabase();
        $category=new Webenq_Model_Category();
        $this->assertEquals(1, $category->getCategories(11)->count());
        $this->assertEquals(11, $category->getCategories(11)->getFirst()->id);
        $category->delete();
    }

}
